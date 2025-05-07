<h1><?= $headline ?></h1>
<?= validation_errors() ?>
<div class="card">
    <div class="card-heading">
        Menu Details
    </div>
    <div class="card-body">
        <?php
        echo form_open($form_location);

        echo form_label('Name');
        echo form_input('name', $name, array("placeholder" => "Enter Name", "autocomplete" => "off"));

        echo form_label('Description <span>(optional)</span>');
        echo form_input('description', $description, array("placeholder" => "Enter Description"));

        echo form_label('Template');
        echo form_dropdown('template', $template_options, $template ?? 'default', ['class' => 'select-field']);

        echo '<div>';
        echo form_label('Published', ['style' => 'display: inline-block;']);
        echo validation_errors('published');
        echo form_checkbox('published', 1, $published);
        echo '</div>';

        /* ALTERNATIVELY
        echo form_label('Active');
        echo form_dropdown('published', [1 => 'Yes', 0 => 'No'], $published ?? 1);*/

        echo form_submit('submit', 'Submit');
        echo anchor($cancel_url, 'Cancel', array('class' => 'button alt'));

        echo form_close();
        ?>
    </div>
</div>