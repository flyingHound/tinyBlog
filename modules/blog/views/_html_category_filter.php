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

<style>
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
        color: var(--gray-70); /*#333;*/
        padding: 4px 8px;
        border-radius: 6px;
        border: 1px solid var(--prime-10);
        transition: all 0.3s ease;
        display: block;
        font-size: .875rem;
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
</style>
<?php endif; ?>