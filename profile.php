<?php
include_once("inc/head.php");
include_once("inc/class.upload.php");
error_reporting(0);

if( !isset($_COOKIE['user_id']) )
{
	header('Location: login.php');
	exit();
}

//call these both functions to delete post or comment
delete_post($_GET['post_delete']);
delete_comment($_GET['comment_delete']);
delete_archive($_GET['archive_delete']);
remove_post_from_archive($_GET['remove_post_from_archive']);

// save general information
if( isset($_POST['save_account']) )
{
	// check if user password is matched or not
	$password = md5($_POST['password']);
	$pass = select_query("password", "gwp_users", "id = $user_id");
	$result = ''; // display results in this variable
	$is_action_success = 'danger';

	if($password == $pass)
	{
		$usertitle 	= $_POST['title'];
        $userbranch = $_POST['branch'];
        $company 	= $_POST['company'];
		$firstname	= $_POST['firstname'];
		$lastname	= $_POST['lastname'];
		$email		= $_POST['email'];

		// push errors into this array
		$errors = array();

		if( empty($firstname) )
		{
			$errors[] = "Adınınızı Giriniz";
		}

		if( empty($lastname) )
		{
			$errors[] = "Soyadınızı Girin";
		}

		if( empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL))
		{
			$errors[] = "Geçerli bir e-posta giriniz";
		}
		else
		{
			$email_avaliable = $db->prepare("SELECT email FROM gwp_users WHERE id != :user_id AND email = :email");
			$email_avaliable->bindValue(':email', $email);
			$email_avaliable->bindValue(':user_id', $user_id);
			$email_avaliable->execute();
			if($email_avaliable->rowCount() >= 1)
			{
				$result .= "Bu Email Adresi Kullanılmakadır";
			}
		}

		if( !empty($errors) )
		{
			foreach ($errors as $error) {
				$result .= $error."<br />";
			}
		}
		else
		{

			$query = $db->prepare("UPDATE gwp_users 
								SET name = :name,
									surname = :surname,
									email = :email,
									usertitle = :usertitle,
									userbranch = :userbranch,
									company = :company
								WHERE id = :id");
			$query->bindValue(":name", $firstname);
			$query->bindValue(":surname", $lastname);
			$query->bindValue(":email", $email);
			$query->bindValue(":usertitle", $usertitle);
            $query->bindValue(":userbranch", $userbranch);
            $query->bindValue(":company", $company);
			$query->bindValue(":id", $user_id);
			$done = $query->execute();

			if($done)
			{
				$result .= "Değişiklikler tamamlandı";
				$is_action_success = 'success';
			}
			else
			{
				$result .= "Bilinmeyen bir sebepten ötürü değişiklik tamamlanamadı.<br />Lütfen tekrar deneyin";
			}
		}
	}
	else
	{
		$result .= "Şifrenizi yanlış girdiniz!";
	}
}

// save or change profile image
if( isset($_POST['save_image']) )
{
	// check if user password is matched or not
	$password = md5($_POST['password']);
	$pass = select_query("password", "gwp_users", "id = $user_id");
	$result = ''; // display results in this variable
	$is_action_success = 'danger';

	if($password == $pass)
	{
		$parameter 					= array();
		$parameter['file']         	= $_FILES['file'];
        $parameter['id']            = $user_id;
        $parameter['filename']      = seo(get_current_users("lower"));
        $parameter['directory']     = 'users';
        $parameter['table_name']    = 'gwp_user_meta';
        $parameter['group']    		= 'profile';
        $parameter['width']         = '70';
        $parameter['height']        = '70';

		single_image_upload($parameter);
		$result .= "Resminiz değiştirildi!";
		$is_action_success = 'success';
	}
	else
	{
		$result .= "Şifrenizi yanlış girdiniz!";
	}
}

