<?php

require_once('dbconfig.php');
require_once('language.php');

class USER
{

	private $conn;

	public function __construct()
	{
		$database = new Database();
		$db = $database->dbConnection();
		$this->conn = $db;
    }

	public function runQuery($sql)
	{
		$stmt = $this->conn->prepare($sql);
		return $stmt;
	}

	public function register($uname,$umail,$upass)
	{
		try
		{
			$new_password = password_hash($upass, PASSWORD_DEFAULT);
			$stmt = $this->conn->prepare("INSERT INTO users(user_name,user_email,user_pass)
		                                               VALUES(:uname, :umail, :upass)");

			$stmt->bindparam(":uname", $uname);
			$stmt->bindparam(":umail", $umail);
			$stmt->bindparam(":upass", $new_password);

			$stmt->execute();

			return $stmt;
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
		}
	}

    public function updateAvatar($userid,$userpic)
    {
        try
        {
            $stmt = $this->conn->prepare("UPDATE users SET user_pic=:upic WHERE user_id=:uid");
            $stmt->bindParam(":upic",$userpic);
            $stmt->bindParam(":uid",$userid);

            return $stmt;
        }
        catch(PDOException $e)
		{
			echo $e->getMessage();
		}
    }

    public function updateLevel($userid,$level)
    {
        try
        {
            $stmt = $this->conn->prepare("UPDATE users SET user_role=:role WHERE user_id=:uid");
            $stmt->bindParam(":role",$level);
            $stmt->bindParam(":uid",$userid);

            $stmt->execute();

			return $stmt;
        }
        catch(PDOException $e)
		{
			echo $e->getMessage();
		}
    }

    public function deleteUser($userid)
    {
        try
        {
            $stmt = $this->conn->prepare("DELETE FROM users WHERE user_id=:uid");
            $stmt->bindParam(":uid",$userid);

            $stmt->execute();

			// delete associated scores to user
			$scores = $this->deleteScore($userid);

			return ($stmt && $scores);
        }
        catch(PDOException $e)
		{
			echo $e->getMessage();
		}
    }

	public function deleteScore($userid){
		try
		{
			$stmt = $this->conn->prepare("DELETE FROM scores WHERE score_userid=:uid");
			$stmt->bindParam(":uid",$userid);

			$stmt->execute();

			return $stmt;
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
		}
	}

    public function changeMail($userid, $email)
    {
        try
        {
            $stmt = $this->conn->prepare("UPDATE users SET user_email=:email WHERE user_id=:userid");
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":userid",$userid);

            $stmt->execute();

			return $stmt;
        }
        catch(PDOException $e)
		{
			echo $e->getMessage();
		}
    }

    public function changePass($userid, $pass)
    {
        try
        {
            $stmt = $this->conn->prepare("UPDATE users SET user_pass=:passwd WHERE user_id=:userid");
            $stmt->bindParam(":passwd", password_hash($pass, PASSWORD_DEFAULT));
            $stmt->bindParam(":userid",$userid);

            $stmt->execute();

			return $stmt;
        }
        catch(PDOException $e)
		{
			echo $e->getMessage();
		}
    }

	public function doLogin($uname,$umail,$upass)
	{
		try
		{
			$stmt = $this->conn->prepare("SELECT user_id, user_name, user_email, user_pass FROM users WHERE user_name=:uname OR user_email=:umail ");
			$stmt->execute(array(':uname'=>$uname, ':umail'=>$umail));
			$userRow=$stmt->fetch(PDO::FETCH_ASSOC);
			if($stmt->rowCount() == 1)
			{
				if(password_verify($upass, $userRow['user_pass']))
				{
					$_SESSION['user_session'] = $userRow['user_id'];
					return true;
				}
				else
				{
					return false;
				}
			}
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
		}
	}

    public function createContest($name)
	{
		try
		{
			$stmt = $this->conn->prepare("INSERT INTO contests(contest_name,contest_date)
		                                               VALUES(:cname, :cdate)");

			$stmt->bindparam(":cname", $name);
			$stmt->bindparam(":cdate", date("Y-m-d"));

			$stmt->execute();

			return $stmt;
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
		}
	}

	public function updateContest($contestid,$state)
    {
        try
        {
            $stmt = $this->conn->prepare("UPDATE contests SET contest_state=:state WHERE contest_id=:contestid");
            $stmt->bindParam(":state",$state);
            $stmt->bindParam(":contestid",$contestid);

            $stmt->execute();

			return $stmt;
        }
        catch(PDOException $e)
		{
			echo $e->getMessage();
		}
    }

    public function deleteContest($contestid)
    {
        try
        {
            $stmt = $this->conn->prepare("DELETE FROM contests WHERE contest_id=:con_id");
            $stmt->bindParam(":con_id",$contestid);

            $stmt->execute();

			return $stmt;
        }
        catch(PDOException $e)
		{
			echo $e->getMessage();
		}
    }

    public function addScore($userid, $contestid, $score)
	{
		try
		{
			$stmt = $this->conn->prepare("INSERT INTO scores(score_userid, score_contestid,score_number) VALUES(:suserid, :sconid, :snumber)");

			$stmt->bindparam(":suserid", $userid);
            $stmt->bindparam(":sconid", $contestid);
            $stmt->bindparam(":snumber", $score);

			$stmt->execute();

			return $stmt;
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
		}
	}

    public function addNews($title, $content)
	{
		try
		{
			$stmt = $this->conn->prepare("INSERT INTO news(news_date, news_title, news_content) VALUES(:ndate, :ntitle, :ncontent)");

			$stmt->bindparam(":ndate", date("Y-m-d"));
            $stmt->bindparam(":ntitle", $title);
            $stmt->bindparam(":ncontent", $content);

			$stmt->execute();

			return $stmt;
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
		}
	}

    public function deleteNews($newsid)
    {
        try
        {
            $stmt = $this->conn->prepare("DELETE FROM news WHERE news_id=:n_id");
            $stmt->bindParam(":n_id",$newsid);

            $stmt->execute();

			return $stmt;
        }
        catch(PDOException $e)
		{
			echo $e->getMessage();
		}
    }

	public function is_loggedin()
	{
		if(isset($_SESSION['user_session']))
		{
			return true;
		}
	}

	public function redirect($url)
	{
		header("Location: $url");
	}

	public function doLogout()
	{
		session_destroy();
		unset($_SESSION['user_session']);
		return true;
	}
}
?>
