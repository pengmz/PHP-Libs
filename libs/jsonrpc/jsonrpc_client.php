<?php
require dirname(__FILE__) . '/jsonrpc_app.php';

/**
 * @author pengmz
 */
class JsonRpcClient extends JsonRpcApp {
	
	protected $url;
	
	public function __construct($url = null, $rpc_config = array()) {
		parent::__construct(APP_PATH, $rpc_config);
		$this->url = $url;
	}
		
	public function invoke($function, $params) {
		try {
			$post_url = $this->getRpcUrl($function);
			$post_data['jsondata'] = $this->getRpcData($params[0]);			
			$this->log('[RPC]', $post_url);
			
			$http_client = new HttpClient();
			$http_client->post($post_url, $post_data);
			$result = $http_client->result()->body();			
			$http_client->close();	
					
			$result = $this->decode($result);
			return $result;
		} catch (Exception $ex) {
			$this->handleException($ex);
		}
		return FALSE;
	}
	
	public function getRpcUrl($function, $param_name = 'do') {
		return $this->url . '?' . $param_name . '=' . $function;
	}
	
	public function getRpcData($data) {
		return $this->encode($data);
	}
	
	public final function __call($function, $args) {
		return $this->invoke($function, $args);
	}

}

?>