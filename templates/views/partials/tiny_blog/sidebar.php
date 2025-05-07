<div class="sidebar">
    <div class="panel">
        <?= Modules::run("blog/get_sidebar_text_widget") ?>
    </div>

    <div class="panel">
        <?= Modules::run("blog/get_tag_cloud_widget") ?>
    </div>

    <div class="panel">
        <?= Modules::run("blog/get_categories_counter_widget") ?>
    </div>

    <div class="panel">
        <?= Modules::run("blog/get_random_images_widget") ?>
    </div>

    <div class="panel">
        <?= Modules::run("blog/get_recent_posts_widget") ?>
    </div>
</div>