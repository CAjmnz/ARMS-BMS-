<?php
$login_error  = (string) $this->session->flashdata('error');
$locked_until = (string) $this->session->flashdata('locked_until');
?>
<!doctype html>
<html lang="en">

<head>
	<title>Login Form</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<link rel="stylesheet" href="<?= base_url('assets/css/fonts.css') ?>">
	<link rel="stylesheet" href="<?= base_url('assets/css/font-awesome.min.css') ?>">
	<link rel="stylesheet" href="<?= base_url('assets/css/login.css') ?>">

</head>

<body>
	<section class="ftco-section">
		<div class="container">
			<div class="row justify-content-center">
			</div>
			<div class="row justify-content-center">
				<div class="col-md-12 col-lg-10" style="min-height: 700px;">
					<div class="wrap d-md-flex">
						<div class="img" style="background-image: url(<?= base_url('assets/images/bg-1.png') ?>);">
						</div>
						<div class="login-wrap p-4 p-md-5">
							<div class="d-flex">
								<div class="w-100">
									<h3 class="mb-4">Sign In</h3>
								</div>
							</div>
							<form action="<?= site_url('auth/login') ?>" method="POST" class="signin-form">
								<?php if ($this->config->item('csrf_protection')): ?>
									<input
										type="hidden"
										name="<?= html_escape($this->security->get_csrf_token_name()) ?>"
										value="<?= html_escape($this->security->get_csrf_hash()) ?>"
									>
								<?php endif; ?>
								<?php if ($login_error !== ''): ?>
									<div class="alert alert-danger" id="errorAlert">
										<?= html_escape($login_error) ?>
									</div>
								<?php endif; ?>

								<?php if ($locked_until !== ''): ?>
									<div class="alert alert-warning" id="lockAlert">
										<i class="fas fa-lock"></i>
										Account locked. Try again in <strong id="countdown">30</strong> second(s).
									</div>
									<script>
										var lockedUntil = new Date(<?= json_encode($locked_until) ?>).getTime();

										var countdown = setInterval(function() {
											var now = new Date().getTime();
											var remaining = Math.ceil((lockedUntil - now) / 1000);

											if (remaining <= 0) {
												clearInterval(countdown);
												document.getElementById('lockAlert').innerHTML =
													'<i class="fas fa-unlock"></i> Account unlocked! You may try again.';
												document.getElementById('lockAlert').className = 'alert alert-success';
												document.querySelector('.signin-form button[type="submit"]').disabled = false;
											} else {
												document.getElementById('countdown').innerText = remaining;
												document.querySelector('.signin-form button[type="submit"]').disabled = true;
											}
										}, 1000);
									</script>
								<?php endif; ?>
								<div class="form-group mb-3">
									<label class="label" for="loginCredential">Username or Employee ID</label>
									<input type="text" id="loginCredential" name="username" class="form-control" placeholder="Username or Employee ID" autocomplete="username" required autofocus>
								</div>
								<div class="form-group mb-3">
									<label class="label" for="loginPassword">Password</label>
									<input type="password" id="loginPassword" name="password" class="form-control" placeholder="Password" autocomplete="current-password" required>
								</div>
								<div class="form-group">
									<button type="submit" class="form-control btn btn-primary rounded submit px-3">Sign In</button>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

	<script src="<?= base_url('assets/js/jquery.min.js') ?>"></script>
	<script src="<?= base_url('assets/js/popper.js') ?>"></script>
	<script src="<?= base_url('assets/js/bootstrap.min.js') ?>"></script>
	<script src="<?= base_url('assets/js/login.main.js') ?>"></script>

</body>

</html>
