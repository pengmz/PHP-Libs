<?php

	function test($param = array()) {
		$node_type = 'product';
		$node_list = array();
		for ($i = 1; $i < 6; $i++) {
			$id = $i * 1111;
			$node = array(
				'id'	=>	$id,
				'type'	=>	$node_type,
				'title'	=>	$node_type . ' title ' . $id,
				'price'	=>	$node_type . ' price ' . $id,
				'description'	=>	$node_type . ' description ' . $id,
				'image'	=>	'http://' . $node_type . '_image_' . $id . '.jpg'
			);
			$node_list[] = $node;		
		}		
		return $node_list;
	}
	
?>