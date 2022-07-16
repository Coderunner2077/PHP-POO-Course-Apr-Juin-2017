<?php
namespace OCFram;

abstract class Entity implements \ArrayAccess {
	protected $errors = [],
			  $id;
	
	public function __construct(array $donnees = array()) {
		if(!empty($donnees))
			$this->hydrate($donnees);
	}
	
	public function isNew() {
		return empty($this->id);
	}
	
	public function hydrate(array $donnees) {
		foreach($donnees as $key => $value) {
			$method = 'set'.ucfirst($key);
			if(is_callable([$this, $method]))
				$this->$method($value);
		}
	}
	
	public function id() {
		return $this->id;
	}
	
	public function errors() {
		return $this->errors;
	}
	
	public function setId($id) {
		$this->id = (int) $id;
	}
	
	public function offsetExists($var) {
		return isset($this->$var) && is_callable([$this, $var]);
	}
	
	public function offsetUnset($var) {
		throw new \RuntimeException('Impossible de supprimer une quelconque valeur');
	}
	
	public function offsetGet($var) {
		if(isset($this->$var) && is_callable([$this, $var]))
			return $this->$var;
	}
	
	public function offsetSet($var, $value) {
		$method = 'set' . ucfirst($var);
		if(is_callable([$this, $method]))
			$this->$method($value);
	}
}