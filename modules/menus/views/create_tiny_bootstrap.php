<h1 class='mb-4'><?= $headline ?></h1>
<?php // echo validation_errors(); ?>
<div class='card'>
    <div class='card-header bg-dark text-white'>
        Menu Details
    </div>
    <div class="card-body">
        <?php
        echo form_open($form_location);

        echo '<div class="mb-3">';
        echo form_label('Name');
        echo validation_errors('name');
        echo form_input('name', $name, array('class' => 'form-control', 'placeholder' => 'Enter Name', 'autocomplete' => 'off'));
        echo '</div>';

        echo '<div class="mb-3">';
        echo form_label('Description <span>(optional)</span>');
        echo validation_errors('description');
        echo form_input('description', $description, array('class' => 'form-control', 'placeholder' => 'Enter Description'));
        echo '</div>';

        echo '<div class="mb-3">';
        echo form_label('Template');
        echo validation_errors('template');
        echo form_dropdown('template', $template_options, $template ?? 'default', ['class' => 'form-select', 'class' => 'select-field']);
        echo '</div>';

        echo '<div class="mb-3">';
        echo form_label('Published', ['style' => 'display: inline-block;']);
        echo validation_errors('published');
        echo form_checkbox('published', 1, $published), ['class' => 'form-select'];
        echo '</div>';

        /* ALTERNATIVELY
        echo form_label('Active');
        echo form_dropdown('published', [1 => 'Yes', 0 => 'No'], $published ?? 1);*/

        echo '<div class="d-flex gap-3">';
        echo form_submit('submit', 'Submit', ['class' => 'btn btn-info']);
        echo anchor($cancel_url, 'Cancel', ['class' => 'btn btn-secondary']);
        echo '</div>';

        echo form_close();
        ?>
    </div>
</div>