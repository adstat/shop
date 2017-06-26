<?php
class DB{
	private $driver;
	
	public function __construct($driver, $hostname, $username, $password, $database) {
		if (file_exists(DIR_DATABASE . $driver . '.php')) {
			require_once(DIR_DATABASE . $driver . '.php');
		} else {
			exit('Error: Could not load database file ' . $driver . '!');
		}
				
		$this->driver = new $driver($hostname, $username, $password, $database);
		
	}
		
  	public function query($sql) {
  	    if(defined('SQL_DEBUG') && SQL_DEBUG) { $caller = debug_backtrace(); Debug::trigger('sql', $sql, $caller); }
		return $this->driver->query($sql);
  	}

	public function escape($value) {
		return $this->driver->escape($value);
	}

  	public function countAffected() {
		return $this->driver->countAffected();
  	}

  	public function getLastId() {
		return $this->driver->getLastId();
  	}

    public function begin(){
        return $this->driver->begin();
    }

    public function commit(){
        return $this->driver->commit();
    }

    public function rollback(){
        return $this->driver->rollback();
    }
}
//MASTER
$dbm = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

//SLAVE, SELECT
$db = new DB(DB_DRIVER_SLAVE, DB_HOSTNAME_SLAVE, DB_USERNAME_SLAVE, DB_PASSWORD_SLAVE, DB_DATABASE_SLAVE);

//Last Day
//$db_lastday = new DB(DB_LASTDAY_DRIVER, DB_LASTDAY_HOSTNAME, DB_LASTDAY_USERNAME, DB_LASTDAY_PASSWORD, DB_LASTDAY_DATABASE);
?>