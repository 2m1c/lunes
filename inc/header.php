<?php if( isset($_COOKIE['user_id']) ) { ?>

<div class="js-lightbox-overlay lightboxOverlay"></div>
<div class="js-lightbox lightbox">
	<span class="lightboxClose">X</span>
	<span class="lightboxLeftArrow"><i class="fa fa-arrow-circle-left"></i></span>
	<span class="lightboxRightArrow"><i class="fa fa-arrow-circle-right"></i></span>
	<div class="lightboxContent"></div>
</div>

<header class="wall">

<div class="container">
	
	<div class="row">

		<div class="col-md-1 col-sm-1 col-xs-1 logo">
			<a href="./">
				<!--<i class="fa fa-flask"></i>-->
                <i class="fa fa-home"></i>
			</a>
		</div> <!-- /.col-md-1 -->

		<div class="col-md-5 hidden-sm hidden-xs search">
			<div class="form-group">
				<i class="fa fa-search"></i>
				<input type="text" class="form-control js-keyup-search" data-search="header" placeholder="Arama Yapın">
				<div class="headerSearchResult js-keyup-result"><?php // ajax key up search results here! ?></div>
			</div>
		</div> <!-- /.col-md-5 -->

		<div class="col-md-6 col-sm-6 col-xs-10 userInfo">
			
			<a href="?logout=true" class="action" role="button"><i class="fa fa-sign-out" title="ÇIKIŞ"></i></a>
			<?php /* <a href="#" class="action js-branch-notification" role="button"><i class="fa fa-question"></i></a>*/ ?>
			<a href="#" class="action js-notification js-ajaxForNotification" role="button">0</a>
			
			<a href="profile.php?url=general&profile_id=<?php echo $user_id; ?>"><img src="<?php echo get_current_user_image(); ?>" alt="#"><span><?php echo select_query("name", "gwp_users", "id = $user_id", "lower"); ?></span></a>
        </div> <!-- /.col-md-4 -->

		<div class="js-">

		<div class="notificationList js-notificationList">
			<!-- Ajax Control -->
		</div>
		

	</div> <!-- /.row -->

</div> <!-- /.container -->

</header> <!-- /.header -->

<div class="notificationPopupWrapper js-notification-popup-wrapper hidden-xs">
<!--
<a class="notificationPopup js-notification-popup" href="#">
	<span class="name">Sancak Yüksel</span>
	<span class="branch">Genel Cerrahi</span> <br />
	<span class="text">alanına bir gönderide bulundu!</span>
	<button>X</button>
</a>
-->
</div>

<?php } else { ?> 

<header class="login">

<div class="container">
	
	<div class="row">

		<div class="col-md-7 hidden-xs hidden-sm logo marginTop20">
			<a href="./">
				<img src="img/logo.png" style="margin-top:-26px;">
				<span class="hidden-sm hidden-xs">HELPERDOC</span>
			</a>
		</div> <!-- /.col-md-5 -->
		<form method="post" action="">
		<!--<div class="col-md-1 col-sm-12 col-xs-12 marginTop20">Giriş İçin Formu Doldurun!</div>-->
		<div class="col-md-2 col-sm-4 col-xs-12 marginTop15">
			<div class="form-group">
				<label for="email" class="hidden-sm hidden-xs loginInput">E-Posta Adresiniz </label>
				<input type="text" name="login_email" class="form-control input-md" id="email" tabindex="1" placeholder="E-Posta" value="<?php echo @$_POST['login_email'] ?>">
			</div>
		</div><!-- /.col-md-3 -->

		<div class="col-md-2 col-sm-4 col-xs-12 marginTop15 loginInput">
			<div class="form-group">
				<label for="password" class="hidden-sm hidden-xs">Parolanız </label>
				<input type="password" name="login_password" class="form-control" id="password" tabindex="2" placeholder="Şifre" value="<?php echo @$_POST['login_password'] ?>">
			    <br />
                <a href="getpassword.php" class="forgot_password">Şifremi Unuttum</a>
            </div>
		</div><!-- /.col-md-3 -->

		<div class="col-md-1 col-sm-12 col-xs-12 marginTop40 loginBtn">
			<div class="form-group">
				<input type="submit" name="login" class="btn btn-theme btn-sm" value="GİRİŞ YAP">
			</div>
		</div><!-- /.col-md-1 -->
		</form>

	</div> <!-- /.row -->

</div> <!-- /.container -->

</header> <!-- /.header -->

<?php } ?>