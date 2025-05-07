<h1><?= out($headline) ?> <span class="smaller hide-sm">(Record ID: <?= out($update_id) ?>)</span></h1>
<?= flashdata() ?>
<div class="card">
    <div class="card-heading">
        Options
    </div>
    <div class="card-body">
        <?php 
        echo anchor('menu_items/manage', 'View All Menu Items', array("class" => "button alt"));
        echo anchor('menu_items/create/'.$update_id, 'Update Details', array("class" => "button"));
        $attr_delete = array( 
            "class" => "danger go-right",
            "id" => "btn-delete-modal",
            "onclick" => "openModal('delete-modal')"
        );
        echo form_button('delete', 'Delete', $attr_delete);
        ?>
    </div>
</div>
<div class="two-col">
    <div class="card">
        <div class="card-heading">
            App Nav Details
        </div>
        <div class="card-body">
            <div class="record-details">
                <div class="row">
                    <div>Title</div>
                    <div><?= out($title) ?></div>
                </div>
                <div class="row">
                    <div>URL String</div>
                    <div><?= out($url_string) ?></div>
                </div>
                <div class="row">
                    <div>Menu</div>
                    <div><?= out($menus_id) ?></div>
                </div>
                <div class="row">
                    <div>Parent ID</div>
                    <div><?= out($parent_id) ?></div>
                </div>
                <div class="row">
                    <div>Sort Order</div>
                    <div><?= out($sort_order) ?></div>
                </div>
                <div class="row">
                    <div>Target</div>
                    <div><?= out($target) ?></div>
                </div>
                <div class="row">
                    <div>Date Created</div>
                    <div><?= date($date_format,  strtotime($date_created)) ?></div>
                </div>
                <div class="row">
                    <div>Date Updated</div>
                    <div>
                        <?php if (strtotime($date_updated) != 0) {
                            echo date($date_format,  strtotime($date_updated));
                        } else { echo 'never';}
                        ?>
                    </div>
                </div>
                <?php 
                // published
                $published_answer = ($published == 1 ? 'Yes' : 'No');
                $published_icon_str = ($published == '1') ? 'fa-check-square' : 'fa-times-circle';
                $published_status = $published == '1' ? 'Published' : 'Not Published';
                $published_icon = '<i class="fa '.$published_icon_str.'" id="published-icon-'.$id.'" title="'.$published_status.'"></i>';
                ?>
                <div class="row">
                    <div>
                        <span class="published-label"><?= $published_status ?></span>
                    </div>
                    <div>
                        <span class="published-icon"> <?= $published_icon ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-heading">
            Comments
        </div>
        <div class="card-body">
            <div class="text-center">
                <p><button class="alt" onclick="openModal('comment-modal')">Add New Comment</button></p>
                <div id="comments-block"><table></table></div>
            </div>
        </div>
    </div>
</div>
<div class="modal" id="comment-modal" style="display: none;">
    <div class="modal-heading"><i class="fa fa-commenting-o"></i> Add New Comment</div>
    <div class="modal-body">
        <p><textarea placeholder="Enter comment here..."></textarea></p>
        <p><?php
            $attr_close = array( 
                "class" => "alt",
                "onclick" => "closeModal()"
            );
            echo form_button('close', 'Cancel', $attr_close);
            echo form_button('submit', 'Submit Comment', array("onclick" => "submitComment()"));
            ?>
        </p>
    </div>
</div>
<div class="modal" id="delete-modal" style="display: none;">
    <div class="modal-heading danger"><i class="fa fa-trash"></i> Delete Record</div>
    <div class="modal-body">
        <?= form_open('menu_items/submit_delete/'.$update_id) ?>
        <p>Are you sure?</p>
        <p>You are about to delete an App Nav record.  This cannot be undone.  Do you really want to do this?</p> 
        <?php 
        echo '<p>'.form_button('close', 'Cancel', $attr_close);
        echo form_submit('submit', 'Yes - Delete Now', array("class" => 'danger')).'</p>';
        echo form_close();
        ?>
    </div>
</div>
<script>
const token = '<?= $token ?>';
const baseUrl = '<?= BASE_URL ?>';
const segment1 = '<?= segment(1) ?>';
const updateId = '<?= $update_id ?>';
const drawComments = true;
</script>
<script>
    // collect array publish-icons + add click fx to each
    const publishIcons = document.querySelectorAll('[id^="published-icon"]');
    for (var i = 0; i < publishIcons.length; i++) {
        publishIcons[i].addEventListener('click', (ev) => {
            togglePublishedStatus(ev.target);
        });
    }
    
    // update icon according to i status
    function togglePublishedStatus(clickedIcon) {
        const newPublishedStatus = (clickedIcon.classList.contains('fa-times-circle')) ? 1 : 0;
        const elId = clickedIcon.id;
        let recordId = elId.replace('published-icon-alt-', '');
        recordId = recordId.replace('published-icon-', '');
        recordId = parseInt(recordId);

        const params = {
            published: newPublishedStatus
        }

        clickedIcon.style.display = 'none';
        const targetUrl = '<?= BASE_URL ?>api/update/menu_items/' + recordId;
        const http = new XMLHttpRequest();

        http.open('put', targetUrl);
        http.setRequestHeader('Content-type', 'application/json');
        http.setRequestHeader('trongateToken', '<?= $token ?>');
        http.send(JSON.stringify(params));
        http.onload = (ev) => {
            if(http.status == 200) {
                togglePublishedIcon(clickedIcon);
            }
        };
    }
    // update the icon that was clicked - even add an indicating tooltip_title
    function togglePublishedIcon(clickedIcon) {
        //update the icon that was clicked
        const { classList, id } = clickedIcon;
        const relLabel = document.getElementsByClassName('published-label')[0]; // Get the first element with this class
        
        const isTimesCircle = classList.contains('fa-times-circle');
        const newClass = isTimesCircle ? 'fa-check-square' : 'fa-times-circle';
        const oldClass = isTimesCircle ? 'fa-times-circle' : 'fa-check-square';
        const publishedTitle = (newClass === 'fa-check-square') ? 'Published' : 'Not published';

        clickedIcon.classList.remove(oldClass);
        clickedIcon.classList.add(newClass);
        clickedIcon.style.display = 'inline-block';
        clickedIcon.title = publishedTitle;

        // Update the related label text
        if (relLabel) {
            relLabel.innerHTML = `${publishedTitle}`;
        }
    }
</script>
<style>
    .fa-check-square {
        color: #42a8a3;/*#46b464;*/
        cursor: pointer;
    }

    .fa-times-circle {
        color:  #b44646;
        cursor: pointer;
    }
    .record-details i[id^="published-icon"] {
        font-size: 1.5rem;
    }
</style>