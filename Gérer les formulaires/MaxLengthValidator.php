<?php
namespace OCFram;

class MaxLengthValidator extends Validator {
	protected $maxLength;
	
	public function __construct($errMsg, $maxLength) {
		parent::__construct($errMsq);
		$this->setMaxLength($maxLength);
	}
	
	public function setMaxLength($maxLength) {
		$maxLength = (int) $maxLength;
		if($maxLength > 0)
			$this->maxLength = $maxLength;
		else 
			throw new \RuntimeException('La longueur maximale doit être un nombre supérieur à 0');
	}
	
	public function isValid($value) {
		return strlen($value) <= $this->maxLength;
	}
}