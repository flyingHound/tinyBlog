<h1 class="mb-4"><?= $headline ?></h1>
<?php // echo validation_errors(); ?>
<div class="card shadow">
    <div class="card-header bg-primary text-white">
        Menu Item Details
    </div>
    <div class="card-body">
        <?php
        echo form_open($form_location);

        echo '<div class="mb-3">';
        echo form_label('Associated Menu', array('class' => 'form-label'));
        echo validation_errors('menus_id');
        echo form_dropdown('menus_id', $menus_options, $menus_id, ['class' => 'form-select']);
        echo '</div>';

        echo '<div class="mb-3">';
        echo form_label('Title', array('class' => 'form-label'));
        echo validation_errors('title');
        echo form_input('title', $title, ['id' => 'title-input', 'class' => 'form-control', 'placeholder' => 'Enter Title']);
        echo '</div>';

        echo '<div class="mb-3">';
        echo form_label('URL (automatically created on Title input)', array('class' => 'form-label'));
        echo validation_errors('url_string');
        echo form_input('url_string', $url_string, ['id' => 'url-input', 'class' => 'form-control', 'placeholder' => 'Enter URL']);
        echo '</div>';

        echo '<div class="mb-3">';
        echo form_label('Parent Item <span>(optional)</span>', array('class' => 'form-label'));
        echo validation_errors('parent_id');
        echo form_dropdown('parent_id', $parent_options, $parent_id, ['class' => 'form-select']);
        echo '</div>';

        echo '<div class="mb-3">';
        echo form_label('Sort Order <span>(optional)</span>', array('class' => 'form-label'));
        echo validation_errors('sort_order');
        echo form_number('sort_order', $sort_order, array('class' => 'form-control', 'placeholder' => 'Enter Sort Order'));
        echo '</div>';

        echo '<div class="mb-3">';
        echo form_label('Target <span>(optional)</span>', array('class' => 'form-label'));
        echo validation_errors('target');
        echo form_dropdown('target', $target_options, $target, ['class' => 'form-select']);
        echo '</div>';

        echo '<div class="mb-3">';
        echo form_label('Published', ['class' => 'form-check-label me-2']);
        echo form_checkbox('published', 1, $published, ['class' => 'form-check-input']);
        echo validation_errors('published');
        echo '</div>';

        echo '<div class="d-flex gap-2">';
        echo form_submit('submit', 'Submit', ['class' => 'btn btn-primary']);
        echo anchor($cancel_url, 'Cancel', ['class' => 'btn btn-secondary']);
        echo '</div>';
        echo form_close();
        ?>
    </div>
</div>
<style>
    .card-body label span {
        font-size: 0.8em;
        color: #666;
    }
    .target-select {
        width: 100%;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const titleInput = document.getElementById('title-input');
        const urlInput = document.getElementById('url-input');

        titleInput.addEventListener('input', function() {
            // make URL-Slug from title
            let slug = this.value
                .toLowerCase()
                .trim()
                .replace(/[^a-z0-9\s-]/g, '') // ged rid of special characters
                .replace(/\s+/g, '-')         // hyphens for spaces
                .replace(/-+/g, '-');        // only one hyphen

            // add Base Url
            const baseUrl = '<?= BASE_URL ?>';
            urlInput.value = baseUrl + slug;
        });
    });
</script>