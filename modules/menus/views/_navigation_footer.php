<?php if (!empty($menu_items)): ?>
<ul class="menu">
            <?php
        // Browser-URL holen und BASE_URL abziehen
        $current_url = current_url(); // z. B. 'http://localhost/tinyblog/blog/posts'
        if (str_starts_with($current_url, BASE_URL)) {
            $current_path = substr($current_url, strlen(BASE_URL)); // 'blog/posts'
        } else {
            $current_path = $current_url;
        }
        $current_first = explode('/', $current_path)[0] ?? ''; // 'blog'
        ?>
        <?php foreach ($menu_items as $item): ?>
            <?php
            // url_string bereinigen und erstes Stück nehmen
            $item_url = $item['url_string'];
            if (str_starts_with($item_url, BASE_URL)) {
                $item_path = substr($item_url, strlen(BASE_URL)); // Falls volle URL
            } else {
                $item_path = $item_url;
            }
            $item_first = explode('/', $item_path)[0] ?? ''; // 'blog'
            $is_active = $current_first === $item_first && $item_first !== '';
            ?>
            <li <?php echo $is_active ? 'class="active"' : ''; ?>>
                <a href="<?= $item['url_string'] ?>" target="<?= $item['target'] ?>">
                    <?= out($item['title']) ?>
                </a>
            </li>
        <?php endforeach; ?>
</ul>
<style>/*
.menu {
    list-style: none;
    padding: 0;
    margin: 0;
}
.menu li {
    display: inline-block;
    margin-right: 1em;
}
.menu li.active a {
    font-weight: bold;
}
.menu a {
    text-decoration: none;
    color: #333;
}

.menu a:hover {
    color: #007bff;
}
.menu .submenu {
    list-style: none;
    padding-left: 1em;
    margin: 0;
}
@media (max-width: 768px) {
    .menu li {
        display: block;
        margin: 0.5em 0;
    }
}*/
</style>
<?php endif; ?>