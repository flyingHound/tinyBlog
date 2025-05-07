<h1><?= $headline ?></h1>
<?= validation_errors() ?>
<div class="card">
    <div class="card-heading">
        Blog Category Details
    </div>
    <div class="card-body">
        <?php
        echo form_open($form_location);
        echo form_label('Title');
        echo form_input('title', $title, array("placeholder" => "Enter Title"));
        echo form_submit('submit', 'Submit');
        echo anchor($cancel_url, 'Cancel', array('class' => 'button alt'));
        echo form_close();
        ?>
    </div>
</div>