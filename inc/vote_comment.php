<?php
include_once("db.php");

$commentId = $_POST['commentId'];

if(empty($commentId) || mb_strlen($commentId) >=12) {
	echo 'error';
} 
else 
{

	$query = $db->query("SELECT `post_id`, `val_8`, `val_9`, `val_int` FROM `gwp_post_meta` WHERE `id` = '$commentId'");
	$obj = $query->fetch();
	$post_owner = $obj->val_9;
	$comment_sender = $obj->val_int;
	if($obj->val_8 != 1)
	{
		$post_id = $obj->post_id;
		$db->query("UPDATE `gwp_post_meta` SET `val_8` = '0'  WHERE `post_id` = '$post_id'");
		$done = $query = $db->query("UPDATE `gwp_post_meta` SET `val_8` = '1'  WHERE `id` = '$commentId'");
		if($done)
		{
			// if voting is successfull, insert some values to notificatio to post owner
			$date = date("Y-m-d H:i:s");
			$db->query("INSERT INTO `gwp_notifications` 
						SET `datetime` = '$date', 
							`type` = 'vote',
							`val_1` = '$post_id',
							`val_2` = '$comment_sender',
							`val_3` = '$post_owner'");
			echo 1;
		}
	}
	else
	{
		$query = $db->prepare("UPDATE `gwp_post_meta` SET `val_8` = :val_8  WHERE `id` = :id ");
		$query->bindValue("val_8", 0);
		$query->bindValue("id", $commentId, PDO::PARAM_INT);
		$done = $query->execute();
		if($done)
		{
			echo 0;
		}
	}

}
?>