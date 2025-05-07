<?php if (!empty($menu_items)): ?>
<div class="app-nav">
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
</div>
<style>
.app-nav ul {
    list-style: none;
    padding: 0;
    margin: 0;
}
.app-nav li {
    padding: 0.5em 1em;
}
.app-nav a {
    text-decoration: none;
    color: #333;
    display: block;
}
.app-nav li.active {
    background-color: #f0f0f0;
    border-radius: 4px;
}
.app-nav a:hover {
    color: #007bff;
}
.app-nav .submenu {
    list-style: none;
    padding-left: 1em;
    margin: 0;
}
</style>
<?php endif; ?>