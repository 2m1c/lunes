<?php include_once("inc/head.php"); //error_reporting(0); /*setcookie("user_id", "1", time() - 3600*24);*/ ?>
<body class="loginPage">

<?php // include_once("inc/header.php"); ?>


<?php

function randomPassword() {
    $alphabet = "abcdefghijklmnopqrstuwxyz0123456789";
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 8; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}

if(isset($_POST['getpassword'])) {
    $email = $_POST['email'];
    $errors = array();
    $result = '';
    $type = '';
    if(empty($email)) {
        $errors[] = "Lütfen E-posta Adresinizi Giriniz";
    } else{

        $query = $db->query("SELECT id FROM `gwp_users` WHERE `email` = '$email'");
        $count = $query->rowCount();


        if($count <= 0) {
            $errors[] = "Bu E-Posta Adresi Sistemde Kayıtlı Değil";
        } else {
            $obj = $query->fetch();
            $id = $obj->id;
        }
    }

    if(!empty($errors)) {
        foreach($errors as $error) {
            $result .= $error.'<br />';
        }
        $type = 'danger';
    } else {
        $new_pass = randomPassword();
        $update = $db->query("UPDATE `gwp_users` SET `cookie` = '$new_pass' WHERE `id` = $id");

        if($update) {
            $to = $email;
            $subject = "HELPERDOC Şifre Yenileme";
            $txt    = '<h3>HELPERDOC ŞİFRE YENİLEME</h3>';
            $txt    .= '<a href="http://helperdoc.com/getpassword.php?newpassword='.$new_pass.'&who='.$id.'" style="font-size: 20px; font-weight: bold;">Şifrenizi Yenilemek İçin Bu Linke TIklayın</a>';
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= "From: helperdoc@helperdoc.com" . "\r\n";


            $mailed = mail($to,$subject,$txt,$headers);

            if($mailed) {
                $result .= 'Şifreniz E-Posta Adresinize Gönderildi!';
                $type = 'success';
            } else {
                $result .= 'Başarısız İşlem Lütfen Tekrar Deneyin - email';
                $type = 'danger';
            }
        } else {
            $result .= 'Başarısız İşlem Lütfen Tekrar Deneyin - update';
            $type = 'danger';
        }


    }
}

if(isset($_POST['renew'])) {
    $who = $_GET['who'];
    $code = $_GET['newpassword'];
    $pass = $_POST['password'];
    $repass = $_POST['repassword'];
    $errors = array();
    $result = '';
    $type = 'danger';

    if(!isset($_GET['newpassword']) || !isset($_GET['who'])) {
        $errors[] = "Hatalı link!";
    }

    if( empty($pass) || mb_strlen($pass, 'UTF-8') < 6 )
    {
        $errors[] = "Şifre en az 6 karakterden oluşmalıdır";
    }

    if($pass != $repass) {
        $errors[] = "Şifreler Eşleşmiyor";
    }

    if(!empty($errors)) {
        foreach($errors as $error) {
            $result .= $error.'<br />';
        }
    } else {
        $query = $db->query("SELECT `id` FROM `gwp_users` WHERE `id` = '$who' AND `cookie` = '$code'");
        echo $query->rowCount();
        if($query->rowCount() >= 1) {
            $obj = $query->fetch();
            $id = $obj->id;
            $pass = md5($pass);
            $update = $db->query("UPDATE `gwp_users` SET `password` = '$pass', `cookie` = '' WHERE `id` = $id");
            if($update) {
                $result = '<a href="./">Şifreniz değiştirildi. Giriş yapmak için tıklayın</a>';
                $type = 'success';
            }
        } else {
            $result = "Geçerisiz İşlem";
        }
    }
}
?>


<div class="login_bg_wrapper"></div>

<div class="container">

    <div class="row marginTop20">

        <div class="col-md-7 hidden-sm hidden-xs signResults"></div><!-- /.col-md-7 -->

        <div class="col-md-5 col-sm-12 col-xs-12 signUpPanel">

            <?php
            if(isset($result)) { ?> <div class="alert alert-<?php echo $type; ?>" role="alert"><?php echo $result; ?></div> <?php }
            ?>

            <?php
            if( isset( $_GET['newpassword'] )) { ?>
            <form method="post" action="">
                <div class="row">
                    <div class="col-md-12">
                        <h3>Şifrenizi Yenileyin</h3>
                    </div>
                </div>

                <div class="row marginTop20">
                    <div class="form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <label style="color: white;">Yeni Şifre</label>
                            <input type="password" class="form-control input-lg" name="password" placeholder="Yeni şifre girin">
                        </div>
                    </div>
                </div>

                <div class="row marginTop20">
                    <div class="form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <label style="color: white;">Şifrenizi Yenileyin</label>
                            <input type="password" class="form-control input-lg" name="repassword" placeholder="Şifrenizi yenileyin">
                        </div>
                    </div>
                </div>

                <div class="row marginTop20">
                    <div class="col-md-12">
                        <input type="submit" value="KAYDOL" name="renew" class="btn btn-theme btn-lg pull-right signupBtn">
                    </div>
                </div>
            </form>
            <?php
            } else { ?>
                <form method="post" action="">
                    <div class="row">
                        <div class="col-md-12">
                            <h3>Şifrenizi Almak İçin <br /> E-Posta Adresinizi Girin</h3>
                            <p class="hidden-sm hidden-xs">Ücretsiz üyelik için formu doldurun</p>
                        </div>
                    </div>


                    <div class="row marginTop20">
                        <div class="form-group">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <input type="email" class="form-control input-lg" name="email" placeholder="E-posta adresinizi yazın" value="<?php echo @$_POST['email']; ?>">
                            </div>
                        </div>
                    </div>

                    <div class="row marginTop20">
                        <div class="col-md-12">
                            <input type="submit" value="GÖNDER" name="getpassword" class="btn btn-theme btn-lg pull-right signupBtn">
                        </div>
                    </div>
                </form>
            <?php } ?>

        </div><!-- /.col-md-5 -->

    </div><!-- /.row -->

</div><!-- /.container -->

<?php include_once("inc/footer.php"); ?>