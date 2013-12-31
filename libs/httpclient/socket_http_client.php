<?php

require_once 'http_client.php';

class SocketHttpRequest extends HttpRequest {
	
	protected $scheme;	
	
	protected $host;
	
	protected $port;
	
	protected $path;
	
	protected $user;
	
	protected $pass;
	
	protected $useragent;
	
	public function __construct($url, $data = array(), $method = 'GET', $timeout = false) {
		parent::__construct($url, $data, $method, $timeout);
		$this->init($url);
	}
	
	public function init($url) {	
		$urlparts = parse_url($url);
		$scheme = $urlparts['scheme'];
		$host = $urlparts['host'];
		$port = $urlparts['port'];
		$path = $urlparts['path'];
		$user = $urlparts['user'];
		$pass = $urlparts['pass'];		

		if (! $port) {
			if ($scheme == "http") {
				$port = 80;
			} elseif ($scheme == "https") {
				$port = 443;
				$host = 'ssl://' . $host;				
			}
		}
		
		if (! $path) {
			$path = '/';
		}
		
		if (isset($urlparts['query'])) {
			$path .= '?' . $urlparts['query'];
		}
		
		$this->path = $path;  
		
  		if ($scheme) {
			$this->scheme = $scheme;
  		}
  		if ($host) {
			$this->host = $host;
  		}
  		if ($port) {
			$this->port = $port;
  		}		
		if ($user) {
			$this->user = $user;
		}
		if ($pass) {
			$this->pass = $pass;
		}
		
		return true;        
	}
	
	public function buildPostDataString($data) {
		$data_string = $this->buildQueryString($data);
		return str_replace("&amp;", "&", $data_string); 
	}
	
	public function buildRequestString() {
		$content = '';
		if (!empty($this->data)) {
			$content = $this->buildPostDataString($this->data);
		}
		
		$headers = array();
		$headers['Host'] = 'Host: ' . $this->host;
		if ($this->useragent) {
			$headers['User-Agent'] = 'User-Agent: ' . $this->useragent;
		}		
		if ($this->user) {
		    $headers['Authorization'] = 'Authorization: Basic '. base64_encode($this->user . $this->pass);
		}		
		if ($this->method == 'POST' || $this->method == 'PUT') {
			$headers['Content-Type'] = 'Content-Type: application/x-www-form-urlencoded';
		    $headers['Content-Length'] = 'Content-Length: '. strlen($content);
		}
				
		$request_string = $this->method .' '. $this->path ." HTTP/1.0\r\n";
		$request_string .= implode("\r\n", $headers);
		$request_string .= "\r\n\r\n";
		if ($content) {
			$request_string .= $content;	
		}			
		return $request_string;
	}
		
	public function scheme() {
		return $this->scheme;
	}

	public function host() {
		return $this->host;
	}

	public function port() {
		return $this->port;
	}

	public function path() {
		return $this->path;
	}

	public function useragent() {
		return $this->useragent;
	}

	public function __toString() {
		return $this->scheme . '://' . $this->host . ':' . $this->port . $this->path;
	}
}

class SocketHttpClient {
	
	private $handle;	 
	
	private $request;
	
	private $result = false;
	
	private $error = false;
		
	private $followlocation = true;
	
	public function __construct() {
		
	}
	
	public function init($request) {
		if (!is_resource($this->handle)) {	
	        $this->handle = $this->connect($request->host(), $request->port(), $this->timeout);			
		} else {			
			if ($request->host() != $this->request->host()) {
				$this->handle = $this->connect($request->host(), $request->port(), $this->timeout);		      
			}
		}
		$this->request = $request;
		$this->result = false;
		$this->error = false;
				
        return $this->handle;	
	}
	
	public function get($url, $data = array()) {
		$request = new SocketHttpRequest($url, $data, 'GET');
		return $this->execute($request);		
	}
	
	public function post($url, $data = array()) {
		$request = new SocketHttpRequest($url, $data, 'POST');
		return $this->execute($request);		
	}
	
	public function execute($request) {		
        $request_string = $request->buildRequestString();

        $handle = $this->init($request);  
		fputs($handle, $request_string);
		
		$response_content = '';
		while(! feof($handle)) {
			$line = fgets($handle, 1024);
			$response_content .= $line;
		}
		
		$response = new HttpResponseResult(0, $response_content);
		$http_status_code = $response->status();
		
		$this->result = $response;
		
		if ($this->followlocation) {
			$redirect_url = $response->should_redirect();
			if ($redirect_url) {
				if (! preg_match("|\:\/\/|", $redirect_url)) {					
					if (! preg_match("|^/|", $redirect_url)) {
						$redirect_url .= '/' . $redirect_url;
					}
					$redirect_url = $request->scheme() . '://' . $request->host() . ':' . $request->port() . $redirect_url;
				}	
							
				$this->close();				
				$this->followlocation = false;
									
				$http_status_code = $this->get($redirect_url);
				$this->followlocation = true;

				return $http_status_code;
			}
		}		
		
		return $http_status_code;
	}
	
	public function result() {
		return $this->result;
	}
	
	public function error() {
		return $this->error;
	}
	
	public function connect($host, $port, $timeout) {
		if (! $timeout) {
			$fp = @fsockopen($host, $port, $errno, $errstr);
		} else {
			$fp = @fsockopen($host, $port, $errno, $errstr, $timeout);
			socket_set_timeout($fp, $timeout);
		}	
		if (!$fp) {
			$this->error = $errno . ' - ' . $errstr;
		}
		return $fp;
	}
	
	public function close() {
		fclose($this->handle);
		$this->handle = null;
	}
		
}

?>