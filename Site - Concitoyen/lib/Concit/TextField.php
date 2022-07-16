<?php
namespace Concit;

class TextField extends Field {
	protected $rows,
			  $cols;
	
	public function setRows($rows) {
		$rows = (int) $rows;
		if($rows > 0)
			$this->rows = $rows;
	}
	
	public function setCols($cols) {
		$cols = (int) $cols;
		if($cols > 0)
			$this->cols = $cols;
	}	
	
	public function buildWidget() {
		$widget = '';
		if(!empty($this->errorMessage))
			$widget = $this->errorMessage . '<br />';
		if(!empty($this->label))
			$widget .= '<label>'.$this->label.'</label>';
		$widget .='<textarea name="'.htmlspecialchars($this->name) .'"';
		$widget .= ($this->cols > 0 ? ' cols="'.$this->cols.'"' : '');
		$widget .= ($this->rows > 0 ? ' rows="'.$this->rows.'"' : '');
		$widget .='>';
		if(!empty($this->value))
			$widget .= htmlspecialchars($this->value);
		$widget .= '</textarea>';
		
		return $widget;		
	}
}