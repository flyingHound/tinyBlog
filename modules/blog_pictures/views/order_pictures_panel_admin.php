<h1>
    <?= $headline ?>
    <span class="smaller hide-sm">
        ( <?= ucfirst($target_module) ?> ID: <?= $target_module_id ?> )
    </span>
</h1>
<p style="font-size: 15px;">
    Take the picture to the desired position. Don´t forget to <u>SAVE</u> when you are finished. If you want to delete a picture, make double click over the picture.
</p>

<?#= json($data); ?>
<?= flashdata() ?>
<?= validation_errors() ?>
<div id="errorMsg"></div>

<div class="card">

    <div class="card-heading">
        Options <span style="float: right;">count: <?= $rows_count ?></span>
    </div>
    <div class="card-body">
        <?php 
        echo anchor($cancel_url, 'GO BACK', array("class" => "button alt")); 
        ?>
        
        <input onclick="submitOrder()" type='button' class="button" value='Save Order' id='submit' style="width: 20em;"/>
        <?php 
        echo anchor($upload_url, 'UPLOAD MORE PICTURES', array("class" => "button go-right")); 
        ?>
    </div>
    <div class="card-body">

        <?php if (count($rows) == 0) { ?>

        <div id="gallery-pics" style="border-bottom: 0; grid-template-columns: repeat(1, 1fr);">
            <p class="text-center">There is no Pictures for this record yet</p>
        </div>

        <?php } else { ?>

        <div id="gallery-pics">

            <?php
            $i = 1;
            foreach ($rows as $row) {
            
            $picture_path = $target_directory.$row->picture;
            echo '<div class="sort" id="'.$row->id.'" ondblclick="openPicPreview(\'preview-pic-modal\', \''.$picture_path.'\')">';
            echo '<img src="'.$picture_path.'" alt="'.$row->picture.'"><br><span>#'.$i.'</span></div>';
            $i++;

            } ?>
        </div>
        <?php } ?>
    </div>


    <div class="card-footer">
        <input onclick="submitOrder()" type='button' class="button go-right" value='Save Order' id='submit' />
    </div>
</div>
<?#= json($data); ?>

<div class="modal" id="preview-pic-modal" style="display: none;">
    <div class="modal-heading"><i class="fa fa-image"></i> Picture</div>
    <div class="modal-body">
        <p id="preview-pic"></p>

        <?php 
        $attr_close = array( 
            "class" => "alt",
            "onclick" => "closeModal()"
        );
        echo '<p>'.form_button('close', 'Cancel', $attr_close);

        $attr_ditch_pic = array( 
            "class" => "danger",
            "id" => "ditch-pic-btn",
            "onclick" => "ditchGalleryPic()"
        );
        echo form_button('delete_pic', 'DELETE THIS PICTURE', $attr_ditch_pic);

        $attr_rotate_pic = array( 
            "class" => "alt",
            "id" => "rotate-pic-btn",
            "onclick" => "rotatePreviewPic()"
        );
        echo form_button('rotate_pic', 'ROTATE THIS PICTURE', $attr_rotate_pic); /**/
        echo '</p>';
        ?>
    </div>
</div>

<script type="text/javascript">

    $(document).ready(function() {
        $( "#gallery-pics" ).sortable({
            stop: function(event, ui) { ordenOk(); }
        });
        $( "#gallery-pics" ).disableSelection();
    });

    function ordenOk() {
        document.getElementById("errorMsg").innerHTML = 'Saving Ordered Pictures';
    }

</script>

