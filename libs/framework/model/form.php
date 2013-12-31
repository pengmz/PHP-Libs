<?php

/**
 * @author pengmz
 */	
class FormObject extends BaseObject {

	public function __construct($data) {
		parent::__construct($data);
	}
	
}

/**
 * @author pengmz
 */	
class RequestForm extends FormObject {

	public function __construct($data, $fields = array()) {
		$form_data = array();
		if (! empty($fields)) {
			foreach ($fields as $field) {
				if (isset($data[$field])) {
					$form_data[$field] = $data[$field];
				}
			}
		}
		parent::__construct($form_data);
	}
	
	public function isValid() {
		$data = $this->getAttributes();
		return !empty($data);
	}
	
	protected function alertMessage($message, $type = 'error') {
		ALERT::add($message, $type);
	}	
	
}
	

class ScaffoldForm extends RequestForm {

	public function __construct($data, $fields = array()) {
		parent::__construct($data, $fields);
	}
	
}

?>