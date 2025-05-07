<h1><?= out($headline) ?> <span class="smaller hide-sm">(Record ID: <?= out($update_id) ?>)</span></h1>
<?= flashdata() ?>
<div class="card">
    <div class="card-heading">
        Options
    </div>
    <div class="card-body">
        <?php 
        echo anchor('blog_pictures/manage', 'View All Blog_pictures', array("class" => "button alt"));
        echo anchor('blog_pictures/create/'.$update_id, 'Update Details', array("class" => "button"));
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
            Blog_picture Details
        </div>
        <div class="card-body">
            <div class="record-details">
                <div class="row">
                    <div>Picture</div>
                    <div><?= out($picture) ?></div>
                </div>
                <div class="row">
                    <div>Priority</div>
                    <div><?= out($priority) ?></div>
                </div>
                <div class="row">
                    <div>Target Module</div>
                    <div><?= out($target_module) ?></div>
                </div>
                <div class="row">
                    <div>Target Module ID</div>
                    <div><?= out($target_module_id) ?></div>
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
        <?= form_open('blog_pictures/submit_delete/'.$update_id) ?>
        <p>Are you sure?</p>
        <p>You are about to delete a Blog_picture record.  This cannot be undone.  Do you really want to do this?</p> 
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