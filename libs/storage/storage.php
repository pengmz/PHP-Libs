<?php

/**
 * @author pengmz
 */
interface Storage {
	
	public function read($filename);
	
	public function write($filename, $data);
	
	public function isExists($filename);

	public function mtime($filename);
}

global $STORAGE;

function get_storage() {
	global $STORAGE;	
	if (! $STORAGE) {
		$STORAGE = new FileStorage();
	}	
	return $STORAGE;	
}

function storage_read($filename) {
	return get_storage()->read($filename);
}

function storage_write($filename, $data) {
	return get_storage()->write($filename, $data);
}

function storage_is_exists($filename) {
	return get_storage()->isExists($filename);
}

function storage_mtime($filename) {
	return get_storage()->mtime($filename);
}

include_once dirname(__FILE__) . '/file.php';

?>