<?php
	require_once("session.php");

	require_once("class.user.php");
	$auth_user = new USER();

	$user_id = $_SESSION['user_session'];

    $stmt = $auth_user->runQuery("SELECT user_id, user_name, user_email, user_role FROM users WHERE user_id=:user_id");
    $stmt->execute(array(":user_id"=>$user_id));

    $userRow=$stmt->fetch(PDO::FETCH_ASSOC);

    if(isset($_POST['action']) && $_POST['action'] != ""){
        if($userRow['user_role'] >= 50){
            if($_POST['action'] == "changeLevel"){
                if($userRow['user_role'] >= 75){ // only admins can promote/demote
                    $res = $auth_user->updateLevel(strip_tags($_POST['user_id']), strip_tags($_POST['lvl']));
                    if($res){
                        $success = $_USER_UPDATE_SUCCESS . " #" . strip_tags($_POST['user_id']);
                    } else {
                        $error[] = $_USER_UPDATE_ERROR . " #" . strip_tags($_POST['user_id']);
                    }
                }
            }
            if($_POST['action'] == "delUser"){

                $res = $auth_user->deleteUser(strip_tags($_POST['user_id']));
                if($res){
                    $success = $_USER_DELETE_SUCCESS . " #" . strip_tags($_POST['user_id']);
                } else {
                    $error[] = $_USER_DELETE_ERROR . " #" . strip_tags($_POST['user_id']);
                }
            }
        }
        else{
            $error[] = $_USER_PERMISSION_DENIED;
        }
    }

    // Users List
    $stmt = $auth_user->runQuery("SELECT user_id,user_name,user_email,user_role FROM users ORDER BY user_role DESC, user_id ASC");
    $stmt->execute();

    $allUserRow=$stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
<link href="bootstrap/css/bootstrap-theme.min.css" rel="stylesheet" media="screen">
<script type="text/javascript" src="jquery-1.11.3-jquery.min.js"></script>
<link rel="stylesheet" href="style.css" type="text/css"  />
<title>Kontest - <?php echo $_KONTEST_USERS; ?></title>
</head>

<body>

	<?php require_once('navbar_skeleton.php'); ?>

    <div class="clearfix"></div>


