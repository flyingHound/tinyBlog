<div class="card">
    <div class="card-heading">
        <h1><?= out($headline) ?> <span class="smaller hide-sm">Blog Posts</span></h1>
    </div>
    <?= flashdata() ?>
    <div class="card-body">
        <?php 
        echo anchor('blog_posts/manage', 'View All Posts', ['class' => 'button alt']);
        echo anchor('blog_settings/save_settings', 'Save Settings', ['class' => 'button']);
        ?>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <?php
        // Formular mit CSRF-Token
        echo form_open('blog_settings/save_settings', ['method' => 'POST']);
        echo form_hidden('trongate_token', $token);
        $settings = $picture_settings ?? [];
        ?>

        <div class="flex-checkbox">
            <label for="upload_to_module">Upload to Module:</label>
            <input type="checkbox" id="upload_to_module" name="upload_to_module" <?= isset($settings['upload_to_module']) && $settings['upload_to_module'] ? 'checked' : '' ?>>
        </div>

        <div class="legend">source</div>
        <div class="three-col">    
            <div>
                <label for="destination">Picture Directory:</label>
                <input type="text" id="destination" name="destination" value="<?= out($settings['destination'] ?? 'blog_posts_pics') ?>">
            </div>
            <div>
                <label for="thumbnail_dir">Thumbnail Directory:</label>
                <input type="text" id="thumbnail_dir" name="thumbnail_dir" value="<?= out($settings['thumbnail_dir'] ?? 'blog_posts_pics_thumbnails') ?>">
            </div>
            <div>
                <label for="target_column_name">Target Column Name:</label>
                <input type="text" id="target_column_name" name="target_column_name" value="<?= out($settings['target_column_name'] ?? 'picture') ?>">
            </div>
        </div>
        <br>

        <div class="legend">size</div>
        <div class="four-col">
            <div>
                <label for="resized_max_width">Resized Max Width (px):</label>
                <input type="number" id="resized_max_width" name="resized_max_width" value="<?= out($settings['resized_max_width'] ?? 450) ?>">
            </div>
            <div>
                <label for="resized_max_height">Resized Max Height (px):</label>
                <input type="number" id="resized_max_height" name="resized_max_height" value="<?= out($settings['resized_max_height'] ?? 450) ?>">
            </div>
            <div>
                <label for="thumbnail_max_width">Thumbnail Max Width (px):</label>
                <input type="number" id="thumbnail_max_width" name="thumbnail_max_width" value="<?= out($settings['thumbnail_max_width'] ?? 120) ?>">
            </div>
            <div>
                <label for="thumbnail_max_height">Thumbnail Max Height (px):</label>
                <input type="number" id="thumbnail_max_height" name="thumbnail_max_height" value="<?= out($settings['thumbnail_max_height'] ?? 120) ?>">
            </div>
        </div><br>

        <div class="legend">limit</div>
        <div class="three-col">
            <div>
                <label for="max_file_size">Max File Size (KB):</label>
                <input type="number" id="max_file_size" name="max_file_size" value="<?= out($settings['max_file_size'] ?? 2000) ?>">
            </div>
            <div>
                <label for="max_width">Max Width (px):</label>
                <input type="number" id="max_width" name="max_width" value="<?= out($settings['max_width'] ?? 1200) ?>">
            </div>
            <div>
                <label for="max_height">Max Height (px):</label>
                <input type="number" id="max_height" name="max_height" value="<?= out($settings['max_height'] ?? 1200) ?>">
            </div>
        </div><br>

        <div class="legend">save</div>
        <div class="two-col">
            <div class="flex-checkbox">
                <label for="make_rand_name">Make Random Name:</label>
                <input type="checkbox" id="make_rand_name" name="make_rand_name" <?= isset($settings['make_rand_name']) && $settings['make_rand_name'] ? 'checked' : '' ?>>
            </div>
            <div class="align-right">
                <input class="button" type="submit" value="Save Settings">
            </div>
        </div>

        <?= form_close() ?>
    </div>
</div>

<style>
    .four-col {
        display: grid;
        grid-gap: 1em;
        grid-template-columns: 1fr 1fr 1fr 1fr;
    }
    .three-col {
        display: grid;
        grid-gap: 1em;
        grid-template-columns: 1fr 1fr 1fr;
    }
    .two-col {
        display: grid;
        grid-gap: 1em;
        grid-template-columns: 1fr 1fr;
    }
    .flex-checkbox {
        display: flex;
        align-items: center;
        gap: 0.5em;
    }
    .align-right {
        text-align: right;
    }
    .legend {
        font-weight: 600;
        font-style: italic;
        color: #d9d9d9;
        font-size: 85%;
        margin-bottom: 0.5em;
    }

    @media (max-width: 560px) {
        .two-col, .three-col, .four-col {
            grid-template-columns: 1fr;
        }
    }
</style>