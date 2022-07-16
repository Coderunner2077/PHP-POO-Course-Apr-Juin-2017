<?php


class MyErrorException extends ErrorException {	
	public function __toString() {
		switch($this->severity) {
			case E_USER_ERROR: 
				$type = 'Erreur fatale';
				break;
			case E_USER_WARNING:
			case E_WARNING: 
				$type = 'Attention';
				break;
			case E_USER_NOTICE:
			case E_NOTICE:
				$type = 'Note';
				break;
			default:
				$type = 'Erreur inconnue';					
		}
		return '<strong>' . $type .'</strong> : [' . $this->code .'] '. $this->message .'<br /><strong>'.$this->file.'</strong>' 
				. ' a la ligne <strong>' .$this->line . '</strong>';
	}
}