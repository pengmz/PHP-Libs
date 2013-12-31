<?php

include_once dirname(__FILE__) . '/template.php';

/**
 * @author pengmz
 */	
class ViewObject extends Object {
	
	protected $template;
	
	protected $layout;
	
	public function __construct($template, $data = array(), $layout = null) {
		parent::__construct($data);
		$this->template = $template;
		$this->layout = $layout;
	}
	
	public function layout($layout) {
		$this->layout = $layout;
	}
	
	public function assign($name, $value) {
		$this->setAttribute($name, $value);
	}

}

/**
 * @author pengmz
 */	
class ViewResponse extends ViewObject {
	
	public function __construct($template, $data = array(), $layout = null) {		
		parent::__construct($template, $data, $layout);
	}
		
	public function fetch() {
		if (! $this->layout) {
			$template = new Template($this->template);		
		} else {
			$template = new LayoutTemplate($this->template, $this->layout);
		}
		$template_file = $template->compile();		
	
		
		$request = get_request();
		$response = get_response();
		
		extract($this->data());
		
		ob_start();
		
		if (! is_file($template_file)) {
			echo $template_file;
		} else {			
			include $template_file;
		}
		$contents = ob_get_contents();
		
		ob_end_clean();
		
		return $contents;	
	}
	
	public function render() {
		echo $this->fetch();
	}

	public function __toString() {
		return $this->fetch();
	}
	
	public function import($template) {
		$view = new ViewResponse($template, $this->data());
		return $view->render();		
	}
			
}

?>