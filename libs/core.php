<?php
if (version_compare(phpversion(), '5.2.0', '<') === TRUE) {
	exit(phpversion() . ' is an invalid PHP version.');
}

/**
 * @author pengmz
 */
class Object {
	
	private $_attributes = array();
	
	public function __construct($attributes = array()) {
		$this->data($attributes);
	}

	public function data($attributes = array()) {
		if (! empty($attributes)) {
			foreach ($attributes as $name => $value) {
				$this->setAttribute($name, $value);
			}			
		}		
		return $this->getAttributes();
	}	
		
	public function getAttribute($name) {
		if (!isset($this->_attributes[$name])) {
			return null;
		}
		return $this->_attributes[$name];
	}
	
	public function setAttribute($name, $value) {
		$this->_attributes[$name] = $value;
		return $value;
	}
	
	public function getAttributes() {
		return $this->_attributes;
	}
	
	public function __get($name) {
		return $this->getAttribute($name);
	}
	
	public function __set($name, $value) {
		return $this->setAttribute($name, $value);
	}
	
	public function __toArray() {
		return $this->getAttributes();
	}

	public function __toObject() {
		return (object)$this->getAttributes();		
	}
		
	public function __toJSON() {
		return json_encode($this->getAttributes());
	}
		
	public function __toString() {
		return $this->__toJSON();
	}
}

/**
 * @author pengmz
 */
class BaseObject extends Object {
	
	public function __construct($attributes = array()) {
		parent::__construct($attributes);
	}

	public function getAttribute($attribute) {
		if (property_exists($this, $attribute)) {
			return $this->$attribute;
		}		
		return parent::getAttribute($attribute);
	}
	
	public function setAttribute($attribute, $value) {
		if (property_exists($this, $attribute)) {
			$this->$attribute = $value;
			return $value;
		}
		return parent::setAttribute($attribute, $value);
	}
	
	public function getAttributes() {
		$attributes = parent::getAttributes();
		$objectvars = get_object_vars($this);
		return array_merge($attributes, $objectvars);
	}
}

/**
 * @author pengmz
 */
class Context extends Object {
	
	public $request = null;
	
	public $response = null;
	
	public function __construct($data = array()) {
		parent::__construct($data);
	
		$this->startSession();
		$params = array_merge($_GET, $_POST);
		$this->request = new Request($params);
	}
	
	public function getParameter($name) {
		return $this->request->getParameter($name);
	}
	
	public function setParameter($name, $value) {
		$this->request->setParameter($name, $value);
	}
	
	public function setParameters($params = array()) {
		$this->request->data($params);
	}
	
	public function getSessionAttribute($name) {
		if (! isset($_SESSION[$name])) {
			return null;
		}
		return $_SESSION[$name];
	}
	
	public function setSessionAttribute($name, $value) {
		$_SESSION[$name] = $value;
		return TRUE;
	}

	public function startSession() {
		if (isset($_SESSION) && isset($_SESSION['session_startup_timestamp'])) {
			return true;
		}	
		session_start();
		session_regenerate_id();
		$_SESSION['session_startup_timestamp'] = time();
		return TRUE;	
	}
	
	public function getRequest() {
		return $this->request;
	}
		
	public function getResponse() {
		if (!$this->response) {
			$this->response = new Response();
		}
		return $this->response;
	}		

}

/**
 * @author pengmz
 */
class Request extends Object {
	
	public function __construct($data = array()) {
		parent::__construct($data);
	}
	
	public function getParameter($name) {
		return $this->getAttribute($name);
	}
	
	public function setParameter($name, $value) {
		return $this->setAttribute($name, $value);
	}
	
	public function method() {
		//GET, POST, PUT, DELETE, HEAD, OPTIONS
		return $_SERVER['REQUEST_METHOD'];
	}
	
	public function getReferer() {
		return $this->getHeader('REFERER');
	}
	
	public function getHeader($header) {
		$header = 'HTTP_' . strtoupper($header);
		if (! isset($_SERVER[$header])) {
			return null;
		}
		return $_SERVER[$header];
	}
	
	public function isAjaxRequest() {		 
		//XMLHttpRequest
		return strtolower($this->getHeader('X_REQUESTED_WITH')) == 'xmlhttprequest';
	}	
		
