<?php
namespace Entity;

use \Concit\Entity;

class Comment extends Entity {
	protected $news,
			  $auteur,
			  $contenu,
			  $comm,
			  $mem,
			  $date,
			  $note = 0,
			  $comms = [];
	
	public function isValid() { return (trim($this->auteur) && trim($this->contenu)) == true; }
	
	public function setNews($news) { $this->news = (int) $news;	}
	
	public function setAuteur($auteur) { $this->auteur = $auteur; }
	
	public function setContenu($contenu) { $this->contenu = $contenu; }
	
	public function setDate(\DateTime $date) { $this->date = $date; }
	
	public function setNote($note) { $this->note = $note; }
	
	public function setComms(array $comms) { $this->comms = $comms; }
	
	public function setComm($comm) { $this->comm = (int) $comm; }
	
	public function setMem($mem) { $this->mem = (int) $mem; }
	
	public function news() { return $this->news; }
	
	public function auteur() { return $this->auteur; }
	
	public function contenu() { return $this->contenu; }
	
	public function date() { return $this->date; }
	
	public function note() { return $this->note; }
	
	public function comms() { return $this->comms; }
	
	public function comm() { return $this->comm; }
	
	public function mem() { return $this->mem; }
}