<?php foreach ($menu_sections as $section_title => $items): ?>
    <h6 class="sidebar-section-title font-chau"><?= $section_title ?></h6>
    <ul class="list-unstyled">
        <?php foreach ($items as $item): ?>
            <?php $is_active = strpos($current_url, $item['url']) === 0; ?>
            <li class="nav-item mb-2<?= $is_active ? ' active' : '' ?>">
                <a 
                    href="<?= $item['url'] ?>" 
                    class="nav-link text-decoration-none<?= $is_active ? ' active' : '' ?>"
                >
                    <i class="<?= $item['icon'] ?> nav-icon fs-6"></i>
                    <span class="nav-label"><?= $item['title'] ?></span>
                    <span class="mininav-content"><?= $item['title'] ?></span> <!-- sollte icon sein -->
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endforeach; ?>