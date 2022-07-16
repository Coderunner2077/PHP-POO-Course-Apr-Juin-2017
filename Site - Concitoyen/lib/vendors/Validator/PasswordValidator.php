<?php
namespace Validator;

class MinLengthValidator extends Validator {
	protected $minLength;
	public function __construct($errorMessage, $minLength) {
		parent::__construct($errorMessage);
		$this->setMinLength($minLength);
	}
	
	public function setMinLength($minLength) {
		$minLength = (int) $minLength;
		if($minLength > 0)
			$this->minLength = $minLength;
		else
			throw new \RuntimeException('La valeur minimale doit être un nombre supérieur à 0');
	}
	public function isValid($value) {
		return \strlen($value) >= $this->minLength;
	}
}