
<div class="home-btn d-none d-sm-block">
	<a href="/" class="text-dark"><i class="mdi mdi-home-variant h2"></i></a>
</div>
<div class="account-pages my-5 pt-sm-2">
	<div class="container">
		<div class="row">
			<div class="col-lg-12">
				<div class="text-center">
					<a href="/" class="mb-5 d-block auth-logo">
						<img src="assets/images/dotz_logo.png" alt="" height="200" class="logo logo-dark">
						<img src="assets/images/dotz_logo.png" alt="" height="22" class="logo logo-light">
					</a>
				</div>
			</div>
		</div>
		
        <?php if (session()->get('success')): ?>
          <div class="alert alert-success" role="alert">
            <?= session()->get('success') ?>
          </div>
        <?php endif; ?>
		<div class="row align-items-center justify-content-center">
			<div class="col-md-8 col-lg-6 col-xl-5">
				<div class="card">
					
				<?php if (isset($validation)): ?>
					<div class="col-12">
					<div class="alert alert-danger" role="alert">
						<?= $validation->listErrors() ?>
					</div>
					</div>
				<?php endif; ?>
					<div class="card-body p-4"> 
						<div class="text-center mt-2">
							<h5 class="text-primary"><?=$message?></h5>
						</div>
						<div class="p-2 mt-4">
							<form action="<?php echo base_url('') ?>" method="post" id="form-login">

								<div class="form-group">
									<label for="username"><?=lang('Login.auth')?></label>
									<input type="text" class="form-control" id="username" name="email" placeholder="Enter username">
								</div>
		
								<div class="form-group">
									<?php /* ?>
									<div class="float-right">
										<a href="auth-recoverpw" class="text-muted">Forgot password?</a>
									</div>
									<?php */ ?>
									<label for="userpassword"><?=lang('Login.pass')?></label>
									<input type="password" class="form-control" id="userpassword" name="password" placeholder="Enter password">
								</div>
								<?php /* ?>
								<div class="custom-control custom-checkbox">
									<input type="checkbox" class="custom-control-input" id="auth-remember-check">
									<label class="custom-control-label" for="auth-remember-check"><?=lang('Login.remember')?></label>
								</div>
								<?php */ ?>
								
								<div class="mt-3 text-right">
									<button class="btn btn-primary w-sm waves-effect waves-light" type="submit"><?=lang('Login.sign_in')?></button>
								</div>
							</form>
						</div>
	
					</div>
				</div>

			</div>
		</div>
		<!-- end row -->
	</div>
	<!-- end container -->
</div>
