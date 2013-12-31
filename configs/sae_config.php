<?php
//include_once LIB_PATH . 'storage/sae.php';
//
//global $STORAGE;	
//$STORAGE = new SAEFileStorage();

return array(
	'host'	=> SAE_MYSQL_HOST_M . ':' . SAE_MYSQL_PORT,
	'name'	=> SAE_MYSQL_DB,
	'user'	=> SAE_MYSQL_USER,
	'password'	=> SAE_MYSQL_PASS,
	'debug'	=> 'false',
);

?>