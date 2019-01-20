<?php
session_start();
require_once('class.user.php');
// load recaptcha library
require_once "recaptchalib.php";

$user = new USER();

if($user->is_loggedin()!="")
{
	$user->redirect('home.php');
}

// Retrieve your ReCaptcha token at https://www.google.com/recaptcha
$captcha_secret="PUT_YOUR_TOKEN_SECRET_HERE";
$captcha_datakey="PUT_YOUR_DATAKEY_HERE";

$captcha_response= null;
$reCaptcha= new ReCaptcha($captcha_secret);

if(isset($_POST['btn-signup']))
{
	$uname = strip_tags($_POST['txt_uname']);
	$umail = strip_tags($_POST['txt_umail']);
	$upass = strip_tags($_POST['txt_upass']);
    // verify captcha
    if($_POST["g-recaptcha-response"]) {
        $captcha_response = $reCaptcha->verifyResponse($_SERVER["REMOTE_ADDR"], $_POST["g-recaptcha-response"]);
    }

	if($uname=="")	{
		$error[] = $_SIGNUP_USERNAME_EMPTY;
	}
	else if($umail=="")	{
		$error[] = $_SIGNUP_MAIL_EMPTY;
	}
	else if(!filter_var($umail, FILTER_VALIDATE_EMAIL))	{
	    $error[] = $_SIGNUP_MAIL_VALID;
	}
	else if($upass=="")	{
		$error[] = $_SIGNUP_PASSWORD_EMPTY;
	}
	else if(strlen($upass) < 6) {
		$error[] = $_SIGNUP_PASSWORD_CHAR;
	}
    else if($captcha_response == null || !$captcha_response->success) {
        $error[] = $_SIGNUP_CAPTCHA;
    }
	else {
		try
		{
			$stmt = $user->runQuery("SELECT user_name, user_email FROM users WHERE user_name=:uname OR user_email=:umail");
			$stmt->execute(array(':uname'=>$uname, ':umail'=>$umail));
			$row=$stmt->fetch(PDO::FETCH_ASSOC);

			if($row['user_name']==$uname) {
				$error[] = $_SIGNUP_USERNAME_TAKEN;
			}
			else if($row['user_email']==$umail) {
				$error[] = $_SIGNUP_EMAIL_TAKEN;
			}
			else
			{
				if($user->register($uname,$umail,$upass)){
					$user->redirect('sign-up.php?joined');
				}
			}
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
		}
	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Kontest : <?php echo $_SITE_NAVIGATE_SIGNUP; ?></title>
<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
<link href="bootstrap/css/bootstrap-theme.min.css" rel="stylesheet" media="screen">
<link rel="stylesheet" href="style.css" type="text/css"  />
</head>
<body>

<div class="signin-form">

<div class="container">

        <form method="post" class="form-signin">
            <h2 class="form-signin-heading"><?php echo $_SITE_NAVIGATE_SIGNUP; ?></h2><hr />
            <?php
			if(isset($error))
			{
			 	foreach($error as $error)
			 	{
					 ?>
                     <div class="alert alert-danger">
                        <i class="glyphicon glyphicon-warning-sign"></i> &nbsp; <?php echo $error; ?>
                     </div>
                     <?php
				}
			}
			else if(isset($_GET['joined']))
			{
				 ?>
                 <div class="alert alert-info">
                      <i class="glyphicon glyphicon-log-in"></i> &nbsp; <?php echo $_SIGNUP_SUCCESS; ?> <a href='index.php'><?php echo $_SITE_NAVIGATE_LOGIN; ?></a>
                 </div>
                 <?php
			}
			?>
            <div class="form-group">
            <input type="text" class="form-control" name="txt_uname" placeholder="Enter Username" value="<?php if(isset($error)){echo $uname;}?>" />
            </div>
            <div class="form-group">
            <input type="text" class="form-control" name="txt_umail" placeholder="Enter E-Mail Address" value="<?php if(isset($error)){echo $umail;}?>" />
            </div>
            <div class="form-group">
            	<input type="password" class="form-control" name="txt_upass" placeholder="Enter Password" />
            </div>
            <div class="form-group">
                <div class="g-recaptcha" data-sitekey="<?php echo $captcha_datakey; ?>"></div>
            </div>
            <div class="clearfix"></div><hr />
            <div class="form-group">
            	<button type="submit" class="btn btn-primary" name="btn-signup">
                	<i class="glyphicon glyphicon-open-file"></i>&nbsp; <?php echo $_SITE_NAVIGATE_SIGNUP; ?>
                </button>
            </div>
            <br />
            <label><?php echo $_LOGIN_SIGNIN; ?> <a href="index.php"><?php echo $_SITE_NAVIGATE_LOGIN; ?></a></label>
        </form>
       </div>
</div>

</div>
<script src='https://www.google.com/recaptcha/api.js'></script>
</body>
</html>
