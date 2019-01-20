<?php
	require_once("session.php");
	require_once("class.user.php");

	$auth_user = new USER();

	$user_id = $_SESSION['user_session'];

	$stmt = $auth_user->runQuery("SELECT user_id, user_name, user_email, user_role FROM users WHERE user_id=:user_id");
	$stmt->execute(array(":user_id"=>$user_id));

	$userRow=$stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $auth_user->runQuery("SELECT contest_id, contest_name, contest_date, contest_state FROM contests ORDER BY contest_id DESC");
    $stmt->execute();

    $lastContestRow=$stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $auth_user->runQuery("SELECT COUNT(*) AS count FROM scores WHERE score_contestid = :cid");
    $stmt->execute(array(":cid"=>$lastContestRow['contest_id']));

    $scoreCount=$stmt->fetch(PDO::FETCH_ASSOC);

    if( isset($_POST['action']) && $_POST['action'] != "" ) {
        if($_POST['action'] == "addNews"){
            if( (isset($_POST['txt_newsc']) && $_POST['txt_newsc'] != "") || (isset($_POST['txt_newst']) && $_POST['txt_newst'] != "") ){
                $cnews= strip_tags($_POST['txt_newsc']);
                $tnews= strip_tags($_POST['txt_newst']);
                $res = $auth_user->addNews($tnews, $cnews);
                if($res){
                    $success = $_ARTICLE_CREATE_SUCCESS;
                }
                else {
                    $error[] = $_ARTICLE_CREATE_ERROR;
                }
            }
            else {
                $error[] = $_ARTICLE_TITLE;
            }
        }
        if($_POST['action'] == "delNews"){
            if($userRow['user_role'] >= 50){
                if( isset($_POST['new_id']) && $_POST['new_id'] != ""){
                    $new_id= strip_tags($_POST['new_id']);
                    $res = $auth_user->deleteNews($new_id);
                    if($res){
                        $success = $_ARTICLE_DELETE_SUCCESS . ": <b>#" . $new_id . "</b>!";
                    }
                    else {
                        $error[] = $_ARTICLE_DELETE_ERROR . ": <b>#" . $new_id . "</b>!";
                    }
                }
            }
        }
    }

    // Get LAST 10 NEWS
    $stmt = $auth_user->runQuery("SELECT * FROM news ORDER BY news_id DESC LIMIT 10");
    $stmt->execute();

    $lastNews=$stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
<link href="bootstrap/css/bootstrap-theme.min.css" rel="stylesheet" media="screen">
<script type="text/javascript" src="jquery-1.11.3-jquery.min.js"></script>
<link rel="stylesheet" href="style.css" type="text/css"  />
<title>Kontest - <?php echo $_SITE_TITLE_HOME; ?></title>
</head>

<body>

<?php
	require_once('navbar_skeleton.php');
?>

    <div class="clearfix"></div>


<div class="container-fluid" style="margin-top:80px;">

    <div class="container">

        <h1><span class="glyphicon glyphicon-home"> </span><?php echo $_SITE_TITLE_HOME; ?></h1>
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
            ?>
        <p class="h4"><?php echo $_CONTEST_LAST; ?></p>

        <div class="list-group">
            <?php
                if($lastContestRow == "" ){
                    echo '<div class="alert alert-warning" role="alert"> '. $_CONTEST_EMPTY .'</div>';
                }
                else {
                    if($lastContestRow['contest_state'] == "1"){
                        echo '<span class="list-group-item list-group-item-success">';
                    }
                    else {
                        echo '<span class="list-group-item list-group-item-danger">';
                    }

                    echo '<h4 class="list-group-item-heading">'. $lastContestRow['contest_id'] . ' <span class="glyphicon glyphicon-chevron-right"></span> '. $lastContestRow['contest_name'] . '&nbsp;<small><em>' . ($lastContestRow['contest_state'] == "1" ? $_CONTEST_OPEN : $_CONTEST_CLOSE) . '</em></small></h4>';


                    echo '<p class="list-group-item-text">'. $_CONTEST_ADDDATE . ' ' . $lastContestRow['contest_date'] . '</p><p class="list-group-item-text">' . $_CONTEST_CURRENT . ' <b>' . $scoreCount['count'] . '</b> '. (intval($scoreCount['count']) > 1 ? 'scores' : 'score') .'</p><form action= "contest.php" method="post"><button name="contest" value="' . $lastContestRow['contest_id'] . '" class="btn btn-info">'. $_CONTEST_VIEW .'</button></form></span>';
                }
            ?>
        </div>

        <p class="h4"><?php echo $_ARTICLE_NAME; ?></p>
        <?php
        if($userRow['user_role'] >= 50 ) {
        ?>
        <!-- Modal New Contest -->
        <div class="modal fade" id="myModalNews" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><?php echo $_ARTICLE_ADD_NEW; ?></h4>
                    </div>
                    <div class="modal-body">
                        <form action="" method="post" class="form-horizontal">
                            <div class="form-group">
                                <label class="control-label col-sm-3" for="text"><?php echo $_ARTICLE_ADD_TITLE; ?>:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="txt_newst" placeholder="<?php echo $_ARTICLE_ADD_TITLE_CAPTION; ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-3" for="text"><?php echo $_ARTICLE_ADD_CONTENT; ?>:</label>
                                <div class="col-sm-9">
                                    <textarea class="form-control" rows="5" name="txt_newsc" placeholder="<?php echo $_ARTICLE_ADD_CONTENT_CAPTION; ?>"></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-offset-1 col-sm-11">
                                    <button name="action" value="addNews" class="btn btn-default"><?php echo $_KONTEST_SUBMIT; ?></button>
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
            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#myModalNews"><span class="glyphicon glyphicon-pencil"></span> <?php echo $_ARTICLE_ADD_NEW; ?></button>
        </div>

        <div class="clearfix"></div>
        <br />
        <?php
        }
            if(count($lastNews) == 0 ){
                echo '<div class="alert alert-warning" role="alert"> ' . $_ARTICLE_EMPTY . '</div>';
            }
            else {
                echo '<div class="panel-group" id="accordion">';

                foreach($lastNews as $news) {
                    echo '<div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">';
					if($userRow['user_role'] >= 50){
                    	echo '<div class="pull-right"><form action="" method="post"><input type="hidden" name="new_id" value="'. $news['news_id'] .'"><button name="action" value="delNews" class="btn btn-sm btn-danger"><i class="glyphicon glyphicon-remove"></i></button></form></div>';
					}
                    echo '<a data-toggle="collapse" data-parent="#accordion" href="#collapse'. $news['news_id'] .'"><span class="glyphicon glyphicon-calendar"></span> ' . date("d/m/Y", strtotime($news['news_date'])) . ' <span class="glyphicon glyphicon-menu-right"></span> ' . $news['news_title'] . '</a>
                                </h4>
                            </div>';
                    echo '<div id="collapse' . $news['news_id'] . '" class="panel-collapse collapse">
                            <div class="panel-body">' . $news['news_content'] . '</div>
                        </div></div>';
                }

                echo '</div>'; // end panel accordion
            }
        ?>

    </div>

</div>

<script src="bootstrap/js/bootstrap.min.js"></script>

</body>
</html>
