<?php

class DefaultController extends ModuleController {
	
	private $user;
	
	public function index() {
		return $this->render('index');
	}

	public function login() {
		
	}
	
	public function logout() {
		
	}

	public function setUser($user) {
		$this->user = $user;
	}
	
}

?>