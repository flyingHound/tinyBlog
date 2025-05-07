<ul>
    <li><h3 style="margin-bottom: .5rem;">Blog Management</h3></li>
    <li><?= anchor('blog_dashboard', 'Dashboard') ?></li>
    <li><?= anchor('blog_posts/manage', 'Manage Blog Posts') ?></li>
    <li><?= anchor('blog_categories/manage', 'Manage Categories') ?></li>
    <li><?= anchor('blog_tags/manage', 'Manage Tags') ?></li>
    <li><?= anchor('blog_sources/manage', 'Manage Sources') ?></li>
    <li><?= anchor('blog_pictures/manage', 'Manage Pictures') ?></li>
    <li><?= anchor('blog_comments/manage', 'Manage Comments*') ?></li>
    
    <li style="margin-top: 1rem;"><h3 style="margin-bottom: .5rem;">Navigation</h3></li>
    <li><?= anchor('menus/manage', 'Manage Menus') ?></li>
    <li><?= anchor('menu_items/manage', 'Manage Menu Items') ?></li>

    <li style="margin-top: 1rem;"><h3 style="margin-bottom: .5rem;">App</h3></li>
    <li><?= anchor('trongate_pages/manage', 'Manage Articles') ?></li>
    <li><?= anchor('trongate_admins/manage', 'Manage Admins') ?></li>
    <li><?= anchor('trongate_comments/manage', 'Manage Comments') ?></li>
    <li><?= anchor('enquiries/manage', 'Manage Enquiries') ?></li>

    <li style="margin-top: 1rem;"><h3 style="margin-bottom: .5rem;">App Settings</h3></li>
    <li><?= anchor('app/index', 'Manage App*') ?></li>
    <li><?= anchor('blog_settings/index', 'Settings*') ?></li>
</ul>