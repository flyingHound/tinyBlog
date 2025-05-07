<div class="card recent-posts-widget" id="info-right-column">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <?= anchor('blog_posts/manage', '<i class="fa fa-newspaper-o fa-lg text-primary me-2"></i>Recent Blog Posts', [
                'aria-label' => 'Manage Blog Posts',
                'title' => 'Manage Blog Posts'
            ]) ?>
            <span class="float-end">showing 10 of <?= $num_blog_posts ?></span>
        </h5>
    </div>
    <div class="card-body p-1">
        <ul class="list-group list-group-flush">
            <?php 
            foreach ($rows as $post) : ?>
                <li class="list-group-item">
                    <div class="d-flex align-items-center">
                        <div class="post-image-wrapper me-2">
                            <img src="<?= out($post->picture) ?: $pic_fallback ?>" alt="<?= strip_tags($post->title) ?>" class="rounded" onerror="this.src='<?= $pic_fallback ?>'">
                        </div>
                        <div class="flex-grow-1">
                            <a href="<?= BASE_URL ?>blog_posts/create/<?= out($post->id) ?>" class="text-decoration-none text-primary">
                                <?= htmlspecialchars_decode($post->title) ?>
                            </a>
                            <p class="text-muted mb-0 small"><?= $post->date_published ?></p>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<style>
    .recent-posts-widget .card-body { 
        overflow-y: auto;
    }

    /* Fixierte Bildbreite */
    .post-image-wrapper {
        flex: 0 0 auto; /* no scaling */
        width: 50px;
    }

    .post-image-wrapper img {
        width: 50px;
        height: 50px;
        object-fit: cover;
    }

</style>