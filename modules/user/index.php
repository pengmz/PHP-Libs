<?php

class UserController extends ModuleController {
	
	private $user;
	
	public function index() {
		$users = $this->user->findAll();
		$this->setAttribute('users', $users);
		return $this->render('user_index');
	}

	public function add() {
		return $this->renderForm('user_form');
	}
	
	public function save() {
		$form = $this->bindForm('UserForm');
		if ($form && $form->isValid()) {
			$this->user->save($form->data());
			$this->alertMessage('User Saved');
			return $this->redirectToIndex();
		} else {
			return $this->renderForm('user_form', $form);		
		}
	}
	
	public function edit() {
		$user_id = $this->get('id');
		$user = $this->user->findById($user_id);
		
		return $this->renderForm('user_form', $user);
	}
	
	public function update() {
		$user_id = $this->get('id');
		$form = $this->bindForm('UserForm');
		if ($user_id && $form) {
			$this->user->updateById($user_id, $form->data());
			$this->alertMessage('User Updated');
			return $this->redirectToIndex();
		} else {
			return $this->renderForm('user_form', $form);		
		}		
	}
	
	public function delete() {
		$user_id = $this->get('id');
		$this->user->deleteById($user_id);
		$this->alertMessage('User Deleted');
		return $this->redirectToIndex();		
	}
	
	public function redirectToIndex() {
		return $this->redirect('?c=user');
	}
	
	public function setUser($user) {
		$this->user = $user;
	}
	
}
?>