// change password
if( isset($_POST['change_password']) )
{
	$myPassword 		= md5($_POST['myPassword']);
	$newPassword 		= $_POST['newPassword'];
	$passwordConfirm	= $_POST['passwordConfirm'];
	$is_action_success = 'danger';

	$pass 		= select_query("password", "gwp_users", "id = $user_id");

	$errors = array();
	$result = '';

	if($myPassword != $pass)
	{
		$errors[] =  "Şifrenizi yanlış girdiniz";
	}

	if($newPassword != $passwordConfirm)
	{
		$errors[] = "Şifreler Eşleşmiyor";
	}

	if( mb_strlen($newPassword) < 6 )
	{
		$errors[] = "Şifre en az 6 karakterden oluşmalıdır";
	}

	if(!empty($errors))
	{
		foreach ($errors as $error) {
			$result .= $error.'<br />';
		}
	}
	else
	{
		$enctypted_pass = md5($newPassword);
		$query = $db->prepare("UPDATE gwp_users SET password = :password WHERE id = :id");
		$query->bindValue(":password", $enctypted_pass);
		$query->bindValue(":id", $user_id);
		$done = $query->execute();
		if($done)
		{
			$result .= "Şifreniz değiştirildi";
			$is_action_success = 'success';
		}
		else
		{
			$result .= "Bilinmeyen bir hata oluştu. Lütfen tekrar deneyin!";
		}
	}

}