<script>
    var baseUrl = '<?= BASE_URL ?>';
    var segment1 = '<?= $target_module ?>';
    var updateId = '<?= $target_module_id ?>';
    const token = '<?= $token ?>';
    const targetModule = '<?= $target_module ?>';
    const uploadUrl = '<?= $upload_url ?>';
    const deleteUrl = '<?= $delete_url  ?>';

    function submitOrder() {
        var token = '<?= $token ?>';
        var nodes = document.getElementsByClassName("sort");

        for (var i = 0; i < nodes.length; i++) {

            var recordId = document.getElementsByClassName("sort")[i].id;

            var pos = i+1;
            var id = recordId;
            var params = {
                id: id,
                priority: pos
            }

            var orderUrl = '<?= BASE_URL ?>api/update/blog_pictures/' + recordId;

            // takes: url, token, params
            var http = new XMLHttpRequest()
            http.open('POST', orderUrl)
            http.setRequestHeader("trongateToken", token)
            http.send(JSON.stringify(params))
            http.onload = function() {
                window.location.reload()
            }
        }
        console.log('Ordered the pictures');
    }

    function openPicPreview(modalId, picPath) {
        openModal(modalId);
        var targetEl = document.getElementById('preview-pic');

        while (targetEl.firstChild) {
            targetEl.removeChild(targetEl.lastChild);
        }

        var imgPreview = document.createElement('img');
        imgPreview.setAttribute("src", picPath);
        targetEl.appendChild(imgPreview);

        var ditchPicBtn = document.getElementById('ditch-pic-btn');
        var ditchPicBtnText = ditchPicBtn.innerHTML;
        var iconCode = '<i class="fa fa-trash"></i>';
        ditchPicBtn.innerHTML = ditchPicBtnText.replace(iconCode, '');
        ditchPicBtn.innerHTML = iconCode + ditchPicBtn.innerHTML;
    }

    function ditchPreviewPic() {
        var el = document.querySelector("div.user-panel.main input[name='login']");
        var previewPic =  document.querySelector('#preview-pic img');
        var picPath = previewPic.src;
        var removePicUrl = baseUrl + 'blog_filezone/upload/' + segment1 + '/' + updateId;
        
        const http = new XMLHttpRequest();
        http.open('DELETE', removePicUrl);
        http.setRequestHeader('Content-type', 'application/json');
        http.setRequestHeader('trongateToken', token);
        http.send(picPath);
        http.onload = function() {
            window.location.reload();
        }
        closeModal();
    }

    function ditchGalleryPic() {
        var previewPic = document.querySelector('#preview-pic img');
        var picPath = previewPic.src;
        var removePicUrl = baseUrl + 'blog_filezone/upload/' + segment1 + '/' + updateId;

        const http = new XMLHttpRequest();
        http.open('DELETE', removePicUrl);
        http.setRequestHeader('Content-type', 'application/json');
        http.setRequestHeader('trongateToken', token);
        
        // Daten als JSON senden
        const data = JSON.stringify({ picture_path: picPath });
        http.send(data);

        http.onload = function() {
            if (http.status === 200) {
                window.location.reload();
            } else {
                console.error('Fehler:', http.status, http.responseText);
                alert('Fehler beim Löschen: ' + http.responseText);
            }
        }
        
        http.onerror = function() {
            console.error('Netzwerkfehler');
            alert('Netzwerkfehler beim Löschen');
        }
    }

    function rotatePreviewPic() {
        var previewPic = document.querySelector('#preview-pic img');
        var picPath = previewPic.src;
        var rotatePicUrl = baseUrl + 'blog_filezone/rotate';

        console.log('Token:', token); // Zeigt den Token an


        const http = new XMLHttpRequest();
        http.open('POST', rotatePicUrl);
        // http.setRequestHeader('Content-Type', 'application/json');
        http.setRequestHeader("trongateToken", token);

        http.send(JSON.stringify({ picture_path: picPath }));

        http.onload = function () {
            if (http.status === 200) {
                var response = JSON.parse(http.responseText);
                var imgPreview = document.createElement('img');
                // imgPreview.setAttribute('src', picPath + "?t=" + new Date().getTime());
                imgPreview.setAttribute("src", picPath);
                document.getElementById('preview-pic').innerHTML = '';
                document.getElementById('preview-pic').appendChild(imgPreview);
                alert('Picture rotated successfully');
            } else {
                alert('Failed to rotate picture: ' + http.responseText);
            }
        };

        // closeModal();
    }

    function OLD_rotatePreviewPic() {
        var el = document.querySelector("div.user-panel.main input[name='login']");
        var previewPic =  document.querySelector('#preview-pic img');
        var picPath = previewPic.src;
        var rotatePicUrl = baseUrl + 'blog_filezone/rotate/' + segment1 + '/' + updateId;

     /*    console.log(rotatePicUrl); */
        
        const http = new XMLHttpRequest();
        http.open('POST', rotatePicUrl);
        http.setRequestHeader('Content-type', 'application/json');
        http.setRequestHeader('trongateToken', token);
        http.send(picPath);
        http.onload = function() {
           /*  var res => res.text(); */
            /* var picRotate = res; */
            
            document.getElementById('preview-pic').innerHTML = "";
            var targetEl = document.getElementById('preview-pic');
            
            // ??
            var imgPreview = document.createElement('img');
            imgPreview.setAttribute("src", picPath);
            targetEl.appendChild(imgPreview);
            
            alert(picPath);// ?

            window.location.reload();
        }
        closeModal();
    }

    function closeModal() {
        var modalContainer = _("modal-container");
        var openModal = modalContainer.firstChild;

        openModal.style.zIndex = -4;
        openModal.style.opacity = 0;
        openModal.style.marginTop = '12vh';
        openModal.style.display = 'none';
        body.appendChild(openModal);

        modalContainer.remove();
        var overlay = _("overlay");
        overlay.remove();
    }

</script>

<style>

    .card {
        min-height: 50vh;
    }
    
   #gallery-pics {
       display: grid;
       grid-gap: 1em;
   }

   #gallery-pics div {
       text-align: center;
   }

   #gallery-pics div img {
       max-width: 100%;
       max-height: auto;
       padding: 0.1em;
       cursor: pointer;
   }

   #preview-pic {
       text-align: center;
   }

   #preview-pic img {
       max-width: 100%;
       max-height: 450px;
   }

   @media (min-width: 30em) {
       #gallery-pics {
           grid-template-columns: repeat(3, 1fr);
       }
   }

   @media (min-width: 50em) {
       #gallery-pics {
           grid-template-columns: repeat(4, 1fr);
       }
   }

   @media (min-width: 70em) {

       #gallery-pics {
           grid-template-columns: repeat(5, 1fr);
       }
   }

   @media (min-width: 90em) {
       #gallery-pics {
           grid-template-columns: repeat(5, 1fr);
       }
   }


</style>