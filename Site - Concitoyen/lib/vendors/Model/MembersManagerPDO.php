<?php
namespace Model;

use \Entity\Member;

class MembersManagerPDO extends MembersManager {
	public function connect(Member $membre) {
		$q = $this->dao->prepare('SELECT FROM members WHERE pseudo=:pseudo AND pass=:pass');
		$q->execute([
				':pseudo' => $membre->pseudo(),
				':pass' => $membre->pass()
		]);
		$q->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\Member');
		if($member = $q->fetch()) {
			$member->setDateInscription(new \DateTime($membre->dateInscription()));
			$membre = $member;
			$q->closeCursor();
			return true;
		}
		$q->closeCursor();
		return false;
	}
	
	public function getUnique($id) {
		$q = $this->dao->query('SELECT FROM members WHERE id = '.(int) $id);
		$q->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\Member');
		$membre = $q->fetch();
		if($membre) 
			$membre->setDateInscription(new \DateTime($membre->dateInscription()));
		
		return $membre;
	}
	
	public function getFriends(array $friends) {
		if(empty($friends))
			throw new \RuntimeException('Le tableau des identifiants est vide');
		$sql = 'SELECT * FROM members WHERE';
		foreach($friends as $id) 
			$sql .= ' id = '.(int) $id . ' OR';
		
		$sql = substr($sql, 0, strripos(' OR'));
		$q = $this->dao->query($sql);
		$q->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\Member');
		$membres = $q->fetchAll();
		foreach($membres as $member) 
			$member->setdateInscription(new \DateTime($member->dateInscription()));
		
		return $membres;
	}
	
	public function add(Member $membre) {
		$q = $this->dao->prepare('INSERT INTO members SET pseudo=:pseudo, pass=:pass, email=:email');
		$q->execute([
				':pseudo' => $membre->pseudo(),
				':pass' => sha1($membre->pass()),
				':email' => $membre->email()
		]);
		$membre->setId($this->dao->lastInsertId());
	}
	
	public function modify(Member $membre) {
		$q = $this->dao->prepare('UPDATE members SET pseudo=:pseudo, prenom=:prenom, nom=:nom, email=:email WHERE id=:id AND pass=:pass');
		$q->execute([
				':pseudo' => $membre->pseudo(),
				':prenom' => $membre->prenom(),
				':nom' => $membre->nom(),
				':email' => $membre->email(),
				':id' => $membre->id(),
				':pass' => $membre->pass()
		]);
	}
	
	public function pseudoExists($pseudo) {
		$q = $this->dao->prepare('SELECT id FROM members WHERE pseudo = :pseudo');
		$q->execute([':pseudo' => $pseudo]);
		if($id = $q->fetchColumn()) {
			$q->closeCursor();
			return $id;
		}
		
		return false;
	}
	
	public function passwordExists($pass) {
		$q = $this->dao->prepare('SELECT id FROM members WHERE pass=:pass');
		$q->execute([':pass' => $pass]);
		if($id = $q->fetchColumn()) {
			$q->closeCursor();
			return $id;
		}
		
		return false;
	}
	
	public function changePassword(Member $membre, $newPass) {
		$q = $this->dao->prepare('UPDATE members SET pass=:newPass WHERE id=:id AND pass=:pass');
		$q->bindValue(':newPass', $newPass);
		$q->bindValue(':id', $membre->id(), \PDO::PARAM_INT);
		$q->bindValue(':pass', $membre->pass());
		$q->execute();
	}
	
	public function delete($id) {
		$this->dao->exec('DELETE FROM members WHERE id = ' . (int)$id);
	}
	
	public function inviteFriend($id1, $askingId) {
		$req = $this->dao->query('SELECT pseudo, invitations FROM members WHERE id=' + int($id1));
		$req->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\Member');
		if($membre = $req->fetch()) {
			$membre->invite($askingId);
			$this->dao->exec('UPDATE members SET invitations = ' . $membre->invitations() . ' WHERE id = ' . (int) $id1);
		}
	}
	
	public function addFriend($id, $acceptingId) {
		$req = $this->dao->query('SELECT invitations, friends FROM members WHERE id = ' + (int) $acceptingId);
		$req->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\Member');
		if($membre = $req->fetch()) {
			$membre->addFriend($id);
			$this->dao->exec('UPDATE members SET invitations = '. $membre->invitations() . ', friends = ' . $membre->friends()
					. ' WHERE id = ' . $acceptingId);
		}
	}
}