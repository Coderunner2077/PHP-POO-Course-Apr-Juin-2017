<?php

namespace OCFram;

class FormHandler {
	protected $form,
			  $manager,
			  $request;
	
	public function __construct(Form $form, Manager $manager, HTTPRequest $request) {
		$this->setForm($form);
		$this->setManager($manager);
		$this->setRequest($request);
	}
	
	public function setForm(Form $form) {
		$this->form = $form;
	}
	
	public function setManager(Manager $manager) {
		$this->manager = $manager;
	}
	
	public function setRequest(HTTPRequest $request) {
		$this->request = $request;
	}
	
	public function process() {
		if($request->method() == 'POST' && $form->isValid()) {
			$manager->save($form->entity());
			return true;
		}
		return false;
	}
}