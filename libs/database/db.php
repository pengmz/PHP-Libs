<?php

define('OBJECT', 'OBJECT');
define('ARRAY_A', 'ARRAY_A');
define('ARRAY_N', 'ARRAY_N');

/**
 * @author pengmz
 */
interface DB {
	
	public function queryForList($sql);
	
	public function queryForArray($sql);
	
	public function queryForObject($sql);
	
	public function queryForVar($sql);
	
	public function executeInsert($sql);
	
	public function executeUpdate($sql);
	
	public function executeDelete($sql);
	
	public function executeSelect($sql, $mapping_to = OBJECT);
	
	public function escape($string);

}

abstract class AbstractDB {
	
	public function find($table, $where = '0', $limit = 1) {
		$sql = 'SELECT * FROM ' . $table . ' WHERE ' . $where . ' LIMIT ' . $limit;
		if ($limit > 1){ 
			return $this->queryForList($sql);
		} else {
			return $this->queryForObject($sql);
		}
	}
	
	public function findAll($table, $where = '1') {
		$sql = 'SELECT * FROM ' . $table . ' WHERE ' . $where;
		return $this->queryForList($sql);
	}
	
	public function insert($table, $data) {
		$keys = array();
		$values = array();
		foreach($data as $key => $val) {
			if ($key === NULL || $val === NULL) {
				continue;
			}
			$keys[] = $key;
			$values[] = $this->escape($val);
		}
		$sql = 'INSERT INTO ' . $table . ' (' . implode(', ', $keys) . ') VALUES (' . implode(', ', $values) . ')';
		return $this->executeInsert($sql);
	}
	
	public function update($table, $data, $where = '0') {
		foreach($data as $key => $val) {
			if ($key === NULL || $val === NULL) {
				continue;
			}
			$values[] = $key . ' = ' . $this->escape($val);
		}
		$sql = 'UPDATE ' . $table . ' SET ' . implode(', ', $values) . ' WHERE ' . $where;
		return $this->executeUpdate($sql);
	}
	
	public function updateAll($table, $data, $where = '1') {
		return $this->update($table, $data, $where);
	}
	
	public function delete($table, $where = '0') {
		$sql = 'DELETE FROM ' . $table . ' WHERE ' . $where;
		return $this->executeDelete($sql);
	}
	
	public function deleteAll($table, $where = '1') {
		return $this->delete($table, $where);
	}
	
	public function escape($val) {
		if (get_magic_quotes_gpc()) {
			$val = stripslashes($val);
		}
		return '\'' . $val . '\'';
	}
	
	public function queryForList($sql) {
		return $this->executeSelect($sql);
	}
	
	public function queryForArray($sql) {
		return $this->executeSelect($sql, ARRAY_A);
	}
	
	public function queryForObject($sql) {
		$list = $this->queryForList($sql);
		if ($list) {
			return $list[0];
		}
		return null;
	}
	
	public function queryForVar($sql) {
		$array = $this->queryForArray($sql);
		if ($array) {
			$array = array_values($array[0]);
			return $array[0];
		}
		return null;
	}
		
//	public abstract function executeInsert($sql);	
//	public abstract function executeUpdate($sql);	
//	public abstract function executeDelete($sql);	
//	public abstract function executeSelect($sql, $mapping_to);	
	
}

/**
 * @author pengmz
 */
class DBException extends Exception {
	
    public function __construct($message) {
        parent::__construct($message, 500);
    }
    
    public function __toString() {
		return "[DBException][$this->code] $this->message 
				in file $this->file on line $this->line";
    }
}

global $DB, $DBCFG;

function get_db($db_config = array()) {
	global $DB;	
	if (! $DB) {
		if (empty($db_config)) {
			global $DBCFG;
			$db_config = $DBCFG;
		}
		include_once dirname(__FILE__) . '/mysql.php';
		$DB = new MySQL($db_config);
		if (! $DB) {
			Log::error('Database load error');
		}
	}	
	return $DB;
}

function db_query_for_list($sql) {
	return get_db()->queryForList($sql);
}

function db_query_for_array($sql) {
	return get_db()->queryForArray($sql);
}

function db_query_for_object($sql) {
	return get_db()->queryForObject($sql);
}

function db_query_for_var($sql) {
	return get_db()->queryForVar($sql);
}

function db_execute_insert($sql) {
	return get_db()->executeInsert($sql);
}

function db_execute_update($sql) {
	return get_db()->executeUpdate($sql);
}

function db_execute_delete($sql) {
	return get_db()->executeDelete($sql);
}

global $SQL_DB;

function get_mssql_db($db_config = array()) {
	global $SQL_DB;	
	if (! $SQL_DB) {
		include_once dirname(__FILE__) . '/mssql.php';
		$SQL_DB = new MSSQL($db_config);
		if (! $SQL_DB) {
			Log::error('Database load error');
		}
	}	
	return $SQL_DB;
}

?>