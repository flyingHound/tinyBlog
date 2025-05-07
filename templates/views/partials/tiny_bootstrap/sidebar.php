<div id="sidebar" class="bg-light border-end">

    <!-- Avatar, Name, Administrator-Label and Dropdown -->
    <div class="sidebar-user-section text-center mb-4">
        
        <!-- Avatar -->
        <div class="avatar-wrapper nav-item">
            <img src="<?= BASE_URL ?>public/uploads/users_images/2.jpg" alt="User Avatar" class="sidebar-avatar rounded-circle mb-2 border border-1 border-gray shadow-sm">
            <div class="mininav-content user-tooltip">
                <a href="http://localhost/tinyblog/trongate_administrators/create/2" class="tooltip-link">Profile</a>
                <a href="http://localhost/tinyblog/trongate_administrators/logout" class="tooltip-link">Logout</a>
            </div>
        </div>
        
        <!-- Name und Collapse-Button -->
        <div class="sidebar-username-wrapper">
            
            <button class="btn btn-link text-decoration-none text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#userCollapse" aria-expanded="false" aria-controls="userCollapse">
                <span class="sidebar-username d-flex justify-content-center align-items-center">
                    <h5 class=" mb-0 me-1"><?= isset($username) ? $username : 'Unknown' ?></h6>
                    <i class="fa fa-chevron-down fa-xs"></i>
                </span>
                
                <!-- Administrator-Label -->
                <?php if (isset($user_level_id) && $user_level_id == 1): ?>
                    <small class="sidebar-label text-muted">Administrator</small>
                <?php endif; ?>
            </button>
        </div>

        <!-- Collapse-Element -->
        <div class="collapse mt-2" id="userCollapse">
            <div class="card p-2">
                <a class="d-block text-dark text-decoration-none py-1" href="<?= BASE_URL ?>trongate_administrators/create/<?= isset($user_id) ? $user_id : 0 ?>">
                    <i class="fa fa-user me-2"></i>Profile
                </a>
                <a class="d-block text-dark text-decoration-none py-1" href="<?= BASE_URL ?>trongate_administrators/logout">
                    <i class="fa fa-sign-out me-2"></i>Logout
                </a>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <?= $sidebar_nav ?>
</div>