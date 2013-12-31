<?php

/**
 * @author pengmz
 */
class MySQL extends AbstractDB implements DB {
	
	private static $db_instance;
	
	protected $dbhost = 'localhost';
	protected $dbname = 'mysql';
	protected $dbuser = 'root';
	protected $dbpassword = '';
	protected $dbcharset = 'utf8';
	protected $debug_mode = DEBUG_MODE;
		
	public function MySQL($db_config) {
		if ($db_config) {
			$this->dbhost = $db_config['host'];
			$this->dbname = $db_config['name'];
			$this->dbuser = $db_config['user'];
			$this->dbpassword = $db_config['password'];
			if ($db_config['debug'] == 'true') {
				$this->debug_mode = true;
			}
		}
		if (defined('DB_CHARSET')) {
			$this->dbcharset = DB_CHARSET;
		}
	}
	
	public function escape($val) {
		$conn = $this->getConnection();
		if (get_magic_quotes_gpc()) {
			$val = stripslashes($val);
		}
		$val = mysql_real_escape_string($val, $conn);
		return '\'' . $val . '\'';
	}
	
	public function executeInsert($sql) {
		$conn = $this->getConnection();
		$result = mysql_query($sql, $conn);
		if ($result) {
			Log::debug('[SQL]: ' . $sql);
			return mysql_insert_id($conn);
		} else {
			Log::error('[INSERT][SQL]: ' . $sql . ' [' .  mysql_error($conn) . ']');
		}
		return false;
	}
	
	public function executeUpdate($sql) {
		$conn = $this->getConnection();
		$result = mysql_query($sql, $conn);
		if ($result) {
			Log::debug('[SQL]: ' . $sql);
			return mysql_affected_rows($conn);
		} else {
			Log::error('[UPDATE][SQL]: ' . $sql . ' [' .  mysql_error($conn) . ']');
		}
		return false;
	}
	
	public function executeDelete($sql) {
		return $this->executeUpdate($sql);
	}
	
	public function executeSelect($sql, $mapping_to = OBJECT) {
		$conn = $this->getConnection();
		$result = mysql_query($sql, $conn);
		
		if ($result) {
			Log::debug('[SQL]: ' . $sql);
			if ($mapping_to == OBJECT) {
				$object_result = $this->resultMapperToObject($result);
				mysql_free_result($result);
				return $object_result;
			} else {
				$array_result = $this->resultMapperToArray($result);
				mysql_free_result($result);
				return $array_result;
			}
		} else {
			Log::error('[SELECT][SQL]: ' . $sql . ' [' .  mysql_error($conn) . ']');
		}
		return false;
	}
	
	private function resultMapperToObject($result) {
		$result_list = array();
		while(($obj = mysql_fetch_object($result)) != false) {
			$result_list[] = $obj;
		}
		return $result_list;
	}
	
	private function resultMapperToArray($result) {
		$result_list = array();
		while(($obj = mysql_fetch_object($result)) != false) {
			$result_list[] = get_object_vars($obj);
		}
		return $result_list;
	}
	
	public function getConnection() {
		if (is_null(self::$db_instance)) {
			self::$db_instance = $this->connectToMySQL();
		}
		return self::$db_instance;
	}
	
	public function closeConnection() {
		if (is_resource(self::$db_instance)) {
			mysql_close(self::$db_instance);
		}
		self::$db_instance = NULL;
	}
	
	private function connectToMySQL() {
		$conn = @mysql_connect($this->dbhost, $this->dbuser, $this->dbpassword, true);
		if ($conn == null) {
			Log::error('[DB CONNECT]: Failed to connect to database');
		}
		if (! @mysql_select_db($this->dbname)) {
			Log::error('[DB SELECT]: Unknown database name');
		}
		
		$conn = $this->initCharset($conn);
		return $conn;
	}
	
	private function initCharset($conn) {
		mysql_query('SET NAMES ' . $this->dbcharset, $conn);
		return $conn;
	}
	
	private function initTimeout($conn) {
		mysql_query('SET interactive_timeout=24*3600', $conn);
		return $conn;
	}
	
	public function __destruct() {
		$this->closeConnection();
	}

}
?>