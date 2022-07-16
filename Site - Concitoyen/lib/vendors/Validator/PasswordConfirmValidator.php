<?php
namespace Validator;

class PasswordConfirmValidator extends Validator {
	protected $pass;
	
	public function __construct($errorMessage, $pass) {
		parent::__construct($errorMessage);
		$this->pass = $pass;
	}
	
	public function isValid($value) {
		return $value == $this->pass;
	}
}