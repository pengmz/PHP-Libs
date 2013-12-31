<?php

class ModuleController extends BaseController {
	
	public function __construct() {
		parent::__construct();
	}
		
	protected function beforeAction($action) {
		if (!$this->hasPermission($action)) {
			$this->handleAccessDenied();
		}
		return true;
	}
	
	protected function hasPermission($action) {
		return true;
	}
	
	protected function alertMessage($message, $type = 'info') {
		ALERT::add($message, $type);
	}	
	
	protected function handleAccessDenied() {
		//TODO show 403 page
		exit;
	}

}

?>