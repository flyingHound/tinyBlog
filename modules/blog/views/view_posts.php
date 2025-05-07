<div class="page-headline">
    <h1>
        <?= out($headline) ?>
    </h1>
</div>

<?php if (!empty($categories)): ?>
<div class="category-filter">
    <ul>
        <li <?= segment(1) === 'blog' && segment(2) === 'posts' && segment(3) === '' ? 'class="active"' : '' ?>>
            <a href="<?= BASE_URL ?>blog/posts">All</a>
        </li>
        <?php foreach ($categories as $category): ?>
            <li <?= !empty($category_id) && $category->id === $category_id ? 'class="active"' : '' ?>>
                <a href="<?= BASE_URL ?>blog/category/<?= out($category->url_string) ?>">
                    <?= out($category->title) ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<?php 
if( !empty($rows) ) { $i = 1; // If rows exist, show posts ?>
<div class="publications">
    
    <?php foreach( $rows as $row ) { ?>
        
        <div class="post-card">

            <?php if (isset($row->category->title) && !empty($row->category->title) ): ?>
            <span class="post-category">
                <?= out($row->category->title)?>
            </span>
            <?php endif ?>

            <?php 
            // picture or fallback
            if( !empty($row->picture) ) { 
                $pic_url = $pic_dir.'/'.$row->id.'/'.$row->picture; 
            } else { 
                $pic_url = $pic_fallback;
            }
            ?>

            <?php if ($pic_url != ''): ?>
                <div class="post-img">
                <img src="<?= $pic_url ?>" alt="<?= $row->url_string ?>-image">
            </div>
        <?php endif ?>

            <div class="post-info">
                <span class="post-date"><?= date($date_format, strtotime($row->date_published)) ?></span>
                <span class="post-author">| written by <?= ucfirst(out($row->created_by)) ?></span>

                <?php if (!empty($row->source)): ?>
                <span class="post-source">| source: <?= out($row->source->author) ?> from <a class="post-source-link" href="<?= out($row->source->link) ?>" target="_blank"><?= out($row->source->website) ?></a></span>
                <?php endif; ?>

            </div>

            <h2 class="post-title"><?= htmlspecialchars_decode($row->title, ENT_QUOTES) ?></h2>
            
            <h3 class="post-sub"><?= htmlspecialchars_decode($row->subtitle, ENT_QUOTES) ?></h3>

            <p class="post-short"><?= $row->text_short ?? '' ?></p>

            <div class="post-meta hidden">
                Written by 
                <span class="created"><?= out($row->created_by) ?></span> | 
                <span class="published"><?= out($row->date_published) ?></span>
            </div>

            <?php if (isset($row->tags) && $row->tags > 0): ?>
            <div class="post-tags">
                <?php foreach ($row->tags as $tag) : ?>
                    <a href="<?= BASE_URL ?>blog/tag/<?= out($tag->url_string) ?>" class="tag-link">
                        #<?= out($tag->name) ?>
                    </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <a class="post-anchor" href="<?= BASE_URL ?>blog/post/<?= out($row->url_string) ?>" target=""></a>

            <div>
                <a href="<?= BASE_URL ?>blog/post/<?= out($row->url_string) ?>" class="btn read-more">Read More
                    <span> &rsaquo;</span>
                </a>
            </div>
            
        </div>
        
    <?php } ?>
</div>

<section class=" border-top">

    <?php
    // Display Pagination
    #unset($pagination_data['include_showing_statement']);
    echo Pagination::display($pagination_data);
    ?>
</section>

<section>
    <!-- Category Filter -->
    <?php if(isset($cat_filter)) { echo $cat_filter; }?>
        
</section>

<?php } else { // If no rows exist, display a message ?>
    <p>There are currently no blog posts published.</p>
<?php } ?>

<style>
    .publications {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); /* Responsive grid */
        gap: 2rem; /* Space between posts */
        padding: 0;
        max-width: 610px;
        margin: 0 auto;
    }

    .post-card {
        position: relative;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .post-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }

    .post-img img {
        width: 100%;
        height: 180px;
        object-fit: cover;
        object-position: center;
        display: block;
    }

    .post-card:hover .post-img img {
        /*height: auto;*/
    }

    .post-info {
        font-size: 0.9rem;
        color: var(--prime-40);
        padding: 10px 16px 0;
    }

    .post-title {
        font-size: 1.4rem;
        font-weight: bold;
        margin: 8px 16px;
        color: var(--primary-color);
    }

    .post-sub {
        font-size: 1.2rem;
        font-weight: normal;
        margin: 0 16px 10px;
        color: var(--primary-color);
    }

    .post-short {
        font-size: 1rem;
        color: var(--prime-60);
        padding: 0 16px 10px;
        line-height: 1.5;
        flex-grow: 1;
    }

    .post-meta {
        font-size: 0.85rem;
        color: #888;
        padding: 10px 16px;
        border-top: 1px solid var(--prime-10);
    }

    .btn.read-more {
        display: inline-block;
        margin: 1rem 0 0;
        padding: 8px 12px;
        font-size: 0.9rem;
        color: #fff;
        background: var(--prime-70);
        text-decoration: none;
        border-radius: 6px;
        transition: background 0.3s ease;
    }

    .btn.read-more:hover {
        background: var(--primary-color);
    }

   /* Mobile Optimization */
    @media (max-width: 600px) {
        .post-card {
            border-radius: 0;
            box-shadow: none;
        }
    }
   /* CATEGORY FILTER */
    .category-filter ul {
        list-style: none;
        padding: 0;
        display: flex;
        gap: .5rem;
        flex-wrap: wrap;
        margin-bottom: 1rem;
        justify-content: flex-start;
    }

    .category-filter li {
        margin: 0;
    }

    .category-filter a {
        text-decoration: none;
        color: var(--gray-70);
        padding: 4px 8px;
        border-radius: 6px;
        border: 1px solid var(--prime-10);
        transition: all 0.3s ease;
        display: block;
        font-size: .825rem;
    }

    .category-filter li.active a {
        background-color: var(--prime-70);
        color: white;
        border-color: var(--prime-70);
    }

    .category-filter a:hover {
        background-color: var(--prime-20);
    }

    .category-filter li.active a:hover {
        background-color: var(--link-hover-color);
    }

    /* category batch */
    .post-category {
        font-size: 85%;
        color: white;
        background-color: var(--highlight-color);
        display: inline-block;
        padding: 0 4px 2px;
        margin-bottom: .5rem;
    }

   /* tags */
    .post-tags {
        margin-top: 0.5rem;
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .tag-link {
        color: var(--prime-70);
        text-decoration: none;
        font-size: 0.9rem;
        padding: 0.2rem 0.5rem;
        border: 1px solid var(--prime-10);
        border-radius: 3px;
        transition: background 0.3s ease, color 0.3s ease;
    }

    .tag-link:hover {
        color: var(--link-hover-color);
        background: var(--prime-20);
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
        background-color: var(--prime-70);
        color: white;
        border: 1px solid var(--primary-color);
    }

    .pagination a:hover:not(.active) {
        background-color: var(--prime-10);
    }

    .pagination a:first-child {
        border-top-left-radius: 5px;
        border-bottom-left-radius: 5px;
    }

    .pagination a:last-child {
        border-top-right-radius: 5px;
        border-bottom-right-radius: 5px;
    }

    /* Neue Klasse für inaktive Elemente */
    .pagination .pagination-inactive {
        color: var(--prime-30); 
        padding: 8px 16px;
        border: 1px solid var(--prime-10); /* Gleiche Border wie Links */
        cursor: not-allowed;
    }
</style>