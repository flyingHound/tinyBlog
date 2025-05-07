<?php if (!empty($menu_items)): ?>
<div class="app-nav">
    <ul>
        <?php foreach ($menu_items as $index => $item): ?>
            <li <?php 
                // Wenn es der erste Eintrag (Dashboard) ist und die URL nur /app ist
                if ($index === 0 && $current_url === $base_module_url) {
                    echo 'class="active"';
                } 
                // Normaler Vergleich für alle anderen Fälle
                elseif ($current_url === $item['url_string']) {
                    echo 'class="active"';
                }
            ?>>
                <a href="<?= $item['url_string'] ?>">
                    <?= out($item['title']) ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>
<style>
    .app-nav ul {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        gap: 1em;
    }

    .app-nav li {
        padding: 0.5em 1em;
    }

    .app-nav a {
        text-decoration: none;
        color: #333;
        font-weight: 500;
    }

    .app-nav li.active {
        background-color: #f0f0f0;
        border-radius: 4px;
    }

    .app-nav a:hover {
        color: #007bff;
    }

    @media (max-width: 768px) {
        .app-nav ul {
            flex-direction: column;
            gap: 0.5em;
        }
    }
</style>