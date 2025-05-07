<?php if (!empty($menu_items)): ?>
<nav id="nav-bar" role="navigation" aria-label="Main Navigation">
    <ul role="list">
        <?php
        // Browser-URL holen und BASE_URL abziehen
        $current_url = current_url();
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
</nav>
<style>
/* CSS ohne Änderung 
#nav-bar ul[role="list"] {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    gap: 1em;
}
#nav-bar li {
    position: relative;
    padding: 0.5em 1em;
}
#nav-bar a {
    text-decoration: none;
    color: #333;
    font-weight: 500;
}
#nav-bar li.active {
    background-color: #f0f0f0;
    border-radius: 4px;
}
#nav-bar a:hover {
    color: #007bff;
}*/
</style>
<?php endif; ?>