<?php
    error_reporting( ~E_NOTICE ); // avoid notice
    require_once("session.php");

    require_once("class.user.php");
    $auth_user = new USER();

    $user_id = $_SESSION['user_session'];

    function getUser($auth_user, $user_id){
        $stmt = $auth_user->runQuery("SELECT * FROM users WHERE user_id=:user_id");
        $stmt->execute(array(":user_id"=>$user_id));

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    $userRow = getUser($auth_user, $user_id);

    if(isset($_POST['btnsave'])) {
        $imgFile = $_FILES['user_image']['name'];
        $tmp_dir = $_FILES['user_image']['tmp_name'];
        $imgSize = $_FILES['user_image']['size'];
        $imgerr  = $_FILES['user_image']['error'];

        if($imgFile)
        {
            $upload_dir = 'user_images/'; // upload directory
            $imgExt = strtolower(pathinfo($imgFile,PATHINFO_EXTENSION)); // get image extension
            $valid_extensions = array('jpeg', 'jpg', 'png', 'gif'); // valid extensions
            $userpic = rand(1000,1000000).".".$imgExt;
            if(in_array($imgExt, $valid_extensions))
            {
                if($imgSize < 5000000 && $imgerr != 1) // prevent php max_file_size to give an empty file
                {
                    if($userRow['user_pic'] != "default.jpg") { // prevent default.jpg to be deleted
                        unlink($upload_dir.$userRow['user_pic']); // delete old pic
                    }
                    move_uploaded_file($tmp_dir,$upload_dir.$userpic);

                    $img = imagecreatefromstring(file_get_contents($upload_dir.$userpic)); // reduce image quality
                    imagejpeg($img, $upload_dir.$userpic, 50);

                    $userRow['user_pic'] = $userpic; // to prevent blank avatar on refresh
                }
                else
                {
                    $error[] = $_PROFILE_AVATAR_BIG;
                }
            }
            else
            {
                $error[] = $_PROFILE_AVATAR_EXT;
            }
        }
        else
        {
            // if no image selected the old image remain as it is.
                    $error[] = $_PROFILE_AVATAR_EMPTY;
        }

        // if no error occured, continue ....
        if(!isset($error))
        {
            $stmt = $auth_user->updateAvatar($user_id,$userpic);
            if($stmt->execute()){
                        $success = $_PROFILE_AVATAR_SUCCESS;
            }
            else{
                $error[] = $_PROFILE_AVATAR_ERROR;
            }
        }
    }

    if( isset($_POST['action']) && $_POST['action'] != "" ) {
        if($_POST['action'] == "changeMail"){
            if( isset($_POST['txt_email']) && $_POST['txt_email'] != ""){
                $email= strip_tags($_POST['txt_email']);
                $res = $auth_user->changeMail($user_id, $email);
                if($res){
                    $success = $_PROFILE_EMAIL_SUCCESS . " : <b>" . $email . "</b> !";
                }
                else {
                    $error[] = $_PROFILE_EMAIL_ERROR . " : <b>" . $cname . "</b> !";
                }
            }
            else {
                $error[] = $_PROFILE_EMAIL_EMPTY;
            }
        }
        if($_POST['action'] == "changePass"){
            if( (isset($_POST['txt_passwd']) && $_POST['txt_passwd'] != "") && (isset($_POST['txt_passwd_confirm']) && $_POST['txt_passwd_confirm'] != "")){
                $passwd= strip_tags($_POST['txt_passwd']);
                $passwdconfirm= strip_tags($_POST['txt_passwd_confirm']);

                if($passwd != $passwdconfirm ){
                    $error[] = $_PROFILE_PASSWORD_MATCH;
                }
                else{
                    $res = $auth_user->changePass($user_id, $passwd);
                    if($res){
                        $success = $_PROFILE_PASSWORD_SUCCESS;
                    }
                    else {
                        $error[] = $_PROFILE_PASSWORD_ERROR;
                    }
                }
            }
            else {
                $error[] = $_PROFILE_PASSWORD_EMPTY;
            }
        }
    }

    $userRow = getUser($auth_user, $user_id);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
<link href="bootstrap/css/bootstrap-theme.min.css" rel="stylesheet" media="screen">
<script type="text/javascript" src="jquery-1.11.3-jquery.min.js"></script>
<link rel="stylesheet" href="style.css" type="text/css"  />
<title>Kontest - <?php echo $_KONTEST_PROFILE . " " . $userRow['user_name']; ?></title>
</head>

<body>

    <?php require_once('navbar_skeleton.php'); ?>

	<div class="clearfix"></div>

    <div class="container-fluid" style="margin-top:80px;">

    <div class="container">
        <h1><span class="glyphicon glyphicon-user"></span> <?php echo $_KONTEST_PROFILE; ?></h1>
       	<hr />

    <?php
        if(isset($error))
        {
            foreach($error as $error)
            {
    ?>
            <div class="alert alert-danger alert-dismissable fade in">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <i class="glyphicon glyphicon-warning-sign"></i> &nbsp; <?php echo $error; ?>
            </div>
    <?php
            }
        }
        else if(isset($success)) {
    ?>
            <div class="alert alert-success alert-dismissable fade in">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <i class="glyphicon glyphicon-ok"></i> <?php echo $success; ?>
            </div>
    <?php
        }
    ?>
	<div class="col-xs-6">

	<?php
        switch(true){
                case ($userRow['user_role'] >= 25 && $userRow['user_role'] < 50):
                        $levelU = "<font color=\"#375D81\">" . $_KONTEST_USER . "</font>";
                        break;
                case ($userRow['user_role'] >= 50 && $userRow['user_role'] < 75):
                        $levelU = "<font color=\"#5C0515\">" . $_KONTEST_MODERATOR . "</font>";
                        break;
                case ($userRow['user_role'] >= 75):
                        $levelU = "<font color=\"#FF5B2B\">" . $_KONTEST_ADMIN . "</font>";
                        break;
                default:
                        $levelU = "<font color=\"#375D81\">" . $_KONTEST_USER . "</font>";
                        break;
        }
        ?>

        <!-- Modal New Avatar -->
        <div class="modal fade" id="myModalAvatar" role="dialog">
            <div class="modal-dialog ">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><?php echo $_PROFILE_AVATAR_UPLOAD; ?></h4>
                    </div>
                    <div class="modal-body">
                        <form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
                            <div class="form-group">
                                <label class="control-label col-sm-3" for="text"><?php echo $_PROFILE_AVATAR_FILE; ?>:</label>
                                <div class="col-sm-9">
                                    <input type="file" name="user_image" accept="image/*" />
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-offset-1 col-sm-11">
                                    <button type="submit" name="btnsave" class="btn btn-default"><span class="glyphicon glyphicon-save"></span> &nbsp; <?php echo $_KONTEST_SUBMIT; ?></button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><?php echo $_KONTEST_CLOSE; ?></button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal New Mail -->
        <div class="modal fade" id="myModalMail" role="dialog">
            <div class="modal-dialog ">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><?php echo $_PROFILE_EMAIL_CHANGE; ?></h4>
                    </div>
                    <div class="modal-body">
                        <form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
                            <div class="form-group">
                                <label class="control-label col-sm-3" for="text"><?php echo $_PROFILE_EMAIL_ADDRESS; ?>:</label>
                                <div class="col-sm-9">
                                    <input class="form-control" type="text" name="txt_email" placeholder="<?php echo $userRow['user_email']; ?>" style="width: 75%;"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-offset-1 col-sm-11">
                                    <button name="action" value="changeMail" class="btn btn-default"><span class="glyphicon glyphicon-pencil"></span> &nbsp; <?php echo $_KONTEST_UPDATE; ?></button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><?php echo $_KONTEST_CLOSE; ?></button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal New Password -->
        <div class="modal fade" id="myModalPassword" role="dialog">
            <div class="modal-dialog ">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><?php echo $_PROFILE_PASSWORD_CHANGE; ?></h4>
                    </div>
                    <div class="modal-body">
                        <form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
                            <div class="form-group">
                                <label class="control-label col-sm-3" for="text"><?php echo $_PROFILE_PASSWORD_NEW; ?>:</label>
                                <div class="col-sm-9">
                                    <input class="form-control" type="password" name="txt_passwd" placeholder="Enter new password" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-3" for="text"><?php echo $_PROFILE_PASSWORD_CONFIRM; ?>:</label>
                                <div class="col-sm-9">
                                    <input class="form-control" type="password" name="txt_passwd_confirm" placeholder="Repeat new password" />
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-offset-1 col-sm-11">
                                   <button name="action" value="changePass" class="btn btn-default"><span class="glyphicon glyphicon-pencil"></span> &nbsp; <?php echo $_KONTEST_UPDATE; ?></button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><?php echo $_KONTEST_CLOSE; ?></button>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid well span6">
            <div class="row-fluid">
                <div class="pull-right">
                    <div class="btn-group">
                        <a class="btn dropdown-toggle btn-info" data-toggle="dropdown" href="#">
                        Action
                        <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="#" data-toggle="modal" data-target="#myModalPassword"><span class="glyphicon glyphicon-cog"></span> <?php echo $_PROFILE_PASSWORD_CHANGE; ?></a></li>
                        </ul>
                    </div>
                </div>

                <div class="span2" >

                        <div class="view overlay hm-stylish-strong">
                            <img id="avatar" src="user_images/<?php echo $userRow['user_pic']; ?>" class="img-circle" width="150px" height="150px" />
                            <a  href="#" data-toggle="modal" data-target="#myModalAvatar">
                                <div class="mask">
                                    <p class="white-text"><span class="glyphicon glyphicon-pencil"></span>&nbsp;<?php echo $_KONTEST_UPDATE; ?></p>
                                </div>
                            </a>
                        </div>
                </div>

                <div class="span8">
                    <h3><?php echo $userRow['user_name']; ?></h3>
                    <h6><b><?php echo $_PROFILE_EMAIL_ADDRESS; ?>:</b> <i><?php echo $userRow['user_email']; ?></i> &nbsp;<a href="#" data-toggle="modal" data-target="#myModalMail">(<?php echo $_KONTEST_UPDATE; ?>&nbsp;)</a></h6>
                    <h6><b><?php echo $_KONTEST_RANK; ?>:</b> <?php echo $levelU; ?></h6>

                </div>
            </div>
        </div>

	<p class="page-header"></p>
   	</div>
    </div>

</div>


<script src="bootstrap/js/bootstrap.min.js"></script>

</body>
</html>
