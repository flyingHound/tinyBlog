<h1 class='mb-4'><?= $headline ?></h1>
<?php // echo validation_errors(); ?>
<div class="card">
	<div class='card-header bg-dark text-white'>
        Picture Details
    </div>
    <div class="card-body">
        <?php
        echo form_open($form_location);
        echo '<div class="mb-3">';
        echo form_label('Picture', array('class' => 'form-label'));
        echo validation_errors('picture');
        echo form_input('picture', $picture, array('class' => 'form-control', 'placeholder' => 'Enter Picture'));
        echo '</div>';

        echo '<div class="mb-3">';
        echo form_label('Priority <span>(optional)</span>', array('class' => 'form-label'));
        echo validation_errors('priority');
        echo form_number('priority', $priority, array('class' => 'form-control', 'placeholder' => 'Enter Priority'));
        echo '</div>';

        echo '<div class="mb-3">';
        echo form_label('Target Module <span>(optional)</span>', array('class' => 'form-label'));
        echo validation_errors('target_module');
        echo form_input('target_module', $target_module, array('class' => 'form-control', 'placeholder' => 'Enter Enter Target Module'));
        echo '</div>';

        echo '<div class="mb-3">';
        echo form_label('Target Module ID <span>(optional)</span>', array('class' => 'form-label'));
        echo validation_errors('target_module_id');
        echo form_number('target_module_id', $target_module_id, array('class' => 'form-control', 'placeholder' => 'Enter Enter Target Module ID'));
        echo '</div>';

        echo '<div class="d-flex gap-3">';
        echo form_submit('submit', 'Submit', ['class' => 'btn btn-info']);
        echo anchor($cancel_url, 'Cancel', ['class' => 'btn btn-secondary']);
        echo '</div>';
        echo form_close();
        ?>
    </div>
</div>