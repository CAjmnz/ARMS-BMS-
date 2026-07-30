<?php $this->load->view('templates/head'); ?>
<?php $this->load->view('templates/sidebar'); ?>
<?php $this->load->view('templates/topbar'); ?>

<div id="main-content">

    <div class="container-fluid">

<!-- Page Header -->
<div class="page-header">
    <div>
        <h4>
            <i class="fas fa-info-circle mr-2"></i>
            About A.R.M.S
        </h4>
        <small class="text-muted">
            Borrower's Monitoring System
        </small>
    </div>
</div>

<!-- Hero / System Introduction -->
<div class="card-arms about-hero mb-4">
    <div class="row align-items-center">

        <div class="col-md-8">
            <div class="about-title">
                <span class="about-badge">
                    A.R.M.S
                </span>

                <h2>
                    Borrower's Monitoring System
                </h2>

                <p>
                    A centralized system designed to help manage,
                    monitor, and organize item records and borrowing
                    activities efficiently.
                </p>

                <div class="about-version">
                    <i class="fas fa-code-branch mr-1"></i>
                    Version 1.0
                </div>
            </div>
        </div>

        <div class="col-md-4 text-center">
            <div class="about-logo-wrapper">
                <img
                    src="<?= base_url('assets/images/logo.png') ?>"
                    alt="A.R.M.S Logo"
                    class="about-logo"
                >
            </div>
        </div>

    </div>
</div>


<!-- System Overview -->
<div class="row">

    <div class="col-lg-8 mb-4">

        <div class="card-arms h-100">

            <div class="about-section-title">
                <i class="fas fa-layer-group"></i>
                <h5>System Overview</h5>
            </div>

            <p>
                A.R.M.S (Alturas Record Management System) is a
                web-based record management solution designed to
                provide a centralized and organized way of managing
                organizational assets and borrowing activities.
            </p>

            <p>
                The system allows authorized users to manage item
                records, monitor item availability, track borrowing
                transactions, and monitor returned items while
                maintaining organized records.
            </p>

            <p class="mb-0">
                By centralizing these processes, A.R.M.S helps reduce
                manual record keeping, improve data organization,
                and make asset monitoring more efficient.
            </p>

        </div>

    </div>


    <!-- Purpose -->
    <div class="col-lg-4 mb-4">

        <div class="card-arms h-100 about-purpose-card">

            <div class="about-section-title">
                <i class="fas fa-bullseye"></i>
                <h5>Purpose</h5>
            </div>

            <p>
                To provide a reliable and organized platform for
                monitoring assets and borrowing activities.
            </p>

            <ul class="about-check-list">
                <li>
                    <i class="fas fa-check-circle"></i>
                    Organized records
                </li>

                <li>
                    <i class="fas fa-check-circle"></i>
                    Efficient monitoring
                </li>

                <li>
                    <i class="fas fa-check-circle"></i>
                    Centralized information
                </li>

                <li>
                    <i class="fas fa-check-circle"></i>
                    Improved accountability
                </li>
            </ul>

        </div>

    </div>

</div>


<!-- System Features -->
<div class="card-arms mb-4">

    <div class="about-section-title mb-4">
        <i class="fas fa-cubes"></i>
        <h5>System Features</h5>
    </div>

    <div class="row">

        <!-- Users -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-users"></i>
                </div>

                <div>
                    <h6>User Management</h6>
                    <p>
                        Manage system users and their access
                        to the system.
                    </p>
                </div>
            </div>
        </div>


        <!-- Items -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-boxes"></i>
                </div>

                <div>
                    <h6>Item Monitoring</h6>
                    <p>
                        Maintain and monitor available
                        organizational items.
                    </p>
                </div>
            </div>
        </div>


        <!-- Itemized -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>

                <div>
                    <h6>Itemized Records</h6>
                    <p>
                        Manage detailed records for individual
                        items.
                    </p>
                </div>
            </div>
        </div>


        <!-- Borrowing -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-hand-holding"></i>
                </div>

                <div>
                    <h6>Borrowing Monitoring</h6>
                    <p>
                        Track borrowed items and borrowing
                        transactions.
                    </p>
                </div>
            </div>
        </div>


        <!-- Returning -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-undo-alt"></i>
                </div>

                <div>
                    <h6>Returning Monitoring</h6>
                    <p>
                        Monitor returned items and their
                        corresponding status.
                    </p>
                </div>
            </div>
        </div>


        <!-- Records -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-database"></i>
                </div>

                <div>
                    <h6>Record Management</h6>
                    <p>
                        Keep asset and transaction information
                        organized and accessible.
                    </p>
                </div>
            </div>
        </div>

    </div>

</div>


<!-- Technology -->
<div class="row">

    <div class="col-md-6 mb-4">

        <div class="card-arms h-100">

            <div class="about-section-title">
                <i class="fas fa-cogs"></i>
                <h5>Technology</h5>
            </div>

            <div class="technology-list">

                <div class="technology-item">
                    <span>
                        <i class="fab fa-php"></i>
                        Backend Framework
                    </span>

                    <strong>CodeIgniter 3</strong>
                </div>

                <div class="technology-item">
                    <span>
                        <i class="fab fa-bootstrap"></i>
                        UI Framework
                    </span>

                    <strong>Bootstrap 4.6</strong>
                </div>

                <div class="technology-item">
                    <span>
                        <i class="fab fa-js-square"></i>
                        JavaScript Library
                    </span>

                    <strong>jQuery 3.6</strong>
                </div>

                <div class="technology-item">
                    <span>
                        <i class="fas fa-database"></i>
                        Database
                    </span>

                    <strong>MySQL</strong>
                </div>

            </div>

        </div>

    </div>


    <!-- Version -->
    <div class="col-md-6 mb-4">

        <div class="card-arms h-100 about-system-card">

            <div class="about-system-icon">
                <i class="fas fa-server"></i>
            </div>

            <h5>
                A.R.M.S
            </h5>

            <p>
                Borrower's Monitoring System
            </p>

            <div class="system-divider"></div>

            <span class="system-version">
                Version 1.0
            </span>

            <small>
                © 2026 A.R.M.S. All rights reserved.
            </small>

        </div>

    </div>

</div>

</div>

</div>

<?php $this->load->view('templates/footer'); ?>