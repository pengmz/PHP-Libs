<?php if ( ! defined('ROOT')) exit('Access forbidden');

	require LIB_PATH . 'framework/application.php';
	require APP_PATH . 'admin_controller.php';
	include APP_PATH . 'admin_functions.php';

	$APP = new Application(APP_PATH);	
	$APP->initDB($DBCFG);	
	$APP->run();
	
?>
