<?php if (!empty($menu_items)): ?>
<nav class="main-nav">
    <ul>
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
<?php endif; ?>

<style>
.main-nav ul {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    gap: 1em;
}
.main-nav li {
    position: relative;
    padding: 0.5em 1em;
}
.main-nav a {
    text-decoration: none;
    color: #333;
    font-weight: 500;
}
.main-nav li.active {
    background-color: #f0f0f0;
    border-radius: 4px;
}
.main-nav a:hover {
    color: #007bff;
}
.main-nav .submenu {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    background-color: #fff;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    border-radius: 4px;
    min-width: 150px;
}
.main-nav li:hover .submenu {
    display: block;
}
.main-nav .submenu li {
    padding: 0.5em 1em;
    display: block;
}
@media (max-width: 768px) {
    .main-nav ul {
        flex-direction: column;
        gap: 0.5em;
    }
    .main-nav .submenu {
        position: static;
        box-shadow: none;
    }
}
</style>