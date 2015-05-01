<?php
include_once("inc/class.upload.php");
error_reporting(0);
if(isset($_POST['save']))
{
	$issue 	= $_POST['issue'];
	$branch = $_POST['branch'];
	$archive= $_POST['archive'];
	$errors = array();
	$result = '';
	$is_action_success = 'danger';

	/*
		- if any image is uplaoded, insert '1' to val_3 or '0' to val_3
		- the reason is that if post.val_3 is 0, the system does not check
		- if there is an image or not for that post
	*/
	$imageExist = ( $_FILES['file']['tmp_name'] != "" ? 1 : 0 );

	if( empty($issue) || mb_strlen($issue) < 10 )
	{
		$errors[] = "Konu en az 10 karakterden oluşmalıdır";
	}

	if( empty($branch) || $branch == 0 && $branch != 'move_archive' )
	{
		$errors[] = "Lütfen gönderiniz yayınlanmasını istediğiniz alanı seçiniz";
	}

    if( $branch == 'move_archive' && !isset($archive) ) {
        $errors[] = '<a href="profile.php?url=folder&profile_id=<?php echo $user_id; ?>" class=" text-warning">Gönderi yapmak için arşiv seçimi yaplısınız! Arşiv oluşturmak için tıklayın</a>';
    }

	//if '$branch' is move_archive, insert post into arcihive (it wont be published on wall)
	// else insert post as branch_post (it will be published on wall)
	if( $branch == 'move_archive' ) {
		$post_type = 'archive_post';
		$branch = 0;
	} else {
		$post_type 	= 'branch_post';
		$archive 	= 0;
	}

	if(!empty($errors))
	{
		foreach ($errors as $error) {
			$result .= $error."<br />";
		}
	}
	else
	{
		try 
		{
			$db->beginTransaction();

			$query = $db->prepare("INSERT INTO gwp_posts
								SET `status` = :status,
									`datetime` = :datetime,
									`type` = :type,
									`content` = :content,
									`val_1` = :val_1,
									`val_2` = :val_2,
									`val_3` = :val_3,
									`val_4` = :val_4");

			$query->bindValue(':status', 1);
			$query->bindValue(":datetime", date('Y-m-d H:i:s'));
			$query->bindValue(':type', $post_type);
			$query->bindValue(':content', $issue);
			$query->bindValue(':val_1', $user_id);
			$query->bindValue(':val_2', $branch);
			$query->bindValue(':val_3', $imageExist);
			$query->bindValue(':val_4', $archive);
			$done = $query->execute();

			$last_id = $db->lastInsertId(); 

			$fileInput     	= $_FILES['file'];
	        $postId        	= $last_id;
	        $fileName      	= $last_id;
	        $directory     	= 'post';
	        $table_name    	= 'gwp_post_meta';
	        $group    		= 'post_img';

			multiple_image_upload($fileInput, $postId, $fileName, $directory, $table_name, $group);

	        // if post type is archive insert post information into gwp_user_meta
	        if($post_type = 'archive_post') {
	            $db->query("INSERT INTO `gwp_user_meta` SET `post_id` = '$user_id', `group` = 'archived_post', `key` = 'archived_post', `val_1` = '$last_id', `val_2` = '$archive'");
	        }

			if($done)
			{
				$result .= "Gönderi Yapıldı";
				$is_action_success = 'success';
				$_POST['issue'] = '';
			}
			else
			{
				$result .= "Bilinmeyen bir sebepten ötürü gönderi tamamlanamadı.<br />Lütfen tekrar deneyin";
			}

			$db->commit();
		} 
		catch ( Exception $e )
		{
			$db->rollBack();

			$myfile = fopen("log_errors.txt", "a");
			$txt = $e->getMessage();
			fwrite($myfile, $txt);
			fclose($myfile);
		}
		

	}
}
?>
<?php
if(!empty($result)) { ?>
	<div class="row">
		<div class="col-md-12 col-sm-12 col-xs-12">
			<div class="alert alert-<?php echo $is_action_success; ?> alert-dismissable">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			  	<?php echo $result; ?>
			</div>
		</div>
	</div>
<?php } ?>
<div class="row postPanel marginBottom50">
	
	<div class="col-md-2 col-sm-2 hidden-xs">
		<img src="<?php echo get_current_user_image(); ?>" class="img-responsive" />
	</div>

	<div class="col-md-10 col-sm-10 col-xs-12">

	<form method="post" action="" enctype="multipart/form-data">
		<div class="row">
			<div class="form-group">
				<div class="col-md-12">
					<textarea class="form-control" id="postPanelTextarea" name="issue" placeholder="<?php echo select_query("name", "gwp_users", "id = $user_id", "title"); ?> gönderini buradan paylaşabiilirsin."><?php echo @$_POST['issue']; ?></textarea>
				</div>
			</div>
		</div><!-- /.row -->

<?php
// if user displays a field page
// dont force user to select a field
if(isset($_GET['url']))
{ ?>

		<div class="row marginTop10">
			<div class="form-group">
				<div class="col-md-12">
					<input type="text" disabled value="#<?php echo select_query("title", "qwp_category", " id = '$page_id'", "lower"); ?>" class="form-control">
					<input type="hidden" name="branch" value="<?php echo $page_id; ?>" class="form-control">
				</div>
			</div>
		</div><!-- /.row -->

<?php }
else
{ ?>

		<div class="row marginTop10">
			<div class="form-group">
				<div class="col-md-12 col-xs-12">
					<select name="branch" <?php echo $disabled; ?> class="form-control js-infotip js-display-option" data-tip="Gönderi yapmak istediğiniz alanı takip etmelisiniz">
					<option value="0">Alan Seçin</option>
					<option value="move_archive">ARŞİVE TAŞI</option>



<?php
$branches = $db->query("SELECT * FROM `qwp_category`");
while ($obj = $branches->fetch(PDO::FETCH_OBJ)) { ?>
						<option value="<?php echo $obj->id; ?>">#<?php echo $obj->title; ?></option>
<?php } ?>


					</select>
				</div>
			</div>
		</div><!-- /.row -->

		<div class="row marginTop10 js-hidden-option">
			<div class="form-group">
				<div class="col-md-12 col-xs-12">

                    <?php
                    if(get_user_archive_file_name($user_id) == false) { ?>
                        <a href="profile.php?url=folder&profile_id=<?php echo $user_id; ?>" class=" text-warning">Kayıtlı Arşiviniz Yok. Oluşturmak İçin Tıklayın</a>
                    <?php
                    } else { ?>
					<select name="archive" class="form-control js-infotip" data-tip="Gönderinizi Arşivleyebiilir ve Bir Düzende Tutabilirsiniz!">
<?php
$arhive = get_user_archive_file_name($user_id);
while ($row = $arhive->fetch(PDO::FETCH_OBJ)) {
	$archive_id 	= $row->id;
	$archive_name 	= $row->val_text;
?>
						<option value="<?php echo $archive_id; ?>"><?php echo $archive_name; ?></option>
<?php } ?>


					</select>
                    <br />
                    <a href="profile.php?url=folder&profile_id=<?php echo $user_id; ?>" class=" text-warning">Yeni Arşiv Oluşturmak İçin Tıklayın</a>

                    <?php } ?>

				</div>
			</div>
		</div><!-- /.row -->

<?php } ?>
		<div class="row marginTop10">
			<div class="form-group">
				<div class="col-md-6 js-clone-body">
					<input type="file" <?php echo $disabled; ?> name="file[]" multiple class="js-clone-trigger">
				</div>
				<div class="col-md-6">
					<button type="submit" name="save" <?php echo $disabled; ?> class="btn btn-theme pull-right js-preloader-trigger">Gönder</button>
				</div>
			</div>
		</div><!-- /.row -->
	</form>


	</div><!-- /.col-md-10 -->

</div><!-- /.row (postPanel)-->