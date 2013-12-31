<?php
	
	define('ROOT', dirname(dirname(dirname(__FILE__))));	
		
	define('DS', DIRECTORY_SEPARATOR);
	define('PS', PATH_SEPARATOR);
	define('EXT', '.php');	
	
	define('LIB_PATH', ROOT . DS . 'libs' . DS);
	define('COMPONENT_PATH', ROOT . DS . 'components' . DS);
	
	require ROOT . DS . 'configs/config.php';
	require LIB_PATH . 'jsonrpc/jsonrpc_server.php';
	
	include dirname(__FILE__) . '/api.php';
	
	$server = new JsonRpcServer();
	$server->add('test', 'test');
	$server->run();
	
?>