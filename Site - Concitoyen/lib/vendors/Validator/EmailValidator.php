<?php
namespace Validator;

class EmailValidator extends Validator {
	public function isValid($value) {
		if(preg_match('#^[a-zA-Z0-9_.-]+@[a-zA-Z0-9_-]{3,10}\.[a-zA-Z]{2,6}$#', $value))
			return true;
		else 
			return false;
	}
}