<div class="container-fluid" style="margin-top:80px;">

    <div class="container">

        <h1><span class="glyphicon glyphicon-list"></span> <?php echo $_KONTEST_USERS; ?></h1>
       	<hr />
        <?php
        if($userRow['user_role'] >= 50 ){
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
            else if(isset($success))
            {
            ?>
            <div class="alert alert-success alert-dismissable fade in">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <i class="glyphicon glyphicon-ok"></i> <?php echo $success; ?>
            </div>
            <?php
            }
            ?>
            <div class="table-responsive">
            <?php
                echo '
                <table class="table table-bordered table-condensed">
                <thead>
                <tr>
                <th>' . $_KONTEST_NUMBER . '</th>
                <th>' . $_KONTEST_USERNAME . '</th>
                <th>' . $_KONTEST_MAIL . '</th>
                <th>' . $_KONTEST_RANK . '</th>
                <th>' . $_KONTEST_ACTION . '</th>
                </tr>
                </thead>
                <tbody>
                ';
                foreach($allUserRow as $user){
                    $levelU;
                    $tr = "<tr class=\"info\">";
                    switch(true){
                        case ($user['user_role'] >= 25 && $user['user_role'] < 50):
                            $levelU = "<font color=\"#375D81\">" . $_KONTEST_USER . "</font>";
                            $tr = "<tr class=\"info\">";
                            break;
                        case ($user['user_role'] >= 50 && $user['user_role'] < 75):
                            $levelU = "<font color=\"#5C0515\">" . $_KONTEST_MODERATOR . "</font>";
                            $tr = "<tr class=\"warning\">";
                            break;
                        case ($user['user_role'] >= 75):
                            $levelU = "<font color=\"#FF5B2B\">" . $_KONTEST_ADMIN . "</font>";
                            $tr = "<tr class=\"danger\">";
                            break;
                        default:
                            $levelU = "<font color=\"#375D81\">" . $_KONTEST_USER . "</font>";
                            $tr = "<tr class=\"info\">";
                            break;
                    }

                    echo $tr . "\n" .
                    "<td>" . $user['user_id'] . "</td>\n" .
                    "<td>" . $user['user_name'] . "</td>\n" .
                    "<td>" . $user['user_email'] . "</td>\n" .
                    "<td>" . $levelU . "</td>\n";

					// Ranks Operations
                    // SuperAdmins are gods
					if($user['user_role'] > 100){
                        echo "<td> </td>";
                    }
					else {
						echo "<td><div class=\"row\">";
						// If is an Administrator
						if($user['user_role'] >= 75 && $user['user_role'] < 100){
							// Only SuperAdmins can demote Administrator
	                        if($userRow['user_role'] > 75 ) {
								echo "<div class=\"col-xs-2\"><form class=\"form-inline\" action= \"\" method=\"post\"> <input type=\"hidden\" name=\"user_id\" value=\"". $user['user_id'] . "\">";
	                           	echo "<input type=\"hidden\" name=\"lvl\" value=\"" . ($user['user_role'] - 25) . "\"> <button name=\"action\" value=\"changeLevel\" class=\"btn btn-sm btn-warning\"><i class=\"glyphicon glyphicon-arrow-down\"></i></button> ";
							   	echo "</div></form>";
	                        }
						}
						// If is a Moderator
	                    else if($user['user_role'] >= 50 && $user['user_role'] < 75){
							// Only SuperAdmins can promote moderator to administrator
	                        if($userRow['user_role'] > 100 ) {
								echo "<div class=\"col-xs-2\"><form class=\"form-inline\" action= \"\" method=\"post\"> <input type=\"hidden\" name=\"user_id\" value=\"". $user['user_id'] . "\">";
	                           	echo "<input type=\"hidden\" name=\"lvl\" value=\"" . ($user['user_role'] + 25) . "\"> <button name=\"action\" value=\"changeLevel\" class=\"btn btn-sm btn-success\"><i class=\"glyphicon glyphicon-arrow-up\"></i></button> ";
								echo "</div></form>";
	                        }
	                        // Only Admins can demote moderator
	                        if($userRow['user_role'] > 50 ) {
								echo "<div class=\"col-xs-2\"><form class=\"form-inline\" action= \"\" method=\"post\"> <input type=\"hidden\" name=\"user_id\" value=\"". $user['user_id'] . "\">";
	                           	echo "<input type=\"hidden\" name=\"lvl\" value=\"" . ($user['user_role'] - 25) . "\"> <button name=\"action\" value=\"changeLevel\" class=\"btn btn-sm btn-warning\"><i class=\"glyphicon glyphicon-arrow-down\"></i></button> ";
							   	echo "</div></form>";
	                        }
	                    }
						// If is a user
						else if($user['user_role'] >= 25 && $user['user_role'] < 50){
							// Can be promoted to Moderator by Admins
							if($userRow['user_role'] >= 75 ) {
								echo "<div class=\"col-xs-2\"><form class=\"form-inline\" action= \"\" method=\"post\"> <input type=\"hidden\" name=\"user_id\" value=\"". $user['user_id'] . "\">";
	                           	echo "<input type=\"hidden\" name=\"lvl\" value=\"" . ($user['user_role'] + 25) . "\"> <button name=\"action\" value=\"changeLevel\" class=\"btn btn-sm btn-success\"><i class=\"glyphicon glyphicon-arrow-up\"></i></button> ";
								echo "</div></form>";
	                        }
							// Only Moderators can delete regular members
	                        if( $userRow['user_role'] >= 75 || ($userRow['user_role'] >= 50 && ($user['user_role'] >= 25 && $user['user_role'] < 50)) ){
								echo "<div class=\"col-xs-2\"><form class=\"form-inline\" action= \"\" method=\"post\"> <input type=\"hidden\" name=\"user_id\" value=\"". $user['user_id'] . "\">";
	                            echo "<button name=\"action\" value=\"delUser\" class=\"btn btn-sm btn-danger\"><i class=\"glyphicon glyphicon-remove\"></i></button>";
								echo "</div></form>";
	                        }
						}
						echo "</div></td>";
					}
                    echo "</tr>\n";
                }

                echo '
                </tbody>
                </table>';
        }
        else {
        ?>
            <div class="alert alert-danger">
            <i class="glyphicon glyphicon-warning-sign"></i> &nbsp; <?php echo $_USER_PERMISSION_DENIED; ?>
            </div>
        <?php
        }
        ?>
        </div>
    </div>

</div>

<script src="bootstrap/js/bootstrap.min.js"></script>

</body>
</html>
