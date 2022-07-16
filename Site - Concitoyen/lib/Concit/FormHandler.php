<?php
namespace Concit;


class FormHandler {
	protected $form,
			  $manager,
			  $request;
	
	public function __construct(Form $form, Manager $manager, HTTPRequest $request) {
		$this->form = $form;
		$this->manager = $manager;
		$this->request = $request;
	}
	
	public function process() {
		if($request->method() == 'POST' && $this->form->isValid()) {
			$this->manager->save($this->form->entity());
			return true;
		}
		return false;
	}
	
	public function setForm(Form $form) { $this->form = $form; }
	
	public function setManager(Manager $manager) { $this->manager = $manager; }
	
	public function setRequest(HTTPRequest $request) { $this->request = $request; }
	
	public function processConnection() {
		if($request->method() == 'POST' && $form->isValid()) {
			return $manager->connect($this->form->entity());
		}
		
		return false;
	}
	
	public function processRegister() {
		if($this->request->method() == 'POST' && $this->form->isValid()) {
			if($this->manager->pseudoExists($this->form->entity()->pseudo())) {
				$this->form->field('pseudo')->setErrorMessage('Le pseudo choisi existe déjà. Veuillez en choisir un autre');
				return false;
			}
			
			$this->manager->add($this->form->entity());
			return true;
		}
		else
			return false;
	}
	
	public function processChangePassword() {
		if($this->request->method() == 'POST' && $this->form->isValid()) {
			$namesValues = $this->form->namesValues();
			$membre = $this->form->entity();
			if(($id = $this->manager->passwordExists($membre->pass())) && $id === $member->id()) {
				$this->manager->changePassword($membre, sha1($namesValues['newPass']));
				
				return true;
			}
			
			$this->form->field('pass')->setErrorMessage('Le mot de passe est incorrect. Veuillez réessayer');
			
			return false;
		}
		else 
			return true;
	}
	
	public function processUpdateMember(User $user) {
		if($this->request->method() == 'POST' && $this->form->isValid()) {
			$membre = $this->form->entity();
			if(($id = $this->manager->passwordExists($membre->pass())) === false || $id !== $membre->id()) {
				$this->form->field('pass')->setErrorMessage('Le mot de passe est incorrect. Veuillez réessayer');
				
				return false;
			}
			if($user->getAttribute('pseudo') != $membre->pseudo() && $this->manager->pseudoExists($this->form->entity()->pseudo())) {
				$this->form->field('pseudo')->setErrorMessage('Un tel pseudo existe déjà. Choisissez-en un autre');
				return false;
			}
			
			$this->manager->modify($membre);
			
			return true;
		}
		
		return false;
	}
	
	public function processDeleteMember() {
		if($this->request->method() == 'POST' && $this->form->isValid()) {
			$membre = $this->form->entity();
			if(($id = $this->manager->passwordExists($membre->pass())) === false || $id !== $membre->id()) {
				$this->form->field('pass')->setErrorMessage('Le mot de passe est incorrect. Veuillez réessayer');
				
				return false;
			}
			$this->manager->delete($membre->id());
			
			return true;
		}
		
		return false;
	}
}