<?php
namespace Validator;

abstract class Validator {
	protected $errorMessage;
	
	public function __construct($errorMessage) {
		$this->setErrorMessage($errorMessage);
	}
	
	abstract public function isValid($value);
	
	public function errorMessage() { return $this->errorMessage; }
	
	public function setErrorMessage($errorMessage) {
		if(is_string($errorMessage) && \trim($errorMessage))
			$this->errorMessage = $errorMessage;
	}
}