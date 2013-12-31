<?php
if (! defined('MODULE_PATH')) {
	define('MODULE_PATH', ROOT . DS . 'modules' . DS);
}

/**
 * @author pengmz
 */
class Module extends Application {
	
	protected $name;
	
	public function __construct($path = null) {
		$name = get_module_name();
		$path = get_module_path($name);
		$this->name = $name;
		parent::__construct($path);
	}
	
	public function init() {
		parent::init();
		include $this->path . 'index.php';
	}
		
	public function run() {
		parent::run();
	}
	
	public final function initComponentConfig($component_config) {
		parent::initComponentConfig($component_config);
		if (isset($component_config['required'])) {
			$required_modules = $component_config['required'];
			foreach ($required_modules as $required_module) {
				$this->initRequiredModuleComponent($required_module);
			}
		}		
	}

	public final function initRequiredModuleComponent($module_name) {
		static $completed;
		if (!isset($completed[$module_name])) {
			$component_config_file = MODULE_PATH . $module_name . DS . 'configs/component_config.php';
			if (file_exists($component_config_file)) {
				$component_config = include $component_config_file;
				$this->initComponentConfig($component_config);
			}
			$completed[$module_name] = 'true';
		}		
	}

}

?>