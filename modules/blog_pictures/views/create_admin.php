<h1><?= $headline ?></h1>
<?= validation_errors() ?>
<div class="card">
    <div class="card-heading">
        Blog_picture Details
    </div>
    <div class="card-body">
        <?php
        echo form_open($form_location);
        echo form_label('Picture');
        echo form_input('picture', $picture, array("placeholder" => "Enter Picture"));
        echo form_label('Priority <span>(optional)</span>');
        echo form_number('priority', $priority, array("placeholder" => "Enter Priority"));
        echo form_label('Target Module <span>(optional)</span>');
        echo form_input('target_module', $target_module, array("placeholder" => "Enter Target Module"));
        echo form_label('Target Module ID <span>(optional)</span>');
        echo form_number('target_module_id', $target_module_id, array("placeholder" => "Enter Target Module ID"));
        echo form_submit('submit', 'Submit');
        echo anchor($cancel_url, 'Cancel', array('class' => 'button alt'));
        echo form_close();
        ?>
    </div>
</div>