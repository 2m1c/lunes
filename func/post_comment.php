<?php
include_once("../inc/db.php");
include_once("../inc/functions.php");

$commentData 	= $_POST['commentData'];
$postId 		= $_POST['postId'];
$postOwner 		= $_POST['postOwner'];

$user_id 		= isset($_COOKIE['user_id']) ? $_COOKIE['user_id'] : 0;
$date 			= date("Y-m-d H:i:s");

if( $user_id == 0 )
{
	echo 'no-auth';
	die;
	exit;
}

if(empty($commentData))
{
    echo 0;
}
else
{
	// Input has some text and is not empty. 
	$query = $db->prepare("INSERT INTO `gwp_post_meta`
						SET `post_id` = :post_id,
							`status` = :status,
							`group` = :group,
							`key` = :key,
							`val_8` = :val_8,
							`val_9` = :val_9,
							`val_int` = :val_int,
							`val_date` = :val_date,
							`val_text`= :val_text");
	$query->bindValue(":post_id", $postId, PDO::PARAM_INT);
	$query->bindValue(":status", 1, PDO::PARAM_INT);
	$query->bindValue(":group", "comment", PDO::PARAM_STR);
	$query->bindValue(":key", "comment", PDO::PARAM_STR);
	$query->bindValue(":val_8", 0); // comment vote
	$query->bindValue(":val_9", $postOwner);
	$query->bindValue(":val_int", $user_id, PDO::PARAM_INT); //comment sender
	$query->bindValue(":val_date", $date);
	$query->bindValue(":val_text", $commentData, PDO::PARAM_STR);
	$done = $query->execute();
	if($done)
	{
		// if comment insert is successfull, insert some values to notification to post owner
		// if user id and post owner is same person do not insert
		if($user_id != $postOwner) {
			$db->query("INSERT INTO `gwp_notifications` 
						SET `datetime` = '$date', 
							`type` = 'comment',
							`val_1` = '$postId',
							`val_2` = '$postOwner', 
							`val_3` = '$user_id'");
		}
		echo get_post_comments($postId);
	}
	else
	{
		echo 0;
	}
}