<?php
require dirname(__FILE__) . '/jsonrpc_app.php';

/**
 * @author pengmz
 */
class JsonRpcServer extends JsonRpcApp {
	
	protected $functions = array();
	
	public function __construct($path = null, $rpc_config = array()) {
		parent::__construct($path, $rpc_config);
	}
	
	public function init() {
		parent::init();
		header("HTTP/1.1 200 OK");
		header('Content-Type: text/javascript');
		header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
		header("Access-Control-Allow-Origin: *");		
	}
	
	public function run() {
		parent::run();
		try {
			$function = $this->getFunction();			
			if ($function) {
				$params = $this->getParams();
				//$params = $this->getPostBody();
				$result = call_user_func($function, $params);
			} else {
				$result = array('code' => 'error', 'message' => 'api not found');
			}
		} catch (Exception $ex) {
			$result = $this->handleException($ex);
		}		
		$this->returnResult($result);
	}
	
	public function returnResult($result) {		
		echo $this->encode($result);
	}
	
	public function add($name, $function) {
		$this->functions[strtolower($name)] = $function;
	}

	protected function getFunction($param_name = 'do') {
		$do = $this->context->getParameter($param_name);	
		$this->log('[RPC]', $do);
		
		$do = strtolower($do);	
		if (array_key_exists($do, $this->functions)) {
			return $this->functions[$do];
		}
		return false;
	}
	
	protected function getParams() {
		$params = $this->getRequestData();
		$added_params = $this->getPostData();
		if (! empty($added_params)) {
			$params = array_merge($params, $added_params);
		}
		return $params;
	}
	
	protected function getPostData($param_name = 'jsondata') {
		$jsondata = $this->context->getParameter($param_name);	
		if (! $jsondata) {
			return array();
		}
		return $this->decode($jsondata);
	}
		
	protected function getPostBody() {
		if (isset($HTTP_RAW_POST_DATA)) {
			$jsondata = $HTTP_RAW_POST_DATA;
		} else {
			$jsondata = file_get_contents('php://input');
		}
		return $this->decode($jsondata);
	}	
}

?>