<?php
class Database
{
    private $host = "YOUR_DB_HOST";
    private $db_name = "YOUR_DB_NAME";
    private $username = "YOUR_DB_USERNAME";
    private $password = "YOUR_DBUSER_PASSWD";
    public $conn;

    public function dbConnection()
	{

	    $this->conn = null;
        try
		{
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
			$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
		catch(PDOException $exception)
		{
            echo "Connection error: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
?>
