<?php if (!empty($menu_items)): ?>
<nav id="nav-bar" role="navigation" aria-label="Main Navigation">
    <ul role="list">
        <?php foreach ($menu_items as $item): ?>
            <li <?php echo $current_url === BASE_URL . ltrim($item['url_string'], '/') ? 'class="active"' : ''; ?>>
                <a href="<?= $item['url_string'] ?>" target="<?= $item['target'] ?>">
                    <?= out($item['title']) ?>
                </a>
                <?php if (!empty($item['children'])): ?>
                    <ul class="submenu">
                        <?php foreach ($item['children'] as $child): ?>
                            <li <?php echo $current_url === BASE_URL . ltrim($child['url_string'], '/') ? 'class="active"' : ''; ?>>
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