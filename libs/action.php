<?php
class ACTION {

	private static $actions;
	
	public static function addAction($action_name, $function, $method = 'any', $priority = 10) {
		$request = get_request();
		if (($method == 'any') || ($method == $request->method())) {
			self::$actions[] = array('name' => $action_name, 'function' => $function, 'priority' => $priority);		
		}
	}
	
	public static function execAction($action_name, $added_params = array()) {
		if (empty(self::$actions)) {
			return;
		}		
				
		$request = get_request();
		$params = $request->data();
		if (! empty($added_params)) {
			$params = array_merge($params, $added_params);
		}
							
		$actions = self::$actions;
		
		/*
		//Sort
		foreach($actions as $key => $item) {
			$sort_keys[$key] = $item['priority'];
		}		
		array_multisort($sort_keys, SORT_ASC, $actions);
		*/
		
		foreach($actions as $action) {
			if ($action['name'] == $action_name) {
				try {
					call_user_func_array($action['function'], $params);
				} catch (Exception $ex) {
					throw new CoreException($ex->getMessage());
				}
			}			
		}
	}
}

class HOOK {

	private static $filters;
	
	public static function addFilter($hook_name, $function, $filter = 'before', $priority = 10) {
		self::$filters[] = array('name' => $hook_name, 'function' => $function, 'filter' => $filter, 'priority' => $priority);
	}
	
	public static function addFilters($hooks = array(), $function, $filter = 'before', $priority = 10) {
		if (! empty($hooks)) {
			foreach ($hooks as $hook_name) {
				self::addFilter($hook_name, $function, $filter, $priority);
			}
		}
	}

	public static function afterFilter($hooks, $added_params = array()) {
		self::execFilter($hooks, $added_params, 'after');
	}
	
	public static function beforeFilter($hooks, $added_params = array()) {
		self::execFilter($hooks, $added_params, 'before');
	}
	
	public static function execFilter($hooks, $added_params = array(), $filter = 'before') {
		if (empty(self::$filters)) {
			return;
		}	

		$request = get_request();
		$params = $request->data();
		if (! empty($added_params)) {
			$params = array_merge($params, $added_params);
		}
				
		$filters = self::$filters;
		
		/*
		//Sort
		foreach($filters as $key => $item) {
			$sort_keys[$key] = $item['priority'];
		}		
		array_multisort($sort_keys, SORT_ASC, $filters);
		*/
				
		foreach($filters as $hook) {
			if (($hook['filter'] == $filter) && in_array($hook['name'], $hooks)) {
				try {
					//call_user_func($hook['function']);
					call_user_func_array($hook['function'], $params);
				} catch (Exception $ex) {
					throw new CoreException($ex->getMessage());
				}
			}
		}
	}	
	
}
?>