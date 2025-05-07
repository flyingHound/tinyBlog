<?php
$body_class = $body_class ?? '';
$page_title = !empty($headline) ? strip_tags($headline) : (defined('WEBSITE_NAME') ? WEBSITE_NAME : 'Blog');
$layout = $layout ?? '0';
?>
<!DOCTYPE html>
<html lang="en-US">
<head>
	<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="<?= BASE_URL ?>">
    <title><?= htmlspecialchars($page_title) ?> | <?= defined('WEBSITE_NAME') ? WEBSITE_NAME : 'Blog' ?></title>
    <?= Template::partial('partials/tiny_blog/head-meta', ['page_title' => $page_title]) ?>
    <link rel="stylesheet" href="<?= THEME_DIR ?>assets/css/tiny_blog.css">
    <link rel="stylesheet" href="<?= THEME_DIR ?>assets/css/fontawesome.css">
    <?= $additional_includes_top ?? '' ?>
</head>
<body <?= $body_class ?>>

    <div class="wrapper">

        <header id="header">

            <div class="inside flex-row">

                <div class="logo">
                    <!--<span class="feather" style="display: none;"></span>-->
                    <span class="trongate-tag">Trongate </span>
                    <span class="logo-tag"><span>tiny</span>Blog</span>
                    <span class="slogan">True insight is instantaneous.</span>
                </div>

                <div class="mobile-panel">
                    <button class="tiny-toggle" aria-label="Toggle navigation">
                        <span class="tiny-toggle__icon"></span>
                    </button>
                </div>

                <?= Modules::run('menus/render_navigation', 1) ?>

            </div>

            <div class="mobile-overlay"></div>

        </header>

        <main id="page-content" class="content-bg" role="main">

            <div class="page flex-2col">

                <div class="content ">

                    <?= Template::display($data) ?>

                </div>

                <?php 

                if( $layout === '1' ) { 
                    echo Template::partial('partials/tiny_blog/sidebar');
                } ?>

            </div>

        </main>
        
        <footer id="footer">

            <div class="inside">

                <div class="ul-container">

                    <?= Modules::run('menus/render_navigation', 2) ?>

                    <ul class="company-info">
                        <li><b><?= WEBSITE_NAME ?></b></li>           
                        <li><?= OUR_ADDRESS ?></li>
                        <li><?= OUR_TELNUM ?></li>
                        <li><a href="mailto:#"><?= OUR_EMAIL_ADDRESS ?></a></li>
                    </ul>

                    <ul class="social-media">
                        <li><a href="#"><i class="fa fa-instagram"></i></a></li>
                        <li><a href="#"><i class="fa fa-linkedin"></i></a></li>
                        <li><a href="#"><i class="fa fa-facebook"></i></a></li>
                    </ul>

                </div>

                <div class="flex-row">

                    <p class="credit">
                        &copy <?= date('Y') ?> all rights reserved, <span class="font-chau">tinyBlog</span> for Trongate
                    </p>

                    <a class="btn go-up" aria-label="Scroll Up" href="<?= current_url() ?>#">
                        <span class="fa fa-angle-up" aria-hidden="true"></span>
                    </a>

                </div>

            </div>
        </footer>

    </div>

    <script src="<?= THEME_DIR ?>assets/js/tiny_blog.js"></script>
    <?= $additional_includes_btm ?>
</body>
</html>