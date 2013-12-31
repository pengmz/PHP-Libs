<?php

class ScaffoldController extends BaseController {
	
	private $model;
	
	public function __construct($table) {
		parent::__construct();
		$this->model = new ScaffoldModel($table);
	}
	
	public function index() {
		$list = $this->model->findAll();
		$this->set('list', $list);
		return $this->render('_list');
	}

	public function add() {
		$fields = $this->getModelFields();
		$this->set('fields', $fields);
		return $this->renderForm('_form');
	}

	public function save() {
		$form = $this->getScaffoldForm();
		if ($form && $form->isValid()) {
			$this->model->save($form->data());
			return $this->redirectToIndex();
		} else {
			$fields = $this->getModelFields();
			$this->set('fields', $fields);				
			return $this->renderForm('_form', $form);		
		}		
	}
	
	public function edit() {
		$id = $this->get('id');
		$obj = $this->model->findById($id);
		$fields = $this->getModelFields();
		$this->set('fields', $fields);		
		return $this->renderForm('_form', $obj);
	}
	
	public function update() {
		$id = $this->get('id');
		$form = $this->getScaffoldForm();
		if ($id && $form) {
			$this->model->updateById($id, $form->data());
			return $this->redirectToIndex();
		} else {
			$fields = $this->getModelFields();
			$this->set('fields', $fields);				
			return $this->renderForm('_form', $form);		
		}			
	}
		
	protected function render($template, $added_data = array()) {
		$template = $this->getScaffoldTemplate($template);
		return parent::render($template, $added_data);
	}
		
	public function redirectToIndex() {
		return $this->redirect(array('d' => 'index'));
	}
		
	protected function getModelFields() {
		return $this->model->getFields();
	}
	
	protected function getScaffoldForm() {
		$form_fields = array();
		$fields = $this->getModelFields();
		if (! empty($fields)) {
			foreach ($fields as $field) {
				$form_fields[] = $field->Field;
			}
		}
		return new ScaffoldForm($this->context->request->data(), $form_fields);
	}
	
	protected function getScaffoldTemplate($template) {
		$scaffold_template = get_controller_name() . $template;
		if (is_file(get_view_path() . $scaffold_template . EXT)) {
			return $scaffold_template;
		}
		return $template;		
	}
	
}
?>