<?php include_once("inc/head.php"); ?>
<body>

<?php
$parameter = array();
$parameter['url'] 			= $_GET['url'];
$parameter['default_url'] 	= "radyoloji";
$parameter['table_name'] 	= 'qwp_category';
$parameter['where'] 		= "url ='".$_GET['url']."'";

$page_id 	= page_url_confirm($parameter);

$status 	= ( following_status($page_id) == true ? true : false );
$warning 	= ( $status == true ? '' : 'Gönderi yapmak için takip etmelisiniz!');
$btnClass 	= ( $status == true ? 'btn-unfollow' : 'btn-follow');
$btnText 	= ( $status == true ? 'Takibi Bırak' : 'Takip Et');
?>

<?php include_once("inc/header.php"); ?>


<?php
if(isset($_GET['instant_notification']) && $_GET['instant_notification'] == "true")
{
	$insert = $db->query("INSERT INTO `gwp_user_meta` 
						SET `post_id` 	= '$user_id', 
							`group` 	= 'instant_follow',
							`key` 		= 'instant_follow',
							`val_1` 	= '$page_id',
							`val_int` 	= '$page_id'");

	if($insert) {
		header('Location: ' . $_SERVER['HTTP_REFERER']);
	}
}
else if(isset($_GET['instant_notification']) && $_GET['instant_notification'] == "false")
{
	$delete = $db->query("DELETE FROM `gwp_user_meta` 
						WHERE `post_id` = '$user_id'
						AND `group` 	= 'instant_follow'
						AND `key` 		= 'instant_follow'
						AND	`val_1` 	= '$page_id'
						AND	`val_int` 	= '$page_id'");
	if($delete) {
		header('Location: ' . $_SERVER['HTTP_REFERER']);
	}
}

$instant 		= (get_user_instant_following_branch($user_id, $page_id) == true ? "false" : "true");
$instant_icon 	= ( $instant == "true" ? 'fa-link' : 'fa-unlink' );
?>

<div class="container wall">

	<div class="row">
		
		<div class="col-md-8 wallFlow">

			<div class="row">
				<div class="col-md-12">
					<div class="fieldInfo">
						<p>	<?php $field_name = select_query("url", "qwp_category", " id = '$page_id'", "lower"); ?>
							<strong>#<?php echo  $field_name; ?></strong> sayfasını görüntülemektesiniz.
							<button class="btn <?php echo $btnClass; ?> btn-sm status js-follow-branch pull-right js-infotip" data-branch="<?php echo $page_id; ?>" data-tip="Takip ettiğiniz alanlar duvarınızda yayınlanır"><?php echo $btnText; ?></button>
						</p>
						<p class="warning">
							<?php echo $warning; ?>
							<a href="field.php?url=<?php echo $_GET['url']; ?>&instant_notification=<?php echo $instant; ?>" class="pull-right notificationLink js-infotip" data-tip="#<?php echo $field_name; ?> alanında yayınlanan her gönderiden haberdar olmak için buraya tıklayın!"><i class="fa <?php echo $instant_icon; ?>"></i></a>
						</p>
						<span class="detail">Bu sayfayı <?php echo get_branch_follower_amount($page_id); ?> kişi takip ediyor ve <?php echo get_total_issue_amount_in_brach($page_id); ?> konu bulunmakta.</span>
					</div>
				</div><!-- /.col-md-12 -->
			</div><!-- /.row -->
			
			<?php include_once('inc/postpanel.php'); ?>

<?php
delete_post($_GET['post_delete']);
post_visibility($_GET['post_visibility'], $_GET['visibility']);
delete_comment($_GET['comment_delete']);

// Begin: pagination setting
$per_page = 20;
$pages_query = $db->query("SELECT id FROM gwp_posts WHERE `type` = 'branch_post' AND CASE WHEN `val_1` = '$user_id' THEN `status` IN (1,2) ELSE `status` = '1' END AND `val_2` = '$page_id' ORDER BY datetime");
$count_row = $pages_query->rowCount();
$pages = ceil($count_row / $per_page );
$page = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;
// if $_GET['page'] is changed in url as over-number, set it to 1;
if(isset($_GET['page'])) { if($_GET['page']>$pages || $_GET['page']<1) {$page = 1;}}
$start  =   ($page - 1 ) * $per_page;
// End: pagination setting

$post_query = $db->query("SELECT * FROM gwp_posts WHERE `type` = 'branch_post' AND CASE WHEN `val_1` = '$user_id' THEN `status` IN (1,2) ELSE `status` = '1' END AND `val_2` = '$page_id' ORDER BY datetime DESC LIMIT $start, $per_page");
$gallery_no = 0;
while ($post = $post_query->fetch(PDO::FETCH_OBJ)) {
$gallery_no++;
$post_id 			= $post->id;
$post_sender 		= $post->val_1;
$sender_name 		= get_an_user_fullname($post_sender, "title");
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

            <div class="col-md-12 col-sm-10 col-xs-12 commentBoxList js-commentsBoxlist">

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

<?php } ?>

			<div class="row">

				<div class="col-md-2 hidden-xs">&nbsp;</div>
				<div class="col-md-10 col-xs-12">
					<ul class="pagination">
<?php
// pagination nav bar
$btn = '';
if($pages >= 1 && $page<=$pages ) {

	$startPoint = 1;
	$limitPoint = $pages >=2 ? 8 : 1;
	
	if($page > 7) {
		
		$startPoint = $page - 1;
		$limitPoint = 8 + $startPoint;
		if($limitPoint>$pages) {
			$startPoint = $pages - 8;
			$limitPoint = $pages;
		}
	}
	
    for($x=$startPoint; $x<=$limitPoint; $x++) {
        echo ($x == $page) ? '<li><a class="active" href="'.url().'&page='.$x.'">'.$x.'</a></li>' : '<li><a href="'.url().'&page='.$x.'">'.$x.'</a></li>';
    }

} ?>
					</ul>
				</div>

			</div><!-- /.row -->


		</div><!-- /.col-md-8 -->

		<?php include_once('inc/sidebar.php'); ?>

	</div><!-- /.row -->

</div><!-- /.container -->

<?php include_once("inc/footer.php"); ?>