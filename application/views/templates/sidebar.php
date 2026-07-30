<?php
$current = $this->router->fetch_class();

$itemMonitoringActive = in_array($current, ['items', 'itemized'], true);
$borrowingMonitoringActive = in_array(
    $current,
    ['borrowing', 'reservation', 'returns'],
    true
);
?>

<div id="sidebar">

    <!-- Sidebar Brand -->
    <div class="sidebar-header">
        <div class="sidebar-brand">
            <img
                src="<?= base_url('assets/images/logo.png') ?>"
                alt="ARMS-BMS Logo"
            >
        </div>
    </div>

    <!-- Sidebar Navigation -->
    <nav class="sidebar-nav">

        <div class="sidebar-section-label">
            Workspace
        </div>

        <!-- Dashboard -->
        <a
            href="<?= base_url('dashboard') ?>"
            class="sidebar-link <?= $current === 'dashboard' ? 'active' : '' ?>"
            data-title="Dashboard"
        >
            <i class="fas fa-tachometer-alt"></i>
            <span class="nav-label">Dashboard</span>
        </a>

        <!-- Users -->
        <a
            href="<?= base_url('users') ?>"
            class="sidebar-link <?= $current === 'users' ? 'active' : '' ?>"
            data-title="Users"
        >
            <i class="fas fa-users"></i>
            <span class="nav-label">Users</span>
        </a>

        <!-- Item Monitoring -->
        <div
            class="accordion sidebar-accordion"
            id="itemMonitoringAccordion"
        >
            <div class="card sidebar-accordion-item">

                <div class="card-header" id="itemMonitoringHeading">
                    <h2 class="mb-0">
                        <button
                            type="button"
                            class="btn btn-link btn-block text-left sidebar-link sidebar-accordion-toggle
                                <?= $itemMonitoringActive ? 'active' : 'collapsed' ?>"
                            data-toggle="collapse"
                            data-target="#itemMonitoringCollapse"
                            aria-expanded="<?= $itemMonitoringActive ? 'true' : 'false' ?>"
                            aria-controls="itemMonitoringCollapse"
                            data-title="Item Monitoring"
                        >
                            <i class="bi bi-box2-fill"></i>

                            <span class="nav-label">
                                Item Monitoring
                            </span>

                            <i class="fas fa-chevron-down chevron"></i>
                        </button>
                    </h2>
                </div>

                <div
                    id="itemMonitoringCollapse"
                    class="collapse <?= $itemMonitoringActive ? 'show' : '' ?>"
                    aria-labelledby="itemMonitoringHeading"
                    data-parent="#itemMonitoringAccordion"
                >
                    <div class="card-body sidebar-submenu">

                        <a
                            href="<?= base_url('items') ?>"
                            class="sidebar-link sidebar-sublink
                                <?= $current === 'items' ? 'active' : '' ?>"
                            data-title="Items"
                        >
                            <i class="bi bi-clipboard-check-fill"></i>
                            <span class="nav-label">Items</span>
                        </a>

                        <a
                            href="<?= base_url('itemized') ?>"
                            class="sidebar-link sidebar-sublink
                                <?= $current === 'itemized' ? 'active' : '' ?>"
                            data-title="Itemized"
                        >
                            <i class="fas fa-list-ul"></i>
                            <span class="nav-label">Itemized</span>
                        </a>

                    </div>
                </div>

            </div>
        </div>

        <!-- Borrowing Monitoring -->
        <div
            class="accordion sidebar-accordion"
            id="borrowingMonitoringAccordion"
        >
            <div class="card sidebar-accordion-item">

                <div class="card-header" id="borrowingMonitoringHeading">
                    <h2 class="mb-0">
                        <button
                            type="button"
                            class="btn btn-link btn-block text-left sidebar-link sidebar-accordion-toggle
                                <?= $borrowingMonitoringActive ? 'active' : 'collapsed' ?>"
                            data-toggle="collapse"
                            data-target="#borrowingMonitoringCollapse"
                            aria-expanded="<?= $borrowingMonitoringActive ? 'true' : 'false' ?>"
                            aria-controls="borrowingMonitoringCollapse"
                            data-title="Borrowing Monitoring"
                        >
                            <i class="bi bi-file-earmark-bar-graph"></i>

                            <span class="nav-label">
                                Borrowing Monitoring
                            </span>

                            <i class="fas fa-chevron-down chevron"></i>
                        </button>
                    </h2>
                </div>

                <div
                    id="borrowingMonitoringCollapse"
                    class="collapse <?= $borrowingMonitoringActive ? 'show' : '' ?>"
                    aria-labelledby="borrowingMonitoringHeading"
                    data-parent="#borrowingMonitoringAccordion"
                >
                    <div class="card-body sidebar-submenu">

                        <a
                            href="<?= base_url('borrowing') ?>"
                            class="sidebar-link sidebar-sublink
                                <?= $current === 'borrowing' ? 'active' : '' ?>"
                            data-title="Borrowing"
                        >
                            <i class="bi bi-box-arrow-up-right"></i>
                            <span class="nav-label">Borrowing</span>
                        </a>

                        <a
                            href="<?= base_url('reservation') ?>"
                            class="sidebar-link sidebar-sublink
                                <?= $current === 'reservation' ? 'active' : '' ?>"
                            data-title="Reservations"
                        >
                            <i class="bi bi-calendar-check"></i>
                            <span class="nav-label">Reservations</span>
                        </a>

                        <a
                            href="<?= base_url('returns') ?>"
                            class="sidebar-link sidebar-sublink
                                <?= $current === 'returns' ? 'active' : '' ?>"
                            data-title="Returns"
                        >
                            <i class="bi bi-arrow-return-left"></i>
                            <span class="nav-label">Returns</span>
                        </a>

                    </div>
                </div>

            </div>
        </div>

    </nav>

    <!-- Optional Help Card -->
    <div class="sidebar-help">
        <strong>
            <i class="fas fa-question-circle mr-1"></i>
            Need assistance?
        </strong>

        <small>
            Contact your system administrator.
        </small>
    </div>

    <!-- Sidebar Footer -->
    <div class="sidebar-footer">
        <div class="sidebar-footer-content">
            <i class="fas fa-shield-alt"></i>

            <span class="nav-label">
                ARMS-BMS 2026
            </span>
        </div>
    </div>

</div>