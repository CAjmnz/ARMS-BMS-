<?php $this->load->view('templates/head'); ?>
<?php $this->load->view('templates/sidebar'); ?>
<?php $this->load->view('templates/topbar'); ?>

<div id="main-content">

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <h4 style="margin:0; font-weight:700; color:var(--ink);">My Profile</h4>
    </div>

    <div class="row">
        <!-- Left — Profile Card -->
        <div class="col-md-4 mb-3">
    <div style="background:#fff; border-radius:14px; border:1px solid var(--line); padding:30px; text-align:center;">
        <?php
            $photo_url = (!empty($employee) && !empty($employee->employee_photo))
                ? base_url('user/photo_proxy?path=' . urlencode($employee->employee_photo))
                : null;
        ?>
        <?php if ($photo_url): ?>
            <img src="<?= $photo_url ?>" alt="Profile Photo"
                 style="width:100px; height:100px; border-radius:50%; object-fit:cover; border:3px solid var(--green-100); margin:0 auto 16px; display:block;"
                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
            <div style="display:none; width:100px; height:100px; border-radius:50%; background:var(--green-600); color:#fff; align-items:center; justify-content:center; font-size:34px; font-weight:700; margin:0 auto 16px;">
                <?= strtoupper(substr($user->username, 0, 1)) ?>
            </div>
        <?php else: ?>
            <div style="width:100px; height:100px; border-radius:50%; background:var(--green-600); color:#fff; display:flex; align-items:center; justify-content:center; font-size:34px; font-weight:700; margin:0 auto 16px;">
                <?= strtoupper(substr($user->username, 0, 1)) ?>
            </div>
        <?php endif; ?>

        <h5 style="font-weight:700; margin-bottom:4px;">
            <?= htmlspecialchars(!empty($employee) ? $employee->employee_name : $user->username) ?>
        </h5>
        <div style="font-size:12px; color:var(--muted); margin-bottom:12px;">
            Member since <?= date('M Y', strtotime($user->created_at)) ?>
        </div>

        <?php if (!empty($employee)): ?>
            <div style="text-align:left; border-top:1px solid var(--line); padding-top:14px; margin-top:6px;">
                <div style="margin-bottom:10px;">
                    <div style="font-size:11px; color:var(--muted); font-weight:700; text-transform:uppercase;">Employee ID</div>
                    <div style="font-size:13px;"><?= htmlspecialchars($employee->employee_id) ?></div>
                </div>
                <div style="margin-bottom:10px;">
                    <div style="font-size:11px; color:var(--muted); font-weight:700; text-transform:uppercase;">Position</div>
                    <div style="font-size:13px;"><?= htmlspecialchars($employee->employee_position ?? '-') ?></div>
                </div>
                <div style="margin-bottom:10px;">
                    <div style="font-size:11px; color:var(--muted); font-weight:700; text-transform:uppercase;">Department</div>
                    <div style="font-size:13px;"><?= htmlspecialchars($employee->employee_dept ?? '-') ?></div>
                </div>
                <div style="margin-bottom:10px;">
                    <div style="font-size:11px; color:var(--muted); font-weight:700; text-transform:uppercase;">Company</div>
                    <div style="font-size:13px;"><?= htmlspecialchars($employee->employee_company ?? '-') ?></div>
                </div>
                <div style="margin-bottom:10px;">
                    <div style="font-size:11px; color:var(--muted); font-weight:700; text-transform:uppercase;">Business Unit</div>
                    <div style="font-size:13px;"><?= htmlspecialchars($employee->employee_bunit ?? '-') ?></div>
                </div>
                <div>
                    <div style="font-size:11px; color:var(--muted); font-weight:700; text-transform:uppercase;">Role</div>
                    <div style="font-size:13px;">
                        <span class="badge <?= $employee->role === 'Admin' ? 'badge-danger' : 'badge-secondary' ?>">
                            <?= htmlspecialchars($employee->role) ?>
                        </span>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div style="font-size:12px; color:var(--muted); border-top:1px solid var(--line); padding-top:14px; margin-top:6px;">
                No linked employee record found for this account.
            </div>
        <?php endif; ?>
    </div>
</div>

        <!-- Right — Editable Sections -->
        <div class="col-md-8 mb-3">

            <!-- Username Section -->
            <div style="background:#fff; border-radius:14px; border:1px solid var(--line); padding:24px; margin-bottom:20px;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                    <h6 style="font-weight:700; margin:0;">Account Information</h6>
                </div>
                <div class="form-group">
                    <label style="font-size:12px; color:var(--muted); font-weight:700; text-transform:uppercase;">Username</label>
                    <div style="display:flex; gap:10px;">
                        <input type="text" class="form-control" id="profile_username" value="<?= htmlspecialchars($user->username) ?>">
                        <button class="btn btn-success" id="btnSaveUsername" style="white-space:nowrap;">
                            <i class="fas fa-check"></i> Save
                        </button>
                    </div>
                </div>
            </div>

            <!-- Password Section -->
            <div style="background:#fff; border-radius:14px; border:1px solid var(--line); padding:24px;">
                <h6 style="font-weight:700; margin-bottom:16px;">Change Password</h6>

                <div class="form-group">
                    <label style="font-size:12px; color:var(--muted); font-weight:700; text-transform:uppercase;">Current Password</label>
                    <input type="password" class="form-control" id="current_password">
                </div>
                <div class="form-row">
                    <div class="form-group col-6">
                        <label style="font-size:12px; color:var(--muted); font-weight:700; text-transform:uppercase;">New Password</label>
                        <input type="password" class="form-control" id="new_password">
                    </div>
                    <div class="form-group col-6">
                        <label style="font-size:12px; color:var(--muted); font-weight:700; text-transform:uppercase;">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirm_password">
                    </div>
                </div>
                <button class="btn btn-success" id="btnSavePassword">
                    <i class="fas fa-key"></i> Update Password
                </button>
            </div>

        </div>
    </div>

</div>

<?php $this->load->view('templates/footer'); ?>