<?php
/** @Target("class") */
class ClassInfo extends Annotation {
	public $name;
	public $description;
	
	public function checkConstraints($target) {
		if(!is_string($this->name))
			throw new Exception('Le nom doit être une chaîne');
		if(!is_string($this->description))
			throw new Exception('La description...');
	}
}

/** @Target("property") */
class AttrInfo extends Annotation {
	
}

/** @Target("method") */
class MethodInfo extends Annotation {
	public $description;
	public $return;
	public $returnDescription;
	
	public function checkConstraints($target) {
		if(!is_string($this->return))
			throw new Exception('Le nom doit être une chaîne');
		if(!is_string($this->description))
				throw new Exception('La description...');
	}
}

class ParamInfo extends Annotation {
	public $name;
	public $description;
	
	public function checkConstraints($target) {
		if(!is_string($this->name))
			throw new Exception('Le nom doit être une chaîne');
		if(!is_string($this->description))
				throw new Exception('La description...');
	}
}