	public function url() {
		if (! isset($_SERVER['HTTPS']) || strtolower($_SERVER['HTTPS']) != 'on') {
			$protocol = 'http://';
		} else {
			$protocol = 'https://';
		}
		$host = $_SERVER['HTTP_HOST'];
		$scriptUrl = $_SERVER['PHP_SELF'];
		return $protocol . $host . $scriptUrl;
	}
	
	public function clientIP() {
		if ($_SERVER['HTTP_CLIENT_IP']) {
			return $_SERVER['HTTP_CLIENT_IP'];
		} elseif ($_SERVER['REMOTE_ADDR']) {
			return $_SERVER['REMOTE_ADDR'];
		} elseif ($_SERVER['HTTP_X_FORWARDED_FOR']) {
			return $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			return '0.0.0.0';
		}
	}
	
	public function browser() {
		return get_browser();
	}
}

/**
 * @author pengmz
 */
class Response extends Object {
	
	public function __construct($data = array()) {
		parent::__construct($data);
	}
	
}

/**
 * @author pengmz
 */
interface Component {
	function __autowire();
}

/**
 * @author pengmz
 */
class BaseComponent extends BaseObject implements Component {
	
	protected $context;
	
	public function __construct() {
		$this->__autowire();
	}

	public function __autowire() {
		//$vars = array_keys(get_object_vars($this));		
		$reflection = new ReflectionClass($this);
		$vars = array_keys($reflection->getdefaultProperties());
		
		foreach ($vars as $var){
			if ($var == 'context') {
				$this->context = ComponentLoader::getContext();
			}			
			if ($var == 'db') {
				$this->db = ComponentLoader::getDB();
			}
			$setMethod = 'set' . ucfirst($var);
			if (method_exists($this, $setMethod)){
				$component = ComponentLoader::getComponent($var);
				if ($component) {
					$this->$setMethod($component);
				}
			}
		}
	}
		
	public function __autoload($name) {
		return ComponentLoader::getComponent($name);
	}
	
	public function __call($method, $args) {
		Log::error('Method not found - ' . $method . '(' . implode(',', $args) . ')');
		header("HTTP/1.0 404 Not Found");
		exit;		
	}
}

/**
 * ClassLoader
 * @author pengmz
 */
class ComponentLoader {
	
	private static $components = array();
	private static $component_config = array();
	
	public static function registerComponent($component_name, $component_alias) {
		self::$component_config[$component_name] = $component_alias;
		return TRUE;
	}
		
	public static function getComponent($name, $paths = null) {
		if ($name == 'context') {
			return self::getContext();
		}
		if ($name == 'db') {
			return self::getDB();
		}
		$component_name = $name;
		if (isset(self::$component_config[$name])) {
			$component_name = self::$component_config[$name];
		}	
					
		if (isset(self::$components[$component_name])) {
			return self::$components[$component_name];
		} 
			
		$component = self::loadComponent($component_name, $paths);
		if ($component) {
			self::$components[$component_name] = $component;
			return $component;	
		}		
				
		return FALSE;		
	}
		
	public static function loadComponent($name, $paths = null) {
		$componentClass = self::getClassName($name);
		
		if (class_exists($componentClass, false)) {
			return new $componentClass();
		}
		
		if (empty($paths)) {
			$paths = explode(PS, get_include_path());
		} elseif (!is_array($paths)) {
			$paths = array($paths);
		}
		foreach ($paths as $path) {
			$file_name = $path . $name . EXT;
			if (file_exists($file_name)) {
				$component_file = $file_name;				
				break;
			}
		}		
		if ($component_file) {
			include_once $component_file;
			if (class_exists($componentClass, false)) {		
				return new $componentClass();		
			}	
		}

		Log::debug('Component not found - ' . $componentClass . '(' . implode(',', $paths) . ')');
		
		return FALSE;
	}
	
	public static function getContext() {
		return get_context();	
	}
	
	public static function getDB($db_config = array()) {
		global $DB;		
		if ($DB) {
			return $DB;
		}			
		return get_db($db_config);
	}
	
	private static function getClassName($name) {
		$words = explode('_', $name);
		foreach ($words as $key => $word) {
			$words[$key] = ucfirst($word);
		}
		return implode('', $words);
	}

}

//spl_autoload_register

?>