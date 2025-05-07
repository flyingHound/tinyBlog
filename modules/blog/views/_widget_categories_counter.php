<?php $current_cat = segment(3); ?>
<div class="panel mod-categories-list">
    <h3>Categories</h3>
    <?php if (!empty($rows)) : ?>
        <ul class="mod-categories-ul">
            <?php foreach ($rows as $cat) : ?>
                <li <?= $current_cat === $cat->url_string ? 'class="active"' : '' ?>>
                    <a href="<?= BASE_URL ?>blog/category/<?= out($cat->url_string) ?>">
                        <?= out($cat->title) ?> <span>(<?= $cat->post_count ?>)</span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else : ?>
        <p>No categories available.</p>
    <?php endif; ?>
</div>
<style>
    .mod-categories-list {}
    .mod-categories-list ul {}
    ul.mod-categories-list-ul {}

    .mod-categories-list li { display: block; }

    .mod-categories-list li {
        display: block;
        padding: 5px 0;
        line-height: 36px;
        padding: 2px 0;
    }

    .mod-categories-list li a { 
        display: flex; 

        -webkit-box-align: center;
        -ms-flex-align: center;
        align-items: center;
        -webkit-box-pack: justify;
        -ms-flex-pack: justify;
        justify-content: space-between;
        padding: 0;
        line-height: 1.8;
    }
</style>