// Dosya Ekle
if( isset($_POST['save_filename']) )
{

	$filename = $_POST['filename'];
	$errors = array();
	$result = '';
	$is_action_success = 'danger';

	if(empty($filename))
	{
		$errors[] =  "Arşiv adını giriniz";
	}

	if(mb_strlen($filename) <= 3 || mb_strlen($filename) >= 20)
	{
		$errors[] =  "Arşiv adı en az 3 ve  en çok 20 karakterden oluşmalıdır";
	}

	if(!empty($errors))
	{
		foreach ($errors as $error) {
			$result .= $error.'<br />';
		}
	}
	else
	{
		$query = $db->query("INSERT INTO `gwp_user_meta`
							SET `group` = 'folder',
								`key` = 'folder',
								`post_id` = '$user_id',
								`val_text` = '$filename'");
		
		if($query)
		{
			$result .= $filename." isimli arşiv eklendi";
			$is_action_success = 'success';
		}
		else
		{
			$result .= "Bilinmeyen bir hata oluştu. Lütfen tekrar deneyin!";
		}
	}

}

// Dosya Ekle
if( isset($_POST['archive_post']) )
{
	$post_id = $_POST['post_id'];
	$filename = $_POST['filename'];

	$errors = array();
	$result = '';
	$is_action_success = 'danger';

	if($filename == NULL || $filename == 0 )
	{
		$errors[] =  "Arşiv seçimi yapınız";
	}

	if(empty($post_id))
	{
		$errors[] =  "Gönderi Seçilmedi";
	}

	if(!empty($errors))
	{
		foreach ($errors as $error) {
			$result .= $error.'<br />';
		}
	}
	else
	{
        $query = $db->query("INSERT INTO `gwp_user_meta` SET `post_id` = '$user_id', `group` = 'archived_post', `key` = 'archived_post', `val_1` = '$post_id', `val_2` = '$filename'");
		if($query)
		{
			$result .= "Gönderiniz arşive eklendi";
			$is_action_success = 'success';
		}
		else
		{
			$result .= "Bilinmeyen bir hata oluştu. Lütfen tekrar deneyin!";
		}

	}

}
?>
<body>

<?php include_once("inc/header.php"); ?>


<div class="container wall">

<?php

$profile_id = $_GET['profile_id'];

if(!empty($result)) { ?>
	<div class="row">
		<div class="col-md-12 col-sm-12 col-xs-12">
			<div class="alert alert-<?php echo $is_action_success; ?> alert-dismissable">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			  	<?php echo $result; ?>
			</div>
		</div>
	</div>
<?php } ?>

	<div class="row">
    <?php include_once('inc/sidebar_setting.php'); ?>
<?php
$url = $_GET['url'];
if($url == "general") { ?>

		<div class="col-md-10 col-sm-12 col-xs-12 profile">
			
			<div class="row">
				
				<div class="col-md-2 col-sm-10 col-xs-2">
					<img src="<?php echo get_an_user_image($profile_id); ?>" class="img-responsive" />
				</div><!-- /.col-md-2 -->

				<div class="col-md-10 col-sm-10 col-xs-10">
					
					<div class="row">
						<div class="col-md-12 col-xs-12 profileBasic">
							<p class="profileTitle"><?php echo select_query("usertitle", "gwp_users", "id = $profile_id", "upper"); ?> </p>
							<p class="profileName"><?php echo get_an_user_fullname($profile_id,"upper"); ?>  <span class="profileIssueAmount pull-right">Şimdiye kadar <?php echo get_an_user_issue_amount($profile_id) ?> konu açtı</span></p>
						    <p>Çalıştığı Kurum: <?php echo select_query("company", "gwp_users", "id = $profile_id", "upper"); ?></p>
                        </div><!-- /.col-md-12 -->
					</div><!-- /.row -->

					<div class="row">
						<div class="col-md-12 col-sm-12 col-xs-12 profileFields">
							<p class="profileSubTitle">Takip Ettiği Alanlar</p>

<?php
$branches = get_user_following_branches($profile_id);
while ($row = $branches->fetch(PDO::FETCH_OBJ)) {
	$category_id = $row->val_1;
	$branch_name = select_query("title", "qwp_category", "id = $category_id", "lower");
	$branch_url = select_query("url", "qwp_category", "id = $category_id", "lower");
?>
						<a href="field.php?url=<?php echo $branch_url; ?>" class="profileFollowField">#<?php echo $branch_name; ?></a>
<?php } ?>


						</div><!-- /.col-md-12 -->
					</div><!-- /.row -->

				</div><!-- /.col-md-10 -->

			</div><!-- /.row -->

		</div><!-- /.col-md-10 -->

<?php } else if($url == "posts") { ?>
		
		<div class="col-md-10 col-sm-10 col-xs-10 profile">
<?php // Gönderilerim

// if user displays post in a folder, add this variable to query
$folder_id 		= ( isset($_GET['folder_id']) ? $_GET['folder_id'] : false );

/*
 * arşiv attığı gönderileri gwp_user_meta tablosundan çek ve array içine at
 * eğer arşiv dosyası görüntüleniyorsa diğer kullancılarında gönderileri gösterilecek
 * eğer sadece kendi göderileri listelenmek isteniyorsa sadece kendine ait gödneriler listelencek
*/
$archive_query = $db->query("SELECT `val_1` FROM `gwp_user_meta` WHERE `group` = 'archived_post' AND `key` = 'archived_post' AND `post_id` = '$user_id' AND `val_2` = '$folder_id'");
$arcihive_array = array();
while($archive_obj = $archive_query->fetch(PDO::FETCH_OBJ)) {
    array_push($arcihive_array, $archive_obj->val_1);
}

$folder_query 	= ( $folder_id == false ? " AND `val_1` = '$profile_id'" : " AND `id` IN (".implode(',',$arcihive_array).")");
echo 			$folder_id == false ? '' : '<h3><strong>ARŞİV: </strong>'.get_folder_name_by_id($folder_id).' <a onClick="return confirm('."'Bu arşivi silmek istediğinizden emin misiniz?'".')" href="?archive_delete='.$_GET['folder_id'].'" class="delete_link">Arşivi Sil</a></h3>';
// Begin: pagination setting
$pages_query = $db->query("SELECT id FROM gwp_posts WHERE `type` IN ('branch_post', 'archive_post') AND CASE WHEN `val_1` = '$user_id' THEN `status` IN (1,2) ELSE `status` = '1' END $folder_query");
$total_rows	= $pages_query->rowCount();
$per_page 	= 20;
$start 		= ( isset($_GET['page']) ? ($_GET['page'] - 1) * $per_page  : 0 );
// End: pagination setting
$post_query = $db->query("SELECT * FROM gwp_posts WHERE `type` IN ('branch_post', 'archive_post') AND CASE WHEN `val_1` = '$user_id' THEN `status` IN (1,2) ELSE `status` = '1' END $folder_query ORDER BY datetime DESC  LIMIT $start, $per_page");


$gallery_no = 0;
while ($post = $post_query->fetch(PDO::FETCH_OBJ)) {
$gallery_no++;
$post_id 			= $post->id;
$post_sender 		= $post->val_1;
$sender_name 		= get_an_user_fullname($profile_id, "title");
$sender_image 		= get_an_user_image($post_sender);
$time_since_post 	= time_since_post($post->datetime);
$comment_amount 	= get_post_comment_amount($post_id);

// user title (doktor, prof. doç vb...) and user branch (nörolog, ürolog)
$user_title = select_query("usertitle", "gwp_users", "id = $post_sender", "title");
$user_branch = select_query("userbranch", "gwp_users", "id = $post_sender", "title");

// if post has an image, query from gwp_post_meta
$post_img = '';
if($post->val_3 == 1)
{
    $post_img_query = $db->query("SELECT `key`,`val_1`,`val_3`,`val_text` FROM `gwp_post_meta` WHERE `post_id` = '$post_id' AND `group` = 'post_img'");
    while ($post_img_query_obj = $post_img_query->fetch(PDO::FETCH_OBJ)) {
        $image_path 	= $post_img_query_obj->val_3;
        /* resim mi pdf mi olup olamdığını belirler. ona göre pdf iconu ekler*/
        $ext = $post_img_query_obj->val_1;
        if($ext == 'png' || $ext == 'jpg' || $ext == 'jpeg') {
            $image_src 		= $image_path.$post_img_query_obj->key.'_thumb.';
            $image_ext 		= $image_src.$post_img_query_obj->val_1;
            /*$post_img 		.= '<a href="'.$post_img_query_obj->val_text.'" class="issueImg js-lighbox-trigger"><img src="'.$image_ext.'" class="img-responsive" /></a>';*/
            $post_img 		.= '<a href="'.$post_img_query_obj->val_text.'" class="swipebox issueImg" rel="gallery-'.$gallery_no.'"><img src="'.$image_ext.'" class="img-responsive" alt="" /></a>';
        } else {
            $post_img 		.= '<a href="'.$post_img_query_obj->val_text.'" class="issueImg" target="_blank"><img src="img/pdf_icon.png" class="img-responsive" /></a>';
        }
    }
}

// display post menu icon if the post belongs to the current user else display only archive option
$visibility = ($post->status == 2 ? '<a href="?post_visibility='.$post_id.'&visibility=1">Gönderiyi Gizlemeyi Kaldır</a>' : '<a href="?post_visibility='.$post_id.'&visibility=2">Gönderiyi Gizle</a>');
// if post is archived display remove option else display move archive option
$file_id = get_an_user_archived_post($post_id, $user_id);
$archive    = ( $file_id != false ? '<li><a href="profile.php?url=posts&profile_id='.$user_id.'&folder_id='.$file_id.'">Bu Gönderi '.get_folder_name_by_id($file_id).' İsimli Arşivde</a></li><li><a href="profile.php?remove_post_from_archive='.$post_id.'">'.get_folder_name_by_id($file_id).' Arşivinden Kaldır</a></li>' : '<li><a href="profile.php?url=folder&profile_id='.$user_id.'&move_post='.$post_id.'">Arşive Taşı</a></li>' );
$post_menu 	= ($post_sender == $user_id ? '<a href="#" class="postMenu js-display-item" data-show="js-postMenu" role="button"> <i class="fa fa-arrow-down"></i></a> <ul class="postMenuList js-postMenu">'.$archive.' <li> <a onClick="return confirm('."'Bu gönderiyi silmek istediğinizden emin misiniz?'".')" href="?post_delete='.$post_id.'">Gönderiyi Sil</a> </li> <li> '.$visibility.' </li></ul>' : '<a href="#" class="postMenu js-display-item" data-show="js-postMenu" role="button"> <i class="fa fa-arrow-down"></i></a> <ul class="postMenuList js-postMenu">'.$archive.'</ul>' );


// which branch this post belong
$branch_name    = select_query("title", "qwp_category", "id = $post->val_2", "lower");
if($branch_name != false) {
    $branch_url     = select_query("url", "qwp_category", "id = $post->val_2", "lower");
    $posted_branch  = '<a href="field.php?url='.$branch_url.'" class="postField"> <i class="fa fa-arrow-right"></i> <strong>'.$branch_name.'</strong> alanında bir gönderi paylaştı</a>';
}
?>

    <section class="issuePanelWrapper">

        <div class="row">

            <div class="col-md-12 col-sm-12 col-xs-12 issuePanel">
                <a href="profile.php?url=general&profile_id=<?php echo $post_sender; ?>" class="pull-left">
                    <img src="<?php echo $sender_image; ?>" width="32" height="32">
                </a>

                <a href="profile.php?url=general&profile_id=<?php echo $post_sender; ?>" class="posterName"> <?php echo $user_title.' '.$sender_name; ?></a> <?php echo $posted_branch; ?>
                <br /> <div class="clear hidden-lg hidden-md display-sm display-xs"></div> <span class="userbranch"><?php echo $user_branch; ?></span><span class="time"><?php echo $time_since_post; ?> Önce</span>
                <?php echo $post_menu; ?>

                <div class="clear"></div>

                <article class="issueContent">
                    <p><?php echo $post->content; ?></p>
                </article>

                <div>
                    <?php echo $post_img; ?>
                </div>

                <div class="clear"></div>

                <strong>Tanı:</strong>
                <p class="js-diganosis"><?php echo $post->val_text; ?></p>
                <?php if($post_sender == $user_id) { ?>
                    <textarea data-post="<?php echo $post_id; ?>" class="form-control js-post-diganosis" placeholder="Tanı yazdıktan sonra enter tuşuna basınız"><?php echo $post->val_text; ?></textarea>
                <?php } ?>

                <footer class="marginTop10">
                    <span class="data pull-left"><?php echo $comment_amount; ?> Yorum</span>
                </footer>

            </div><!-- /.col-md-10 -->
        </div><!-- /.row -->

        <div class="row marginTop10">

            <div class="col-md-12 col-sm-12 col-xs-12 commentBoxList js-commentsBoxlist">

                <div class="js-comments">
                    <?php
                    //fetch comments
                    echo get_post_comments($post_id);
                    ?>
                </div>

                <div class="row marginTop10">

                    <div class="col-md-12 col-xs-12 commentPlace">
                        <img src="<?php echo get_current_user_image(); ?>" />
                        <textarea data-post="<?php echo $post_id; ?>" data-postowner="<?php echo $post_sender; ?>" class="form-control js-post-comment" placeholder="yorumunuzu göndermek için enter tuşuna basınız"></textarea>
                    </div><!-- /.col-md-2 -->

                </div><!-- /.row (commentPlace) -->

            </div><!-- /.col-md-10 -->

        </div><!-- /.row -->


    </section><!-- issuePanel -->
<?php 
} // => End: while()
?>

			<div class="row">

				<div class="col-md-2 hidden-sm hidden-xs">&nbsp;</div>
				<div class="col-md-10 col-sm-12 col-xs-12">
					<ul class="pagination">
<?php
// pagintaion buttons
$curr_page = ( isset($_GET['page']) ? $_GET['page'] : 1 );
$paging 	= ceil($total_rows / $per_page);
$break 		= ($curr_page + 5 == $paging ? $paging - 1 : $curr_page + 5 );
$startPoint = ($paging <= 8 ? 1 : ( $paging - ($curr_page+1) <= 3 && $paging >= 8 ? $curr_page - 3 : $curr_page ) );

for($i = $startPoint; $i <= $paging; $i++)
{
	if($i <= $break) {
		echo ($i == $curr_page ? '<li><a class="active" href="'.url().'&page='.$x.'">'.$x.'</a></li>' : '<li><a href="'.url().'&page='.$x.'">'.$x.'</a></li>' );
		if($i==$break) {
			echo '<li><a>...</a> </li><li>a class="active" href="'.url().'&page='.$x.'">'.$x.'</a></li>';
		}
	}
}
?>
					</ul>
				</div>

			</div><!-- /.row -->


		</div><!-- /.col-md-10 -->


<?php } else if($url == "notification") { ?>

		<div class="col-md-10 col-sm-10 col-xs-10 profile">

<?php 
$user_id 		= $_COOKIE['user_id'];
$query 			= $db->query("SELECT `datetime`, `type`, `val_1`, `val_3` FROM `gwp_notifications` WHERE `val_2` = '$user_id' ORDER BY id DESC");
while ($obj = $query->fetch(PDO::FETCH_OBJ)) {

$text 	= ( $obj->type == 'comment' ? 'gönderinize cevap verdi' : 'yorumunuzu onayladı' );

$url 	= ( $obj->type = 'comment' ? 'post.php?id='.$obj->val_1 : 'post.php?id='.$obj->val_1 );
?>
	<p class="notifications"><a href="profile.php?url=general&profile_id=<?php echo $obj->val_3; ?>"><span class="poster"><?php echo get_an_user_fullname($obj->val_3, 'title'); ?></span></a> <a href="<?php echo $url; ?>"><?php echo $text; ?></a>&nbsp;<span class="postTime"><?php echo time_since_post($obj->datetime); ?> önce</span></p>
<?php } ?>
		</div><!-- /.col-md-10 -->

<?php	} else if($url == "account") { ?>

		<div class="col-md-10 col-sm-12 col-xs-12 profile">

			
			<form method="post" action="" enctype="multipart/form-data">

            <div class="row">
                <div class="form-group">
                    <label class="col-md-2 col-xs-12 control-label">Ünvan</label>
                    <div class="col-md-5 col-sm-6 col-xs-6">
                        <input type="text" class="form-control" name="title" placeholder="Ünvan yazın" value="<?php echo select_query("usertitle", "gwp_users", "id = $user_id", "title"); ?>">
                    </div>
                    <div class="col-md-5 col-sm-6 col-xs-6">
                        <input type="text" class="form-control" name="branch" placeholder="Uzmanlığınızı yazın" value="<?php echo select_query("userbranch", "gwp_users", "id = $user_id", "title"); ?>">
                    </div>
                </div>
            </div>

            <div class="row marginTop20">
                <div class="form-group">
                    <label for="title" class="col-md-2 col-xs-12 control-label">Çalıştığınız Kurum</label>
                    <div class="col-md-10 col-sm-12 col-xs-12">
                        <input type="text" class="form-control" name="company" placeholder="Çalıştığınız Kurumu Yazın" value="<?php echo select_query("company", "gwp_users", "id = $user_id", "title"); ?>">
                    </div>
                </div>
            </div>
			
			<div class="row marginTop20">
				<div class="form-group">
					<label class="col-md-2 col-xs-12 control-label">Ad &amp; Soyad</label>
					<div class="col-md-5 col-xs-6 col-xs-6">
						<input type="text" name="firstname" class="form-control input-lg" placeholder="Adınızı yazın" value="<?php echo select_query("name", "gwp_users", "id = $user_id", "title"); ?>">
					</div>
					<div class="col-md-5 col-sm-6 col-xs-6">
						<input type="text" name="lastname" class="form-control input-lg" placeholder="Soyadınızı yazın" value="<?php echo select_query("surname", "gwp_users", "id = $user_id", "title"); ?>">
					</div>
				</div>
			</div>

			<div class="row marginTop20">
				<div class="form-group">
					<label class="col-md-2 col-xs-12 control-label">E-Posta</label>
					<div class="col-md-10 col-sm-12 col-xs-12">
						<input type="email" name="email" class="form-control input-lg" placeholder="E-mail adresinizi yazın" value="<?php echo select_query("email", "gwp_users", "id = $user_id", "lower"); ?>">
					</div>
				</div>
			</div>


			<div class="row marginTop20">
				<div class="form-group">
					<label class="col-md-2 col-xs-12 control-label">Şifre</label>
					<div class="col-md-10 col-xs-12">
						<input type="password" name="password" class="form-control input-lg" placeholder="Şifrenizi Giriniz">
					</div>
				</div>
			</div>

			<div class="row marginTop20">
				<div class="col-md-12 col-sm-12 col-xs-12">
					<input type="submit" name="save_account" value="DEĞİŞİKLİKLERİ KAYDET" class="btn btn-theme btn-lg pull-right">
				</div>
			</div>
			</form>
			<div class="clear marginTop20"></div>
		</div><!-- /.col-md-10 -->

<?php } else if($url == "image") { ?>
		
		<div class="col-md-10 col-sm-12 col-xs-12 profile">

			
			<form method="post" action="" enctype="multipart/form-data">
			
			<div class="row">

			<div class="col-md-2 col-sm-4 col-xs-4">

				<div class="fileinput fileinput-new" data-provides="fileinput">
				  <div class="fileinput-preview thumbnail" data-trigger="fileinput" style="background: transparent; border: none; padding: 0px;">
				  	<img src="<?php echo get_current_user_image(); ?>" alt="" class="img-responsive"><br />
				  </div>
				  <div>
				    <span class="btn btn-theme btn-sm"><span class="fileinput-new">Resim Seç</span><input type="file" name="file" class="styled-file-btn"></span>
				  </div>
				</div>

			</div><!-- /.col-md-4 -->

			<div class="col-md-10 col-sm-8 col-xs-8">
				<div class="row">
					<div class="form-group">
						<label for="passwordImage" class="control-label">Şifre</label>
						<input type="password" name="password" class="form-control input-lg" id="passwordImage" placeholder="Resmi değiştirmek için şifrenizi giriniz">
					</div>
				</div>
			</div><!-- /.col-md-8 -->

			</div>
			<div class="row marginTop20">
				<div class="col-md-12 col-sm-12 col-xs-12">
					<input type="submit" name="save_image" value="DEĞİŞİKLİKLERİ KAYDET" class="btn btn-theme btn-lg pull-right">
				</div>
			</div>
			</form>

		</div><!-- /.col-md-10 -->

<?php } else if($url == "password") { ?>
		
		<div class="col-md-10 col-sm-12 col-xs-12 profile">

			
			<form method="post" action="">
			
			<div class="row marginTop20">
				<div class="form-group">
					<label for="myPassword" class="col-md-2 col-xs-12 control-label">Şuan ki Şifreniz</label>
					<div class="col-md-10 col-sm-12 col-xs-12">
						<input type="password" name="myPassword" class="form-control input-lg" id="myPassword" placeholder="Şifrenizi Giriniz">
					</div>
				</div>
			</div>


			<div class="row marginTop20">
				<div class="form-group">
					<label for="newPassword" class="col-md-2 col-xs-12 control-label">Yeni Şifre</label>
					<div class="col-md-10 col-sm-12 col-xs-12">
						<input type="password" name="newPassword" class="form-control input-lg" id="newPassword" placeholder="Şifrenizi Giriniz">
					</div>
				</div>
			</div>

			<div class="row marginTop20">
				<div class="form-group">
					<label for="passwordConfirm" class="col-md-2 col-xs-12 control-label">Şifre Tekrarı</label>
					<div class="col-md-10 col-sm-12 col-xs-12">
						<input type="password" name="passwordConfirm" class="form-control input-lg" id="passwordConfirm" placeholder="Şifrenizi Tekrar Giriniz">
					</div>
				</div>
			</div>

			<div class="row marginTop20">
				<div class="col-md-12 col-sm-12 col-xs-12">
					<input type="submit" name="change_password" value="DEĞİŞİKLİKLERİ KAYDET" class="btn btn-theme btn-lg pull-right">
				</div>
			</div>
			<div class="clear marginTop20"></div>
			</form>

		</div><!-- /.col-md-10 -->

<?php } else if($url == "folder") { ?>

		<div class="col-md-10 col-sm-12 col-xs-12 profile">
			
			<?php if( isset($_GET['move_post']) ) { ?>
			<form method="post" action="">
			
			<div class="row marginTop20">
				<div class="form-group">
					<label class="col-md-2 col-sm-12 col-xs-12 control-label">Arşiv Seç</label>
					<input type="hidden" name="post_id" value="<?php echo $_GET['move_post']; ?>">
					<div class="col-md-10 col-sm-12 col-xs-12">
						<select class="form-control" name="filename">
							<option value="0">Seçim Yapınız</option>
<?php
$user_folders = $db->query("SELECT `id`, `val_text` FROM `gwp_user_meta` 
				WHERE `group` = 'folder' AND `key` = 'folder' AND `post_id` = '$user_id'");
while ($folder = $user_folders->fetch(PDO::FETCH_OBJ)) { ?>
							<option value="<?php echo $folder->id; ?>"><?php echo $folder->val_text; ?></option>
<?php } ?>
						</select>


					</div>
				</div>
			</div>

			<div class="row marginTop20">
				<div class="col-md-12 col-sm-12 col-xs-12">
					<input type="submit" name="archive_post" value="Gönderiyi Bu Dosyada Arşivle" class="btn btn-theme btn-lg pull-right">
				</div>
			</div>
			<div class="clear marginTop20"></div>
			</form>

			<?php } else { ?>

			<form method="post" action="">
			
				<div class="row marginTop20">
					<div class="form-group">
						<label for="filename" class="col-md-2 col-sm-12 col-xs-12 control-label">Arşiv Adı</label>
						<div class="col-md-10 col-sm-12 col-xs-12">
							<input type="text" name="filename" class="form-control input-lg" id="filename" placeholder="Arşiv Adı Girin">
						</div>
					</div>
				</div>

				<div class="row marginTop20">
					<div class="col-md-12 col-sm-12 col-xs-12">
						<input type="submit" name="save_filename" value="ARŞİVİ KAYDET" class="btn btn-theme btn-lg pull-right">
					</div>
				</div>

			</form>

			<?php } ?>

			<div class="row marginTop20">
				<h3 class="col-md-12 col-sm-12 col-xs-12">Arşiv Dosyalarım</h3>
			</div>

			<div class="row marginTop10">
<?php
$user_folders = $db->query("SELECT `id`, `val_text` FROM `gwp_user_meta` 
				WHERE `group` = 'folder' AND `key` = 'folder' AND `post_id` = '$profile_id'");
$no = 0;
while ($folder = $user_folders->fetch(PDO::FETCH_OBJ)) {
$no++;
?>
				<a href="profile.php?url=posts&profile_id=<?php echo $user_id; ?>&folder_id=<?php echo $folder->id; ?>" class="col-md-12 col-xs-12 folder"><span><?php echo $no; ?>)&nbsp;</span><?php echo $folder->val_text; ?>&nbsp;<small>Arşiv Dosyası</small></a>
<?php } ?>
			</div>

		<div class="clear marginTop20"></div>
		</div><!-- /.col-md-10 -->

<?php } else if($url == "delete") { ?>
		
	
		<div class="col-md-10 col-sm-12 col-xs-12 profile">

			<div class="alert alert-info" role="alert">Hesabımı Sil Sayfası Henüz Aktif Değil !</div>
			<?php /*
			<form method="post" action="" enctype="multipart/form-data">
				<p>Hesabınızı sildiğiniz takdirde bütün bilgileriniz silinecektir.</p>

				<div class="row marginTop30">
					<div class="form-group">
						<label for="passwordImage" class="col-md-12 control-label">Siteyi daha iyi hale getirmek adına neden hesabınız kapatmak istediğinizi bizimle paylaşır mısınız?</label>
					</div>
				</div>

				<div class="row">
					<div class="form-group">
						<div class="col-md-12">
							<textarea class="form-control" name="feedback" placeholder="Geri bildirim mesajınızı giriniz"></textarea>
						</div>
					</div>
				</div>

				<div class="row marginTop20">
					<div class="form-group">
						<label for="passwordImage" class="col-md-2 control-label">Şifre</label>
						<div class="col-md-12">
							<input type="password" name="password" class="form-control input-lg" id="passwordImage" placeholder="Hesabınızı silmek için şifrenizi giriniz">
						</div>
					</div>
				</div>

				<div class="row marginTop20">
					<div class="form-group">
						<div class="col-md-12">
							<input type="submit" name="delete_account" class="btn btn-danger btn-lg pull-right" value="Hesabımı Sil">
						</div>
					</div>
				</div>
			</form>
			*/ ?>
		</div><!-- /.col-md-10 -->

<?php } else { // if page has different get parameter or does not have redirect to index page
header('Location: ./');
} ?>



	</div><!-- /.row -->

</div><!-- /.container -->

<?php include_once("inc/footer.php"); ?>