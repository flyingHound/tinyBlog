<?php
$post_class = $post_class ?? '';
?>
<article <?= $post_class ?>>

    <p class="post-category"><?= out($category ? $category->title : 'Uncategorized') ?></p>

    <?php if ($picture_url != ''): ?>
        <figure class="block-image text-center size-full">
        <img src="<?= BASE_URL . out($picture_url) ?>" alt="<?= out($record->title) ?>-image" class="post-article-img">
    </figure>
    <?php endif; ?>

    <p class="post-date">
        <?= date($date_format, strtotime($record->date_published)) ?> | written by <?= ucfirst(out($author)) ?>
        <?= out($category ? ', category: '.$category->title : '') ?>
            
        </p>

    <h1><?= htmlspecialchars_decode($record->title) ?></h1>

    <?php if (!empty($record->subtitle)): ?>
        <h2><?= htmlspecialchars_decode($record->subtitle, ENT_QUOTES) ?></h2>
    <?php endif; ?>

    <div class="post-content"><?= htmlspecialchars_decode($record->text, ENT_QUOTES) ?></div>

    <div class="post-info-btm">
        <span class="post-author">Written by <?= ucfirst(out($author)) ?> on <?= date('F d, Y', strtotime($record->date_published)) ?></span>
        <?php if (!empty($source)): ?>
            <span class="post-source">| source: <?= out($source->author) ?> from 
                <a class="post-source-link" href="<?= out($source->link) ?>" target="_blank"><?= out($source->website) ?></a>
            </span>
        <?php endif; ?>
    </div>

    <div class="post-tags">
        <?php foreach ($tags as $tag): ?>
            <a href="<?= BASE_URL ?>blog/tag/<?= out($tag->url_string) ?>" class="tag-link"><?= out($tag->name) ?></a>
        <?php endforeach; ?>
    </div>

    <?php if (!empty($html_youtube)): ?>
    <section class="post-youtube">
        <?= $html_youtube ?>
    </section> 
    <?php endif; ?>

    <?php if (!empty($html_gallery)): ?>
    <section class="post-gallery">
        <?= $html_gallery ?>
    </section> 
    <?php endif; ?>

    <section class="post-pagination border-top">
        <div class="pagination">
            <?php if ($prev_link !== null): ?>
                <a href="<?= out($prev_link) ?>" class="previous">«<span class="pagination-tag"> previous </span></a>
            <?php else: ?>
                <span class="pagination-inactive" aria-disabled="true">«<span class="pagination-tag"> previous </span></span>
            <?php endif; ?>
            <a href="<?= BASE_URL ?>blog/posts" class="button-small">Back to Blog Posts</a>
            <?php if ($next_link !== null): ?>
                <a href="<?= out($next_link) ?>" class="next"><span class="pagination-tag"> next </span>»</a>
            <?php else: ?>
                <span class="pagination-inactive" aria-disabled="true"><span class="pagination-tag"> next </span>»</span>
            <?php endif; ?>
        </div>
    </section>
</article>
<style>
  /* single-post */
    .single-post {} /* body class */
    .article-rounded {} /* post class */

    .post-category { font-size: 85%; color: white; background-color: var(--highlight-color); display: inline-block; padding: 0 4px 2px;}

    figure { margin: 0 0 1rem; }

    .post-article-img {
        max-width: 100%;
        height: auto;
    }

    .post-date { font-size: 85%; }

    .post-content {
        line-height: 1.6;
        margin: 1rem 0;
    }

    .post-info-btm { font-size: 85%; }

    .post-author {}

    .post-source {}

    .post-source-link {}


  /* tags */
    .post-tags {
        margin: 1rem 0;
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .tag-link {
        color: var(--prime-70);
        text-decoration: none;
        font-size: 0.9rem;
        padding: 0.2rem 0.5rem;
        background: var(--prime-10);
        border-radius: 3px;
        transition: background 0.3s ease, color 0.3s ease;
    }

    .tag-link:hover {
        color: var(--link-hover-color);
        background: var(--prime-20);
    }

  /* youtube, gallery */
    section.post-youtube, section.post-gallery {
        margin: 2rem 0;
    }

  /* pagination */
    .post-pagination {}

    .pagination {
        display: flex; /* Nur flex, inline-block wird überschrieben */
        flex-direction: row;
        text-align: center;
        margin: 1em auto;
        width: fit-content;
    }

    .pagination:last-of-type {
        margin-top: 1em;
    }

    .pagination a {
        color: var(--primary-color);
        padding: 8px 16px;
        text-decoration: none;
        border: 1px solid var(--prime-10);
    }

    .pagination a.active {
        background-color: var(--primary-color);
        color: white;
        border: 1px solid var(--primary-color);
    }

    .pagination a:hover:not(.active) {
        background-color: var(--prime-10);
    }

    /* Neue Klasse für inaktive Elemente */
    .pagination .pagination-inactive {
        color: var(--prime-30); 
        padding: 8px 16px;
        border: 1px solid var(--prime-10); /* Gleiche Border wie Links */
        cursor: not-allowed;
    }

    /* Borders zwischen Elementen */
    .pagination a:not(:first-child),
    .pagination .pagination-inactive:not(:first-child) {
        border-left: none; /* Entfernt doppelte linke Border */
    }

    /* Rundungen */
    .pagination a:first-child,
    .pagination .pagination-inactive:first-child {
        border-top-left-radius: 5px;
        border-bottom-left-radius: 5px;
    }

    .pagination a:last-child,
    .pagination .pagination-inactive:last-child {
        border-top-right-radius: 5px;
        border-bottom-right-radius: 5px;
    }
</style>