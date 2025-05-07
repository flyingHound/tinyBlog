<?php if (!empty($menu_items)): ?>
<nav id="nav-bar" role="navigation" aria-label="Main Navigation">
    <ul role="list">
        <?php
        // Basis-Pfad aus BASE_URL extrahieren
        $base_path = ltrim(parse_url(BASE_URL, PHP_URL_PATH) ?? '', '/'); // z. B. 'tinyblog'
        $current_path = ltrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'); // z. B. 'tinyblog/blog'
        if ($base_path && str_starts_with($current_path, $base_path)) {
            $current_path = substr($current_path, strlen($base_path) + 1); // Entfernt 'tinyblog/' → 'blog'
        }
        ?>
        <?php foreach ($menu_items as $item): ?>
            <?php
            $item_path = $item['url_string'];
            if (filter_var($item_path, FILTER_VALIDATE_URL)) {
                $item_path = parse_url($item_path, PHP_URL_PATH);
            }
            $item_path = ltrim($item_path, '/');
            if ($base_path && str_starts_with($item_path, $base_path)) {
                $item_path = substr($item_path, strlen($base_path) + 1); // Entfernt 'tinyblog/' falls vorhanden
            }
            $is_active = $current_path === $item_path;
            ?>
            <li <?php echo $is_active ? 'class="active"' : ''; ?>>
                <a href="<?= $item['url_string'] ?>" target="<?= $item['target'] ?>">
                    <?= out($item['title']) ?>
                </a>
                <?php if (!empty($item['children'])): ?>
                    <ul class="submenu">
                        <?php foreach ($item['children'] as $child): ?>
                            <?php
                            $child_path = $child['url_string'];
                            if (filter_var($child_path, FILTER_VALIDATE_URL)) {
                                $child_path = parse_url($child_path, PHP_URL_PATH);
                            }
                            $child_path = ltrim($child_path, '/');
                            if ($base_path && str_starts_with($child_path, $base_path)) {
                                $child_path = substr($child_path, strlen($base_path) + 1);
                            }
                            $child_is_active = $current_path === $child_path;
                            ?>
                            <li <?php echo $child_is_active ? 'class="active"' : ''; ?>>
                                <a href="<?= $child['url_string'] ?>" target="<?= $child['target'] ?>">
                                    <?= out($child['title']) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
<style>
/* Dein bestehender CSS bleibt unverändert */
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
}
#nav-bar .submenu {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    background-color: #fff;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    border-radius: 4px;
    min-width: 150px;
}
#nav-bar li:hover .submenu {
    display: block;
}
#nav-bar .submenu li {
    padding: 0.5em 1em;
    display: block;
}
@media (max-width: 768px) {
    #nav-bar ul[role="list"] {
        flex-direction: column;
        gap: 0.5em;
    }
    #nav-bar .submenu {
        position: static;
        box-shadow: none;
    }
}
</style>
<?php endif; ?>