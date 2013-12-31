<?php

global $MODULE_NAME, $CONTROLLER_NAME, $ACTION_NAME;

function get_module_name() {
	global $MODULE_NAME;
	if (!$MODULE_NAME) {
		$MODULE_NAME = get_parameter('m');
		if (!$MODULE_NAME) {
			$MODULE_NAME = get_controller_name();
		}
	}
	return $MODULE_NAME;	
}

function get_controller_name() {
	global $CONTROLLER_NAME;
	if (!$CONTROLLER_NAME) {
		$CONTROLLER_NAME = get_parameter('c');
		if (!$CONTROLLER_NAME) {
			$CONTROLLER_NAME = 'default';
		}
	}
	return $CONTROLLER_NAME;
}

function get_action_name() {
	global $ACTION_NAME;
	if (!$ACTION_NAME) {
		$ACTION_NAME = get_parameter('d');
		if (!$ACTION_NAME) {
			$ACTION_NAME = 'index';
		}
	}
	return $ACTION_NAME;
}

function get_layout_name() {
	$layout = false;
	if (! is_ajax_request()) {
		$layout = get_parameter('layout');
		if (! $layout) {
			$layout = 'default';
		}
	}
	return $layout;
}

function get_controller_path() {
	global $CONTROLLER_PATH;
	if ($CONTROLLER_PATH) {
		return $CONTROLLER_PATH;
	} elseif (defined('CONTROLLER_PATH')) {
		$CONTROLLER_PATH = CONTROLLER_PATH;
	} else {
		$CONTROLLER_PATH = APP_PATH . 'controllers' . DS;
	}
	return $CONTROLLER_PATH;
}

function get_model_path() {
	global $MODEL_PATH;
	if ($MODEL_PATH) {
		return $MODEL_PATH;
	} elseif (defined('MODEL_PATH')) {
		$MODEL_PATH = MODEL_PATH;
	} else {
		$MODEL_PATH = APP_PATH . 'models' . DS;
	}
	return $MODEL_PATH;
}

function get_view_path() {
	global $VIEW_PATH;
	if ($VIEW_PATH) {
		return $VIEW_PATH;	
	} elseif (defined('VIEW_PATH')) {
		$VIEW_PATH = VIEW_PATH;
	} else {
		$VIEW_PATH = APP_PATH . 'views' . DS;
	}
	return $VIEW_PATH;
}

function get_module_path($module_name) {
	$module_path = MODULE_PATH . $module_name . DS;
	if (! file_exists($module_path)) {
		$module_path = MODULE_PATH . 'default' . DS;
	}	
	return $module_path;
}

?>