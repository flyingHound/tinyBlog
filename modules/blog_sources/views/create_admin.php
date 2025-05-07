<h1><?= $headline ?></h1>
<?= validation_errors() ?>
<div class="card">
    <div class="card-heading">
        Source Details
    </div>
    <div class="card-body">
        <?php
        echo form_open($form_location);
        echo form_label('Author');
        echo form_input('author', $author, array("placeholder" => "Enter Author"));
        echo form_label('Website <span>(optional)</span>');
        echo form_input('website', $website, array("placeholder" => "Enter Website"));
        echo form_label('Link <span>(optional)</span>');
        echo form_input('link', $link, array("placeholder" => "Enter Link"));
        echo form_submit('submit', 'Submit');
        echo anchor($cancel_url, 'Cancel', array('class' => 'button alt'));
        echo form_close();
        ?>
    </div>
</div>