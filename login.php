<?php include_once("inc/head.php"); ?>

<?php
//login function
if( isset($_POST['login']) )
{
	$email = $_POST['login_email'];
	$password = md5($_POST['login_password']);
	// push errors into this array
	$errors = array();

	if( empty($email) || empty($password) )
	{
		$errors[] = "Lütfen email ve şifrenizi giriniz";
	}

	if(!empty($errors))
	{
		foreach ($errors as $error) {
			$result .= $error;
		}
	}
	else
	{

		$query = $db->prepare("SELECT id, email, password FROM gwp_users WHERE email = :email AND password = :password");
		$query->bindValue(':email', $email);
		$query->bindValue(':password', $password);
		$query->execute();
		if( $query->rowCount() == 1 )
		{
			$obj = $query->fetch();
			setcookie("user_id", $obj->id, time()+3600*24);
			header("location: ./");
		}
		else
		{
			$result .= "E-posta adresiniz veya şifreniz hatalı";
		}

	}
}
?>

<body class="loginPage">

<?php include_once("inc/header.php"); ?>

<div class="login_bg_wrapper"></div>

<div class="container">

	<div class="row marginTop20">

		<div class="col-md-7 hidden-sm hidden-xs signResults">
<?php /*
			<p>Helperdoc  doktorlar arasında etkin iletişim sağlayan yeni nesil sosyal ağdır.</p>
			<p class="marginTop30">Doktorlara kendi aralarında güvenilir bilgi paylaşımı,<br />bu bilgileri depolama ve güvenilir bir şekilde<br />geleceğe taşıma konusunda yeni çözümler sunmaktadır.</p>
			<p class="marginTop30">HELPERDOC sitesine ücretsiz üye olabilir, meslektaşlarınızla anlık paylaşımlarda bulunabilir ve dilediğiniz bilgilieri arşivinizde saklayabilirsiniz.</p>
*/ ?>


		</div><!-- /.col-md-7 -->

		<div class="col-md-5 col-sm-12 col-xs-12 signUpPanel">
		<form method="post" action="">
			<div class="row">
				<div class="col-md-12">
					<h3>Üye Olun &amp; Aramıza Katılın</h3>
					<p class="hidden-sm hidden-xs">Ücretsiz üyelik için formu doldurun</p>
				</div>
			</div>

			<?php if( isset( $signup_result ) ) { ?>
			<div class="row">
				<div class="col-md-12">
					<div class="alert alert-danger" role="alert">
						<button type="button" class="close" data-dismiss="alert" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
						<span class="sr-only">Error:</span>
						<?php echo $signup_result; ?>
					</div>			
				</div>
			</div>
			<?php } ?>

			<div class="row marginTop20">
				<div class="form-group">
					<div class="col-md-6 col-sm-6 col-xs-6">
						<input type="text" class="form-control input-lg" id="nameSignup" name="firstname" placeholder="Adınızı Girin" value="<?php echo @$_POST['firstname']; ?>">
					</div>
					<div class="col-md-6 col-sm-6 col-xs-6">
						<input type="text" class="form-control input-lg" id="nameSignup" name="lastname" placeholder="Soyadınızı girin" value="<?php echo @$_POST['lastname']; ?>">
					</div>
				</div>
			</div>

            <div class="row marginTop20">
                <div class="form-group">
                    <div class="col-md-6 col-sm-6 col-xs-6">
                        <input type="text" class="form-control input-lg" name="title" placeholder="Ünvan Giriniz" value="<?php echo @$_POST['title']; ?>">
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-6">
                        <input type="text" class="form-control input-lg" name="branch" placeholder="Branş Giriniz" value="<?php echo @$_POST['branch']; ?>">
                    </div>
                </div>
            </div>

            <div class="row marginTop20">
                <div class="form-group">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <input type="text" class="form-control input-lg" name="company" placeholder="Çalıştığınız Kurum" value="<?php echo @$_POST['company']; ?>">
                    </div>
                </div>
            </div>

			<div class="row marginTop20">
				<div class="form-group">
					<div class="col-md-12 col-sm-12 col-xs-12">
						<input type="email" class="form-control input-lg" id="emailSignup" name="email" placeholder="E-posta adresinizi yazın" value="<?php echo @ $_POST['email']; ?>">
					</div>
				</div>
			</div>


			<div class="row marginTop20">
				<div class="form-group">
					<div class="col-md-12 col-sm-12 col-xs-12">
						<input type="password" class="form-control input-lg" id="passwordSignup" name="password" placeholder="Parola Girin" value="<?php echo @$_POST['password']; ?>">
					</div>
				</div>
			</div>

			<div class="row marginTop20">
				<div class="form-group">
					<div class="col-md-12">
						<input type="password" class="form-control input-lg" id="passwordRepeatSignup" name="passwordConfirm" placeholder="Parolayı Tekrar Girin" value="<?php echo @$_POST['passwordConfirm']; ?>">
					</div>
				</div>
			</div>

			<div class="row marginTop20">
				<div class="col-md-12">
					<input type="submit" value="KAYDOL" name="signup" class="btn btn-theme btn-lg pull-right signupBtn">
				</div>
			</div>
		</form>
		</div><!-- /.col-md-5 -->

	</div><!-- /.row -->

</div><!-- /.container -->

<?php include_once("inc/footer.php"); ?>