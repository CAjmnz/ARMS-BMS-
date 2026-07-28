<?php $this->load->view('templates/head'); ?>
<?php $this->load->view('templates/sidebar'); ?>
<?php $this->load->view('templates/topbar'); ?>

<div id="main-content">

    <!-- Page Header -->
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <h4 style="margin:0; font-weight:700; color:#333;">Employee Management</h4>
        <button class="btn btn-success btn-sm" id="btnAddUser">
            <i class="fas fa-plus"></i> Add User
        </button>
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
                    <label style="font-size:13px;">Role</label>
                    <select name="role" class="form-control form-control-sm">
                        <option value="">All Roles</option>
                        <option value="Admin">Admin</option>
                        <option value="User">User</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label style="font-size:13px;">Account Status</label>
                    <select name="account_status" class="form-control form-control-sm">
                        <option value="">All Status</option>
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
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
        <table class="table table-bordered table-hover" id="usersTable" width="100%">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="18%">Employee</th>
                    <th width="10%">Employee ID</th>
                    <th width="12%">Department</th>
                    <th width="8%">Status</th>
                    <th width="7%">B.U.</th>
                    <th width="9%">Type</th>
                    <th width="7%">Role</th>
                    <th width="10%">Account Status</th>
                    <th width="6%">Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
    <!-- Add/Edit Modal -->
    <div class="modal fade" id="userModal" tabindex="-1">
        <div class="modal-dialog modal-custom modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Add System User</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="system_user_id">
                    <input type="hidden" id="employee_photo">

                    <!-- Live Search -->
                    <div class="form-group" style="position:relative;">
                        <label>Search Employee (name, ID, or scan barcode)</label>
                        <input type="text" class="form-control" id="modalEmployeeSearch"
                            placeholder="Type a name/ID or scan barcode..." autocomplete="off">
                        <div id="employeeSearchResults" style="
                        display:none;
                        position:absolute;
                        top:100%;
                        left:0;
                        right:0;
                        z-index:1060;
                        background:#fff;
                        border:1px solid #ddd;
                        border-radius:4px;
                        max-height:220px;
                        overflow-y:auto;
                        box-shadow:0 4px 10px rgba(0,0,0,0.1);
                    "></div>
                    </div>

                    <hr>

                    <!-- Photo Preview -->
                    <div class="form-row">
                        <div class="col-12 text-center mb-3">
                            <img id="employee_photo_preview" src="" alt="Employee Photo"
                                style="width:90px; height:90px; object-fit:cover; border-radius:50%; border:2px solid #e3e6f0; display:none;">
                            <div id="employee_photo_placeholder" style="width:90px; height:90px; border-radius:50%; background:#f0f0f0; display:flex; align-items:center; justify-content:center; margin:0 auto; color:#aaa; font-size:11px;">
                                No Photo
                            </div>
                        </div>
                    </div>

                    <!-- Employee Details (auto-filled, read-only) -->
                    <div class="form-row">
                        <div class="form-group col-6 mb-2">
                            <label>Employee ID / Default Username</label>
                            <input type="text" class="form-control" id="employee_id"  readonly>
                        </div>
                        <div class="form-group col-6 mb-2">
                            <div class="field-wrap">
                                <label for="password">Password</label>
                                <input type="text"
                                    class="form-control"
                                    id="password"
                                    value="bms-2026"
                                    readonly>
                                <span class="field-error-icon">&#9888;<span class="error-tooltip"></span></span>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-6 mb-2">
                        <label>Employee Name</label>
                        <input type="text" class="form-control" id="employee_name" readonly>
                        </div>
                        <div class="form-group col-6 mb-2">
                        <label>Position</label>
                        <input type="text" class="form-control" id="employee_position" readonly>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-6 mb-2">
                        <label>Department</label>
                        <input type="text" class="form-control" id="employee_dept" readonly>
                        </div>
                        <div class="form-group col-6 mb-2">
                        <label>Company</label>
                        <input type="text" class="form-control" id="employee_company" readonly>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-6 mb-2">
                           <label>Business Unit</label>
                            <input type="text" class="form-control" id="employee_bunit" readonly>
                        </div>
                        <div class="form-group col-6 mb-2">
                            <label>Employee Status</label>
                            <input type="text" class="form-control" id="employee_status" readonly>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-6 mb-2">
                            <label>Role</label>
                            <select class="form-control" id="role">
                                <option value="User">User</option>
                                <option value="Admin">Admin</option>
                            </select>
                        </div>
                        <div class="form-group col-6 mb-2">
                            <label>Account Status</label>
                            <select class="form-control" id="account_status">
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="form-group">
                        <label>Password Changed</label>
                        <input type="text" class="form-control" id="edit_password_change_count" readonly>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="btnSaveUser" disabled>Save</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit User</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit_user_id">
                    <div class="form-group">
                        <label>Employee Name</label>
                        <input type="text" class="form-control" id="edit_employee_name" readonly>
                    </div>
                    <div class="form-group">
                        <label>Role</label>
                        <select class="form-control" id="edit_role">
                            <option value="User">User</option>
                            <option value="Admin">Admin</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Account Status</label>
                        <select class="form-control" id="edit_account_status">
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="btnUpdateUser">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

</div>
<?php $this->load->view('templates/footer'); ?>