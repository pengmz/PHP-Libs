<?php

/**
 * @author pengmz
 */
class SAEFileStorage {
	
	private $kv;
	private $sae;
	
	public function __construct() {
		$this->kv = new SaeKV();
		$this->kv->init();
		$this->sae = new SaeStorage();
	}
		
	public function read($key) {		
		return $this->kv->get($key);
	}
	
	public function write($key, $data) {
		if ($this->isExists($key)) {
			$this->kv->set($key . '_mtime', time());
			return $this->kv->set($key, $data);
		} else {
			$this->kv->add($key . '_mtime', time());
			return $this->kv->add($key, $data);
		}		
	}
	
	public function isExists($key) {
		if ($this->read($key) != false) {
			return true;
		}
		return false;
	}

	public function upload(string $domain, string $dest_filename, string $src_filename) {
		return $this->sae->upload($domain, $dest_filename, $src_filename);
	}
	
	public function mtime($filename) {
		return $this->read($filename . '_mtime');
	}	
}
?>
