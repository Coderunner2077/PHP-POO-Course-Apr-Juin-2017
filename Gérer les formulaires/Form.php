<?php
namespace OCFram;

class Form {
	protected $entity,
			  $fields;
	
	public function __construct(Entity $entity) {
		$this->setEntity($entity);
		$this->fields = [];
	}
	
	public function setEntity(Entity $entity) {
		$this->entity = $entity;
	}
	
	public function entity() {
		return $this->entity;
	}
	
	public function isValid() {
		$valid = true;
		foreach($this->fields as $field) {
			if(!$field->isValid())
				$valid = false;
		}
		return $valid;
	}
	
	public function add(Field $field) {
		$name = $field->name(); // on récupère le nom du champ
		$field->setValue($this->entity->name()); // On assigne la valeur correspondante au champ
		$this->fields[] = $field; // on ajoute le champ à la liste des champs
		
		return $this;
	}
	
	/**
	 * Méthode retournant le formulaire sous la forme d'une chaîne de caractères
	 * @return string
	 */
	public function createView() {
		$view = '';
		foreach($this->fields as $field)
			$view .= $field->buildWidget() .'<br />';
		
		return $view;
	}
}