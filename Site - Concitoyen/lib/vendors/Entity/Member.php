<?php
namespace Entity;

use \Concit\Entity;

class Member extends Entity {
	protected $pseudo,
			  $prenom,
			  $nom,
			  $pass,
			  $email,
			  $dateInscription,
			  $avatar,
			  $friends = [],
			  $invitations = [];
	
	public function addFriend($id) {
		$id = (int) $id;
		if($key = array_search($id, $this->invitations)) {
			$this->friends[] = $id;
			unset($this->invitations[$key]);
		}
	}
	
	public function isValid() {
		return !(empty($this->pseudo && empty($this->email) && empty($this->pass)));
	}
	
	public function invite($id) {
		$id = (int) $id;
		if(!in_array($id, $this->invitations))
			$this->invitations[] = $id;
	}
	
	public function setInvitations(array $invitations) { $this->invitations = $invitations; }
	
	public function setPseudo($pseudo) { $this->pseudo = $pseudo; }
	
	public function setPrenom($prenom) { $this->prenom = $prenom; }
	
	public function setNom($nom) { $this->nom = $nom; }
	
	public function setPass($pass) { $this->pass = $pass; }
	
	public function setAvatar($avatar) { $this->avatar = $avatar; }
	
	public function setEmail($email) { $this->email = $email; }
	
	public function setDateInscription(\DateTime $date) { $this->dateInscription = $date; }
	
	public function setFriends(array $friends) { $this->friends = $friends; }
	
	public function pseudo() { return $this->pseudo; }
	
	public function prenom() { return $this->prenom; }
	
	public function nom() { return $this->nom; }
	
	public function pass() { return $this->pass; }
	
	public function avatar() { return $this->avatar; }
	
	public function email() { return $this->email; }
	
	public function dateInscription() { return $this->dateInscription; }
	
	public function friends() { return $this->friends(); }
	
	public function invitations() { return $this->invitations; }
}