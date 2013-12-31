<?php

/**
 * @author pengmz
 */
class CoreException extends RuntimeException {
	
    public function __construct($message, $code = 500) {
        parent::__construct($message, $code);
    }
    
    public function __toString() {
		return "[Exception][$this->code] $this->message 
				in file $this->file on line $this->line";
    }
    
}

function __exception_handler($exception) {
    $message = '[Exception]: ' . $exception->getMessage();
    $message .= ' in file ' . $exception->getFile();
    $message .= ' on line ' . $exception->getLine();   
    $message .= $exception->getTraceAsString();  	
	Log::error($message);
}

function __error_handler($errno, $errstr, $errfile, $errline) {
	$levels = array (
	    E_ERROR            	=> 'ERROR',
	    E_WARNING        	=> 'WARNING',
	    E_PARSE          	=> 'PARSING ERROR',
	    E_NOTICE         	=> 'NOTICE',
	    E_CORE_ERROR     	=> 'CORE ERROR',
	    E_CORE_WARNING   	=> 'CORE WARNING',
	    E_COMPILE_ERROR  	=> 'COMPILE ERROR',
	    E_COMPILE_WARNING 	=> 'COMPILE WARNING',
	    E_USER_ERROR     	=> 'USER ERROR',
	    E_USER_WARNING   	=> 'USER WARNING',
	    E_USER_NOTICE    	=> 'USER NOTICE',
	    E_STRICT         	=> 'STRICT NOTICE',
	    E_RECOVERABLE_ERROR  => 'RECOVERABLE ERROR'
	);
	
	$error_message = "$errstr in file $errfile on line $errline"; 
	if (isset($levels[$errno])) {
		$error_message = '[' . $levels[$errno] .']: ' . $error_message;
	}
	$errno = $errno & error_reporting();
	if ($errno > 0) {
		Log::error($error_message);
	} else {
		log::debug($error_message);
	}
}

set_error_handler('__error_handler');
set_exception_handler('__exception_handler');


/**
 * @author pengmz
 */
class ALERT {

	private static $messages = array();
	
	public static function add($message, $type = 'info') {
		$messages = self::getMessages();
		$messages[] = array('message' => $message, 'type' => $type);
		self::saveMessages($messages);
	}
	
	public static function show() {
		$messages = self::getMessages();
		foreach ($messages as $message) {
			echo '<div class="alert alert-' . $message['type'] . '"><button class="close" data-dismiss="alert">&times;</button><strong>' . $message['message'] . '</strong></div>';
		}
	}
	
	public static function clear() {
		self::$messages = array();
		self::saveMessages();
	}
	
	public static function getMessages() {
		$messages = get_session('alert_message');
		if ($messages) {
			return $messages;
		}
		return self::$messages;
	}
	
	public static function saveMessages($messages = null) {
		set_session('alert_message', $messages);
	}
	
}
?>