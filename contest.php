<?php
	require_once("session.php");
	require_once("class.user.php");

	$auth_user = new USER();

	$user_id = $_SESSION['user_session'];

	$stmt = $auth_user->runQuery("SELECT user_id, user_name, user_email, user_role FROM users WHERE user_id=:user_id");
	$stmt->execute(array(":user_id"=>$user_id));

	$userRow=$stmt->fetch(PDO::FETCH_ASSOC);

    // actions part
    if( isset($_POST['action']) && $_POST['action'] != "" ) {
        if($_POST['action'] == "addContest"){
            if( isset($_POST['txt_cname']) && $_POST['txt_cname'] != ""){
                $cname= strip_tags($_POST['txt_cname']);
                $res = $auth_user->createContest($cname);
                if($res){
                    $success = $_CONTEST_ADD_SUCCESS . $cname . " !";
                }
                else {
                    $error[] = $_CONTEST_ADD_ERROR . " <b>" . $cname . "</b> !";
                }
            }
            else {
                $error[] = $_CONTEST_TITLE;
            }
        }
        if($_POST['action'] == "changeContest"){
            if( isset($_POST['con_id']) && $_POST['con_id'] != ""){
                $con_id= strip_tags($_POST['con_id']);
                $res = $auth_user->updateContest($con_id, strip_tags($_POST['con_state']));
                if($res){
                    $success = $_CONTEST_UPDATE_SUCCESS . " <b>#" . $con_id . "</b>!";
                }
                else {
                    $error[] = $_CONTEST_UPDATE_ERROR . " <b>#" . $con_id . "</b>!";
                }
            }
        }
        if($_POST['action'] == "delContest"){
            if($userRow['user_role'] >= 75){
                if( isset($_POST['con_id']) && $_POST['con_id'] != ""){
                    $con_id= strip_tags($_POST['con_id']);
                    $res = $auth_user->deleteContest($con_id);
                    if($res){
                        $success = $_CONTEST_DELETE_SUCCESS . " <b>#" . $con_id . "</b>!";
                    }
                    else {
                        $error[] = $_CONTEST_DELETE_ERROR . " <b>#" . $con_id . "</b>!";
                    }
                }
            }
        }
        if($_POST['action'] == "addScore"){
            if( isset($_POST['txt_scorenb']) && $_POST['txt_scorenb'] != ""){
                $sel_user= strip_tags($_POST['sel_user']);
                $con_id= strip_tags($_POST['con_id']);
                $scorenb= strip_tags($_POST['txt_scorenb']);

                $res = $auth_user->addScore($sel_user, $con_id, $scorenb);
                if($res){
                    $stmt = $auth_user->runQuery("SELECT user_name FROM users WHERE user_id=:uid");

                    $stmt->execute(array(":uid"=>$sel_user));

                    $uRow=$stmt->fetch(PDO::FETCH_ASSOC);
                    $success = $_CONTEST_SCORE_SUCCESS1 . " <b>(". $scorenb . ")</b> " . $_CONTEST_SCORE_SUCCESS2 . " <b>" . $uRow['user_name'] . "</b>!";
                }
                else {
                    $error[] = $_CONTEST_SCORE_ERROR;
                }
                // reload page with current contest, messing with $_POST data
                $_POST['contest'] = $con_id;
            }
            else {
                $error[] = $_CONTEST_SCORE_NEED;
            }
        }
    }

    // contest part
    if(isset($_POST['contest']) && $_POST['contest'] != ""){
        $query = "SELECT user_name, score_number FROM scores INNER JOIN users ON users.user_id = scores.score_userid INNER JOIN contests ON scores.score_contestid = contests.contest_id WHERE contests.contest_id = :contest_id ORDER BY scores.score_number DESC";
        $stmt = $auth_user->runQuery($query);
        $stmt->execute(array(":contest_id"=>strip_tags($_POST['contest'])));

        $contestRow=$stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $auth_user->runQuery("SELECT contest_id, contest_name, contest_date, contest_state FROM contests WHERE contest_id = :con_id");
        $stmt->execute(array(":con_id"=>strip_tags($_POST['contest'])));

        $contestNameRow=$stmt->fetch(PDO::FETCH_ASSOC);
    }
    else {
        $stmt = $auth_user->runQuery("SELECT contest_id, contest_name, contest_date, contest_state FROM contests");
        $stmt->execute();

        $allContestRow=$stmt->fetchAll(PDO::FETCH_ASSOC);
    }


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
<link href="bootstrap/css/bootstrap-theme.min.css" rel="stylesheet" media="screen">
<script type="text/javascript" src="jquery-1.11.3-jquery.min.js"></script>
<link rel="stylesheet" href="style.css" type="text/css"  />
<title>Kontest - <?php echo $_SITE_TITLE_CONTEST ?></title>
</head>

