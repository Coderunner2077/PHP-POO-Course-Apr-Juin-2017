<?php
namespace Concit;

session_start();

class User {
	public function getAttribute($var) {
		return isset($_SESSION[$var]) ? $_SESSION[$var] : null;
	}
	
	public function setAttribute($var, $value) {
		$_SESSION[$var] = $value;
	}
	
	public function hasAttribute($var) {
		return isset($_SESSION[$var]);
	}
	
	public function setFlash($value) {
		$_SESSION['flash'] = $value;
	}
	
	public function getFlash() {
		$flash = $_SESSION['flash'];
		unset($_SESSION['flash']);
		return $flash;
	}
	
	public function hasFlash() {
		return isset($_SESSION['flash']);
	}
	
	public function isAuthenticated() {
		return isset($_SESSION['auth']) && $_SESSION['auth'] === true; 
	}
	
	public function setAutheticated($authenticated = true) {
		if(!is_bool($authenticated))
			throw new \InvalidArgumentException('La valeur spécifiée à la méthode User::setAuthenticated() doit être un booléen');
		$_SESSION['auth'] = $authenticated;
	}
	
	public function isMember($id) {
		return isset($_SESSION['member']) && $_SESSION['member'] === true && $_SESSION['memberId'] === $id;
	}
	
	public function isConnected() {
		return isset($_SESSION['member']) && $_SESSION['member'] === true;
	}
	
	public function setMember($mem = true) {
		if(!is_bool($mem))
			throw new \InvalidArgumentException('La valeur spécifiée à User::setMember() doit être un booléen');
		$_SESSION['member'] = $mem;
	}
	
	public function setMemberId($id) {
		$_SESSION['memberId'] = $id;
	}
	
	public function memberId() { return $_SESSION['memberId']; }	
	
	public function disconnectMember($flash) {
		$this->setMember(false);
		$this->setMemberId(null);
		$this->setAttribute('pseudo', null);
		$this->setFlash($flash);
	}
}