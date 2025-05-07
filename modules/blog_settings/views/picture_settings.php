<h1><?= $headline ?></h1>
<div class="card">
    <div class="card-heading">Picture Settings</div>
    <div class="card-body">
    	<div class="danger alert alert-danger">
			<h2>Note:</h2>
            <p>This page is under construction!</p>
			<p>The main image size is determined by Max Width and Max Height. The values for Resized Max Width and Resized Max Height are present here but not processed by the current implementation.</p>
		</div>

        <?php echo form_open($form_location); 
        $settings = $picture_settings ?? []; ?>

        <?php echo validation_errors(); ?>

        <div class="flex-checkbox">
            <?php echo form_label('Upload to Module'); ?>
            <?php echo form_checkbox('upload_to_module', '1', isset($settings['upload_to_module']) && $settings['upload_to_module']); ?>
        </div>

        <div class="legend">source</div>
        <div class="three-col">    
            <div>
                <?php echo form_label('Picture Directory'); ?>
                <?php echo validation_errors('destination'); ?>
                <?php echo form_input('destination', $settings['destination'] ?? 'blog_posts_pics', array("id" => "destination", "placeholder" => "Enter Picture Directory")); ?>
            </div>
            <div>
                <?php echo form_label('Thumbnail Directory'); ?>
                <?php echo validation_errors('thumbnail_dir'); ?>
                <?php echo form_input('thumbnail_dir', $settings['thumbnail_dir'] ?? 'blog_posts_pics_thumbnails', array("id" => "thumbnail_dir", "placeholder" => "Enter Thumbnail Directory")); ?>
            </div>
            <div>
                <?php echo form_label('Target Column Name'); ?>
                <?php echo validation_errors('target_column_name'); ?>
                <?php echo form_input('target_column_name', $settings['target_column_name'] ?? 'picture', array("id" => "target_column_name", "placeholder" => "Enter Target Column Name")); ?>
            </div>
        </div>
        <br>

        <div class="legend">size</div>
        <div class="four-col">
            <div>
                <?php echo form_label('Resized Max Width (px)'); ?>
                <?php echo validation_errors('resized_max_width'); ?>
                <?php echo form_input('resized_max_width', $settings['resized_max_width'] ?? 450, array("id" => "resized_max_width", "type" => "number", "placeholder" => "Enter Resized Max Width")); ?>
            </div>
            <div>
                <?php echo form_label('Resized Max Height (px)'); ?>
                <?php echo validation_errors('resized_max_height'); ?>
                <?php echo form_input('resized_max_height', $settings['resized_max_height'] ?? 450, array("id" => "resized_max_height", "type" => "number", "placeholder" => "Enter Resized Max Height")); ?>
            </div>
            <div>
                <?php echo form_label('Thumbnail Max Width (px)'); ?>
                <?php echo validation_errors('thumbnail_max_width'); ?>
                <?php echo form_input('thumbnail_max_width', $settings['thumbnail_max_width'] ?? 120, array("id" => "thumbnail_max_width", "type" => "number", "placeholder" => "Enter Thumbnail Max Width")); ?>
            </div>
            <div>
                <?php echo form_label('Thumbnail Max Height (px)'); ?>
                <?php echo validation_errors('thumbnail_max_height'); ?>
                <?php echo form_input('thumbnail_max_height', $settings['thumbnail_max_height'] ?? 120, array("id" => "thumbnail_max_height", "type" => "number", "placeholder" => "Enter Thumbnail Max Height")); ?>
            </div>
        </div>
        <br>

        <div class="legend">limit</div>
        <div class="three-col">
            <div>
                <?php echo form_label('Max File Size (KB):'); ?>
                <?php echo validation_errors('max_file_size'); ?>
                <?php echo form_input('max_file_size', $settings['max_file_size'] ?? 2000, array("id" => "max_file_size", "type" => "number", "placeholder" => "Enter Max File Size")); ?>
            </div>
            <div>
                <?php echo form_label('Max Width (px):'); ?>
                <?php echo validation_errors('max_width'); ?>
                <?php echo form_input('max_width', $settings['max_width'] ?? 1200, array("id" => "max_width", "type" => "number", "placeholder" => "Enter Max Width")); ?>
            </div>
            <div>
                <?php echo form_label('Max Height (px):'); ?>
                <?php echo validation_errors('max_height'); ?>
                <?php echo form_input('max_height', $settings['max_height'] ?? 1200, array("id" => "max_height", "type" => "number", "placeholder" => "Enter Max Height")); ?>
            </div>
        </div>
        <br>

        <div class="legend">save</div>
        <div class="two-col">
            <div class="flex-checkbox">
                <?php echo form_label('Make Random Name'); ?>
                <?php echo form_checkbox('make_rand_name', '1', isset($settings['make_rand_name']) && $settings['make_rand_name']); ?>
            </div>
            <div class="align-right">
                <?php echo form_submit('submit', 'Save Settings', array('class' => 'button')); ?>
                <?php echo anchor($cancel_url ?? '/admin/picture_settings', 'Cancel', array('class' => 'button alt')); ?>
            </div>
        </div>

        <?= form_close() ?>
    </div>
</div>