<?php
// logut cobdition
if( isset($_GET['logout']) )
{
	if($_GET['logout'] == "true")
	{
		setcookie("user_id", "1", time() - 3600*24);
		header("location: ./");
		exit();
	}
}
// if user is logged in, allow to execute following functions
$user_id = 0;
if( isset($_COOKIE['user_id']) )
{
	$user_id = $_COOKIE['user_id'];
}
	// sef url
	function seo($s) {
		$tr = array('ş','Ş','ı','I','İ','ğ','Ğ','ü','Ü','ö','Ö','Ç','ç','(',')','/',':',',');
		$eng = array('s','s','i','i','i','g','g','u','u','o','o','c','c','','','-','-','');
		$s = str_replace($tr,$eng,$s);
		$s = strtolower($s);
		$s = preg_replace('/[^A-Za-z0-9\-]/', '-', $s);
		$s = preg_replace('/&amp;amp;amp;amp;amp;amp;amp;amp;amp;.+?;/', '', $s);
		$s = preg_replace('/\s+/', '-', $s);
		$s = preg_replace('|-+|', '-', $s);
		$s = preg_replace('/#/', '', $s);
		$s = str_replace('.', '', $s);
		$s = trim($s, '-');
		return $s;
	}

	/* 
		* orginize url
		* it is used for paginations
	*/
	function url()
	{
		$deger = $_SERVER["REQUEST_URI"];
		$deger2 = explode('&page',$deger);
		$deger = $deger2[0];
	
		return $url = "http://$_SERVER[HTTP_HOST]".$deger;
	}

	/*
		* select one row from specified table
	*/
	function select_query($data, $table, $where, $case=false) {

		global $db;
		$select_query = $db->query("SELECT $data FROM $table WHERE $where");
		if( $select_query->rowCount() > 0 )
		{
			$query = $select_query->fetch();
			if($case == "upper")
			{
				$obj = $query->$data;
				return mb_convert_case($obj, MB_CASE_UPPER, "UTF-8");
			} elseif($case == "title")
			{
				$obj = $query->$data;
				return mb_convert_case($obj, MB_CASE_TITLE, "UTF-8");
			}
			elseif($case == "lower")
			{
				$obj = $query->$data;
				return mb_convert_case($obj, MB_CASE_LOWER, "UTF-8");
			}
			else
			{
				return $query->$data;
			}
		}
		else
		{
			return false;
		}
	}

	/* 
		* query page url from database
		* if it is true
	*/
	function page_url_confirm($parameter)
	{
		global $db;
		$page_url 			= $parameter['url'];
		$page_default_url 	= $parameter['default_url'];
		$table_name			= $parameter['table_name'];
		$where 				= $parameter['where'];
		$page_url 			= ( !empty($page_url) ? $page_url : $page_default_url );
		$query 				= $db->query("SELECT id,url FROM $table_name WHERE $where");
		
		if($query->rowCount() == 1 )
		{
			$obj = $query->fetch();
			return $obj->id;
		}
		else
		{
			return false;
		}
	}

	function get_folder_name_by_id($param)
	{
		global $db;
		$folder_id 	= $param;
		$query 		= $db->query("SELECT val_text FROM `gwp_user_meta` WHERE `group` = 'folder' AND `key` = 'folder' AND `id` = '$param'");
		$obj = $query->fetch();
		return $obj->val_text;
	}

	// select current user
	function get_current_users($case=false)
	{
		global $db;
		$user_id = $_COOKIE['user_id'];
		$query = $db->query("SELECT name,surname FROM gwp_users WHERE id = '$user_id'");
		$obj = $query->fetch();
		$result = $obj->name." ".$obj->surname;
		if($case == "upper")
		{
			$result = $obj->name." ".$obj->surname;
			return mb_convert_case($result, MB_CASE_UPPER, "UTF-8");
		} elseif($case == "title")
		{
			$result = $obj->name." ".$obj->surname;
			return mb_convert_case($result, MB_CASE_TITLE, "UTF-8");
		}
		elseif($case == "lower")
		{
			$result = $obj->name." ".$obj->surname;
			return mb_convert_case($result, MB_CASE_LOWER, "UTF-8");
		}
		else
		{
			return $result = $obj->name." ".$obj->surname;
		}
	}

	function get_current_user_image()
	{
		global $db;
		$user_id = isset($_COOKIE['user_id']) ? $_COOKIE['user_id'] : 0;
		$query = $db->query("SELECT val_text FROM gwp_user_meta WHERE `group` = 'profile' AND `post_id` = '$user_id'");
		$obj = $query->fetch();
		$img = $obj->val_text;
		if($img == false) 
		{
			return "img/doctor.jpg";
		}
		else 
		{
			return $img;
		}
	}

	// select an user's full name
	function get_an_user_fullname($id, $case=false)
	{
		global $db;
		$user_id = isset($_COOKIE['user_id']) ? $_COOKIE['user_id'] : 0;
		$query = $db->query("SELECT name,surname FROM gwp_users WHERE id = '$id'");
		if( $query->rowCount() > 0 )
		{
			$obj = $query->fetch();
			$result = $obj->name." ".$obj->surname;
			if($case == "upper")
			{
				$result = $obj->name." ".$obj->surname;
				return mb_convert_case($result, MB_CASE_UPPER, "UTF-8");
			} elseif($case == "title")
			{
				$result = $obj->name." ".$obj->surname;
				return mb_convert_case($result, MB_CASE_TITLE, "UTF-8");
			}
			elseif($case == "lower")
			{
				$result = $obj->name." ".$obj->surname;
				return mb_convert_case($result, MB_CASE_LOWER, "UTF-8");
			}
			else
			{
				return $result = $obj->name." ".$obj->surname;
			}
		}
		else
		{
			return false;
		}
	}

	// get a user's image
	function get_an_user_image($id)
	{
		global $db;
		$query = $db->query("SELECT val_text FROM gwp_user_meta WHERE `group` = 'profile' AND `post_id` = '$id'");
		$obj = $query->fetch();
		// if image is absent return false else fetch image
		if($obj == false) {
			return 'img/doctor.jpg';
		} else {
			return $obj->val_text;
		}
	}

	function get_an_user_issue_amount($id)
	{
		global $db;
		$query = $db->query("SELECT id FROM `gwp_posts` WHERE `status` = '1' AND `val_1` = '$id'");
		return $query->rowCount();
	}

    function get_an_user_archived_post($param_a, $param_b) {
        global $db;
        $query = $db->query("SELECT `val_2` FROM `gwp_user_meta` WHERE `post_id` = '$param_b' AND `group` = 'archived_post' AND `val_1` = '$param_a'");
        if($query->rowCount() > 0) {
            $obj = $query->fetch();
            $file_id = $obj->val_2;
            return $file_id;
        }
        else
        {
            return false;
        }
    }

	function truncate_string($string, $maxlen, $dots = "...")
	{

		if(mb_strlen($string) >= $maxlen )
		{
			return (strlen($string) > $maxlen) ? substr($string, 0, $maxlen - strlen($dots)) . $dots : $string;
		}

	}

    /**
     * remove a post from archive
     * @param $get_parameter
     * @return bool
     */
    function remove_post_from_archive($get_parameter)
    {
        if(isset($get_parameter) && !empty($get_parameter))
        {
            global $db;
            $user_id= $_COOKIE['user_id'];
            $delete = $db->query("DELETE FROM `gwp_user_meta` WHERE `group` = 'archived_post' AND `key` = 'archived_post' AND `post_id` = '$user_id' AND `val_1` = '$get_parameter'");
            if($delete) {
                header('Location: ./');
                return true;
            }
            else
            {
                return false;
            }
        }
    }

    /**
     * delete archive
     * first delete archive then delete post id from gwp_user_meta
     * @param $get_parameter
     * @return bool
     */
    function delete_archive($get_parameter)
    {
        if(isset($get_parameter) && !empty($get_parameter))
        {
            global $db;
            $user_id= $_COOKIE['user_id'];
            $delete = $db->query("DELETE FROM `gwp_user_meta` WHERE `group` = 'folder' AND `key` = 'folder' AND `post_id` = '$user_id' AND `id` = '$get_parameter'");
            $db->query("DELETE FROM `gwp_user_meta` WHERE `group` = 'archived_post' AND `key` = 'archived_post' AND `post_id` = '$user_id' AND `val_2` = '$get_parameter'");
            if($delete) {
                header('Location: ./');
                return true;
            }
            else
            {
                return false;
            }
        }
    }

	/*
		Delete post function does not delete any post as supposed.
		This function update post status value from 1 to 0.
		If post status value is 0, users cannot see that post
		but it is still avaliable in database.
		if current user ($user_id) equals to post sender id, it executes code
	*/
	function delete_post($get_parameter)
	{
		if(isset($get_parameter) && !empty($get_parameter))
		{
			global $db;
			$user_id= $_COOKIE['user_id'];
			$query 	= $db->query("SELECT `val_1` FROM `gwp_posts` WHERE `id` = '$get_parameter' AND `type` = 'branch_post'");
			$obj 	= $query->fetch();
			if($obj->val_1 == $user_id)
			{
				$update = $db->query("UPDATE `gwp_posts` SET `status` = '0' WHERE `id` = '$get_parameter' AND `type` = 'branch_post'");
				$db->query("UPDATE `gwp_post_meta` SET `status` = '0' WHERE `post_id` = '$get_parameter' AND `group` = 'comment' AND `key` = 'comment'");
				if($update)
				{
					header('Location:'.$_SERVER['HTTP_REFERER']);
					return true;
				}
			}
			else
			{
				return false;
			}
		}
	}

	/*
		post_visibility function is hiding the post or from all user except the post owner
		or display post to the users
		This function update post status value as 2 to 1 and 1 to 2.
		if status 1, all users can see the post
		if post is 2, only post owner can see the post
		if current user ($user_id) equals to post sender id, it executes code
	*/
	function post_visibility($get_parameter, $visibility)
	{
		if(isset($get_parameter) && !empty($get_parameter))
		{
			global $db;
			$user_id= $_COOKIE['user_id'];
			$query 	= $db->query("SELECT `val_1` FROM `gwp_posts` WHERE `id` = '$get_parameter' AND `type` = 'branch_post'");
			$obj 	= $query->fetch();
			if($obj->val_1 == $user_id)
			{
				$update = $db->query("UPDATE `gwp_posts` SET `status` = '$visibility' WHERE `id` = '$get_parameter' AND `type` = 'branch_post'");
				$db->query("UPDATE `gwp_post_meta` SET `status` = '$visibility' WHERE `post_id` = '$get_parameter' AND `group` = 'comment' AND `key` = 'comment'");
				if($update)
				{
					header('Location:'.$_SERVER['HTTP_REFERER']);
					return true;
				}
			}
			else
			{
				return false;
			}
		}
	}

	/*
		Delete comment function does not delete any comment as supposed.
		This function update comment status value from 1 to 0.
		If comment status value is 0, users cannot see that comment
		but it is still avaliable in database.
		if current user ($user_id) equals to comment sender id, it executes code
	*/
	function delete_comment($get_parameter)
	{
		if(isset($get_parameter) && !empty($get_parameter))
		{
			global $db;
			$user_id= $_COOKIE['user_id'];
			$query 	= $db->query("SELECT `val_int` FROM `gwp_post_meta` WHERE `id` = '$get_parameter' AND `group` = 'comment' AND `key` = 'comment'");
			$obj 	= $query->fetch();
			if($obj->val_int == $user_id)
			{
				$update = $db->query("UPDATE `gwp_post_meta` SET `status` = '0' WHERE `id` = '$get_parameter' AND `group` = 'comment' AND `key` = 'comment'");
				if($update)
				{
					header('Location:'.$_SERVER['HTTP_REFERER']);
					return true;
				}
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}

	function time_since_post($time)
	{
		/*
			* credits : http://stackoverflow.com/questions/113829/how-to-convert-date-to-timestamp-in-php
		*/
		$time = strtotime($time);
	    $time = time() - $time; // to get the time since that moment
	    $tokens = array (
	        31536000 => 'yıl',
	        2592000 => 'ay',
	        604800 => 'hafta',
	        86400 => 'gün',
	        3600 => 'saat',
	        60 => 'dakika',
	        1 => 'saniye'
	    );

	    foreach ($tokens as $unit => $text) {
	        if ($time < $unit) continue;
	        $numberOfUnits = floor($time / $unit);
	        return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'':'');
	    }
	}

	// get total comment amount belongs to post
	function get_post_comment_amount($post_id)
	{
		global $db;
		$query = $db->query("SELECT id FROM `gwp_post_meta` WHERE `post_id` = '$post_id' AND `status` = '1' AND `group` = 'comment' AND `key` = 'comment'");
		$count = $query->rowCount();
		return $count;
	}

	// fetch comments belong to $post_id
	// if fullComments parameter is not false, fetch all comments if not fetch only last five
	function get_post_comments($post_id, $fullComments=false)
	{
		global $db;
		$user_id = isset($_COOKIE['user_id']) ? $_COOKIE['user_id'] : 0;
		$comment = '';

		if( $fullComments != false )
		{
			$query = $db->query("SELECT id, val_8, val_9, val_int, val_text, val_date FROM `gwp_post_meta` 
								WHERE `post_id` = '$post_id' AND `status` = '1' AND `group` = 'comment' AND `key` = 'comment'
								ORDER BY id ASC");
		}
		else
		{

			$query = $db->query("SELECT id, val_8, val_9, val_int, val_text, val_date FROM (SELECT id, val_8, val_9, val_int, val_text, val_date FROM `gwp_post_meta` 
								WHERE `post_id` = '$post_id' AND `status` = '1' AND `group` = 'comment' AND `key` = 'comment'
								ORDER BY id DESC
								LIMIT 0, 5) As AliasName ORDER BY id ASC");
			$count = get_post_comment_amount($post_id);
			
			if($count > 5) {
				$count -= 5;
				$comment .= '<a href="post.php?id='.$post_id.'" class="link2oldComments">Diğer <strong>'.$count.'</strong> yorumu görmek için tıklayın</a>';
			}
		}
		while($obj = $query->fetch(PDO::FETCH_OBJ))
		{
			$comment_id 		= $obj->id;
			$commenter_img 		= get_an_user_image($obj->val_int);
            $commenter_title    = select_query("userbranch", "gwp_users", "id = $obj->val_int", "title").' '.select_query("usertitle", "gwp_users", "id = $obj->val_int", "title");
			$commenter_name 	= get_an_user_fullname($obj->val_int, "title");
			$time_since_post 	= time_since_post($obj->val_date);

			$comment_delete = ($obj->val_int == $user_id ? '<a onClick="return confirm('."'Bu yorumu silmek istediğinizden emin misiniz?'".')" href="delete_comment.php?comment_delete='.$comment_id.'" class="deleteComment">X</a>' : '');
			$star_color 	= ($obj->val_8 == 1 ? 'yellow' : 'dark');
			$comment_star 	= ($obj->val_9 == $user_id ? '<a href="#" role="button" class="js-vote commentStar" data-id="'.$comment_id.'"><i class="fa fa-star '.$star_color.'"></i></a>' :  ($obj->val_8 == 1 ? '<a href="" role="button" class="commentStar" data-id="'.$comment_id.'"><i class="fa fa-star '.$star_color.'"></i></a>' : '' ) );

            $like_count     = $db->query("SELECT `id`, `val_int` FROM `gwp_post_meta` WHERE `group` = 'like' AND `key` = 'comment_like' AND `post_id` = '$comment_id'")->rowCount();
            $like_status    = $db->query("SELECT `id` FROM `gwp_post_meta` WHERE `group` = 'like' AND `key` = 'comment_like' AND `post_id` = '$comment_id' AND `val_int` = '$user_id'")->rowCount();
            $like           = ($like_status != 0 ? '<a href="#" role="button" class="like" data-type="comment" data-id="'.$comment_id.'" data-post="'.$post_id.'" data-object="'.$obj->val_int.'""><i class="fa fa-thumbs-up"></i>&nbsp;Beğendin <span>'.$like_count.' Kişi Beğendi</span></a>' : '<a href="#" role="button" class="like js-like" data-type="comment" data-id="'.$comment_id.'" data-post="'.$post_id.'" data-object="'.$obj->val_int.'""><i class="fa fa-thumbs-up"></i>&nbsp;Beğen <span>'.$like_count.' Kişi Beğendi</span></a>');

			$comment .= '<div class="row commentBox">';
			$comment .= '<a href="profile.php?url=general&profile_id='.$obj->val_int.'" class="col-md-2 col-xs-2"><img src="'.$commenter_img.'" class="img-responsive" /></a>';
			$comment .= '<div class="col-md-10 col-xs-10">';
			$comment .= '<a href="profile.php?url=general&profile_id='.$obj->val_int.'" class="profile">'.$commenter_title.' '.$commenter_name.'</a>';
			$comment .= $comment_delete;
			// Yorumu yıldızlayarak onaylama kaldırıldı fakat sistem çalışır durumda. Kulalncıya tekrar göstermek için yorumu kaldır
            //$comment .= $comment_star;
			$comment .= '<div class="commentContent"><p>'.$obj->val_text.'</p></div>';
			$comment .= '<footer>';
            $comment .=  $like;
            $comment .= '<span class="data pull-right">'.$time_since_post.' Önce</span>';
            $comment .= '</footer>';
			$comment .= '</div>'; //end: /.col-md-10
			$comment .= '</div>'; //end: /.row (commentBox)
		}
		return $comment;
	}


	// if user following branh or not
	function following_status($id)
	{
		global $db;
		$user_id = $_COOKIE['user_id'];

		$query = $db->query("SELECT id FROM gwp_user_meta 
							WHERE `post_id` = '$user_id' 
							AND `val_1` = '$id'
							AND `group` = 'branch_follow'
							AND `key` = 'branch_follow'");

		$count = $query->rowCount();

		if($count == 0)
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	function get_branch_name_by_id($param) {
		global $db;
		$query = $db->query("SELECT * FROM `qwp_category` WHERE `type` = 'branch' AND `id` = '$param'");
		$obj = $query->fetch();
		return $obj->title;
	}

	// get branch has how many follower 
	function get_branch_follower_amount($id)
	{
		global $db;
		$query = $db->query("SELECT id FROM gwp_user_meta WHERE `val_1` = '$id' AND `group` = 'branch_follow' AND `key` = 'branch_follow'");
		$count = $query->rowCount();
		return $count;
	}

	// get total created issue in a branch
	function get_total_issue_amount_in_brach($id)
	{
		global $db;
		$query = $db->query("SELECT id FROM `gwp_posts` WHERE `status` = '1' AND `val_2` = '$id'");
		return $query->rowCount();
	}

	// get user's following branches
	function get_user_following_branches($data)
	{
		global $db;
		$query = $db->query("SELECT val_1 FROM gwp_user_meta WHERE `post_id` = '$data' AND `group` = 'branch_follow' AND `key` = 'branch_follow'");
		return $query;
	}

	// get user's following branch amount
	function get_user_following_branch_amount($data)
	{
		global $db;
		$query = $db->query("SELECT id FROM gwp_user_meta WHERE `post_id` = '$data' AND `group` = 'branch_follow' AND `key` = 'branch_follow'");
		return $query->rowCount();
	}

	// get user's instant following branches
	function get_user_instant_following_branch($user, $branch)
	{
		global $db;
		$query = $db->query("SELECT id FROM gwp_user_meta WHERE `post_id` = '$user' AND `val_1` = '$branch' AND `group` = 'instant_follow' AND `key` = 'instant_follow'");
		if($query->rowCount() >= 1)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	// get user's archive file name
	function get_user_archive_file_name($data)
	{
		global $db;
		$query = $db->query("SELECT id, val_text FROM gwp_user_meta WHERE `post_id` = '$data' AND `group` = 'folder' AND `key` = 'folder'");
		$count = $query->rowCount();
        if($count == 0) {
            return false;
        } else {
            return $query;
        }
	}

	
	
	//single image upload
	function single_image_upload($parameter)
	{
		$resim 		= $parameter['file'];
		$width 		= ( isset($parameter['width']) ? $parameter['width'] : '70' );
		$height 	= ( isset($parameter['height']) ? $parameter['height'] : '70' );
		$postId 	= $parameter['id'];
		$fileName 	= $parameter['filename'];
		$directory 	= $parameter['directory'];
		$tableName 	= $parameter['table_name'];
		$group 		= $parameter['group'];

		if( $resim["tmp_name"] !="" ) {

			global $db;
			$user_id 	= $_COOKIE['user_id'];
			// resim yolunu ayarla
		    $year 	= date('Y');
		    $month 	= date('m');
		    $day 	= date('d');
		    if(!is_dir("./uploads/$directory")){
		    	mkdir("./uploads/$directory", 0777);
		    }
		    if(!is_dir("./uploads/$directory/$year")){
		    	mkdir("./uploads/$directory/$year", 0777);
		    }
		    if(!is_dir("./uploads/$directory/$year/$month")){
		    	mkdir("./uploads/$directory/$year/$month", 0777);
		    }
		    if(!is_dir("./uploads/$directory/$year/$month/$day")){
		    	mkdir("./uploads/$directory/$year/$month/$day", 0777);
		    }
		    if(!is_dir("./uploads/$directory/$year/$month/$day/".$postId."")){
		    	mkdir("./uploads/$directory/$year/$month/$day/".$postId."", 0777);
		    }

		    $old_image = $db->query("SELECT val_text FROM $tableName WHERE `post_id` = '$postId' AND `group` = '$group'");
			if($old_image->rowCount() > 0)
			{
				$obj = $old_image->fetch();
				@unlink($obj->val_text);
				$db->query("DELETE FROM $tableName WHERE `post_id` = '$postId' AND `group` = '$group'");
			}
			
			
			$image = new upload($resim);
			
			if($image->uploaded) {

				if($group == "profile")
				{
					$image->file_new_name_body	= $fileName;
					$image->image_ratio_crop = true;
					$image->image_ratio_fill = true;
					$image->image_resize = true;
					$image->jpeg_quality = 100;
					$image->image_x = $width;
					$image->image_y = $height;
				}
				else if ($group == "post_img") {
					$image->file_new_name_body	= $fileName;
					
					$image->image_ratio_fill = true;
					$image->jpeg_quality = 60;

					if($image->image_src_x >= 1000) {
						$image->image_x = 1000;
					}
					if($image->image_src_y >= 1000) {
						$image->image_y = 1000;
					}
				}

				$image->process('./uploads/'.$directory.'/'.$year.'/'.$month.'/'.$day.'/'.$postId.'/');

				
				
				$extension 	= $image->file_src_name_ext;
				$src_type 	= $image->image_src_type;
				$path 		= str_replace("\\",'', $image->file_dst_path);
				$file_size 	= $image->file_src_size;
				$src_x 		= $image->image_src_x;
				$src_y 		= $image->image_src_y;
				$pathname 	= str_replace("\\",'', $image->file_dst_pathname);
				$now 		= date('Y-m-d H:i:s');
				$query = $db->query("INSERT INTO $tableName 
									SET `post_id` = '$postId',
										`group` = '$group',
										`key` 	= '$fileName',
										`val_1` = '$extension',
										`val_2` = '$src_type',
										`val_3` = '$path',
										`val_4` = '$file_size',
										`val_5` = '$src_x',
										`val_6` = '$src_y',
										`val_int` = '$user_id',
										`val_text` = '$pathname',
										`val_date` = '$now'");

			}
		}
	}


	/*Çoklu resim yükleme fonksiyonu*/
	// fileInput parametresi controllerdan gelen $_FILES[]
	// postId parametresi controllerdan gelen ilanın id'isi
	// fileName parametresi dosyanın adı
	function multiple_image_upload($fileInput, $postId, $fileName, $directory, $tableName, $group)
	{

		global $db;
		$user_id 	= $_COOKIE['user_id'];

		$files = $fileInput;

	    // verot class.upload çoklu resim yükleme işlemi
		$filename = $files;
		$dosyalar = array();
		foreach ($fileInput as $k_1 => $l_1) {
			foreach ($l_1 as $i_1 => $v_1) {
				if (!array_key_exists($i_1, $dosyalar))
					$dosyalar[$i_1] = array();
					$dosyalar[$i_1][$k_1] = $v_1;
			}
		}

		$inc = 0;
		$count_file = count( $dosyalar );
		foreach ($dosyalar as $i => $val){
			if( $i <= 9 )
			{	
				if($val["tmp_name"]=="") { continue; }
				$inc++;


				// resim yolunu ayarla
				$year 	= date('Y');
				$month 	= date('m');
				$day 	= date('d');
				if(!is_dir("./uploads/$directory")){
			    	mkdir("./uploads/$directory", 0777);
			    }
			    if(!is_dir("./uploads/$directory/$year")){
			    	mkdir("./uploads/$directory/$year", 0777);
			    }
			    if(!is_dir("./uploads/$directory/$year/$month")){
			    	mkdir("./uploads/$directory/$year/$month", 0777);
			    }
			    if(!is_dir("./uploads/$directory/$year/$month/$day")){
			    	mkdir("./uploads/$directory/$year/$month/$day", 0777);
			    }
			    if(!is_dir("./uploads/$directory/$year/$month/$day/".$postId."")){
			    	mkdir("./uploads/$directory/$year/$month/$day/".$postId."", 0777);
			    }

				// $fileName = dosyanın adı
				// $i = dosya adları aynı olmaması için $i değerinde ki rakamı alıyoruz

				$dosya_ad 	= $fileName.'-'.$inc;

				$file = new Upload($val);

				$img_quality = 80;
				
				if( $count_file >= 5 )
				{
					$img_quality = 60;
				}
				
				if ($file->uploaded) {
					// upload thumb
					$file->file_new_name_body = $dosya_ad.'_thumb';
					$file->image_ratio_crop = true;
					$file->image_ratio_fill = true;
					$file->image_resize  = true;
					$file->jpeg_quality = $img_quality;
					$file->image_x = 200;
					$file->image_y = 200;
					$file->allowed = array('application/pdf','application/msword', 'image/*');
	                $ext = $file->file_src_name_ext;
	                
					$file->process('./uploads/'.$directory.'/'.$year.'/'.$month.'/'.$day.'/'.$postId.'/');
	                
					// resmin orjinalini croplayarak upload ettir
					$file->file_new_name_body = $dosya_ad;
					$file->image_ratio_crop = true;
					$file->image_ratio_fill = true;
					$file->image_resize  = true;
					$file->jpeg_quality = $img_quality;
					if($file->image_src_x >= 700) {
						$file->image_x = 700;
					}
					if($file->image_src_y >= 600) {
						$file->image_y = 600;
					}
					
					$file->allowed = array('application/pdf','application/msword', 'image/*');
					$file->process('./uploads/'.$directory.'/'.$year.'/'.$month.'/'.$day.'/'.$postId.'/');

					//resim galerisi için post_meta tablosuna verileri ekleyelim.

					$extension 	= $file->file_src_name_ext;
					$src_type 	= $file->image_src_type;
					$path 		= str_replace("\\",'', $file->file_dst_path);
					$file_size 	= $file->file_src_size;
					$src_x 		= $file->image_src_x;
					$src_y 		= $file->image_src_y;
					$pathname 	= str_replace("\\",'', $file->file_dst_pathname);
					$now 		= date('Y-m-d H:i:s');
					$name_body 	= $file->file_dst_name_body;
					
					$query = $db->query("INSERT INTO $tableName 
										SET `post_id` = '$postId',
											`group` = '$group',
											`key` 	= '$name_body',
											`val_1` = '$extension',
											`val_2` = '$src_type',
											`val_3` = '$path',
											`val_4` = '$file_size',
											`val_5` = '$src_x',
											`val_6` = '$src_y',
											`val_int` = '$user_id',
											`val_text` = '$pathname',
											`val_date` = '$now'");
				}
			}
		}
	}

?>