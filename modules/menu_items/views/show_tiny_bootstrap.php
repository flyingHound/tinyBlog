<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="mb-0"><?= out($headline) ?> <small class="fs-6 text-muted d-none d-md-inline">(Record ID: <?= out($update_id) ?>)</small></h1>
</div>

<!-- Flash Messages -->
<?php flashdata(); ?>

<!-- Options Card -->
<div class="card mb-3">
    <div class="card-header">Options</div>
    <div class="card-body d-flex gap-2">
        <?= anchor('menu_items/manage', 'View All Menu Items', ['class' => 'btn btn-outline-primary']) ?>
        <?= anchor('menu_items/create/' . $update_id, 'Update Details', ['class' => 'btn btn-primary']) ?>
        <?php 
        $attr_delete = array( 
            "class" => "btn btn-danger ms-auto",
            "data-bs-toggle" => "modal",
            "data-bs-target" => "#delete-modal"
        );
        echo form_button('delete', 'Delete', $attr_delete); ?>
    </div>
</div>


<div class="row">
    <!-- Source Details -->
    <div class="col-12 col-xl-6 mb-3">
        <div class="card h-100">
            <div class="card-header">
            	App Nav Details
        	</div>
	        <div class="card-body">
	            <div class="record-details">
	                <div class="row detail-row">
	                    <div class="col-4 label">Title</div>
	                    <div class="col-8 value"><?= out($title) ?></div>
	                </div>
	                <div class="row detail-row">
	                    <div class="col-4 label">URL String</div>
	                    <div class="col-8 value"><?= out($url_string) ?></div>
	                </div>
	                <div class="row detail-row">
	                    <div class="col-4 label">Menu</div>
	                    <div class="col-8 value"><?= out($menus_id) ?></div>
	                </div>
	                <div class="row detail-row">
	                    <div class="col-4 label">Parent ID</div>
	                    <div class="col-8 value"><?= out($parent_id) ?></div>
	                </div>
	                <div class="row detail-row">
	                    <div class="col-4 label">Sort Order</div>
	                    <div class="col-8 value"><?= out($sort_order) ?></div>
	                </div>
	                <div class="row detail-row">
	                    <div class="col-4 label">Target</div>
	                    <div class="col-8 value"><?= out($target) ?></div>
	                </div>
	                <div class="row detail-row">
	                    <div class="col-4 label">Date Created</div>
	                    <div class="col-8 value"><?= date($date_format,  strtotime($date_created)) ?></div>
	                </div>
	                <div class="row detail-row">
	                    <div class="col-4 label">Date Updated</div>
	                    <div class="col-8 value">
	                        <?php if (strtotime($date_updated) != 0) {
	                            echo date($date_format,  strtotime($date_updated));
	                        } else { echo 'never';}
	                        ?>
	                    </div>
	                </div>
                    <?php
                    $published_status = $published == 1 ? 'Published' : 'Not Published';
                    $published_icon_class = $published == 1 ? 'fa-check-square' : 'fa-times-circle';
                    ?>
	                <div class="row detail-row">
	                    <div class="col-4 label">
	                        <span class="published-label"><?= $published_status ?></span>
	                    </div>
	                    <div class="col-8 value">
	                        <span class="published-icon">
                                <i class="fa <?= $published_icon_class ?>" id="published-icon-<?= $id ?>" title="<?= $published_status ?>"></i>
                            </span>
	                    </div>
	                </div>
	            </div>
	        </div>
	    </div>
	</div>

    <!-- Comments -->
    <div class="col-12 col-xl-6 mb-3">
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
                <?= form_open('menu_items/submit_delete/' . $update_id, ['id' => 'delete-form']) ?>
                    <p>Are you sure?</p>
                    <p>You are about to delete a Blog Source record. This cannot be undone. Do you really want to do this?</p>
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
const token = '<?= $token ?>';
const baseUrl = '<?= BASE_URL ?>';
const segment1 = '<?= segment(1) ?>';
const updateId = '<?= $update_id ?>';
const drawComments = true;
</script>
<script>
    // Publish/Unpublish Toggle for Menu Items
	document.querySelectorAll('[id^="published-icon"]').forEach(icon => {
	    icon.addEventListener('click', (ev) => {
	        const icon = ev.target;
	        const newStatus = icon.classList.contains('fa-times-circle') ? 1 : 0;
	        const recordId = parseInt(icon.id.replace(/published-icon-(alt-)?/, '')); // Handles both 'published-icon-' and 'published-icon-alt-'

	        icon.style.display = 'none';

	        fetch(`${baseUrl}api/update/menu_items/${recordId}`, {
	            method: 'PUT',
	            headers: {
	                'Content-Type': 'application/json',
	                'trongateToken': token
	            },
	            body: JSON.stringify({ published: newStatus })
	        })
	        .then(response => {
	            if (response.ok) {
	                const isUnpublished = icon.classList.contains('fa-times-circle');
	                icon.classList.replace(
	                    isUnpublished ? 'fa-times-circle' : 'fa-check-square',
	                    isUnpublished ? 'fa-check-square' : 'fa-times-circle'
	                );
	                icon.title = isUnpublished ? 'Published' : 'Not published';
	                icon.style.display = 'inline-block';

	                const label = document.querySelector('.published-label');
	                if (label) label.textContent = isUnpublished ? 'Published' : 'Not published';
	            } else {
	                throw new Error('Failed to update published status');
	            }
	        })
	        .catch(error => {
	            console.error('Error updating published status:', error);
	            icon.style.display = 'inline-block'; // Ensure icon is visible again on error
	        });
	    });
	});
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