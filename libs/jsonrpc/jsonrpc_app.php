<?php
require LIB_PATH . 'app.php';
require FRAMEWORK_PATH . 'model/model.php';
require FRAMEWORK_PATH . 'model/form.php';

/**
 * @author pengmz
 */
class JsonRpcApp extends App {
	
	private $encrypt = false;
	private $gzip = false;
	private $debug = DEBUG_MODE;
	
	public function __construct($path = null, $rpc_config = array()) {
		parent::__construct($path);
		if ($rpc_config) {
			$this->encrypt = $rpc_config['encrypt'];
			$this->gzip = $rpc_config['gzip'];
			if ($rpc_config['debug'] == 'true') {
				$this->debug = true;
			}
		}		
	}
	
	public function init() {
		parent::init();
	}
	
	protected function encode($data) {
		$data = json_encode($data);
		$this->log('[Data]', $data);
		
		if ($this->encrypt) {
			$data = base64_encode($data);
		}
		return $data;
	}
	
	protected function decode($data) {
		if ($this->encrypt) {
			$data = base64_decode($data);
		}
		$this->log('[Data]', $data);
		$data = json_decode($data, true);
		return $data;
	}
		
	protected function getRequest() {
		return $this->context->getRequest();
	}

	protected function getRequestData() {
		return $this->getRequest()->data();
	}
		
	protected function handleException($exception) {
		$this->log('[Error]', $exception->getMessage());
		return array('code' => $exception->getCode(), 'message' => $exception->getMessage());
	}
	
	protected function log($operation, $message) {
		if ($this->debug) {			
			$message = $operation . ": " . $message . "\n";
			$fp = @fopen('jsonrpc.log', "a+");
			if ($fp) {
				flock($fp, LOCK_EX);
				fwrite($fp, $message);
				flock($fp, LOCK_UN);
				fclose($fp);
			}		
		}
	}
}

?>