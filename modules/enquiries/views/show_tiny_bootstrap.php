<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="mb-0"><?= out($headline) ?> <small class="fs-6 text-muted d-none d-md-inline">(Record ID: <?= out($update_id) ?>)</small></h1>
</div>

<!-- Flash Messages -->
<?php if (flashdata()): ?>
    <div class="alert alert-danger"><?= flashdata() ?></div>
<?php endif; ?>

<!-- Options Card -->
<div class="card mb-3">
    <div class="card-header">Options</div>
    <div class="card-body d-flex gap-2">
        <?= anchor('enquiries/manage', 'View All Enquiries', ['class' => 'btn btn-outline-primary']) ?>
        <?php // echo anchor('enquiries/create/' . $update_id, 'Update Details', ['class' => 'btn btn-primary']) ?>
        <?php 
        $attr_delete = array( 
            "class" => "btn btn-danger ms-auto",
            "data-bs-toggle" => "modal",
            "data-bs-target" => "#delete-modal"
        );
        // echo form_button('delete', 'Delete', $attr_delete); ?>
    </div>
</div>

<div class="row">
    <!-- Enquiry Details -->
    <div class="col-12 col-xl-6 mb-3">
        <div class="card h-100">
            <div class="card-header">
            	Enquiry Details
	        </div>
	        <div class="card-body">
	            <div class="record-details">
	            	<div class="row detail-row">
                        <div class="col-4 label">Date Sent</div>
	            		<div class="col-8 value"><?= date('l jS F Y \a\t H:i',  $date_created) ?></div>
		            </div>
		            <div class="row detail-row">
		            	<div class="col-4 label">Name</div>
		            	<div class="col-8 value"><?= out($name) ?></div>
		            </div>
		            <div class="row detail-row">
		            	<div class="col-4 label">Email Address</div>
		            	<div class="col-8 value"><?= out($email_address) ?></div>
		            </div>
		            <div class="row detail-row mt-3">
		                <div class="col-12 label">Message</div>
		                <div class="col-12 mt-2 value"><?= out($message) ?></div>
		            </div>
		        </div>
	        </div>
	    </div>
	</div>

    <!-- Comments -->
    <div class="col-12 col-md-6 col-xl-4 mb-3">
        <div class="card h-100">
            <div class="card-header">
                Comments
            </div>
            <div class="card-body"> 
                    <p><button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#comment-modal">Add New Comment</button></p>
                <div id="comments-block"><table class="table"></table></div>
            </div>
        </div>
    </div>
</div>

<!-- Comment Modal -->
<div class="modal fade" id="comment-modal" tabindex="-1" aria-labelledby="commentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-commenting-o"></i> Add New Comment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><textarea placeholder="Enter comment here..." class="form-control"></textarea></p>
                <p class="text-end">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="submitComment()">Submit Comment</button>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="delete-modal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fa fa-trash"></i> Delete Record</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?= form_open('enquiries/submit_delete/' . $update_id, ['id' => 'delete-form']) ?>
                    <p>Are you sure?</p>
                    <p>You are about to delete an Enquiry record. This cannot be undone. Do you really want to do this?</p>
                    <p class="text-end">
                        <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel</button>
                        <?= form_submit('submit', 'Yes - Delete Now', ['class' => 'btn btn-danger']) ?>
                    </p>
                <?= form_close() ?>
            </div>
        </div>
    </div>
</div>
<script>
var token = '<?= $token ?>';
var baseUrl = '<?= BASE_URL ?>';
var segment1 = '<?= segment(1) ?>';
var updateId = '<?= $update_id ?>';
var drawComments = true;
</script>

<!-- CSS -->
<style>
.record-details {
    overflow: hidden;
}

.record-details .detail-row {
    border-bottom: 1px solid var(--bs-border-color);
    padding: 0.5rem 0;
    align-items: center;
}

.record-details .label {
    font-size: 0.825rem;
    font-variant-caps: all-petite-caps;
    font-weight: bold;
    color: #6c757d; 
}

.record-details .value {
    margin-bottom: 0;
}

.record-details .detail-row:last-child {
    border-bottom: none;
}
</style>