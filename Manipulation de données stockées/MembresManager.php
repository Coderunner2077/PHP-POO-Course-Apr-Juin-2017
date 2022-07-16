<?php
//require 'Membre.php';
class MembresManager {
	private $_db;
	
	public function __construct($db) {
		$this->setDb($db);
	}
	
	public function setDb(PDO $db) {
		if($db instanceof PDO) {
			$this->_db = $db;
		}
	}
	
	public function add(Membre $membre) {
		$req = $this->_db->prepare('SELECT pseudo FROM membres WHERE pseudo = ?');
		$req->execute(array($membre->getPseudo()));
		if($exists = $req->fetch()) {
			trigger_error('Un tel pseudo existe déjà', E_USER_ERROR);
			return;
		}
		$req->closeCursor();
		$req = $this->_db->prepare('INSERT INTO membres(pseudo, pass, adresse_mail, avatar_url, ville, date_naissance, travail, '
				.'passions, date_inscription) VALUES(:pseudo, :pass, :adresse_mail, :avatar_url, :ville, :date_naissance, :travail, '
				.':passions, CURDATE())');
		$req->bindValue(':pseudo', $membre->getPseudo(), PDO::PARAM_STR);
		$req->bindvalue(':pass', $membre->getPass(), PDO::PARAM_STR);
		$req->bindValue(':adresse_mail', $membre->getAdresse_mail(), PDO::PARAM_STR);
		$req->bindValue('avatar_url', $membre->getAvatar_url(), PDO::PARAM_STR);
		$req->bindValue('ville', $membre->getVille(), PDO::PARAM_STR);
		$req->bindValue('date_naissance', $membre->getDate_naissance(), PDO::PARAM_STR);
		$req->bindValue('travail', $membre->getTravail(), PDO::PARAM_STR);
		$req->bindValue('passions', $membre->getPassions(), PDO::PARAM_STR);
		//$req->bindValue('date_inscription', $membre->getDate_inscription(), PDO::PARAM_STR);
		$req->execute();
	}
	
	public function delete(Membre $membre) {
		$req = $this->_db->prepare('DELETE FROM membres WHERE id = ?');
		$req->bindValue('membre_id', $membre->getMembre_id(), PDO::PARAM_INT);
		$req->execute();
	}
	
	public function update(Membre $membre) {
		$req = $this->_db->prepare('SELECT pseudo FROM membres WHERE pseudo = ? AND membre_id != ?');
		$req->execute(array($membre->getPseudo(), $membre->getMembre_id()));
		if($exists = $req->fetch()) {
			trigger_error('Un tel pseudo existe déjà', E_USER_ERROR);
			return;
		}
		$req->closeCursor();
		$req = $this->_db->prepare('UPDATE membres SET pseudo = :pseudo, pass = :pass, adresse_mail=:adresse_mail, avatar_url = '
				.':avatar_url, ville = :ville, date_naissance = :date_naissance, travail = :travail, passions = :passions '
				. 'WHERE membre_id = :membre_id');
		$req->bindValue('pseudo', $membre->getPseudo(), PDO::PARAM_STR);
		$req->bindvalue('pass', $membre->getPass(), PDO::PARAM_STR);
		$req->bindValue('adresse_mail', $membre->getAdresse_mail(), PDO::PARAM_STR);
		$req->bindValue('avatar_url', $membre->getAvatar_url(), PDO::PARAM_STR);
		$req->bindValue('ville', $membre->getVille(), PDO::PARAM_STR);
		$req->bindValue('date_naissance', $membre->getDate_naissance(), PDO::PARAM_STR);
		$req->bindValue('travail', $membre->getTravail(), PDO::PARAM_STR);
		$req->bindValue('passions', $membre->getPassions(), PDO::PARAM_STR);
		$req->bindValue('membre_id', $membre->getMembre_id(), PDO::PARAM_INT);
		$req->execute();
	}
	
	public function get($id) {
		$id = (int) $id;
		$q = $this->_db->query('SELECT * FROM membres WHERE membre_id = ' . $id);

		
		return new Membre($q->fetch(PDO::FETCH_ASSOC));
	}
	
	public function getList() {
		$membres = [];
		$q = $this->_db->query('SELECT * FROM membres ORDER BY pseudo');
		while($donnees = $q->fetch(PDO::FETCH_ASSOC)) {
			$membres[] = new Membre($donnees);
		}
		
		return $membres;
	}
}