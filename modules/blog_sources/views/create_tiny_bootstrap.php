<h1 class='mb-4'><?= $headline ?></h1>
<?php // echo validation_errors(); ?>
<div class='card'>
    <div class='card-header bg-dark text-white'>
        Source Details
    </div>
    <div class='card-body'>
        <?php
        echo form_open($form_location, array('class' => 'highlight-errors'));
        echo '<div class="mb-3">';
        echo form_label('Author', array('class' => 'form-label'));
        echo validation_errors('author');
        echo form_input('author', $author, array('class' => 'form-control', 'placeholder' => 'Enter Author'));
        echo '</div>';

        echo '<div class="mb-3">';
        echo form_label('Website <span>(optional)</span>', array('class' => 'form-label'));
        echo validation_errors('website');
        echo form_input('website', $website, array('class' => 'form-control', 'placeholder' => 'Enter Website'));
        echo '</div>';

        echo '<div class="mb-3">';
        echo form_label('Link <span>(optional)</span>', array('class' => 'form-label'));
        echo validation_errors('link');
        echo form_input('link', $link, array('class' => 'form-control', 'placeholder' => 'Enter Link'));
        echo '</div>';

        echo '<div class="d-flex gap-3">';
        echo form_submit('submit', 'Submit', ['class' => 'btn btn-info']);
        echo anchor($cancel_url, 'Cancel', ['class' => 'btn btn-secondary']);
        echo '</div>';
        echo form_close();
        ?>
    </div>
</div>