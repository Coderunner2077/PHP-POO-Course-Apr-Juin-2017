<?php
namespace Entity;

use \OCFram\Entity;

class News extends Entity {
	protected $auteur,
			  $titre,
			  $contenu,
			  $dateAjout,
			  $dateModif;
	
	const AUTEUR_INVALIDE = 1;
	const TITRE_INVALIDE = 2;
	const CONTENU_INVALIDE = 3;
	
	public function isValid() {
		//return !(empty($this->auteur) || empty($this->titre) || empty($this->contenu));
		return  trim($this->auteur) && trim($this->titre) && trim($this->contenu); // c'est mieux ya pense !
	}
	
	// SETTERS
	public function setAuteur($auteur) {
		if(!is_string($auteur) || !trim($auteur)) {
			$this->errors[] = self::AUTEUR_INVALIDE;
		}
		$this->auteur = $auteur;
	}

	public function setTitre($titre) {
		if(!is_string($titre) || !trim($titre)) 
			$this->errors[] = self::TITRE_INVALIDE;
		
		$this->titre = $titre;
	}
	
	public function setContenu($contenu) {
		if(!is_string($contenu) || !trim($contenu))
			$this->errors[] = self::CONTENU_INVALIDE;
		
		$this->contenu = $contenu;
	}
	
	public function setDateAjout(\DateTime $date) {
		$this->dateAjout = $date;
	}
	
	public function setDateModif(\DateTime $date) {
		$this->dateModif = $date;
	}
	
	// GETTERS	
	public function auteur() {
		return $this->auteur;
	}
	
	public function titre() {
		return $this->titre;
	}
	
	public function contenu() {
		return $this->contenu;
	}
	
	public function dateAjout() {
		return $this->dateAjout;
	}
	
	public function dateModif() {
		return $this->dateModif;
	}
}
