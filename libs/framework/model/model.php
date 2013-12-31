<?php

/**
 * @author pengmz
 */	
class DataAccessObject extends BaseComponent {
	
	protected $db;
	
	public function __construct() {
		parent::__construct();
	}
	
	public function escape($string) {
		return $this->db->escape($string);
	}	
}
	
/**
 * @author pengmz
 */
class Model extends DataAccessObject {
		
	protected $table;
	
	public function __construct($table_name) {
		parent::__construct();
		$this->table = $table_name;
	}
	
	public function findById($id) {
		return $this->findBy('id', $id);
	}
	
	public function findBy($key, $value, $limit = 1) {
		$value = $this->escape($value);
		return $this->find("$key = $value", $limit);
	}

	public function updateById($id, $data) {
		return $this->updateBy('id', $id, $data);
	}
	
	public function updateBy($key, $value, $data) {
		$value = $this->escape($value);
		return $this->update($data, "$key = $value");
	}
	
	public function deleteById($id) {
		return $this->deleteBy('id', $id);
	}
	
	public function deleteBy($key, $value) {
		$value = $this->escape($value);
		return $this->delete("$key = $value");
	}
		
	public function find($where = '0', $limit = 1) {
		return $this->db->find($this->table, $where, $limit);
	}
	
	public function save($data) {
		return $this->db->insert($this->table, $data);
	}
	
	public function update($data, $where = '0') {
		return $this->db->update($this->table, $data, $where);
	}
	
	public function delete($where = '0') {
		return $this->db->delete($this->table, $where);
	}
		
	public function findAll($where = '1') {
		return $this->db->findAll($this->table, $where);
	}
		
	public function updateAll($data, $where = '1') {
		return $this->db->updateAll($this->table, $data, $where);
	}
	
	public function deleteAll($where = '1') {
		return $this->db->deleteAll($this->table, $where);
	}

}

class ScaffoldModel extends Model {
		
	public function __construct($table_name) {
		parent::__construct($table_name);
	}
		
	public function getFields() {
		return $this->db->queryForList('DESC ' . $this->table);
	}	
	
}

?>