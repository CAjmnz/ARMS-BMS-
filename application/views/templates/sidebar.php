<?php $current = $this->router->fetch_class(); ?>

<div id="sidebar">
    <div class="sidebar-brand">ARMS-BMS</div>

    <nav class="sidebar-nav">
        <a href="<?= base_url('dashboard') ?>"
           class="sidebar-link <?= ($current === 'dashboard') ? 'active' : '' ?>">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>

        <a href="<?= base_url('users') ?>"
           class="sidebar-link <?= ($current === 'users') ? 'active' : '' ?>">
            <i class="fas fa-users"></i> Users
        </a>

        <a href="<?= base_url('myprofile') ?>"
           class="sidebar-link <?= ($current === 'profile') ? 'active' : '' ?>">
            <i class="fas fa-user-circle"></i> My Profile
        </a>
    </nav>

    <div class="sidebar-footer">
        <a href="<?= base_url('dashboard/logout') ?>" class="sidebar-link">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
</div>