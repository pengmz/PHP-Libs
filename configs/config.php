<?php

if (! defined('SITE_URL')) {
	define('SITE_URL', '/myapp4');
}

define('DEBUG_MODE', true);
		
date_default_timezone_set('PRC');

//Session
//$login_session_time_out = 60 * 60 * 24;
//ini_set('session.gc_maxlifetime', $login_session_time_out);
//ini_set('session.save_path',  '/home/site/sessions/');
//ini_set('session.cookie_domain',  '.site.com');

//DB config
global $DBCFG;
$DBCFG = include dirname(__FILE__) . '/db_config.php';
if (defined('SAE_APPNAME')) {
	$DBCFG = include dirname(__FILE__) . '/sae_config.php';
}

//include dirname(__FILE__) . '/push_config.php';

?>