<?php
final class MySQL {
	private $link;
	public $error = false;
	
	public function __construct($hostname, $username, $password, $database, $pconnect = false) {
	    if($pconnect){
	    	
	    	try {
	    		$this->link = mysql_pconnect($hostname, $username, $password) or $this->throw_ex(mysql_error());
	    	
	    	}
	    	catch (Exception $e){
	    		echo "系统异常，请联系管理员。";
	    		$e_message = $e->getMessage();
	    		$log = new Log("log_sql_error.txt");
	    		$log->write("sql : pconnect_error : " . 'Could not make a database link using ' . $username . '@' . $hostname . " . " . $e_message . "\n\r" . "url : " . 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'] . "\n\r\n\r" );
	    		exit;
	    	}
	    	
	        
	    }else{
	    	
	    	
	    	try {
	    		$this->link = mysql_connect($hostname, $username, $password) or $this->throw_ex(mysql_error());
	    	
	    	}
	    	catch (Exception $e){
	    		echo "系统异常，请联系管理员。";
	    		$e_message = $e->getMessage();
	    		$log = new Log("log_sql_error.txt");
	    		$log->write("sql : connect_error : " . 'Could not make a database link using ' . $username . '@' . $hostname . " . " . $e_message . "\n\r" . "url : " . 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'] . "\n\r\n\r" );
	    		exit;
	    	}
	    	
	    	
	    }
	   
	    
	    
	    
	    
	    
	    
	    try {
	    	mysql_select_db($database, $this->link) or $this->throw_ex(mysql_error());
	    
	    }
	    
	    catch (Exception $e){
	    	echo "系统异常，请联系管理员。";
	    	$e_message = $e->getMessage();
	    	$log = new Log("log_sql_error.txt");
	    	$log->write("sql : select_db_error : " . 'Could not connect to database ' . $database . " . " . $e_message . "\n\r" . "url : " . 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'] . "\n\r\n\r" );
			exit;
	    }
	    
		
		mysql_query("SET NAMES 'utf8'", $this->link);
		mysql_query("SET CHARACTER SET utf8", $this->link);
		mysql_query("SET CHARACTER_SET_CONNECTION=utf8", $this->link);
		mysql_query("SET SQL_MODE = ''", $this->link);
  	}
		
  	public function throw_ex($er,$sql = ''){
  		
  		$title = "数据库异常";
  		$content = "数据库存在异常，请及时修复." . var_export($er, true) . "      url : " . 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'] . ".   sql:" . $sql;
  		
  		//send_database_error_mail( $title, $content);
  		
  		throw new Exception($er);
  		
  		
  	}
  	
  	public function query($sql) {
		if ($this->link) {
			
			
			try {
				$resource = mysql_query($sql, $this->link) or $this->throw_ex(mysql_error(),$sql);
				
			}
			catch (Exception $e){
				//echo "系统异常，请联系管理员。";
				$e_message = $e->getMessage();
				$log = new Log("log_sql_error.txt");
				
				$log->write("sql : " . $sql . "\n\r" . "error : " . $e_message . "\n\r" . "url : " . 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'] . "\n\r\n\r" );
				//exit;
			}
			if ($resource) {
				if (is_resource($resource)) {
					$i = 0;
			
					$data = array();
			
					while ($result = mysql_fetch_assoc($resource)) {
						$data[$i] = $result;
			
						$i++;
					}
					
					mysql_free_result($resource);
					
					$query = new stdClass();
					$query->row = isset($data[0]) ? $data[0] : array();
					$query->rows = $data;
					$query->num_rows = $i;
					
					unset($data);
					
					return $query;	
				} else {
					return true;
				}
			} else {
				trigger_error('Error: ' . mysql_error($this->link) . '<br />Error No: ' . mysql_errno($this->link) . '<br />' . $sql);
				exit();
			}
		}
  	}
	
	public function escape($value) {
		if ($this->link) {
			return mysql_real_escape_string($value, $this->link);
		}
	}
	
  	public function countAffected() {
		if ($this->link) {
    		return mysql_affected_rows($this->link);
		}
  	}

  	public function getLastId() {
		if ($this->link) {
    		return mysql_insert_id($this->link);
		}
  	}

    public function begin(){
        if($this->link){
            mysql_query("SET AUTOCOMMIT=0");
            mysql_query('START TRANSACTION');
        }
    }

    public function commit(){
        if($this->link){
            mysql_query("COMMIT");
            mysql_query("SET AUTOCOMMIT=1");
        }
    }

    public function rollback(){
        if($this->link){
            mysql_query("ROLLBACK");
            mysql_query("SET AUTOCOMMIT=1");
        }
    }

	public function __destruct() {
        if(mysql_error() !== false) {
			mysql_close($this->link);
		}
	}
}
?>