<?php

global $APP, $CONTEXT;

/*================================ CONTEXT FUNCTIONS =================================*/
function context() {
	return get_context();
}
function get_context() {
	global $CONTEXT;
	if (! $CONTEXT) {
		$CONTEXT = new Context();
	}
	return $CONTEXT;
}

function get_request() {
	return context()->getRequest();
}

function get_response() {
	return context()->getResponse();
}

function is_ajax_request() {
	return get_request()->isAjaxRequest();
}

function get_attribute($name) {
	return context()->getAttribute($name);
}

function set_attribute($name, $value) {
	return context()->setAttribute($name, $value);
}

function get_parameter($name) {
	return context()->getParameter($name);
}

function set_parameter($name, $value) {
	return context()->setParameter($name, $value);
}

function get_session($name) {
	return context()->getSessionAttribute($name);
}

function set_session($name, $value) {
	return context()->setSessionAttribute($name, $value);
}

/*================================ COMPONENT FUNCTIONS =================================*/
function import($file) {
	include_once ROOT . $file;
}
function get_component($name, $paths = null) {
	return ComponentLoader::getComponent($name, $paths);
}

/*================================ XML FUNCTIONS =================================*/
class SimpleXMLExtended extends SimpleXMLElement {
	public function addCData($cdata_text) {
		$node = dom_import_simplexml($this);
		$document = $node->ownerDocument;
		$node->appendChild($document->createCDATASection($cdata_text));
	}
}

function load_xml($file) {
	if (is_readable($file)) {
		return simplexml_load_file($file, 'SimpleXMLExtended', LIBXML_NOCDATA);
	}
	return FALSE;
}

/*================================ i18N FUNCTIONS =================================*/
global $LANG;

function __($str, $data = array()) {
	global $LANG;	
	if (! $LANG) {
		$language = get_language();
		$LANG = include ROOT . DS . 'configs/lang_' . $language . '.php';	
	}
	if (isset($LANG[$str])) {
		$str = $LANG[$str];
	}
	if (! empty($data)) {
		$str = vsprintf($str, $data);
	}
	return $str;
	//return _($str, $data);
}

function get_language() {
	if (get_parameter('lang')) {
		return get_parameter('lang');
	} else if (get_session('lang')) {
		return get_session('lang');
	} else if (isset($_COOKIE['lang'])) {
		return $_COOKIE['lang'];
	} else if(defined('LANGUAGE')) {
		return LANGUAGE;
	}
	return 'en';
}

?>