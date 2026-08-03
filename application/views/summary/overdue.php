<?php $this->load->view('templates/head'); ?>
<?php $this->load->view('templates/sidebar'); ?>
<?php $this->load->view('templates/topbar'); ?>

<div id="main-content">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <h4 style="margin:0; font-weight:700; color:#333;">Summary &rsaquo; Overdue</h4>
        <a href="<?= base_url('dashboard') ?>" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <div style="background:#fff; border-radius:8px; padding:20px; border:1px solid #e3e6f0;">
        <table class="table table-bordered table-hover" id="summaryOverdueTable">
            <thead style="background:#f8f9fa;">
                <tr>
                    <th>#</th>
                    <th>Borrower's Id</th>
                    <th>Borrower's name</th>
                    <th>Position</th>
                    <th>Item Name</th>
                    <th>Category</th>
                    <th>Quantity</th>
                    <th>Condition Before Borrowing</th>
                    <th>Borrowed Date</th>
                    <th>Due date</th>
                    <th>Borrowing status</th>
                    <th>Released by</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<?php $this->load->view('templates/footer'); ?>