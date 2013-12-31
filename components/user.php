<?php

class User extends Model {
		
	public function __construct() {
		parent::__construct('users');
	}
		
}

class UserForm extends RequestForm {

	public function __construct($data) {
		parent::__construct($data, array('id', 'username', 'password', 'email'));
	}
	
	public function isValid() {
		$valid = parent::isValid();
		if (! $valid) {
			$this->alertMessage('Please fill out the form');
			return false;
		}
		if (! IT::isNotNull($this->username)) {
			$this->alertMessage('Please enter username');
			return false;			
		}
		if (! IT::isEmail($this->email)) {
			$this->alertMessage('Email is incorrect');
			return false;			
		}
		return true;
	}	
}

?>