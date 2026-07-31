<?php $this->load->view('templates/head'); ?>
<?php $this->load->view('templates/sidebar'); ?>
<?php $this->load->view('templates/topbar'); ?>

<div id="main-content">

    <!-- Page Header -->
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <h4 style="margin:0; font-weight:700; color:#333;">Borrowing Management &rsaquo; Reservations</h4>
        <div style="display:flex; gap:8px;">
            <button class="btn btn-success btn-sm" id="btnAddReservation">
                <i class="fas fa-plus"></i> Reserve
            </button>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success"><?= $this->session->flashdata('success') ?></div>
    <?php endif; ?>
    <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger"><?= $this->session->flashdata('error') ?></div>
    <?php endif; ?>

    <!-- Filters -->
    <div style="background:#fff; border-radius:8px; padding:15px; margin-bottom:20px; border:1px solid #e3e6f0;">
        <form id="filterForm">
            <div class="row">
                <div class="col-md-3">
                    <label style="font-size:13px;">Status</label>
                    <select name="reservation_status" class="form-control form-control-sm">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="released">Released</option>
                        <option value="rejected">Rejected</option>
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

    <!-- Table -->
    <div style="background:#fff; border-radius:8px; padding:20px; border:1px solid #e3e6f0;">
        <table class="table table-bordered table-hover" id="reservationTable">
            <thead style="background:#f8f9fa;">
                <tr>
                    <th>#</th>
                    <th>Reservation's Id</th>
                    <th>Borrower's Id</th>
                    <th>Borrower's name</th>
                    <th>Position</th>
                    <th>Item Name</th>
                    <th>Category</th>
                    <th>Quantity</th>
                    <th>Reservation Date</th>
                    <th>Return Date</th>
                    <th>Purpose</th>
                    <th>Status</th>
                    <th>Reserved by</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

</div>

<!-- Add Reservation Modal -->
<div class="modal fade" id="reservationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">New Reservation</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <!-- Borrower -->
                <input type="hidden" id="res_borrower_employee_id">
                <input type="hidden" id="res_borrower_position">
                <input type="hidden" id="res_borrower_dept">
                <input type="hidden" id="res_borrower_photo">

                <div class="form-group" style="position:relative;">
                    <label>Borrower <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="reservationBorrowerSearch"
                        placeholder="Type a name/ID or scan barcode..." autocomplete="off">
                    <div id="reservationBorrowerResults" style="
        display:none; position:absolute; top:100%; left:0; right:0; z-index:1060;
        background:#fff; border:1px solid #ddd; border-radius:4px;
        max-height:220px; overflow-y:auto; box-shadow:0 4px 10px rgba(0,0,0,0.1);
    "></div>
                </div>

                <!-- Item -->
                <div class="form-group">
                    <label>Item</label>
                    <select class="form-control" id="res_item_id">
                        <option value="">-- Select Item --</option>
                        <?php foreach ($items as $item): ?>
                            <?php if ($item->available_quantity > 0): ?>
                                <option value="<?= $item->id ?>" data-available="<?= $item->available_quantity ?>">
                                    <?= htmlspecialchars($item->item_name) ?>
                                </option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Units — populated via AJAX -->
                <div class="form-group">
                    <label>Available Units</label>
                    <div id="resUnitCheckboxList" style="max-height:150px; overflow-y:auto; border:1px solid #ddd; border-radius:4px; padding:8px;">
                        <span class="text-muted">Select an item first.</span>
                    </div>
                    <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="btnResAddToStagingList">
                        <i class="fas fa-plus"></i> Add to List
                    </button>
                </div>

                <div class="form-group">
                    <label>Items to Reserve <span class="text-danger">*</span></label>
                    <div id="resStagedItemsList" style="border:1px solid #ddd; border-radius:4px; padding:8px; min-height:50px;">
                        <span class="text-muted" id="resStagedEmptyMsg">No items added yet.</span>
                    </div>
                </div>
                <!-- Reservation (pick up) Date -->
                <div class="form-group">
                    <label>Pick up Date <span class="text-danger">*</span></label>
                    <input type="datetime-local" class="form-control" id="res_reservation_date">
                </div>

                <!-- Return / Due Date -->
                <div class="form-group">
                    <label>Return Date <span class="text-danger">*</span></label>
                    <input type="datetime-local" class="form-control" id="res_due_date">
                </div>

                <!-- Purpose -->
                <div class="form-group">
                    <label>Purpose</label>
                    <input type="text" class="form-control" id="res_purpose" placeholder="e.g. Thesis defense">
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="btnSaveReservation">Reserve</button>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('templates/footer'); ?>