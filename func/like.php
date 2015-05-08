<?php
include_once("../inc/db.php");
include_once("../inc/functions.php");

$user_id    = isset($_COOKIE['user_id']) ? $_COOKIE['user_id'] : 0;
if( $user_id == 0 )
{
  echo 'no-auth';
  die;
  exit;
}

/*
 * önce insert ettir kullanıcı id ve comment id olsun
 * */

$itemId = $_POST['itemId'];
$itemType = $_POST['itemType'];
$postId = $_POST['postId'];
$object = $_POST['object']; /* gönderisi ayda yorumu beğenilen kişi */
$date = date("Y-m-d H:i:s");

if($itemType == "comment") {
    $count = $db->query("SELECT `id` FROM `gwp_post_meta` WHERE `group` = 'like' AND `key` = 'comment_like' AND `post_id` = '$itemId' AND `val_int` = '$user_id'")->rowCount();
   if($count <= 0) {
        $query = $db->query("INSERT INTO `gwp_post_meta`
                              SET `group` = 'like',
                                  `key` = 'comment_like',
                                  `status` = '1',
                                  `post_id` = '$itemId',
                                  `val_8` = '$postId',
                                  `val_9` = '$object',
                                  `val_int` = '$user_id',
                                  `val_date` = '$date'
                                  ");
        if($query) {
            // if like is successfull, insert some values to notification to item (post or comment) owner
            // if user_id and object is not same
            if($user_id != $object) {
            $db->query("INSERT INTO `gwp_notifications`
						SET `datetime` = '$date',
                            `type` = 'like_comment',
                            `val_1` = '$postId',
                            `val_2` = '$object',
                            `val_3` = '$user_id'");
            }
            $count = $db->query("SELECT `id` FROM `gwp_post_meta` WHERE `group` = 'like' AND `key` = 'comment_like' AND `post_id` = '$itemId'")->rowCount();
            echo '<i class="fa fa-thumbs-up"></i>&nbsp;Beğendin <span>'.$count.' Kişi Beğendi</span>';
        }
   } else {
       return false;
   }
}