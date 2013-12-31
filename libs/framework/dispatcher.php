<?php

/**
 * @author pengmz
 */
class Dispatcher implements Component {
	
	private $controller;
	private $action;
	private $module;
	private $layout;
	
	public function __construct() {
		$this->__autowire();
	}
	
	public function dispatch() {
		$hooks = array (
			$this->controller . '/' . $this->action,
			$this->controller . '/*',
			'*'			
		);
		HOOK::beforeFilter($hooks);
		$controller = $this->getController($this->controller);
		if ($controller) {
			$controller->handleRequest($this->action, $this->layout);
		}
		//HOOK::afterFilter($hooks);
	}

	public function getController($controller_name) {
		$controller_suffix = '_controller';
		$controller_name .= $controller_suffix;			
		return ComponentLoader::getComponent($controller_name, get_controller_path());		
	}
	
	public final function __autowire() {
		$this->controller = get_controller_name();
		$this->action = get_action_name();
		$this->module = get_module_name();
		$this->layout = get_layout_name();
	}
		
}
?>