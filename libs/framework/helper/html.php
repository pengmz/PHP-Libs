<?php
/**
 * @author pengmz
 */
class HTML {

	public static function link($url, $title = null, $attributes = array()) {
		if (is_null($title)) $title = $url;
		echo '<a href="'.$url.'"'. self::attributes($attributes) . '>' . $title . '</a>';
	}	
	
	public static function mailto($mail, $title = null, $attributes = array()) {
		if (is_null($title)) $title = $mail;
		echo self::link('mailto:' . $mail, $title, $attributes);
	}
	
	public static function image($url, $attributes = array()) {
		echo '<img src="'. $url .'"' . self::attributes($attributes). '>';
	}
	
	public static function ol($list, $attributes = array()) {
		$html = '<ol' . self::attributes($attributes) . '>';
		$html .= self::listing($list);
		$html .='</ol>';
		echo $html;
	}
	
	public static function ul($list, $attributes = array()) {
		$html = '<ul' . self::attributes($attributes) . '>';
		$html .= self::listing($list);
		$html .='</ul>';
		echo $html;
	}
	
	public static function listing($list = array()) {
		if (empty($list)) {
			return '';
		}
		$html = '';
		foreach ($list as $livalue) {
			$html .= '<li>' . $livalue . '</li>';
		}
		return $html;		
	}
	
	public static function attributes($attributes = array()) {
		if (empty($attributes)) {
			return '';			
		}		
		$html = ' '; 
		foreach ($attributes as $key => $value) {
			$html .= $key . '="' . $value . '"';
		}
		$html .= ' ';		
		return $html;
	}
	
	public static function input($id, $value = null, $attributes = array()) {
		if (is_null($value) || trim($value) == '') {
			$value = get_parameter($id);
		}
		echo new TextInputTag($id, $value, $attributes);
	}

	public static function password($id, $value = null, $attributes = array()) {
		if (is_null($value) || trim($value) == '') {
			$value = get_parameter($id);
		}		
		echo new PasswordInputTag($id, $value, $attributes);
	}

	public static function hidden($id, $value = null, $attributes = array()) {
		if (is_null($value) || trim($value) == '') {
			$value = get_parameter($id);
		}		
		echo new HiddenInputTag($id, $value, $attributes);
	}

	public static function button($id, $value = null, $attributes = array()) {
		echo new ButtonInputTag($id, $value, $attributes);
	}

	public static function submit($id, $value = null, $attributes = array()) {
		echo new SubmitButtonTag($id, $value, $attributes);
	}

	public static function reset($id, $value = null, $attributes = array()) {
		echo new ResetButtonTag($id, $value, $attributes);
	}
		
	public static function radio($id, $name, $value = null, $checked = false, $attributes = array()) {
		echo new RadioTag($id, $name, $value, $checked, $attributes);
	}
		
	public static function checkbox($id, $name, $value = null, $checked = false, $attributes = array()) {
		echo new CheckboxTag($id, $name, $value, $checked, $attributes);
	}
		
	public static function textarea($id, $value = null, $attributes = array()) {
		if (is_null($value) || trim($value) == '') {
			$value = get_parameter($id);
		}		
		echo new TextareaTag($id, $value, $attributes);
	}
	
	public static function option($value, $text = '', $selected = false, $attributes = array()) {
		echo new OptionTag($value, $text, $selected, $attributes);
	}
	
	public static function select($id, $name, $attributes = array()) {
		echo new SelectTag($id, $name, $attributes);
	}
	
	public static function selectEnd() {
		echo '</select>';
	}
	
	public static function form($action, $fields = array()) {
		echo '<form id="form1" name="form1" method="post" action="' . $action . '">';
		if (! empty($fields)) {
			foreach ($fields as $field) {
				$field_name = $field->Field;
				$field_value = $field->Default;
				if ($field_name == 'id') {
					self::hidden($field_name, $field_value);
				} else {
					echo '<label for="' . $field_name . '">' . ucfirst($field_name) .'</label>';
					if ($field->Type == 'text') {
					self::textarea($field_name, $field_value, array('rows' => '12', 'style'=>'width:80%'));
					} else {
						self::input($field_name, $field_value, array('style'=>'width:50%'));
					}
					echo '<br/>';
					echo '<div class="h10"></div>';
				}
			}
		}
		echo '<button class="btn btn-primary btn-large" id="saveForm" type="submit" style="width:200px">Save</button>';	    		
		echo '</form>';
	}
	
	public static function table($data = array()) {
		echo '<table class="table table-bordered table-striped">';
		if (! empty($data)) {
			self::tableHeader($data['headers']);
			self::tableBody($data['rows']);
		}
		echo '</table>';
	}	
	
	public static function tableHeader($headers = array()) {
		if (! empty($headers)) {
			$table = '<thead><tr>';
			foreach ($headers as $th) {
				$table .= '<th>';
				$table .= $th;
				$table .= '</th>';
			}
			$table .= '</tr></thead>';
			echo $table;
		}		
	}
	
	public static function tableBody($rows = array()) {
		if (! empty($rows)) {
			$table = '<tbody>';
			foreach ($rows as $tr) {
				$table .= '<tr>';
				foreach ($tr as $td) {
					$table .= '<td>';
					$table .= $td;
					$table .= '</td>';
				}
				$table .= '</tr>';
			}
			$table .= '</tbody>';
			echo $table;
		}		
	}
	
