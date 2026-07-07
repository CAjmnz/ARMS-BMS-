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
        <div class="accordion" id="accordionExample">
            <div class="card">
                <div class="card-header" id="headingOne">
                    <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed sidebar-link" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                        <i class="bi bi-box2-fill"></i> Item-Monitoring
                        </button>
                    </h2>
                </div>
                <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample">
                    <div class="card-body">
                        <a href="<?= base_url('items') ?>"
                            class="sidebar-link <?= ($current === 'items') ? 'active' : '' ?>">
                            <i class="bi bi-clipboard-check-fill"></i>Items
                        </a>

                        <a href="<?= base_url('itemized') ?>"
                            class="sidebar-link <?= ($current === 'itemized') ? 'active' : '' ?>">
                            <i class="fas fa-list-ul"></i> Itemized

                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        </a>
        <div class="accordion" id="accordionExample">
            <div class="card">
                <div class="card-header" id="headingThree">
                    <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed sidebar-link" type="button" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            <i class="bi bi-file-earmark-bar-graph"></i>Borrowing-Monitoring
                        </button>
                    </h2>
                </div>
                <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordionExample">
                    <div class="card-body">
                        <a href="<?= base_url('borrowing') ?>"
                            class="sidebar-link <?= ($current === 'borrowing') ? 'active' : '' ?>">
                            <i class="bi bi-bootstrap"></i></i>borrowing
                        </a>

                        <a href="<?= base_url('itemized') ?>"
                            class="sidebar-link <?= ($current === 'itemized') ? 'active' : '' ?>">
                            <i class="bi bi-arrow-return-left"></i> returning

                        </a>
                    </div>
                </div>
            </div>
        </div>

    </nav>

    <div class="sidebar-footer">
        <label class="sidebar-link"> @ARMS-BMS 2026 </label>


    </div>
</div>