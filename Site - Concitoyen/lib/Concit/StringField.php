<?php
namespace Concit;

class StringField extends Field {
	protected $maxLength,
			  $type;
	
	public function __construct(array $options = array(), $type = 'text') {
		parent::__construct($options);
		$this->setType($type);
	}
	
	public function setMaxLength($maxLength) { 
		if($maxLength <= 0)
			throw new \RuntimeException('La longueur maximale doit être un nombre supérieur à 0');
		$this->maxLength = (int) $maxLength; }
	
	public function setType($type) { if(is_string($type)) $this->type = $type; }
	
	public function buildWidget() {
		$widget = '';
		if(!empty($this->errorMessage))
			$widget = $this->errorMessage .'<br />';
		if(!emtpty($this->label))
			$widget .= '<label>'.htmlspecialchars($this->label).'</label>';
		$widget .= '<input type="'.$this->type.'" name="'.htmlspecialchars($this->name).'"';
		if(!empty($this->value))
			$widget .= ' value="'.htmlspecialchars($this->value).'"';
		if($this->maxLength > 0)
			$widget .=' maxlength="'.$this->maxLength.'"';
		
		return $widget . ' />';		
	}
}