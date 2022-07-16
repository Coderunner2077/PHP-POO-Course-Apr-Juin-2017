<?php
namespace Concit;

abstract class Entity implements \ArrayAccess {
	use Hydrator;
	
	protected $errors = [],
			  $id;
	
	public function __construct(array $data = array()) {
		if(!empty($data))
			$this->hydrate($data);
	}
	
	public function setId($id) {
		$this->id = (int) $id;
	}
	
	public function id() { return $this->id; }
	
	public function errors() { return $this->errors; }
	
	public function isNew() {
		return empty($this->id);
	}
	
	public function offsetGet($offset) {
		return isset($this->$offset) && is_callable([$this, $offset]) ? $this->$offset : null;
	}
	
	public function offsetSet($var, $value) {
		$method = 'set'.ucfirst($var);
		if(is_callable([$this, $method]))
			$this->$method($value);
	}
	
	public function offsetExists($var) {
		return isset($this->$var) && is_callable([$this, $var]);
	}
	
	public function offsetUnset($var) {
		throw new \RuntimeException('Impossible de supprimer une quelconque valeur');
	}
}