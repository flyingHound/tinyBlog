<?php
# Admin
$admin_theme = [
    "dir" => "default_admin/blue",
    "template" => "admin.php",
];
$themes['admin'] = $admin_theme;
# Tiny Bootstrap
$tiny_bootstrap_theme = [
    "dir" => "tiny_bootstrap",
    "template" => "tiny_bootstrap.php",
];
$themes['tiny_bootstrap'] = $tiny_bootstrap_theme;
# Tiny Blog
$tiny_blog_theme = [
    "dir" => "tiny_blog",
    "template" => "tiny_blog.php",
];
$themes['tiny_blog'] = $tiny_blog_theme;
define('THEMES', $themes);