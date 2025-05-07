<div class="mod-recent-posts-headline">
    <h3>Recent Posts</h3>
</div>
<ul class="mod-recent-posts-ul">
    <?php foreach ($rows as $post) : ?>
        <li>
			<a class="text-small" href="<?= BASE_URL ?>blog/post/<?= out($post->url_string) ?>">
				<div class="recent-posts-flex">
                    <?php if($post->picture != ''): ?>
                    	<div class="recent-posts-image-wrapper">
                    		<img src="<?= out($post->picture) ?>" alt="<?= strip_tags($post->title) ?>">
                    	</div>
                    <?php endif; ?>

                    <div class="recent-posts-title-wrapper">
                    	<p class="mod-recent-posts-date text-tiny"><?= $post->date_published ?></p>
                    	<div class="recent-posts-title"><?= htmlspecialchars_decode($post->title) ?></div>
                    </div>
                </div>
            </a>
        </li>
    <?php endforeach; ?>
</ul>
<style>
    .mod-recent-posts {
        max-width: 100%;
    }

    .mod-recent-posts-headline {}

    .recent-posts-flex {
        display: flex;
    	justify-content: space-between;
    	align-items: center;
    	gap: 1rem;
    }

    .mod-recent-posts-ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .mod-recent-posts-ul > li {
        padding: 1rem 0.1rem 1rem;
        border-bottom: 1px solid var(--prime-10);
        overflow: hidden;
        transition: background-color 0.2s;
    }

    .mod-recent-posts-ul > li:last-child {
    	border: none;
    }

    .mod-recent-posts-ul > li:hover {
        background-color: #f5f5f5; /*var(--light-color);*/
    }

    .recent-posts-image-wrapper {}

    .mod-recent-posts-ul img {
        width: 50px;
        height: 50px;
        object-fit: cover;
    }

    .recent-posts-title-wrapper {
        flex-grow: 1;
    }

    .mod-recent-posts-ul .mod-recent-posts-date {
        margin: 0 0 0.2rem;
        color: var(--prime-40);
        font-family: 'Nunito', sans-serif;
        font-size: 0.75rem;
        line-height: 1;
    }

    .mod-recent-posts-ul > li a {
    	color: var(--prime-50);
        display: inline-block;
        line-height: 1.2;
        text-decoration: none;
        font-size: 0.875rem; /
    }

    .mod-recent-posts-ul > li a:hover {
        color: var(--primary-color);
    }

    .recent-posts-title {
    	font-size: 0.9rem;
    }
</style>