<?php

	define('ROOT', dirname(__FILE__));	
		
	define('DS', DIRECTORY_SEPARATOR);
	define('PS', PATH_SEPARATOR);
	define('EXT', '.php');	
	
	define('SITE_PATH', ROOT . DS);
	define('LIB_PATH', ROOT . DS . 'libs' . DS);
	define('MODULE_PATH', ROOT . DS . 'modules' . DS);
	define('COMPONENT_PATH', ROOT . DS . 'components' . DS);
	define('THEME_PATH', ROOT . DS . 'themes/default' . DS);	
	
	include ROOT . DS . 'configs/config.php';
	require LIB_PATH . 'framework/application.php';
	require LIB_PATH . 'framework/module.php';
	require MODULE_PATH . 'module_controller.php';
	include MODULE_PATH . 'module_functions.php';

	$APP = new Module(MODULE_PATH);	
	$APP->initDB($DBCFG);	
	$APP->run();
	
?>	
