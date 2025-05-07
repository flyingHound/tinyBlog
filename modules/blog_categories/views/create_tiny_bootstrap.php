<h1 class='mb-4'><?= $headline ?></h1>
<?php // echo validation_errors(); ?>
<div class='card'>
    <div class='card-header bg-dark text-white'>
        Category Details
    </div>
    <div class="card-body">
        <?php
        echo form_open($form_location);
        echo '<div class="mb-3">';
        echo form_label('Title', array('class' => 'form-label'));
        echo validation_errors('title');
        echo form_input('title', $title, array('class' => 'form-control', 'placeholder' => 'Enter Title'));
        echo '</div>';

        echo '<div class="d-flex gap-3">';
        echo form_submit('submit', 'Submit', ['class' => 'btn btn-info']);
        echo anchor($cancel_url, 'Cancel', ['class' => 'btn btn-secondary']);
        echo '</div>';
        echo form_close();
        ?>
    </div>
</div>