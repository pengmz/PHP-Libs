<?php

class Sorter {
	
	private static $sortby;
	
	private static $order;
	
	public static function init($items = array(), $sortby, $order = 'asc') {
		return self::sorting($items, $sortby, $order);
	}
	
	public static function sorting($items = array(), $sortby, $order = 'asc') {
		self::$sortby = $sortby;
		self::$order = $order;
				
		foreach ($items as $key => $item) {
			$sort_keys[$key] = $item->$sortby;
		}		
		if ($order == 'asc') {
			array_multisort($sort_keys, SORT_ASC, $items);
		} else {
			array_multisort($sort_keys, SORT_DESC, $items);			
		}
			
		return $items;	
	}
	
	public static function link($id, $link_text, $url = null) {
		if (is_null($url)) {
			$url = $_SERVER['PHP_SELF'];
		}
		$url .= (stripos($url, '?') !== false) ? '&' : '?';
		
		$param = $_GET;
		$param['sortby'] = $id;
		$param['order'] = 'asc';
		if (self::$sortby == $id) {
			echo self::generate_link($link_text, $url, $id, self::$order, TRUE);
		} else {
			echo self::generate_link($link_text, $url, $id, 'dasc');
		}
	}
	
	private static function generate_link($link_text, $url, $sortby, $order, $is_sorted = FALSE) {
		$param = $_GET;
		$param['sortby'] = $sortby;
		$param['order'] = $order == 'asc' ? 'desc' : 'asc';
		$url .= http_build_query($param);
		
		if ($is_sorted) {
			return '<a href="' . $url . '" class="' . $order . '">' . $link_text . '</a>';
		} else {
			return '<a href="' . $url . '" >' . $link_text . '</a>';
		}
	}

}

?>