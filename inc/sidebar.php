<div class="col-md-4 hidden-xs rightSideBar">
	
	<h6>Alanlar <a href="fields.php">tümünü göster</a></h6>
	<p class="subInfo">Bildirim almak istediğiniz alanı takip edebilirsiniz</p>

	<div class="form-group searchField">
		<i class="fa fa-search"></i>
		<input type="text" class="form-control js-keyup-search" data-search="branch" placeholder="Takip etmek istediğiniz alan adını yazınız">
		<div class="brachSearchResult js-keyup-result"><?php // ajax key up search results here! ?></div>
	</div>

	<div class="fields">
<?php
$query = $db->query("SELECT id, title, url FROM qwp_category WHERE `type` = 'branch' ORDER BY `title` ASC ");
while ($obj = $query->fetch(PDO::FETCH_OBJ)) {
$status 	= ( following_status($obj->id) == true ? true : false );
$btnClass 	= ( $status == true ? 'btn-unfollow' : 'btn-follow');
$btnText 	= ( $status == true ? 'Takibi Bırak' : 'Takip Et');
?>		
		<div class="field">
			<a href="field.php?url=<?php echo $obj->url; ?>"><?php echo $obj->title; ?></a>
			<span class="followerAmount"><?php echo get_branch_follower_amount($obj->id); ?> kişi takip ediyor</span>
			<button class="btn <?php echo $btnClass; ?> btn-sm status js-follow-branch" data-branch="<?php echo $obj->id; ?>"><?php echo $btnText; ?></button>
		</div>
<?php } ?>

	</div><!-- /.fields -->

</div><!-- /.col-md-4 -->