<body>

	<?php
		require_once('navbar_skeleton.php');
	?>

    <div class="clearfix"></div>


<div class="container-fluid" style="margin-top:80px; margin-bottom:50px">

    <div class="container">

        <h1><span class="glyphicon glyphicon-screenshot"></span> <?php echo $_SITE_TITLE_CONTEST; ?></h1>
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
            else if(isset($success))
            {
            ?>
            <div class="alert alert-success alert-dismissable fade in">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <i class="glyphicon glyphicon-ok"></i> <?php echo $success; ?>
            </div>
        <?php
            }
        // <!-- ** Contest part ** !-->
        if( isset($allContestRow)) {
            if($userRow['user_role'] >= 50 ) {
        ?>
            <!-- Modal New Contest -->
            <div class="modal fade" id="myModal" role="dialog">
                <div class="modal-dialog ">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title"><?php echo $_CONTEST_ADD; ?></h4>
                        </div>
                        <div class="modal-body">
                            <form action="" method="post" class="form-horizontal">
                                <div class="form-group">
                                    <label class="control-label col-sm-3" for="text"><?php echo $_CONTEST_ADD_TITLE; ?>:</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="txt_cname" placeholder="<?php echo $_CONTEST_ADD_CAPTION; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-offset-1 col-sm-11">
                                        <button name="action" value="addContest" class="btn btn-default"><?php echo $_KONTEST_SUBMIT; ?></button>
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

            <div class="pull-right">
                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#myModal"><span class="glyphicon glyphicon-pencil"></span> <?php $_CONTEST_ADD; ?></button>
            </div>

            <div class="clearfix"></div>
            <br />
        <?php
            }
        ?>
        <div class="list-group">
        <?php
            if(count($allContestRow) == 0){
                echo '<div class="alert alert-warning" role="alert">' . $_CONTEST_EMPTY . '</div>';
            }
            else{
                foreach($allContestRow as $contest){

                    if($contest['contest_state'] == "1"){
                        echo '<span class="list-group-item list-group-item-success">';
                    }
                    else {
                        echo '<span class="list-group-item list-group-item-danger">';
                    }

                    echo '<h4 class="list-group-item-heading">'. $contest['contest_id'] . ' <span class="glyphicon glyphicon-chevron-right"></span> '. $contest['contest_name'] . '&nbsp;<small><em>' . ($contest['contest_state'] == "1" ? $_CONTEST_OPEN : $_CONTEST_CLOSE) . '</em></small></h4>';

                    if($userRow['user_role'] >= 50 ) {
                    echo '<div class="pull-right"><form action="" method="post"><input type="hidden" name="con_id" value=' . $contest['contest_id'] .'><input type="hidden" name="con_state" value="' . ($contest['contest_state'] == "1" ? '0' : '1') .'">';

                    if($contest['contest_state'] == "1"){
                        echo '<button name="action" value="changeContest" class="btn btn-sm btn-warning"><i class="glyphicon glyphicon-eye-close"></i></button>';
                    }
                    else {
                        echo '<button name="action" value="changeContest" class="btn btn-sm btn-success"><i class="glyphicon glyphicon-eye-open"></i></button>';
                    }
                    if($userRow['user_role'] > 50 ) {
                        echo '&nbsp;<button name="action" value="delContest" class="btn btn-sm btn-danger"><i class="glyphicon glyphicon-remove"></i></button>';
                    }
                    echo '</form></div>';
                    }
                    echo '<p class="list-group-item-text">' . $_CONTEST_ADDDATE . ' ' . $contest['contest_date'] . '</p><form action= "" method="post"><button name="contest" value="' . $contest['contest_id'] . '" class="btn btn-info">' . $_CONTEST_VIEW . '</button></form></span>';
                }
            }
        ?>
        </div>
        <!-- ** Score part ** !-->
        <?php
        }
        elseif( isset($contestRow) && isset($contestNameRow) ) {
        ?>
            <h4><span class="glyphicon glyphicon-chevron-right"></span> <?php echo $contestNameRow['contest_name']; ?>:</h4>
            <?php
                if($userRow['user_role'] >= 50 && $contestNameRow['contest_state'] == "1") {
            ?>
            <div class="pull-right">
                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#myModalScore"><span class="glyphicon glyphicon-pencil"></span> <?php echo $_CONTEST_SCORE_ADD; ?></button>
            </div>

            <div class="clearfix"></div>
            <br />
            <!-- Modal Score -->
            <div class="modal fade" id="myModalScore" role="dialog">
                <div class="modal-dialog ">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title"><?php echo $_CONTEST_ADD_NEWSCORE . ' ' . $contestNameRow['contest_name']; ?></h4>
                        </div>
                        <div class="modal-body">
                            <form action="" method="post" class="form-horizontal">
                                <div class="form-group">
                                    <label class="control-label col-sm-3" for="user"><?php echo $_KONTEST_USER; ?>:</label>
                                    <div class="col-sm-9">
                                        <select name="sel_user">
                                            <?php
                                                $stmt = $auth_user->runQuery("SELECT user_id,user_name FROM users ORDER BY user_role DESC, user_id ASC");
                                                $stmt->execute();

                                                $allUserRow=$stmt->fetchAll(PDO::FETCH_ASSOC);

                                                foreach($allUserRow as $user){
                                                    echo '<option value="'. $user['user_id'] .'">'. $user['user_name'] .'</option>';
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3" for="user"><?php echo $_KONTEST_SCORE; ?>:</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="txt_scorenb" placeholder="<?php echo $_KONTEST_USER; ?>">
                                        <input type="hidden" name="con_id" value="<?php echo $contestNameRow['contest_id']; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-offset-1 col-sm-11">
                                        <button name="action" value="addScore" class="btn btn-default"><?php echo $_KONTEST_SUBMIT; ?></button>
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
            <?php
                }

                if(count($contestRow) == 0){
                    echo '<div class="alert alert-warning" role="alert">' . $_CONTEST_SCORE_EMPTY . '</div>';
                }
                else {
                ?>
                <div class="text-center">
                    <div class="competition-podium well">
                      <div class="podium-block bronze">
                        <div class="name"><?php echo isset($contestRow[2]['user_name']) ? $contestRow[2]['user_name'] : 'N/A' ?></div>
                          <div class="podium">
                            <span><p>3</p><p><small><em>(<?php echo isset($contestRow[2]['score_number']) ? $contestRow[2]['score_number'] : '0' ?>)</em></small></p></span>
                        </div>
                      </div>
                      <div class="podium-block gold">
                        <div class="name"><?php echo isset($contestRow[0]['user_name']) ? $contestRow[0]['user_name'] : 'N/A' ?></div>
                          <div class="podium">
                            <span><p>1</p><p><small><em>(<?php echo isset($contestRow[0]['score_number']) ? $contestRow[0]['score_number'] : '0' ?>)</em></small></p></span>
                          </div>
                      </div>
                      <div class="podium-block silver">
                        <div class="name"><?php echo isset($contestRow[1]['user_name']) ? $contestRow[1]['user_name'] : 'N/A' ?></div>
                          <div class="podium">
                            <span><p>2</p><p><small><em>(<?php echo isset($contestRow[1]['score_number']) ? $contestRow[1]['score_number'] : '0' ?>)</em></small></p></span>
                          </div>
                      </div>
                    </div>
                </div>
                <ul class="list-group">
                <?php
                    $index=1;
                    foreach($contestRow as $score){
                        switch($index){
                            case 1:
                                echo '<li class="list-group-item list-group-item-success">';
                                break;
                            case 2:
                                echo '<li class="list-group-item list-group-item-warning">';
                                break;
                            case 3:
                                echo '<li class="list-group-item list-group-item-danger">';
                                break;
                            default:
                                echo '<li class="list-group-item">';
                                break;
                        }
                        echo '<span class="badge">'. $score['score_number'] .'</span>' .
                                $score['user_name'] .
                             '</li>';
                        $index++;
                    }
                }
            ?>
            </ul>
        <a class="btn btn-default" href="" role="button"><?php echo $_KONTEST_RETURN; ?></a>
        <?php
        }
        ?>
    </div>

</div>

<script src="bootstrap/js/bootstrap.min.js"></script>

</body>
</html>
