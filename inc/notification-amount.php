<?php
include_once("db.php");
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
	val_4		=> extra value
					-> if type is new_post, it means branch id
*/

/*
	-> count new regular notifications where type is comment or vote and status is 0
*/
$user_id 		= $_COOKIE['user_id'];
$query 			= $db->query("SELECT `id` FROM `gwp_notifications` WHERE `type` IN ('comment', 'vote', 'like_comment') AND `status` = '0' AND `val_2` = '$user_id'");
echo $query->rowCount();
?>