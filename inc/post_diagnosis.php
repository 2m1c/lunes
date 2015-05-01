<?php
include_once("db.php");
include_once("functions.php");

$commentData 	= $_POST['commentData'];
$postId 		= $_POST['postId'];
$date 			= date("Y-m-d H:i:s");

if(empty($commentData))
{
    echo 0;
}
else
{
	// Input has some text and is not empty. 
	$query = $db->prepare("UPDATE `gwp_posts`
							SET `val_text` = :val_text
							WHERE `id` = :id");
	$query->bindValue(":val_text", $commentData);
	$query->bindValue(":id", $postId);
	
	$done = $query->execute();
	if($done)
	{
		echo $commentData;
	}
	else
	{
		echo 0;
	}
}
?>