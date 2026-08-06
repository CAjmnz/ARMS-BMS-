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
                    <label style="font-size:13px;">Filter by date</label>
                    <select name="date_type" class="form-control form-control-sm">
                        <option value="return_date">Return Date</option>
                        <option value="borrowed_date">Borrowed Date</option>
                        <option value="due_date">Due Date</option>
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
                    <th>Position</th>
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

    <!-- Details Modal -->
    <div class="modal fade" id="detailsModal" tabindex="-1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Borrowing & Return Details</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <h6 class="text-muted" style="border-bottom:1px solid #eee; padding-bottom:5px;">Borrower Information</h6>
                    <div class="row mb-3">
                        <div class="col-md-6"><strong>Name:</strong> <span id="d_borrower_name">-</span></div>
                        <div class="col-md-3"><strong>ID No:</strong> <span id="d_borrower_employee_id">-</span></div>
                        <div class="col-md-3"><strong>Type:</strong> <span id="d_borrower_position">-</span></div>
                    </div>

                    <h6 class="text-muted" style="border-bottom:1px solid #eee; padding-bottom:5px;">Item Information</h6>
                    <div class="row mb-3">
                        <div class="col-md-6"><strong>Item:</strong> <span id="d_item_name">-</span></div>
                        <div class="col-md-3"><strong>Category:</strong> <span id="d_category">-</span></div>
                        <div class="col-md-3"><strong>Unit No:</strong> <span id="d_unit_no">-</span></div>
                        <div class="col-md-6"><strong>Brand/Model:</strong> <span id="d_brand_model">-</span></div>
                        <div class="col-md-6"><strong>Serial No:</strong> <span id="d_serial">-</span></div>
                    </div>


                    <h6 class="text-muted" style="border-bottom:1px solid #eee; padding-bottom:5px;">Borrowing Details</h6>
                    <div class="row mb-3">
                        <div class="col-md-6"><strong>Transaction:</strong> <span id="d_txn">-</span></div>
                        <div class="col-md-6"><strong>Purpose:</strong> <span id="d_purpose">-</span></div>
                        <div class="col-md-4"><strong>Date Requested:</strong> <span id="d_date_requested">-</span></div>
                        <div class="col-md-4"><strong>Date Released:</strong> <span id="d_date_released">-</span></div>
                        <div class="col-md-4"><strong>Due Date:</strong> <span id="d_due_date">-</span></div>
                        <div class="col-md-6"><strong>Released By:</strong> <span id="d_released_by">-</span></div>
                        <div class="col-md-6"><strong>Condition Before:</strong> <span id="d_condition_before">-</span></div>
                    </div>

                    <h6 class="text-muted" style="border-bottom:1px solid #eee; padding-bottom:5px;">Return Details</h6>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>Return Status:</strong> <span id="d_item_status">-</span></div>
                        <div class="col-md-4"><strong>Date Returned:</strong> <span id="d_date_returned">-</span></div>
                        <div class="col-md-4"><strong>Condition After:</strong> <span id="d_condition_after">-</span></div>
                        <div class="col-md-6"><strong>Received By:</strong> <span id="d_received_by">-</span></div>
                        <div class="col-md-12"><strong>Remarks:</strong> <span id="d_remarks">-</span></div>
                    </div>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <?php $this->load->view('templates/footer'); ?>