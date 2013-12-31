<?php

/**
 * @author pengmz
 */
class MSSQL extends AbstractDB implements DB {
	
	private static $db_instance;
	
	protected $dbhost = 'localhost';
	protected $dbname = 'mysql';
	protected $dbuser = 'root';
	protected $dbpassword = '';
	protected $dbcharset = 'utf8';
	protected $debug_mode = DEBUG_MODE;
		
	public function MSSQL($db_config) {
		if ($db_config) {
			$this->dbhost = $db_config['host'];
			$this->dbname = $db_config['name'];
			$this->dbuser = $db_config['user'];
			$this->dbpassword = $db_config['password'];
			if ($db_config['debug'] == 'true') {
				$this->debug_mode = true;
			}
		}
	}
	
	public function escape($val) {
		if (get_magic_quotes_gpc()) {
			$val = stripslashes($val);
		}
		$val = str_replace("'", "''", $val);
		return '\'' . $val . '\'';
	}	
	
	public function executeInsert($sql) {
		$conn = $this->getConnection();
		$result = mssql_query($sql, $conn);
		if ($result) {
			Log::debug('[SQL]: ' . $sql);
			return mssql_insert_id($conn);
		} else {
			Log::error('[INSERT][SQL]: ' . $sql);
		}
		return false;
	}
	
	public function executeUpdate($sql) {
		$conn = $this->getConnection();
		$result = mssql_query($sql, $conn);
		if ($result) {
			Log::debug('[SQL]: ' . $sql);
			return mssql_rows_affected($conn);
		} else {
			Log::error('[UPDATE][SQL]: ' . $sql);
		}
		return false;
	}
	
	public function executeDelete($sql) {
		return $this->executeUpdate($sql);
	}
	
	public function executeSelect($sql, $mapping_to = OBJECT) {
		$conn = $this->getConnection();
		$result = mssql_query($sql, $conn);
		
		if ($result) {
			Log::debug('[SQL]: ' . $sql);
			if ($mapping_to == OBJECT) {
				$object_result = $this->resultMapperToObject($result);
				mssql_free_result($result);
				return $object_result;
			} else {
				$array_result = $this->resultMapperToArray($result);
				mssql_free_result($result);
				return $array_result;
			}
		} else {
			Log::error('[SELECT][SQL]: ' . $sql);
		}
		return false;
	}
	
	private function resultMapperToObject($result) {
		$result_list = array();
		while(($obj = mssql_fetch_object($result)) != false) {
			$result_list[] = $obj;
		}
		return $result_list;
	}
	
	private function resultMapperToArray($result) {
		$result_list = array();
		while(($obj = mssql_fetch_object($result)) != false) {
			$result_list[] = get_object_vars($obj);
		}
		return $result_list;
	}
	
	public function getConnection() {
		if (is_null(self::$db_instance)) {
			self::$db_instance = $this->connectToMSSQL();
		}
		return self::$db_instance;
	}
	
	public function closeConnection() {
		if (is_resource(self::$db_instance)) {
			mssql_close(self::$db_instance);
		}
		self::$db_instance = NULL;
	}
	
	private function connectToMSSQL() {
		$conn = @mssql_connect($this->dbhost, $this->dbuser, $this->dbpassword);
		if ($conn == null) {
			Log::error('[DB CONNECT]: Failed to connect to database');
		}
		if (! @mssql_select_db($this->dbname)) {
			Log::error('[DB SELECT]: Unknown database name');
		}
		
		//$conn = $this->initCharset($conn);
		return $conn;
	}
	
	private function initCharset($conn) {
		mssql_query('SET NAMES ' . $this->dbcharset, $conn);
		return $conn;
	}
		
	public function __destruct() {
		$this->closeConnection();
	}

}
?>