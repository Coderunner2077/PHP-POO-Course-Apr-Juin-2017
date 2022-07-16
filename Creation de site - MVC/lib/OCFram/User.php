<?php
namespace OCFram;

session_start();

class User {	
	public function getAttribute($attr) {
		return isset($_SESSION[$attr]) ? $_SESSION[$attr] : null;
	}
	
	public function setAttribute($attr, $value) {
		$_SESSION[$attr] = $value;
	}
	
	public function hasAttribute($attr) {
		return isset($_SESSION[$attr]);
	}
	
	public function getFlash() {
		$flash = $_SESSION['flash'];
		unset($_SESSION['flash']);
		
		return $flash;
	}
	
	public function setFlash($flash) {
		$_SESSION['flash'] = $flash;
	}
	
	public function hasFlash() {
		return isset($_SESSION['flash']);
	}
	
	public function isAuthenticated() {
		return isset($_SESSION['auth']) && $_SESSION['auth'] === true;
	}
	
	public function setAuthenticated($bool = true) {
		if(!is_bool($bool)) 
			throw new \InvalidArgumentException('La valeur spécifiée � la méthode User::setAuthenticated() doit être un booléen');
		$_SESSION['auth'] = $bool;
	}
	
}