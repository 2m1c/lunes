<?php
include_once("db.php");
include_once("functions.php");

/*
	* Following codes fetches data from database via ajax post
	* When user types, it posts 2 variable.
	* $field means which table will be used for searching
	* $data means what user is looking for.
	* if typed value is euqals to and more than 3,
	* ajax post will be executed.
*/


$field 	= $_POST['searchField'];
$data 	= $_POST['keyUpData'];

if(mb_strlen($data) >= 3)
{
	if($field == "header")
	{
		$query = $db->query("SELECT gwp_users.id, gwp_users.type, gwp_users.name AS res FROM gwp_users WHERE gwp_users.name LIKE '%$data%' OR gwp_users.surname LIKE '%$data%'
							UNION SELECT gwp_posts.id, gwp_posts.type, gwp_posts.content AS res FROM gwp_posts WHERE gwp_posts.content LIKE '%$data%' AND gwp_posts.status != '2' AND gwp_posts.status != '0'
							UNION SELECT gwp_posts.id, gwp_posts.type, gwp_posts.val_text AS res FROM gwp_posts WHERE gwp_posts.val_text LIKE '%$data%' AND gwp_posts.status != '2' AND gwp_posts.status != '0'
							LIMIT 0, 15");
        if($query->rowCount() == 0) {
            echo 'Sonuç Bulunamadı';
        } else {
            while ($obj = $query->fetch(PDO::FETCH_OBJ)) {
                $full_issue     = $obj->res;
                $searched_part  = $data;
                $truncated      = explode($searched_part, $full_issue);
                if($obj->type == "user") { echo '<a href="profile.php?url=general&profile_id='.$obj->id.'"> <img src="'.get_an_user_image($obj->id).'" width="32" /> '.get_an_user_fullname($obj->id).'</a>'; }
                else if($obj->type == "branch_post" || $obj->type == "archive_post") { echo '<a href="post.php?id='.$obj->id.'">'.$data.' '.$truncated[1].'</a>'; }
            }
        }
	} 
	else if($field == "branch") 
	{
		$query = $db->prepare("SELECT url, title FROM qwp_category WHERE `title` LIKE :title AND `type` = :type ORDER BY `title` ASC LIMIT 0, 3");
		$query->bindValue(":title", "%".$data."%");
		$query->bindValue(":type", "branch");
		$query->execute();

		while ($obj = $query->fetch(PDO::FETCH_OBJ)) { ?>
			<a role="menuitem" tabindex="-1" href="field.php?url=<?php echo $obj->url; ?>"><?php echo $obj->title; ?></a>
		<?php }
	}
}
?>