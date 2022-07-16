<?php
namespace OCFram;

abstract class Field {
	use Hydrator;
	
	protected $name,
			  $value,
			  $errorMessage,
			  $label,
			  $validators = array();
	
	public function __construct(array $options = array()) {
		if(!empty($options))
			$this->hydrate($options);
	}
	
	abstract public function buildWidget();
	
	public function isValid() {
		foreach($this->validators as $validator) {
			if(!$validator->isValid($this->value)) {
				$this->errorMessage = $validator->errorMessage();
				return false;
			}
		}
		return true;
	}
	
	public function setName($name) {
		if(is_string($name))
			$this->name = $name;
	}
	
	public function setValue($value) {
		if(is_string($value))
			$this->value = $value;
	}
	
	public function setLabel($label) {
		if(is_string($label))
			$this->label = $label;
	}
	
	public function setValidators(array $validators) {
		foreach($validators as $validator) {
			if($validator instanceof Validator && !in_array($validator, $this->validators))
				$this->validators[] = $validator;
		}
	}
	
	public function validators() {
		return $this->validators;
	}
	
	public function name() {
		return $this->name;
	}
	
	public function value() {
		return $this->value;
	}
	
	public function label() {
		return $this->label;
	}	
}