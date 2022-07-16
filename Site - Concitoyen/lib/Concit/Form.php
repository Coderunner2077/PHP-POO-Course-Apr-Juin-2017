<?php
namespace Concit;

class Form {
	protected $entity,
			  $fields = [],
			  $namesValues = [];
	
	public function __construct(Entity $entity, array $namesValues = array()) {
		$this->setEntity($entity);
		$this->namesValues = $namesValues;
	}
	
	public function setEntity(Entity $entity) { $this->entity = $entity; }
	
	public function entity() { return $this->entity; }
	
	public function createView() {
		$view = '';
		foreach($this->fields as $field)
			$view .= $field->buildWidget() . '<br />';
		
		return $view;
	}
	
	public function isValid() {
		$valid = true;
		foreach($this->fields as $field)
			$valid = $field->isValid() && $valid;
		
		return $valid;
	}
	
	public function add(Field $field) {
		$attr = $field->name();
		if(is_callable([$this->entity, $attr])) {
			$field->setValue($this->entity->$attr());
			$this->fields[$attr] = $field;
		}
		elseif(array_key_exists($attr, $this->namesValues)) {
			$field->setValue($this->namesValues[$attr]);
			$this->fields[$attr] = $field;
		}
		
		return $this;	
	}
	
	public function field($name) {
		return isset($this->fields[$name]) ? $this->fields[$name] : null;			
	}
	
	public function namesValues() { return $this->namesValues(); }
	
}