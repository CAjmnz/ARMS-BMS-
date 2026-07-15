<?php $this->load->view('templates/head'); ?>
<?php $this->load->view('templates/sidebar'); ?>
<?php $this->load->view('templates/topbar'); ?>

<div id="main-content">

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <h4 style="margin:0; font-weight:700; color:#333;">Borrowing Management &rsaquo; Returns</h4>
    </div>

    <div style="background:#fff; border-radius:8px; padding:15px; margin-bottom:20px; border:1px solid #e3e6f0;">
        <form id="filterForm">
            <div class="row">
                <div class="col-md-3">
                    <label style="font-size:13px;">Return Status</label>
                    <select name="item_status" class="form-control form-control-sm">
                        <option value="">All Status</option>
                        <option value="returned">Returned</option>
                        <option value="damaged">Damaged</option>
                        <option value="lost">Lost</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label style="font-size:13px;">Date From</label>
                    <input type="date" name="date_from" class="form-control form-control-sm">
                </div>
                <div class="col-md-2">
                    <label style="font-size:13px;">Date To</label>
                    <input type="date" name="date_to" class="form-control form-control-sm">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-sm mr-2">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <button type="button" id="btnReset" class="btn btn-secondary btn-sm">
                        <i class="fas fa-times"></i> Reset
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div style="background:#fff; border-radius:8px; padding:20px; border:1px solid #e3e6f0;">
        <table class="table table-bordered table-hover" id="returnsTable">
            <thead style="background:#f8f9fa;">
                <tr>
                    <th>#</th>
                    <th>Transaction Number</th>
                    <th>Borrower's Id</th>
                    <th>Borrower's Name</th>
                    <th>Item Name</th>
                    <th>Category</th>
                    <th>Quantity</th>
                    <th>Condition Upon Returning</th>
                    <th>Borrowed Date</th>
                    <th>Due date</th>
                    <th>Actual Return date</th>
                    <th>Days Late</th>
                    <th>Return status</th>
                    <th>Received by</th>
                    <th>Remarks</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

</div>

<?php $this->load->view('templates/footer'); ?>