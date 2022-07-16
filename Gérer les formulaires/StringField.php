<?php
namespace OCFram;

class StringField extends Field {
	protected $maxLength;
	
	public function buildWidth() {
		$widget = '';
		if(!empty($this->errorMessage))
			$widget .= $this->errorMessage .'<br />';
		$widget .= '<label>' . $this->label . '</label><input type="text" name="'.$this->name .'"';
		if(trim($this->value))
			$widget .= ' value="'.htmlspecialchars($this->value).'"';
		if($this->maxLength > 0) 
			$widget .= ' maxlength="'.$this->maxLangth.'"';
		
		$widget .= ' />';
		return $field;
	}
	
	public function setMaxLength($maxLength) {
		$maxLength = (int) $maxLength;
		if($maxLength > 0)
			$this->maxLength = $maxLength;
		else 
			throw new \RuntimeException('La longuer maximale doit être un nombre supérieur à 0');
	}
}