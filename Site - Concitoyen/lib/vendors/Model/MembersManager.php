<?php
namespace Model;

use \Concit\Manager;
use \Entity\Member;

abstract class MembersManager extends Manager {
	/**
	 * Méthode permettant au membre de se connecter
	 * @param Member $membre Le membre qui souhaite se connecter
	 * @return boolean Retourne vrai si connecté, faux sinon
	 */
	abstract public function connect(Member $membre);
	
	/**
	 * Méthode permettant de retourner un membre spécifique
	 * @param int $id L'identifiant du membre à retourner
	 * @return Member 
	 */
	abstract public function getUnique($id);
	
	/**
	 * Méthode permettant de retourner la liste des amis d'un membre
	 * @param array $friends Les identifiants des amis
	 * @return array Un tableau de membres
	 */
	abstract public function getFriends(array $friends);
	
	/**
	 * Méthode permettant d'ajouter un nouveau membre
	 * @param Member $membre Le membre à ajouter
	 * @return void
	 */
	abstract public function add(Member $membre);
	
	/**
	 * Méthode permettant de modifier un membre
	 * @param Member $membre Le membre à modifier
	 * @return void
	 */
	abstract public function modify(Member $membre);
	
	/**
	 * Méthode permettant d'enregistrer un membre
	 * @param Member $membre Le membre à enregistrer
	 * @return void
	 */
	public function save(Member $membre) {
		if($membre->isValid())
			$membre->isNew() ? $this->add($membre) : $this->modify($membre);
		else 
			throw new \RuntimeException('Le membre doit être valide pour être enregistré');
	}
	
	/**
	 * Méthode permettant de vérifier la validité d'un pseudo
	 * @param string $pseudo Le pseudo à tester
	 * @return boolean True si valide, false sinon
	 */
	abstract public function pseudoExists($pseudo);
	
	/**
	 * Méthode permettant de supprimer un membre
	 * @param int $id L'identifiant du membre à supprimer
	 * @return void
	 */
	abstract public function delete($id);
	
	/**
	 * Méthode permettant d'envoyer une proposition d'amitié
	 * @param int $id L'identifiant du membre auxquel on envoie la requête
	 * @param int $askingId L'identifiant du membre qui envoie la requête
	 * @return void
	 */
	abstract public function inviteFriend($id, $askingId);
	
	/**
	 * Méthode permettant d'ajouter un ami
	 * @param int $id L'identifiant du membre à ajouter
	 * @param int $acceptingId L'identifiant du membre qui accepte l'amitié
	 * @return void
	 */
	abstract public function addFriend($id, $acceptingId);
	
	/**
	 * Méthode permettant de vérifier si un pseudo existe déjà
	 * @param string $pseudo Le pseudo vérifié
	 * @return mixed L'identifiant du membre dont le pseudo correspond, false sinon
	 */
	abstract public function pseudoExists($pseudo);
}