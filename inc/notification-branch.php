<?php
header('Content-type: text/javascript');
include_once("db.php");
include_once("functions.php");
/*
	-- instant branch notificaiton --
*/

$json = array(
	'html'		=>	''
);

// get branches which are followed simultaneously by the user
$user_id 		= $_COOKIE['user_id'];
$branch_query = $db->query("SELECT `val_1` FROM gwp_user_meta WHERE `post_id` = '$user_id' AND `group` = 'instant_follow' AND `key` = 'instant_follow'");
$branch_arr = array();
while ($branch_obj = $branch_query->fetch(PDO::FETCH_OBJ)) {
	array_push($branch_arr, $branch_obj->val_1);
}

date_default_timezone_set('Europe/Istanbul');
$date = date("Y-m-d H:i:s", time() - 60);

$html = '';
$query = $db->query("SELECT `id`, `val_1`, `val_2` FROM `gwp_posts` WHERE `datetime` > '$date' AND `val_1` != '$user_id' AND `val_2` IN (".implode(',', $branch_arr).")");
$json['count'] = $date;
if($query == true) {
	while ($obj = $query->fetch(PDO::FETCH_OBJ)) {
	
		$post_owner = get_an_user_fullname($obj->val_1, 'title');
		$branch 	= $obj->val_2;
		$post_id 	= $obj->id;
		$html 		.= '<a class="notificationPopup js-notification-popup" href="post.php?id='.$post_id.'">';
		$html 		.= '<span class="name">'.$post_owner.'</span>';
		$html 		.= '<span class="branch"> '.get_branch_name_by_id($branch).'</span> <br />';
		$html 		.= '<span class="text">alanına bir gönderide bulundu!</span>';
		$html 		.= '<button>X</button>';
		$html 		.= '</a>';
	}
	$json['html'] = $html;
	
}

echo json_encode($json);
?>