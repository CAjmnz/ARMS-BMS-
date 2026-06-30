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
        <a href="<?= base_url('items') ?>"
           class="sidebar-link <?= ($current === 'items') ? 'active' : '' ?>">
           <i class="bi bi-clipboard-check-fill"></i>Items
        </a>
        <a href="<?= base_url('itemized') ?>"
           class="sidebar-link <?= ($current === 'itemized') ? 'active' : '' ?>">
           <i class="bi bi-clipboard-data-fill"></i>Item-Itemized
        </a>
        

    </nav>

    <div class="sidebar-footer">
        <label class="sidebar-link"> @ARMS-BMS 2026 </label> 
            
        
    </div>
</div>