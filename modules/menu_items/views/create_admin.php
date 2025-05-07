<h1><?= $headline ?></h1>
<?= validation_errors() ?>
<div class="card">
    <div class="card-heading">
        Menu Item Details
    </div>
    <div class="card-body">
        <?php
        echo form_open($form_location);

        echo form_label('Associated Menu');
        echo form_dropdown('menus_id', $menus_options, $menus_id, ['class' => 'select-field']);

        /* echo form_label('Menu');
        echo form_dropdown('menu_id', $menu_options, $menus_id, ['class' => 'select-field']); */

        echo form_label('Title');
        echo form_input('title', $title, ["placeholder" => "Enter Title", "id" => "title-input"]);

        echo form_label('URL (automatically created on Title input)');
        echo form_input('url_string', $url_string, ["placeholder" => "Enter URL", "id" => "url-input"]);

        echo form_label('Parent Item <span>(optional)</span>');
        echo form_dropdown('parent_id', $parent_options, $parent_id, ['class' => 'select-field']);

        echo form_label('Sort Order <span>(optional)</span>');
        echo form_number('sort_order', $sort_order, array("placeholder" => "Enter Sort Order"));

        echo form_label('Target <span>(optional)</span>');
        echo form_dropdown('target', $target_options, $target, ["class" => "target-select"]);

        echo '<div>';
        echo form_label('Published', ['style' => 'display: inline-block;']);
        echo validation_errors('published');
        echo form_checkbox('published', 1, $published);
        echo '</div>';

        echo form_submit('submit', 'Submit');
        echo anchor($cancel_url, 'Cancel', array('class' => 'button alt'));
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

            // Basis-URL hinzufügen (z. B. '/blog/')
            const baseUrl = '<?= BASE_URL ?>';
            urlInput.value = baseUrl + slug;
        });
    });
</script>