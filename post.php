<?php include_once("inc/head.php"); ?>
<body>
<?php include_once("inc/header.php"); ?>
<?php
$page_id = $_GET['id'];

$post_query = $db->query("SELECT * FROM gwp_posts WHERE CASE WHEN `val_1` = '$user_id' THEN `status` IN (1,2) ELSE `status` = '1' END AND `type` = 'branch_post' AND id = '$page_id'");
$post = $post_query->fetch();
$gallery_no = 1;
$post_id 			= $post->id;
$post_sender 		= $post->val_1;
$sender_name 		= get_an_user_fullname($post->val_1, "title");
$sender_image 		= get_an_user_image($post->val_1);
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


<div class="container wall">

	<div class="row">
		
		<div class="col-md-8 col-xs-12 wallFlow">

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

                    <div class="col-md-12 col-xs-12 commentBoxList js-commentsBoxlist">

                        <div class="js-comments">
                            <?php
                            //fetch comments
                            echo get_post_comments($post_id, true);
                            ?>
                        </div>

                        <div class="row marginTop10">

                            <div class="col-md-12 col-sm-12 col-xs-12 commentPlace">
                                <img src="<?php echo get_current_user_image(); ?>" />
                                <textarea data-post="<?php echo $post_id; ?>" data-postowner="<?php echo $post_sender; ?>" class="form-control js-post-comment" placeholder="yorumunuzu göndermek için enter tuşuna basınız"></textarea>
                            </div><!-- /.col-md-2 -->

                        </div><!-- /.row (commentPlace) -->

                    </div><!-- /.col-md-10 -->

                </div><!-- /.row -->


            </section><!-- issuePanel -->

		</div><!-- /.col-md-8 -->

		<?php include_once('inc/sidebar.php'); ?>

	</div><!-- /.row -->

</div><!-- /.container -->

<?php include_once("inc/footer.php"); ?>