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
                <div style="width:100px; height:100px; border-radius:50%; background:var(--green-600); color:#fff; display:flex; align-items:center; justify-content:center; font-size:34px; font-weight:700; margin:0 auto 16px;">
                    <?= strtoupper(substr($user->username, 0, 1)) ?>
                </div>
                <h5 style="font-weight:700; margin-bottom:4px;"><?= htmlspecialchars($user->username) ?></h5>
                <div style="font-size:12px; color:var(--muted);">
                    Member since <?= date('M Y', strtotime($user->created_at)) ?>
                </div>
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