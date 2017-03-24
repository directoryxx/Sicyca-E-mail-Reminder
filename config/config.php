<?php
class Database
{
    private $host = "localhost";
    private $username = "root";
    private $password = "xxx";

    private $db_name = "test";
    // Isi Variabel sti sama dengan db_name
    private $sti = "test";

    public $conn;

    public $connsti;



    public function dbConnection()
	   {

	            $this->conn = null;
              $this->connsti = null;
     try
		 {
              $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
			        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
              $this->connsti = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->sti, $this->username, $this->password);
              $this->connsti->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
     }
		catch(PDOException $exception)
		{
              echo "Connection error: " . $exception->getMessage();
    }

              return $this->conn;
              return $this->connsti;
    }
}



?>
