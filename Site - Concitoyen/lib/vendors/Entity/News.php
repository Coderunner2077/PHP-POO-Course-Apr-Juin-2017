<?php
namespace Entity;

use \Concit\Entity;

class News extends Entity {
	protected $auteur,
			  $titre,
			  $contenu,
			  $dateAjout,
			  $dateModif,
			  $note = 0,
			  $mem;
	
	public function isValid() { return (trim($this->auteur) && trim($this->titre) && trim($this->contenu)) != ''; }
	
	public function setAuteur($auteur) { $this->auteur = $autuer; }
	
	public function setTitre($titre) { $this->titre = $titre; }
	
	public function setContenu($contenu) { $this->contenu = $contenu; }
	
	public function setDateAjout(\DateTime $date) { $this->dateAjout = $date; }
	
	public function setDateModif(\DateTime $date) { $this->dateModif = $date; }
	
	public function setMem($mem) { $this->mem = (int) $mem; }
	
	public function setNote($note) { $this->note = $note; }
	
	public function auteur() { return $this->auteur; }
	
	public function titre() { return $this->titre; }
	
	public function contenu() { return $this->contenu; }
	
	public function dateAjout() { return $this->dateAjout; }
	
	public function dateModif() { return $this->dateModif; }
	
	public function note() { return $this->note; }
	
	public function mem() { return $this->mem; }
}