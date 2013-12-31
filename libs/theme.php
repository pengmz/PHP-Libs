<?php
if (! defined('THEME_PATH')) {
	define('THEME_PATH', ROOT . DS . 'themes/default' . DS);
}
	
global $SITE_NAME, $SITE_URL;
global $THEME_URL, $THEME_PATH;

function site_name() {
	echo get_site_name();
}
function get_site_name() {
	global $SITE_NAME;
	if ($SITE_NAME) {
		return $SITE_NAME;
	} elseif (defined('SITE_NAME')) {
		$SITE_NAME = SITE_NAME;
	}
	return $SITE_NAME;
}

function site_url() {
	echo get_site_url();
}
function get_site_url() {
	global $SITE_URL;
	if ($SITE_URL) {
		return $SITE_URL;
	} elseif (defined('SITE_URL')) {
		$SITE_URL = SITE_URL;
	}
	$SITE_URL = rtrim($SITE_URL, '/');
	return $SITE_URL;
}

function theme_url() {
	echo get_theme_url();	
}
function get_theme_url() {
	global $THEME_URL;
	if ($THEME_URL) {
		return $THEME_URL;
	} else {
		$THEME_URL = str_replace(SITE_PATH, '', get_theme_path());
		$THEME_URL = str_replace("\\", "/", $THEME_URL);
		$THEME_URL = rtrim($THEME_URL, '/');
		$THEME_URL = get_site_url() . '/' . $THEME_URL;
	}
	return $THEME_URL;
}

function get_theme_path() {
	global $THEME_PATH;
	if ($THEME_PATH) {
		return $THEME_PATH;
	} elseif (defined('THEME_PATH')) {
		$THEME_PATH = THEME_PATH;
	} else {
		$THEME_PATH = SITE_PATH . 'themes' . DS . 'default' . DS;
	}
	return $THEME_PATH;
}

function theme($contents, $template = 'default') {
	set_attribute('main_body_contents', $contents);
	include_file($template);
}

function include_file($file) {
	$theme_file = get_theme_path() . $file . EXT;
	$context = get_context();
	if (is_file($theme_file)) {
		$request = get_request();
		$response = get_response();		
		extract($context->data());
		return include $theme_file;
	}
	return FALSE;
}

function include_header() {
	include_file('header');
}

function include_sidebar() {
	include_file('sidebar');
}

function include_footer() {
	include_file('footer');
}

function include_theme_css($css_url) {
	$css_file = get_theme_path() . $css_url;
	if (is_file($css_file)) {
		$css_url = get_theme_url() . $css_url . '?ver=' . filemtime($css_file);
		echo '<link type="text/css" rel="stylesheet" href="' . $css_url . '" />' . "\n";
	}	
}

function include_theme_script($script_url) {
	$script_file = get_theme_path() . $script_url;
	if (is_file($script_file)) {
		$script_url = get_theme_url() . $script_url . '?ver=' . filemtime($script_file);
		echo '<script type="text/javascript" src="' . $script_url . '"></script>' . "\n";
	}
}

function include_css($css_url) {
	$css_file = get_css_file($css_url);
	if (is_file($css_file)) {
		$css_url .= '?ver=' . filemtime($css_file);
		echo '<link type="text/css" rel="stylesheet" href="' . $css_url . '" />' . "\n";
	}
}

function include_script($script_url) {
	$script_file = get_script_file($script_url);
	if (is_file($script_file)) {
		$script_url .= '?ver=' . filemtime($script_file);
		echo '<script type="text/javascript" src="' . $script_url . '"></script>' . "\n";
	}	
}

function get_css_file($css_url) {
	return SITE_PATH . str_replace(get_site_url(), '', $css_url);
}

function get_script_file($script_url) {
	return SITE_PATH . str_replace(get_site_url(), '', $script_url);
}

function get_url($paths = array()) {
	$url = get_site_url();
	
	global $FRIENDLY_URL;	
	if ($FRIENDLY_URL) {
		$url .= implode('/', $paths) . '.html';
	} else {
		$url .=  '?' . http_build_query($paths);
	}
	return $url;
}

function link_to($text, $paths = array()) {
	$url = get_url($paths);
	echo '<a href="' . $url . '">' . $text . '</a>';
}

function display($value) {
	if (is_string($value)) {
//		$value = escape($value);
		$value = nl2br($value);
	}
	echo $value;
}

function escape($str) {
	$str = htmlspecialchars($str);
	$str = str_replace(array("&quot;", "&amp;"), array('"', '&'), $str);
	return $str;
}

function init_context($context_data) {
	foreach ($context_data as $key => $value) {
		echo '$scope.' . $key . ' = JSON.parse(\'' . json_encode($value) . '\');';
	}
}

?>