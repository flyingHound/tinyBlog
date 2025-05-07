<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="<?= BASE_URL ?>">
    <title><?= defined('WEBSITE_NAME') ? WEBSITE_NAME . '-Admin' : 'Admin' ?></title>
    
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom css -->
    <link rel="stylesheet" href="<?= THEME_DIR ?>assets/css/tiny_bootstrap.css">
    
    <!-- Fonts -->
    <!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">-->
    <link rel="stylesheet" href="<?= THEME_DIR ?>assets/css/fontawesome.css">
    <?= $additional_includes_top ?>
</head>
<body>
    <div id="root" class="page">

        <header>
            <?= Template::partial('partials/tiny_bootstrap/header', $data); ?>
        </header>

        <div class="wrapper">

            <?= Template::partial('partials/tiny_bootstrap/sidebar', $data); ?>

            <div class="mobile-overlay"></div>

            <div class="content">

                <main>
                    <?= Template::display($data) ?>
                </main>

                <footer class="small">

                    <p class="credit m-0">
                        © <?= date('Y') ?> all rights reserved - <span class="font-chau">tinyBootstrap</span> for Trongate
                    </p>

                </footer>

            </div>

        </div>
            
        
    </div>

    <!-- Scroll-Up Button -->
    <button id="scroll-up" class="btn btn-dark btn-sm" aria-label="Scroll to top">
        <i class="fa fa-chevron-up"></i>
    </button>

    <script src="<?= THEME_DIR ?>assets/js/tiny_bootstrap.js"></script>
    <?= $additional_includes_btm ?>
</body>
</html>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const scrollUpBtn = document.getElementById('scroll-up');

    // Visibility of Scroll-Up-Buttons based on position
    window.addEventListener('scroll', function () {
        if (window.scrollY > 100) { // Show button after 100px scrolling
            scrollUpBtn.classList.add('visible');
        } else {
            scrollUpBtn.classList.remove('visible');
        }
    });

    // Smooth scroll
    scrollUpBtn.addEventListener('click', function (e) {
        e.preventDefault();
        document.getElementById('root').scrollIntoView({ behavior: 'smooth' });
    });
});

document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.querySelector('#sidebar');
    const content = document.querySelector('.content');
    const navbarBrandWrapper = document.querySelector('.navbar-brand-wrapper');
    const toggleButton = document.querySelector('.sidebar-toggle');
    const overlay = document.querySelector('.mobile-overlay');

    // Update sidebar state based on window size
    function updateSidebarState() {
        // Mobile behavior (≤575.98px)
        if (window.innerWidth <= 575.98) {
            sidebar.classList.remove('active', 'minimized');
            overlay.classList.remove('active');
            content.classList.remove('minimized');
            navbarBrandWrapper.classList.remove('minimized');
            document.body.style.overflow = '';
        } 
        // Desktop behavior (>575.98px)
        else {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
            document.body.style.overflow = '';

            // Apply minimized class below 850px, remove it at or above 850px
            if (window.innerWidth < 850) {
                sidebar.classList.add('minimized');
                content.classList.add('minimized');
                navbarBrandWrapper.classList.add('minimized');
            } else {
                sidebar.classList.remove('minimized');
                content.classList.remove('minimized');
                navbarBrandWrapper.classList.remove('minimized');
            }
        }
    }

    // Initialize
    updateSidebarState();
    sidebar.classList.add('loaded'); // Activate transition after page load

    // Toggle-Button Logic
    toggleButton.addEventListener('click', function () {
        if (window.innerWidth <= 575.98) {
            const isActive = sidebar.classList.toggle('active');
            overlay.classList.toggle('active', isActive);

            if (isActive) {
                sidebar.querySelector('a').focus();
                document.body.style.overflow = 'hidden';
            } else {
                toggleButton.focus();
                document.body.style.overflow = '';
            }
        } else {
            sidebar.classList.toggle('minimized');
            content.classList.toggle('minimized');
            navbarBrandWrapper.classList.toggle('minimized');
        }
    });

    // Overlay closes the Sidebar for Mobile
    overlay.addEventListener('click', function () {
        if (window.innerWidth <= 575.98) {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
            document.body.style.overflow = '';
            toggleButton.focus();
        }
    });

    // Close Sidebar with ESC key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && sidebar.classList.contains('active')) {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
            document.body.style.overflow = '';
            toggleButton.focus();
        }
    });

    // Update on resize
    window.addEventListener('resize', updateSidebarState);
});
</script>