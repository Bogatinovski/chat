<?php
require_once(LIB_PATH.DS."config.php");

class MySQLDatabase {
	
	private $connection;
	public $last_query;
	
  function __construct(){
  	 $this->open_connection();	
  }

	public function open_connection() {

		$this->connection = new MySQLi(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
		$this->connection->set_charset("utf8");
		if ($this->connection->connect_error) 
			die("Database connection failed: " . $this->connection->connect_errno . $this->connection->connect_error);
	}

	public function close_connection() {
		if(isset($this->connection)) {
			$this->connection->close();
			unset($this->connection);
		}
	}

	public function query($sql) {
		$this->last_query = $sql;
		$result = $this->connection->query($sql);
		$this->confirm_query($result);
		return $result;
	}

	public function multi_query($sql) {
		$this->last_query = $sql;
		$result = $this->connection->multi_query($sql);
		$this->confirm_query($result);
		return $result;
	}
	
	public function escape_value( $value ) {
		return $this->connection->real_escape_string( $value );
	}
	
  
  public function num_rows($result_set) {
   return mysql_num_rows($result_set);
  }
  
  public function insert_id() {
    // get the last id inserted over the current db connection
    return mysql_insert_id($this->connection);
  }
  
  public function affected_rows() {
    return $this->connection->affected_rows;
  }

	private function confirm_query($result) {
		if (!$result) {
	    $output = "Database query failed: " .$this->connection->error . "<br /><br />";
	    //$output .= "Last SQL query: " . $this->last_query;
	    die( $output );
		}
	}

	public function store_result()
	{
		return $this->connection->store_result();
	}
	
	public function next_result()
	{
		return $this->connection->next_result();
	}

	public function more_results()
	{
		return $this->connection->more_results();
	}

	public function prepare($sql)
	{
		return $this->connection->prepare($sql);
	}
}

$db = new MySQLDatabase();

?>