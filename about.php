<?php
	require_once("session.php");
	require_once("class.user.php");

	$auth_user = new USER();

	$user_id = $_SESSION['user_session'];

	$stmt = $auth_user->runQuery("SELECT user_id, user_name, user_email, user_role FROM users WHERE user_id=:user_id");
	$stmt->execute(array(":user_id"=>$user_id));

	$userRow=$stmt->fetch(PDO::FETCH_ASSOC);


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
<link href="bootstrap/css/bootstrap-theme.min.css" rel="stylesheet" media="screen">
<script type="text/javascript" src="jquery-1.11.3-jquery.min.js"></script>
<link rel="stylesheet" href="style.css" type="text/css"  />
<title>Kontest - <?php echo $_SITE_NAVIGATE_ABOUT; ?></title>
</head>

<body>

	<?php
		require_once('navbar_skeleton.php');
	?>
    <div class="clearfix"></div>

<div class="container-fluid" style="margin-top:80px;">

    <div class="container">

        <div class="page-header">
            <h1><span class="glyphicon glyphicon-info-sign"></span>&nbsp;<?php echo $_SITE_NAVIGATE_ABOUT; ?></h1>
        </div>

        <div class="well">
            <p><?php echo $_ABOUT_WELL1; ?></p>
            <p><?php echo $_ABOUT_WELL2; ?></p>
            <p><?php echo $_ABOUT_WELL3; ?> <a href="https://github.com/Talos51" target="_blank">Talos</a> - © 2018</p>
        </div>
    </div>

</div>

<script src="bootstrap/js/bootstrap.min.js"></script>

</body>
</html>
