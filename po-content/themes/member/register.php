<?=$this->layout('index');?>

<div class="container-fluid">
	<div class="container login-page">
		<div class="row">
			<div class="col-md-12">
				<div class="row">
					<div class="col-md-12 text-center">
						<img src="<?=BASE_URL;?>/<?=DIR_INC;?>/images/logo.png" class="logo" width="100" />
					</div>
				</div>
				<div class="text-center"><h4 class="text-uppercase"><?=$this->e($front_member_register);?></h4></div>
				<?=htmlspecialchars_decode($this->e($alertmsg));?>
				<form role="form" id="register-form" method="post" action="<?=BASE_URL;?>/member/register" autocomplete="off">
					<div class="form-group">
						<label for="username"><?=$this->e($front_member_username);?></label>
						<input type="text" class="form-control login-input" id="username" name="username" value="<?=(isset($_POST['username']) ? $_POST['username'] : '');?>" />
					</div>
					<div class="form-group">
						<label for="email"><?=$this->e($front_member_email);?></label>
						<input type="text" class="form-control login-input" id="email" name="email" value="<?=(isset($_POST['email']) ? $_POST['email'] : '');?>" />
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="password"><?=$this->e($front_member_password);?></label>
								<input type="password" class="form-control login-input" id="password" name="password" />
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="repassword"><?=$this->e($front_member_repassword);?></label>
								<input type="password" class="form-control login-input" id="repassword" name="repassword" />
							</div>
						</div>
					</div>
					<button type="submit" class="btn btn-success btn-full"><i class="fa fa-user"></i>&nbsp;&nbsp;<?=$this->e($front_member_register);?></button>
				</form>
				<div class="login-or">
					<hr class="hr-or"><span class="span-or"><?=$this->e($front_member_or);?></span>
				</div>
				<div class="row">
					<div class="col-md-12">
						<a href="<?=BASE_URL;?>/member/login" class="btn no-bg btn-block"><i class="fa fa-sign-in"></i>&nbsp;&nbsp;<?=$this->e($front_member_login);?></a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>