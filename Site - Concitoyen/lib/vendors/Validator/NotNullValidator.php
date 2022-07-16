<?php
namespace Validator;

class NotNullValidator extends Validator {
	public function isValid($value) {
		return is_string($value) ? \trim($value) != '' : !empty($value);
	}
}