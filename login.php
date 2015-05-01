<body class="loginPage">

<?php include_once("inc/header.php"); ?>

<div class="login_bg_wrapper"></div>

<div class="container">

	<div class="row marginTop20">

		<div class="col-md-7 hidden-sm hidden-xs signResults">
			<?php echo ( isset($result) ? $result : '' ); ?>
<?php /*
			<p>Helperdoc  doktorlar arasında etkin iletişim sağlayan yeni nesil sosyal ağdır.</p>
			<p class="marginTop30">Doktorlara kendi aralarında güvenilir bilgi paylaşımı,<br />bu bilgileri depolama ve güvenilir bir şekilde<br />geleceğe taşıma konusunda yeni çözümler sunmaktadır.</p>
			<p class="marginTop30">HELPERDOC sitesine ücretsiz üye olabilir, meslektaşlarınızla anlık paylaşımlarda bulunabilir ve dilediğiniz bilgilieri arşivinizde saklayabilirsiniz.</p>
*/ ?>
<?php
echo '<h3> En Son Yapılan Gönderiler</h3>';
// display last posted issues as preview
$query = $db->query("SELECT * FROM `gwp_posts` WHERE `type` = 'branch_post' AND `status` = '1' ORDER BY `id` DESC LIMIT 0, 5");
while ($obj = $query->fetch(PDO::FETCH_OBJ)) {
$post_id 			= $obj->id;
$post_sender 		= $obj->val_1;
$sender = $db->query("SELECT name,surname FROM gwp_users WHERE id = '$post_sender'")->fetch();
$sender_name = $sender->name.' '.$sender->surname;
$sender_name = mb_convert_case($sender_name, MB_CASE_TITLE, "UTF-8");

$sender_image = $db->query("SELECT val_text FROM gwp_user_meta WHERE `group` = 'profile' AND `post_id` = '$post_sender'")->fetch();

// if post has an image, query from gwp_post_meta
$post_img = '';
if($obj->val_3 == 1)
{
    $post_img_query = $db->query("SELECT `key`,`val_1`,`val_3`,`val_text` FROM `gwp_post_meta` WHERE `post_id` = '$post_id' AND `group` = 'post_img'");
    while ($post_img_query_obj = $post_img_query->fetch(PDO::FETCH_OBJ)) {
        $image_path 	= $post_img_query_obj->val_3;
        // resim mi pdf mi olup olamdığını belirler. ona göre pdf iconu ekler
        $ext = $post_img_query_obj->val_1;
        if($ext == 'png' || $ext == 'jpg' || $ext == 'jpeg') {
            $image_src 		= $image_path.$post_img_query_obj->key.'_thumb.';
            $image_ext 		= $image_src.$post_img_query_obj->val_1;
            //$post_img 		.= '<a href="'.$post_img_query_obj->val_text.'" class="issueImg js-lighbox-trigger"><img src="'.$image_ext.'" class="img-responsive" /></a>';
            $post_img 		.= '<a href="'.$post_img_query_obj->val_text.'" class="swipebox issueImg" rel="gallery-'.$gallery_no.'"><img src="'.$image_ext.'" class="img-responsive" alt="" /></a>';
        } else {
            $post_img 		.= '<a href="'.$post_img_query_obj->val_text.'" class="issueImg" target="_blank"><img src="img/pdf_icon.png" class="img-responsive" /></a>';
        }
    }
}
?>

<section class="row marginTop20">
	<div class="col-md-2">
		<img src="<?php echo $sender_image->val_text == false ? 'img/doctor.jpg' : $sender_image->val_text; ?>" class="img-responsive">
	</div>
	<div class="col-md-9 preview">
		<small><?php echo $sender_name; ?></small>
		<div class="clear"></div>
		<div class="preview_content"><?php echo $obj->content; ?></div>
		<div class="clear"></div>
		<?php echo $post_img; ?>
		<div class="clear"></div>
		<div class="preview_content"><?php echo mb_strlen($obj->val_text) != 0 ? '<strong>Tanı: </strong>'.$obj->val_text : ''; ?></div>
	</div>
</section>

<?php }  ?>

		</div><!-- /.col-md-7 -->

		<div class="col-md-5 col-sm-12 col-xs-12 signUpPanel">
		<form method="post" action="">
			<div class="row">
				<div class="col-md-12">
					<h3>Üye Olun &amp; Aramıza Katılın</h3>
					<p class="hidden-sm hidden-xs">Ücretsiz üyelik için formu doldurun</p>
				</div>
			</div>

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