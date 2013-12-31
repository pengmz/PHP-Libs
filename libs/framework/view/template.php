<?php

/*
 * @author pengmz
 */
class Template {
	
	protected $name;
	protected $file;
	protected $compiled_file;
	
	public function __construct($name) {
		$this->name = $name;
		$this->init();
	}
	
	public function init() {
		$file = get_view_path() . $this->name . EXT;
		if (is_file($file)) {
			$this->file = $file;
			//$this->compiled_file = get_theme_path() . '_cache' . DS . md5($file) . EXT;
			$this->compiled_file = get_theme_path() . '_cache' . DS . md5($this->name) . EXT;
		}			
	}
	
	public function compile() {
		if (! $this->file) {
			return false;
		}		
		if (! $this->compiled_file) {
			return false;
		}

		if ($this->needRecompile()) {
			$contents = $this->parse($this->file);
			$this->write($this->compiled_file, $contents);
		}

		return $this->compiled_file;
	}
	
	public function parse($file) {
		$contents = $this->load($file);
		$parser = new BaseTemplateParser();
		return $parser->parse($contents);
	}

	public function version($file) {
		if (! $file) {
			$file = $this->file;
		}
		return storage_mtime($file);
	}
	
	public function needRecompile() {
		if (! $this->isExists($this->compiled_file)) {
			return true;
		}
		return $this->version($this->file) > $this->version($this->compiled_file); 
	}
	
	public function compiledFile() {
		return $this->compiled_file;
	}
		
	public function load($filename) {
		return storage_read($filename);
	}
	
	public function write($filename, $data) {
		return storage_write($filename, $data);
	}
	
	public function isExists($filename) {
		return storage_is_exists($filename);
	}
	
	public function __toString() {
		return $this->compiledFile();
	}
		
}

class LayoutTemplate extends Template {
	
	protected $layout_name;
	protected $layout_file;
	
	public function __construct($name, $layout_name) {
		$this->layout_name = $layout_name;	
		parent::__construct($name);
	}

	public function init() {
		parent::init();
		$layout_file = get_theme_path() . $this->layout_name . EXT;
		if (is_file($layout_file)) {
			$this->layout_file = $layout_file;
			//$this->compiled_file = get_theme_path() . '_cache' . DS . md5($this->name . '_with_' . $layout_file) . EXT;
			$this->compiled_file = get_theme_path() . '_cache' . DS . md5($this->name . '_with_' . $this->layout_name) . EXT;
		}		
	}	
		
	public function parse($file) {
		$contents = parent::parse($file);
		if (is_file($this->layout_file)) {
			$layout = file_get_contents($this->layout_file);
			$contents = str_replace("{main_body_contents}", $contents, $layout);				
		}		
		return $contents;
	}
		
	public function needRecompile() {
		if (parent::needRecompile()) {
			return true;
		}
		return $this->version($this->layout_file) > $this->version($this->compiled_file); 
	}

}

/*
 * @author pengmz
 */
interface TemplateParser {
	function parse($contents);
}

/**
 * Base Template Parser
 * @author pengmz
 */	
class BaseTemplateParser implements TemplateParser {
	
	public function __construct() {}
	
	public function parse($contents){
		$patterns = $this->getPatterns();
		return preg_replace(array_keys($patterns), array_values($patterns), $contents);
	}
	
	public function getPatterns() {
		return array(
			"/\\#{(.*?)\\}/s"	=>	"<?php $1; ?>",
			"/\\\${(.*?)\\}/s"	=>	"<?php echo $$1; ?>",		
			"/\\_{(.*?)\\}/s"	=>	"<?php echo __('$1'); ?>"		
		);
	}
}

?>