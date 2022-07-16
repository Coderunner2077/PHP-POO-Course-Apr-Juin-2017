<?php
namespace Validator;

class MaxLengthValidator extends Validator {
	protected $maxLength;
	
	public function __construct($errorMessage, $maxLength) {
		parent::__construct($errorMessage);
		$this->setMaxLength($maxLength);
	}
	
	public function setMaxLength($maxLength) {
		$maxLength = (int) $maxLength;
		if($maxLength > 0)
			$this->maxLength = $maxLength;
		else 
			throw new \RuntimeException('La longuer maximale doit être supérieure à 0');
	}
	
	public function isValid($value) {
		return \strlen($value) <= $this->maxLength;
	}
}