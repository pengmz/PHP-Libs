<?php

class Role extends Model {
		
	public function __construct() {
		parent::__construct('roles');
	}
		
}

class RoleForm extends RequestForm {

	public function __construct($data) {
		parent::__construct($data, array('id', 'name', 'title', 'description'));
	}
	
}

?>