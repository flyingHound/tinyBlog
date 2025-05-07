<div class="card">
    <div class="card-heading"><h1><?= $headline ?></h1></div>
    <div class="card-body">

        <p><?php 
        flashdata();
        $btn_1_attr['class'] = 'button alt';
        $btn_2_attr['class'] = 'button';
        echo anchor($previous_url, '<i class="fa fa-arrow-left"></i> GO BACK', $btn_1_attr);
        echo anchor($order_url, '<i class="fa fa-arrow-right"></i> ORDER PICTURES', $btn_2_attr)
        ?></p>

        <p>You can Order or Upload your '<?= $target_module_desc ?>' pictures here. When finished,  click on 'GO BACK' or cLick to 'ORDER PICTURES'.</p>
    </div>
</div>



<div class="drop-zone" id="drop-zone">
    <div bp="grid 4@sm 3@md 2@lg container" id="thumbnail-grid">
        <?php 
        $num_previously_uploaded_files = count($previously_uploaded_files);
        foreach ($previously_uploaded_files as $previously_uploaded_file) {
            $file_path = $previously_uploaded_file['directory'].'/'.$previously_uploaded_file['filename'];
        ?>
        <div class="drop-zone__thumb" data-label="<?= $previously_uploaded_file['filename'] ?>" id="vWVnX" style="background-image: url('<?= $file_path ?>');">
            <div class="thumboverlay thumboverlay-green" id="<?= $previously_uploaded_file['overlay_id'] ?>">
               <div class="ditch-cross" onclick="deleteImg('<?= $previously_uploaded_file['overlay_id'] ?>')">✘</div>
            </div>
        </div>   
        <?php 
        }
        ?>
    </div>
    <div id="controls">
        <span class="drop-zone__prompt">
            Drag &amp; Drop or click  '<span class="browse" onclick="initBrowse()">Look at the computer</span>'
        </span>
        <form id="multi-form" enctype="multipart/form-data" style="display: none;">
            <input type="file" id="files" name="files" multiple onchange="activateFiles()">
        </form>
    </div>
</div>
<style>
    #thumbnail-grid > div {
    height: 140px;
    background-size: cover;
}
</style>
<script>
const baseUrl = '<?= BASE_URL ?>';
const targetModule = '<?= $target_module ?>';
const updateId = <?= $update_id ?>;
const uploadUrl = '<?= $upload_url ?>';
const deleteUrl = '<?= $delete_url  ?>';
const token = '<?= $token ?>';
</script>