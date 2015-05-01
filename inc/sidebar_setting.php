<div class="col-md-2 hidden-sm hidden-xs rightSideBarProfile">
	
	<a href="profile.php?url=general&profile_id=<?php echo $profile_id; ?>">Genel Bilgiler</a>
	<a href="profile.php?url=posts&profile_id=<?php echo $profile_id; ?>">Gönderilerim</a>
    <a href="profile.php?url=folder&profile_id=<?php echo $profile_id; ?>">Arşivlerim</a>
<?php
// Following links can be seen by profile owner
// if user id value is not matched with profile id from url
// don't display following links.
if($user_id == $profile_id)
{?>
	<a href="profile.php?url=notification&profile_id=<?php echo $user_id; ?>">Bildirimlerim</a>
	<a href="profile.php?url=account&profile_id=<?php echo $user_id; ?>">Hesap Ayarları</a>
	<a href="profile.php?url=image&profile_id=<?php echo $user_id; ?>">Profil Resimleri</a>
	<a href="profile.php?url=password&profile_id=<?php echo $user_id; ?>">Şifre Ayarları</a>
	<a href="profile.php?url=delete&profile_id=<?php echo $user_id; ?>">Hesabı Sil</a>
<?php 
}
?>
</div><!-- /.col-md-2 -->

<div class="hidden-lg hidden-md col-sm-12 col-xs-12" style="margin-bottom: 20px;">
    <select class="form-control" onchange="location = this.options[this.selectedIndex].value;">
        <option>Seçim Yapınız</option>
        <option value="profile.php?url=general&profile_id=<?php echo $profile_id; ?>">Genel Bilgileri</option>
        <option value="profile.php?url=posts&profile_id=<?php echo $profile_id; ?>">Gönderilerim</option>
        <option value="profile.php?url=folder&profile_id=<?php echo $profile_id; ?>">Arşivlerim</option>
    <?php if($user_id == $profile_id)
    { ?>
        <option value="profile.php?url=notification&profile_id=<?php echo $user_id; ?>">Bildirimlerim</option>
        <option value="profile.php?url=account&profile_id=<?php echo $user_id; ?>">Hesap Ayarları</option>
        <option value="profile.php?url=image&profile_id=<?php echo $user_id; ?>">Profil Resimleri</option>
        <option value="profile.php?url=password&profile_id=<?php echo $user_id; ?>">Şifre Ayarları</option>
        <option value="profile.php?url=delete&profile_id=<?php echo $user_id; ?>">Hesabı Sil</option>
    <?php } ?>
    </select>
</div>