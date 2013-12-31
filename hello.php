<?php

	define('ROOT', dirname(__FILE__));
	
	define('DS', DIRECTORY_SEPARATOR);
	define('PS', PATH_SEPARATOR);
	define('EXT', '.php');
	
	define('SITE_PATH', ROOT . DS);
	define('LIB_PATH', ROOT . DS . 'libs' . DS);
	
	$start = microtime(true);
	
	define('DEBUG_MODE', true);
	
	date_default_timezone_set('PRC');
	
	require LIB_PATH . 'app.php';
	
	$APP = new RestApp(APP_PATH);
	$APP->get('hello', 'hello');
	$APP->run();
	
	function hello() {
		echo 'Hello world!<br/>';
	}
	
	echo microtime(true) - $start;

?>