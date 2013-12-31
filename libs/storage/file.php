<?php

/**
 * @author pengmz
 */
class FileStorage implements Storage {
	
	public function read($filename) {
		if ($this->isExists($filename)) {
			return file_get_contents($filename);
		}
		return FALSE;
	}
	
	public function readLines($filename) {
		if ($this->isExists($filename)) {
			return file($filename);
		}
		return FALSE;
	}
	
	public function write($filename, $data) {
		return file_put_contents($filename, $data);
	}
	
	public function writeLines($filename, $lines) {
		if ($this->isExists($filename) && $this->isWritable($filename)) {
			return file_put_contents($filename, implode("\n", $lines), FILE_APPEND);
		}
		return FALSE;
	}
	
	public function isWritable($filename) {
		return is_writable($filename);
	}
	
	public function isReadable($filename) {
		clearstatcache();
		return is_readable($filename);
	}
	
	public function isExists($filename) {
		clearstatcache();
		return file_exists($filename);
	}
	
	public function mtime($filename) {
		return filemtime($filename);
	}

}
?>
