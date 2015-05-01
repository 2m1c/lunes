<?php
include_once("db.php");
include_once("functions.php");
/*
	-- count notification amount --
	pre-information for databse stucture
	status = 0 	=> notification has not read yet
	type 		=> notification type.
					-> if type is comment, it means a person commented to a post
					-> if type is vote, it means a post owner voted for one comment
	val_1  		=> subject. (it can be post-id, comment-id. subject of the notification)
	val_2		=> receiver. (the person who will get the notificaiton)
	val_3 		=> sender. (the person who sent notification to receiver. the person who commented or voted)
*/

$user_id 		= $_COOKIE['user_id'];
$query 			= $db->query("SELECT `id`, `datetime`, `type`, `val_1`, `val_3` FROM `gwp_notifications` WHERE `val_2` = '$user_id' ORDER BY id DESC LIMIT 0, 4");
while ($obj = $query->fetch(PDO::FETCH_OBJ)) {

$text 	= '';
if($obj->type == 'comment') {
	$text = 'gönderinize cevap verdi';
} else if($obj->type == 'vote') {
	$text = 'yorumunuzu onayladı';
} else if ($obj->type == 'new_post') {
	$text = 'yeni gönderi';
} else if ($obj->type == 'like_comment') {
    $text = 'yorumunuzu beğendi';
}

$url 	= ( $obj->type = 'comment' ? 'post.php?id='.$obj->val_1 : 'post.php?id='.$obj->val_1 );

?>
	<a role="menuitem" tabindex="-1" href="<?php echo $url; ?>"><span class="poster"><?php echo get_an_user_fullname($obj->val_3, 'title'); ?></span> <br /> <?php echo $text; ?>&nbsp;<span class="postTime"><?php echo time_since_post($obj->datetime); ?> önce</span></a>
<?php } ?>
	<a role="menuitem" tabindex="-1" href="profile.php?url=notification&profile_id=<?php echo $user_id; ?>" class="showAll">Tüm Bildirimleri Göster</a>
<?php
$db->query("UPDATE `gwp_notifications` SET `status` = '1' WHERE `type` IN ('comment', 'vote', 'like_comment') AND `val_2` = '$user_id'");
?>

