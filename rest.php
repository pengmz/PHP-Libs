<?php

	define('ROOT', dirname(__FILE__));
	
	define('DS', DIRECTORY_SEPARATOR);
	define('PS', PATH_SEPARATOR);
	define('EXT', '.php');
	
	define('SITE_PATH', ROOT . DS);
	define('LIB_PATH', ROOT . DS . 'libs' . DS);
	//define('APP_PATH', ROOT . DS . 'admin' . DS);
	define('COMPONENT_PATH', ROOT . DS . 'components' . DS);
	define('THEME_PATH', ROOT . DS . 'themes/default' . DS);
	
	$start = microtime(true);
	
	include ROOT . DS . 'configs/config.php';
	require LIB_PATH . 'framework/application.php';
	
	$APP = new RestApp(APP_PATH);
	$APP->get('users', 'user_list');
	$APP->initDB($DBCFG);
	$APP->run();
	
	function user_list() {
		$user = get_component('user');
		$users = $user->findAll();
		print_r($users);
	}
	
	//echo microtime(true) - $start;

?>