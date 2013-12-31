<?php

/**
 * @author pengmz
 */
class Controller extends BaseComponent {
	
	public function __construct() {
		parent::__construct();
	}
	
	public final function get($name) {
		return $this->getParameter($name);
	}
	
	public final function set($name, $value) {
		return $this->setAttribute($name, $value);
	}
	
	public final function getParameter($name) {
		return $this->context->getParameter($name);
	}
	
	public final function setParameter($name, $value) {
		return $this->context->setParameter($name, $value);
	}
	
	public final function setParameters($params) {
		return $this->context->setParameters($params);
	}
	
	public final function getAttribute($name) {
		return $this->context->getAttribute($name);
	}
	
	public final function setAttribute($name, $value) {
		return $this->context->setAttribute($name, $value);
	}
	
	public final function getSessionAttribute($name) {
		return $this->context->getSessionAttribute($name);
	}
	
	public final function setSessionAttribute($name, $value) {
		return $this->context->setSessionAttribute($name, $value);
	}
	
	public final function handleRequest($action, $layout = false) {
		if (! $action) {
			$this->handleNotFound($action);
		}
		if (! method_exists($this, $action)) {
			$this->handleNotFound($action);
		}
		$response = $this->execAction($action, $layout);
		echo $response;
	}
		
	protected function execAction($action, $default_layout = false) {
		try {			
			$this->beforeAction($action);
			$response = $this->invokeMethod($action);
			$response = $this->afterAction($action, $response);
			return $response;
		} catch (Exception $ex) {
			$this->handleError($ex);
		}
		return '';
	}
		
	protected function invokeMethod($method) {
		return $this->$method();	
	}
	
	protected function beforeAction($action) {
		return true;
	}
	
	protected function afterAction($action, $response) {
		return $response;
	}
		
	public function __call($method, $args) {
		$this->handleNotFound($method);
	}
		
	protected function handleNotFound($action) {
		Log::error('[' . __CLASS__ . '][' . $action . ']Action no found');
		header("HTTP/1.0 404 Not Found");
		exit;
	}
	
	protected function handleError($action, $exception) {
		Log::error('[Exception][' . __CLASS__ . '][' . $action . ']');
		throw $exception;
	}

}

class BaseController extends Controller {
	
	protected $layout = false;
	
	public function __construct() {
		parent::__construct();
	}
	
	protected function execAction($action, $default_layout = false) {
		$response = parent::execAction($action, $default_layout);
		if ($response instanceof ViewResponse) {
			if ($this->layout) {
				$response->layout($this->layout);
			} elseif ($default_layout) {
				$response->layout($default_layout);
			}
		}				
		return $response;		
	}
	
	protected function bindForm($formClass) {	
		if (! class_exists($formClass, false)) {
			return null;		
		}		
		return new $formClass($this->context->request->data());		
	}
	
	protected function initForm($form) {
		if (!$form) {
			return;
		}
		if ($form instanceof Object) {
			$params = $form->data();
		} else if (is_object($form)) {
			//$params = (array)$form;
			$params = get_object_vars($form);
		} else if (is_array($form)) {
			$params = $form;
		}		
		if (!empty($params)) {
			$this->setParameters($params);
		}
	}
		
	protected function renderForm($template, $form = null) {
		if ($form) {
			$this->initForm($form);
		}
		return $this->render($template);
	}
	
	protected function render($template, $added_data = array()) {
		$context_data = $this->context->data();
		if (! empty($added_data)) {
			$context_data = array_merge($context_data, $added_data);
		}
		return new ViewResponse($template, $context_data);
	}
		
	protected function redirect($url) {
		if (is_array($url)) {
			$default = array(
				//'m' => get_module_name(),
				'c'=> get_controller_name()
			);
			$url = array_merge($default, $url);
			$url = '?' . http_build_query($url);
			$url = str_replace("&amp;", "&", $url);		
		}
		Header('Location:' . $url);
		exit();
	}
		
}

class GeneralController extends BaseController {
	
	public function __construct() {
		parent::__construct();
	}
	
	protected function invokeMethod($method_name) {
		$params = array();
		$method = new ReflectionMethod($this, $method_name);
		$method_params = $method->getParameters();
		if(count($method_params) > 0) {
			foreach ($method_params as $param) {
				$param_name = $param->getName();
				if ($param_name == 'context') {
					$param_value = $this->context;
				} elseif ($param_name == 'request') {
					$param_value = $this->context->getRequest();
				} else {
					$param_value = ComponentLoader::getComponent($param_name);
					if (! $param_value) {
						$param_value = $this->getParameter($param_name);
					}
				}
				$params[] = $param_value;
				//$params[$param_name] = $param_value;
			}
		}	
 			
		switch (count($params)) {
			case 0:
				return $this->{$method_name}();
			case 1:
				return $this->{$method_name}($params[0]);
			case 2:
				return $this->{$method_name}($params[0], $params[1]);
			case 3:
				return $this->{$method_name}($params[0], $params[1], $params[2]);
			case 4:
				return $this->{$method_name}($params[0], $params[1], $params[2], $params[3]);
			case 5:
				return $this->{$method_name}($params[0], $params[1], $params[2], $params[3], $params[4]);
			default:
				return call_user_func_array(array(&$this, $method_name), $params);
		}
	}
	
}
?>