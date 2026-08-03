<!-- Insert this partial inside the existing .topbar-right element. -->
<div class="dropdown topbar-quick-actions">
    <button class="topbar-action dropdown-toggle"
        type="button"
        id="quickActionsMenu"
        data-toggle="dropdown"
        aria-haspopup="true"
        aria-expanded="false"
        title="Quick Actions">
        <i class="fas fa-bolt"></i>
        <span>Quick Actions</span>
    </button>

    <div class="dropdown-menu dropdown-menu-right topbar-dropdown-menu quick-actions-menu"
        aria-labelledby="quickActionsMenu">
        <div class="topbar-dropdown-header">Quick Actions</div>

        <a class="dropdown-item topbar-dropdown-item" href="<?= base_url('items/create') ?>">
            <i class="fas fa-plus-circle"></i>
            <span>Add Item</span>
        </a>

        <a class="dropdown-item topbar-dropdown-item" href="<?= base_url('borrowing') ?>">
            <i class="fas fa-hand-holding"></i>
            <span>Borrow Item</span>
        </a>

        <a class="dropdown-item topbar-dropdown-item" href="<?= base_url('returns') ?>">
            <i class="fas fa-undo-alt"></i>
            <span>Return Item</span>
        </a>

        <a class="dropdown-item topbar-dropdown-item" href="<?= base_url('users') ?>">
            <i class="fas fa-users-cog"></i>
            <span>Manage Users</span>
        </a>

        <a class="dropdown-item topbar-dropdown-item" href="<?= base_url('reports') ?>">
            <i class="fas fa-chart-bar"></i>
            <span>Reports</span>
        </a>
    </div>
</div>
