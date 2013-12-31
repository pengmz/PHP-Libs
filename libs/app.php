<?php
if (! defined('ROOT')) {
	define('ROOT', dirname(dirname(__FILE__)));
}
if (! defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}
if (! defined('PS')) {
	define('PS', PATH_SEPARATOR);
}
if (! defined('EXT')) {
	define('EXT', '.php');	
}
if (! defined('LANGUAGE')) {
	define('LANGUAGE', 'en');
}
if (! defined('CHARSET')) {
	define('CHARSET', 'utf-8');
}
if (! defined('DEBUG_MODE')) {
	define('DEBUG_MODE', FALSE);	
}
if (! defined('LIB_PATH')) {
	define('LIB_PATH', dirname(__FILE__) . DS);
}
if (! defined('FRAMEWORK_PATH')) {
	define('FRAMEWORK_PATH', LIB_PATH . DS . 'framework' . DS);
}

require LIB_PATH . 'core.php';
require LIB_PATH . 'action.php';
require LIB_PATH . 'functions.php';
require LIB_PATH . 'database/db.php';
require LIB_PATH . 'storage/storage.php';
require LIB_PATH . 'exception.php';
require LIB_PATH . 'logger.php';

/**
 * @author pengmz
 */
class App {
	
	protected static $instance;	
	
	protected $context;
	
	protected $path;
	
	protected $db;
	
	public function __construct($path = null) {
		$this->path = $path;
		$this->init();
	}
	
	public function init() {
		if (DEBUG_MODE) {
			ini_set('display_errors', TRUE);
			//error_reporting(E_ALL);
			error_reporting(E_ALL & ~ E_NOTICE);
		} else {
			ini_set('display_errors', FALSE);
			error_reporting(E_ERROR | E_PARSE | E_USER_ERROR);
		}
		ini_set('magic_quotes_runtime', 0);

		global $CONTEXT;
		if (! $CONTEXT) {
			$this->context = new Context();
			$CONTEXT = $this->context;
		} else {
			$this->context = & $CONTEXT;		
		}	

		$this->initComponent();
	}
	
	public function run() {
	
	}
	
	public function initDB($db_config) {
		$this->db = ComponentLoader::getDB($db_config);	
		return $this->db;
	}
	
	public function initComponent() {
		if (defined('COMPONENT_PATH')) {
			$include_paths[] = COMPONENT_PATH;
			$this->initIncludePath($include_paths);
		}			
	}
	
	public function initIncludePath($paths = null) {
		if ($paths) {
			if (!is_array($paths)){
				$paths = array($paths);
			}
			$include_paths = implode(PS, $paths);
			set_include_path($include_paths . PS . get_include_path());
		}	
	}
		
	public function getComponent($name, $paths = null) {
		return ClassLoader::getComponent($name, $paths);
	}
			
	public static function &getInstance() {
		global $APP;
		if ($APP) {
			return $APP;
		}
		if (self::$instance) {
			return self::$instance;
		}
		self::$instance = & $this;
		return self::$instance;
	}
		
	public final function &getContext() {
		return $this->context;
	}
	
	public final function &getDB() {
		return $this->db;
	}
		
	public function __destruct() {
		if ($this->db) {
			$this->db->__destruct();
		}
	}

}

class RestApp extends App {
	
	public function __construct($path = APP_PATH) {
		parent::__construct($path);
	}
		
	public function run() {
		parent::run();
		$action = $this->getActionName();
		$hooks = array ($action, '*');
		HOOK::beforeFilter($hooks);		
		$this->execAction($action);
	}
	
	public function get($action, $function) {
		$this->addAction($action, $function, 'GET');
	}
	
	public function post($action, $function) {
		$this->addAction($action, $function, 'POST');
	}
	
	public function put($action, $function) {
		$this->addAction($action, $function, 'PUT');
	}
	
	public function delete($action, $function) {
		$this->addAction($action, $function, 'DELETE');
	}
		
	public function addAction($action, $function, $method = 'any') {
		ACTION::addAction($action, $function, $method);
	}
		
	public function execAction($action, $added_param = array()) {
		ACTION::execAction($action, $added_param);
	}
	
	public function getActionName($param_name = 'do') {
		return $this->context->getParameter($param_name);
	}	
}
?>