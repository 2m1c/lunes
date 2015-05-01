<?php include_once("inc/head.php"); ?>
<body>
<?php
/*
$status 	= ( following_status($page_id) == true ? true : false );
$btnClass 	= ( $status == true ? 'btn-unfollow' : 'btn-follow');
$btnText 	= ( $status == true ? 'Takibi Bırak' : 'Takip Et');
*/
?>

<?php include_once("inc/header.php"); ?>


<div class="container wall">

	<div class="row">
		
		<div class="col-md-12 allFields">

			<div class="row">
<?php
$no = 0;
$query = $db->query("SELECT id, title, url FROM `qwp_category` ORDER BY `title` ASC");
while ($obj = $query->fetch(PDO::FETCH_OBJ)) {
$no++;
$status 	= ( following_status($obj->id) == true ? true : false );
$btnClass 	= ( $status == true ? 'btn-unfollow' : 'btn-follow');
$btnText 	= ( $status == true ? 'Takibi Bırak' : 'Takip Et');
?>
				<div class="col-md-3 col-sm-6 col-xs-6">
					<div class="fieldPanel">
					<h6><a href="field.php?url=<?php echo $obj->url; ?>"><?php echo $obj->title; ?></a></h6>
					<span class="pull-left"><?php echo get_total_issue_amount_in_brach($obj->id); ?> konu bulunmakta</span>
					<span class="pull-right"><?php echo get_branch_follower_amount($obj->id); ?> kişi takip ediyor</span>
					<img src="img/sample/fields_1.jpg" class="img-responsive">
					<button class="btn <?php echo $btnClass; ?> btn-sm status js-follow-branch" data-branch="<?php echo $obj->id; ?>"><?php echo $btnText; ?></button>
					</div>
				</div>
<?php
if($no % 4 == 0) { echo '<div style="clear:both"></div>'; }
?>

<?php } ?>
			</div>
		</div> <!-- /.col-md-12 -->

	</div><!-- /.row -->

</div><!-- /.container -->

<?php include_once("inc/footer.php"); ?>