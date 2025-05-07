<div class="card h-100">
    <div class="card-header">
        Associated <?= ucwords($associated_plural) ?>
    </div>
    <div class="card-body">
        <p id="<?= $relation_name ?>-create" style="display: none;">
            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#<?= $relation_name ?>-create-modal">
                <i class="fa fa-exchange"></i> Associate With <?= ucwords($associated_singular) ?>
            </button>
        </p>
        <div class="table-responsive">
            <table class="table table-striped table-sm">
                <tbody id="<?= $relation_name ?>-records"></tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="<?= $relation_name ?>-create-modal" tabindex="-1" aria-labelledby="<?= $relation_name ?>CreateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="<?= $relation_name ?>CreateModalLabel">
                    <i class="fa fa-exchange"></i> Associate With <?= ucwords($associated_singular) ?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Select <?= $associated_singular ?> and then hit 'Associate'.</p>
                <p>
                    <select id="<?= $relation_name ?>-dropdown" name="<?= $relation_name ?>-dropdown" class="form-select"></select>
                </p>
                <p class="text-end">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="submitCreateAssociation('<?= $relation_name ?>')" aria-label="Associate with <?= ucwords($associated_singular) ?>">
                        Associate With <?= ucwords($associated_singular) ?>
                    </button>
                </p>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="<?= $relation_name ?>-disassociate-modal" tabindex="-1" aria-labelledby="<?= $relation_name ?>DisassociateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="<?= $relation_name ?>DisassociateModalLabel">
                    <i class="fa fa-ban"></i> Disassociate With <?= ucwords($associated_singular) ?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h5>Confirm Disassociate</h5>
                <p>You are about to remove an association.</p>
                <p>Do you really want to do this?</p>
                <?php 
                $input_attr['id'] = $relation_name.'-record-to-go';
                echo form_hidden('record_id', '', $input_attr); 
                ?>
                <p class="text-end">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" onclick="disassociate('<?= $relation_name ?>')" aria-label="Confirm disassociate with <?= ucwords($associated_singular) ?>">
                        Yes - Disassociate Now!
                    </button>
                </p>
            </div>
        </div>
    </div>
</div>

<script>
window.addEventListener('load', function() {
    fetchAssociatedRecords('<?= $relation_name ?>', '<?= $update_id ?>');
});
</script>