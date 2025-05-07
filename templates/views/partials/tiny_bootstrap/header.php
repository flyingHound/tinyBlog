<nav class="navbar bg-dark text-white border-bottom border-body" data-bs-theme="dark">

    <div class="container-fluid p-0">

        <div class="navbar-brand-wrapper">
            <a class="navbar-brand ms-3" href="<?= BASE_URL ?>blog_dashboard"><span class="logo font-chau">tinyBlog</span> Admin</a>
        </div>

        <div class="d-flex justify-content-between align-items-center flex-grow-1 px-3">
            
            <button class="btn header__btn sidebar-toggle btn-sm me-2" aria-label="Nav Toggler" aria-expanded="false">
                <i class="fa fa-list"></i>
            </button>

            <div class="btn-group">

                <?= Modules::run("enquiries/widget_enquiries_dropdown") ?>

                <?php 
                /**
                 * under construction: user dropdown 
                 * 
                 */ ?>
                <?= anchor('trongate_administrators/create/'.$user_id, '<i class="fa fa-user"></i>', [
                    'class' => 'btn header__btn user-menu-toggle btn-sm me-2',
                    'title' => 'My Account',
                    'role' => 'button',
                    'aria-label' => 'User Account / Menu Toggler'
                ]) ?>

                <?= anchor('trongate_administrators/logout', '<i class="fa fa-sign-out"></i>', [
                    'class' => 'btn header__btn btn-sm',
                    'title' => 'Logout',
                    'role' => 'button',
                    'aria-label' => 'User Logout'
                ]) ?>
            </div>
        </div>
    </div>
</nav>
<style>
    .header__btn {
        background-color: #212529; 
        color: #ffffff; 
        border: none;
    }

    .header__btn .fa {
        font-size: inherit;
    }

    .header__btn .badge {
        position: absolute;
        top: -8px;
        right: -8px;
        font-size: 0.75rem;
    }

    .header__btn:hover {
        background-color: #343a40;
        color: #ffffff; 
    }

    .dropdown-toggle::after {
        display: none;
    }
</style>