<?php
include_once("db.php");
/*
	* following function in order to follow or unfollow a branch
	* when user click on button, it post id of the branch via ajax
	* By using posted branch id and $user_id,
	* it checks if user following or unfollowing the selected branch
	* if user is not following the branch, it inserts data into gwp_user_meta
	* if user is following, it deletes related data from gwp_user_meta
*/
$branch_data 	= $_POST['branch_data'];
$user_id 		= $_COOKIE['user_id'];
$date 			= date('Y-m-d H:i:s');

// first check if $branch_data is avaliable in qwp_category
$confirm 		= $db->prepare("SELECT id FROM `qwp_category` WHERE `id` = :id AND `type` = :branch");
$confirm->bindValue(":id", $branch_data);
$confirm->bindValue(":branch", 'branch');
$confirm->execute();

$count = $confirm->rowCount();

if($count == 0)
{
	return false;
}
else
{
	// check if user following or not following the branch
	$query 			= $db->prepare("SELECT id FROM gwp_user_meta 
								WHERE `post_id` = :post_id
								AND `group` = :group
								AND `key` = :key
								AND `val_1` = :val_1");
	$query->bindValue(":post_id", $user_id, PDO::PARAM_INT);
	$query->bindValue(":group", "branch_follow");
	$query->bindValue(":key", "branch_follow");
	$query->bindValue(":val_1", $branch_data);
	$query->execute();

	$count = $query->rowCount();

	// if user is not following, let the user follow by inserting related data
	if($count == 0)
	{
	$insert 		= $db->prepare("INSERT INTO gwp_user_meta
									SET `post_id` = :post_id,
										`group` = :group,
										`key` = :key,
										`val_1` = :val_1,
										`val_2` = :val_2,
										`val_int` = :val_int,
										`val_date` = :val_date");

	$insert->bindValue(":post_id", $user_id, PDO::PARAM_INT);
	$insert->bindValue(":group", "branch_follow");
	$insert->bindValue(":key", "branch_follow");
	$insert->bindValue(":val_1", $branch_data);
	$insert->bindValue(":val_2", 0); // if 1, user will get notification for every post
	$insert->bindValue(":val_int", $branch_data);
	$insert->bindValue(":val_date", $date);
	$done = $insert->execute();
	echo 1; // this valuse goes to javascirpt
	}// if user is following, let the user unfollow by deleting related data
	else if($count >= 1) 
	{
	$delete 		= $db->prepare("DELETE FROM gwp_user_meta
						WHERE `post_id` = :post_id
								AND `group` = :group
								AND `key` = :key
								AND `val_1` = :val_1");

	$delete->bindValue(":post_id", $user_id, PDO::PARAM_INT);
	$delete->bindValue(":group", "branch_follow");
	$delete->bindValue(":key", "branch_follow");
	$delete->bindValue(":val_1", $branch_data);
	$delete->execute();
	echo 2; // this valuse goes to javascirpt
	}
}
?>