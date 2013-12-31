<?php

	define('ROOT', dirname(__FILE__));	
		
	define('DS', DIRECTORY_SEPARATOR);
	define('PS', PATH_SEPARATOR);
	define('EXT', '.php');	
	
	define('SITE_PATH', ROOT . DS);
	define('LIB_PATH', ROOT . DS . 'libs' . DS);
	define('APP_PATH', ROOT . DS . 'admin' . DS);
	define('THEME_PATH', ROOT . DS . 'themes/default' . DS);	
	
	include ROOT . DS . 'configs/config.php';
	
	include APP_PATH . 'index.php';

?>