	public static function editor($id, $upload_path = 'upload/') {
		$editor_url = get_theme_url() . '/js/ueditor/';
		//$upload_url = get_site_url() . '/' . $upload_path;
				
		$upload_url = 'http://'; 
		$upload_url .= $_SERVER['HTTP_HOST'];
		$upload_url .= get_site_url();
		$upload_url .= '/' . $upload_path;
				
		echo "
		<script type=\"text/javascript\" charset=\"utf-8\">
		    window.UEDITOR_HOME_URL = '$editor_url';
		    window.UEDITOR_UPLOAD_URL = '$upload_url';
		</script>";
 
		include_theme_css('/js/ueditor/themes/default/css/ueditor.css'); 
		include_theme_script('/js/ueditor/ueditor.config.js'); 
		include_theme_script('/js/ueditor/ueditor.all.js'); 

		echo "
		<script type=\"text/javascript\">
		    var ue = UE.getEditor('$id');
		</script>";		
	}
}

abstract class FormTag {
	
	protected $id;	
	protected $name;	
	protected $value;
	protected $attributes;
	
	public function __construct($id, $name, $value = null, $attributes = array()) {
		$this->id = $id;
		$this->name = $name;
		$this->value = $value;
		$this->attributes = $attributes;
	}
	
	public function getTagAttribute($name, $value) {
		if ((! $name) || (! isset($value))) {
			return '';			
		}
		return ' ' . $name . '="' . $value . '"'; 
	}
	
	public function buildTagAttributes(){
		$attributes = '';
		$attributes .= $this->getTagAttribute('id', $this->id);
		$attributes .= $this->getTagAttribute('name', $this->name);
		$attributes .= $this->getTagAttribute('value', $this->value);
		$attributes .= HTML::attributes($this->attributes);		
		return $attributes;
	}
	
	public function __toString() {
		return $this->buildTagHtml();
	}
	
	public abstract function buildTagHtml();
}

class InputTag extends FormTag {
	
	protected $type;
	
	protected $autocomplete;
	
	public function __construct($id, $name, $type = 'text', $value = null, $attributes = array()) {
		parent::__construct($id, $name, $value, $attributes);
		$this->type = $type;

	}
	
	public function buildTagAttributes(){
		$attributes = parent::buildTagAttributes();
		$attributes .= $this->getTagAttribute('type', $this->type);
		return $attributes;
	}
	
	public function buildTagHtml(){
		return '<input' . $this->buildTagAttributes() . '/>';
	}	
	
}

class TextInputTag extends InputTag {
	
	public function __construct($id, $value, $attributes = array()) {
		parent::__construct($id, $id, 'text', $value, $attributes);
	}
		
}

class PasswordInputTag extends InputTag {
	
	public function __construct($id, $value, $attributes = array()) {
		parent::__construct($id, $id, 'password', $value, $attributes);
	}
		
}

class HiddenInputTag extends InputTag {
	
	public function __construct($id, $value, $attributes = array()) {
		parent::__construct($id, $id, 'hidden', $value, $attributes);
	}
	
}

class ButtonInputTag extends InputTag {
	
	public function __construct($id, $value, $attributes = array()) {
		parent::__construct($id, $id, 'button', $value, $attributes);
	}
		
}

class SubmitButtonTag extends InputTag {
	
	public function __construct($id, $value, $attributes = array()) {
		parent::__construct($id, $id, 'submit', $value, $attributes);
	}
		
}

class ResetButtonTag extends InputTag {
	
	public function __construct($id, $value, $attributes = array()) {
		parent::__construct($id, $id, 'reset', $value, $attributes);
	}
		
}

class RadioTag extends InputTag {
	
	protected $checked;
		
	public function __construct($id, $name, $value, $checked, $attributes = array()) {
		if($checked === true){
			$attributes['checked'] = 'checked';
		}		
		$this->checked = $checked;		
		parent::__construct($id, $name, 'radio', $value, $attributes);
	}

}

class CheckboxTag extends InputTag {
	
	protected $checked;
	
	public function __construct($id, $name, $value, $checked, $attributes = array()) {
		if($checked === true){
			$attributes['checked'] = 'checked';
		}		
		$this->checked = $checked;
		parent::__construct($id, $name, 'checkbox', $value, $attributes);
	}
			
}

class TextareaTag extends FormTag {
	
	protected $text;
	
	public function __construct($id, $text, $attributes = array()) {
		parent::__construct($id, $id, null, $attributes);
		$this->text = $text;
	}
	
	public function buildTagHtml(){
		return '<textarea' . $this->buildTagAttributes() . '>' . $this->text . '</textarea>';
	}	
	
}

class OptionTag extends FormTag {
	
	protected $text;
	
	protected $selected;	
		 
	public function __construct($value, $text, $selected, $attributes = array()) {
		if($selected === true){
			$attributes['selected'] = 'selected';
		}		
		$this->text = $text;
		$this->selected = $selected;
		parent::__construct($value, $value, $value, $attributes);
	}
	
	public function buildTagHtml(){
		return '<option' . $this->buildTagAttributes() . '>' . $this->text . '</option>';
	}	
		
}

class SelectTag extends FormTag {
		
	public function __construct($id, $name, $attributes = array()) {
		parent::__construct($id, $name, null, $attributes);
	}
		
	public function buildTagHtml(){
		return '<select' . $this->buildTagAttributes() . '/>';
	}	
		
}

?>