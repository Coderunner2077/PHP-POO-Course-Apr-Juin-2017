<?php
namespace OCFram;

class TextField extends Field {
	protected $rows,
			  $cols;
	
	public function buildWidget() {
		$widget = '';
		if(!empty($this->errorMessage))
			$widget .= $this->errorMessage . '<br />';
		$widget .= '<label>' .$this->label. '</label><textarea name="'.$this->name.'"';
		
		if(!empty($this->rows))
			$widget .= ' rows="'.$this->rows .'"';
		if(!empty($this->cols))
			$widget .= ' cols="'.$this->cols.'"';
		$widget .= !empty($this->value) ? ('>'.htmlspecialchars($this->value).'<textarea>') : '><textarea>';
		
		return $widget;
	}
	
	public function setRows($rows) {
		$rows = (int) $rows;
		if($rows > 0) 
			$this->rows = $rows;
	}
	
	public function setCols($cols) {
		$cols = (int) $cols;
		if($cols)
			$this->cols = $cols;
	}
}