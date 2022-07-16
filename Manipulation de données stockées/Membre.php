<?php
class Membre {
	private $_membre_id;
	private $_pseudo;
	private $_pass;
	private $_adresse_mail;
	private $_ville;
	private $_travail;
	private $_passions;
	private $_date_naissance;
	private $_date_inscription;
	private $_avatar_url;
	private $_description;
	
	public function __construct(array $donnees) {
		$this->hydrate($donnees);
	}
	
	private function hydrate(array $donnees) {
		foreach($donnees as $key => $value) {
			$method = 'set' . ucfirst($key);
			if(method_exists($this, $method))
				$this->$method($value);
		}
	}
	// Liste des getters 
	public function getMembre_id() { return $this->_membre_id; }
	
	public function getPseudo() { return $this->_pseudo; }
	
	public function getPass() { return $this->_pass; }
	
	public function getAdresse_mail() {	return $this->_adresse_mail; }
	
	public function getVille() { return $this->_ville;	}
	
	public function getTravail() {	return $this->_travail;	}
	
	public function getPassions() {	return $this->_passions; }
	
	public function getDate_naissance() { return $this->_date_naissance; }
	
	public function getDate_inscription() {	return $this->_date_inscription; }
	
	public function getAvatar_url() { return $this->_avatar_url; }
	
	// Les setters
	public function setMembre_id($id) {
		// Si non-nombre, La conversion donnera 0 (� quelques exceptions pr�s, mais rien d'important ici)
		$id = (int) $id;
		
		// Je v�rifie si l'id est positif
		if($id > 0) {
			$this->_membre_id = $id;
		}
	}
	
	public function setPseudo($pseudo) {
		if(!is_string($pseudo)) {
			trigger_error('Il n\'y a pas de pseudo ou ce n\'est pas une chaîne de caractères', E_USER_ERROR);
			return;
		} 
		$this->_pseudo = $pseudo;
	}
	
	public function setPass($pass) {
		if(!is_string($pass)) {
			trigger_error('Mot de passe absent ou type incorrect', E_USER_ERROR);
			return;
		}
		$this->_pass = $pass;
	}
	
	public function setAdresse_mail($mail) {
		if(is_string($mail))
			$this->_adresse_mail = $mail;
	}
	
	public function setPassions($passions) {
		if(is_string($passions))
			$this->_passions = $passions;
	}
	
	public function setTravail($travail) {
		if(is_string($travail))
			$this->_travail = $travail;
	}
	
	public function setDate_naissance($date_naissance) {
		if(is_string($date_naissance))
			$this->_date_naissance = $date_naissance;
	}
	
	public function setDate_inscription($date_inscription) {
		if(is_string($date_inscription))
			$this->_date_inscription = $date_inscription;
	}
	
	public function setAvatar_url($avatar) {
		if(is_string($avatar) && preg_match('/^\d+\.(?:png|jpg|jpeg|gif)$/', $avatar))
			$this->_avatar_url = $avatar;
	}
	
	public function setVille($ville) {
		if(is_string($ville))
			$this->_ville = $ville;
	}
	
	public function toString() {
		$this->_description = 'Membre ' . $this->_pseudo . ' (N°' . $this->_membre_id . '' . 
		((trim($this->_adresse_mail)) ? (', adresse mail : ' . $this->_adresse_mail . ') ') : ') ') . '' .
		((trim($this->_date_naissance)) ? ('né le ' . $this->_date_naissance . ', ') : '') . '' .
		((trim($this->_ville)) ? ('habitant à ' . $this->_ville . ', ') : '') . '' . 
		((trim($this->_travail)) ? ('travaillant en tant que ' . $this->_travail . ', ') : '') . '' .
		((trim($this->_passions)) ? ('passionné de ' . $this->_passions . '.') : '');
		
		return $this->_description;
	}
	
	
}