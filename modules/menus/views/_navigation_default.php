<?php if (!empty($menu_items)): ?>
<nav id="nav-bar" role="navigation" aria-label="Main Navigation">
    <ul role="list">
        <?php
        // Erstes Segment nach BASE_URL holen
        $base_path = ltrim(parse_url(BASE_URL, PHP_URL_PATH) ?? '', '/'); // z. B. 'tinyblog'
        $current_segment = segment(1); // Erstes Segment nach dem Basis-Pfad
        if ($base_path && $current_segment === $base_path) {
            $current_segment = segment(2); // Falls BASE_URL im Pfad, nächstes Segment nehmen
        }
        ?>
        <?php foreach ($menu_items as $item): ?>
            <?php
            // url_string bereinigen: BASE_URL und alles nach dem ersten / entfernen
            $item_path = $item['url_string'];
            // BASE_URL entfernen, falls vorhanden
            if (str_starts_with($item_path, BASE_URL)) {
                $item_path = substr($item_path, strlen(BASE_URL));
            }
            // Alles nach dem ersten / abschneiden
            $item_segment = explode('/', ltrim($item_path, '/'))[0] ?? '';
            // Aktiv, wenn Segmente übereinstimmen
            $is_active = $current_segment === $item_segment && $item_segment !== '';
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
                            if (str_starts_with($child_path, BASE_URL)) {
                                $child_path = substr($child_path, strlen(BASE_URL));
                            }
                            $child_path = ltrim($child_path, '/');
                            // Exakter Vergleich für Kinder
                            $current_full_path = implode('/', array_filter(array_slice(explode('/', parse_url(current_url(), PHP_URL_PATH)), 1 + ($base_path ? 1 : 0))));
                            $child_is_active = $current_full_path === $child_path;
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
/* Dein CSS bleibt unverändert */
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