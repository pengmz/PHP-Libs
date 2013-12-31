<?php
require LIB_PATH . 'app.php';
require FRAMEWORK_PATH . 'functions.php';
require FRAMEWORK_PATH . 'dispatcher.php';
require FRAMEWORK_PATH . 'controller/controller.php';
require FRAMEWORK_PATH . 'model/model.php';
require FRAMEWORK_PATH . 'model/form.php';
require FRAMEWORK_PATH . 'view/view.php';
require FRAMEWORK_PATH . 'helper/html.php';
require FRAMEWORK_PATH . 'helper/validation.php';
require LIB_PATH . 'theme.php';


/**
 * @author pengmz
 */
class Application extends App {
	
	public function __construct($path = null) {
		parent::__construct($path);
	}
	
	public function init() {
		parent::init();
		$this->initApplicationPaths();
	}
		
	public function run() {
		parent::run();
		$this->dispatch();
	}
		
	public function dispatch() {
		$dispatcher = new Dispatcher();
		$dispatcher->dispatch();
	}			
	
	public function initComponent() {
		parent::initComponent();
		
		$component_config = $this->loadComponentConfig();
		if ($component_config) {
			$this->initComponentConfig($component_config);
		}
	}	

	public function initComponentConfig($component_config) {
		if (isset($component_config['path'])) {
			$this->initIncludePath($component_config['path']);
		}
		if (isset($component_config['class'])) {
			$component_classes = $component_config['class'];
			foreach ($component_classes as $component_name => $component_alias) {
				ComponentLoader::registerComponent($component_name, $component_alias);
			}
		}
	}
		
	public function loadComponentConfig() {
		$component_config_file = $this->path . 'configs/component_config.php';
		if (is_file($component_config_file)) {
			return include $component_config_file;
		}
		return false;		
	}
		
	public function initApplicationPaths() {
		if (! defined('APP_PATH')) {
			define('APP_PATH', $this->path);
		}
		if (! defined('CONTROLLER_PATH')) {
			define('CONTROLLER_PATH', $this->path . 'controllers' . DS);
		}
		if (! defined('MODEL_PATH')) {
			define('MODEL_PATH', $this->path . 'models' . DS);
		}
		if (! defined('VIEW_PATH')) {
			define('VIEW_PATH', $this->path . 'views' . DS);
		}
		if (! defined('THEME_PATH')) {
			define('THEME_PATH', $this->path . 'themes' . DS);
		}		
	}
	
}

?>