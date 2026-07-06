<?php $current = $this->router->fetch_class(); ?>

<div id="sidebar">
    <div class="sidebar-header">
        <div class="img" style="background-image: url(<?= base_url('assets/images/bg-1.png') ?>);">
            <div class="sidebar-brand">
                <img src="<?= base_url('assets/images/logo.png') ?>" alt="Logo">
            </div>
        </div>
    </div>
    <nav class="sidebar-nav">
        <a href="<?= base_url('dashboard') ?>"
            class="sidebar-link <?= ($current === 'dashboard') ? 'active' : '' ?>">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>

        <a href="<?= base_url('users') ?>"
            class="sidebar-link <?= ($current === 'users') ? 'active' : '' ?>">
            <i class="fas fa-users"></i> Users
        </a>
        <a href="<?= base_url('items') ?>"
            class="sidebar-link <?= ($current === 'items') ? 'active' : '' ?>">
            <i class="bi bi-clipboard-check-fill"></i>Items
        </a>
        <a href="<?= base_url('itemized') ?>"
            class="sidebar-link <?= ($current === 'itemized') ? 'active' : '' ?>">
            <i class="fas fa-list-ul"></i> Itemized
            
        </a>


    </nav>

    <div class="sidebar-footer">
        <label class="sidebar-link"> @ARMS-BMS 2026 </label>


    </div>
</div>