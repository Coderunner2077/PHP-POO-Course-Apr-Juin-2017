<?php
namespace Model;

use \Concit\Manager;
use \Entity\Comment;

abstract class CommentsManager extends Manager {
	
	/**
	 * Méthode renvoyant la liste des commentaires associés à une news spécifique
	 * @param $news int L'identifiant de la News
	 * @return $comments array La liste des commentaires
	 */
	abstract public function getListOf($news);
	
	/**
	 * Méthode ajoutant un commentaire
	 * @param Comment $comment Le commentaire à ajouter
	 * @return void
	 */
	abstract public function add(Comment $comment);
	
	/**
	 * Méthode permettant de modifier un commentaire
	 * @param Comment $comment Le commentaire à modifier
	 * @return void
	 */
	abstract public function modify(Comment $comment);
	
	/**
	 * Méthode permettant d'enregistrer un commentaire
	 * @param Comment $comment Le commentaire à enregistrer
	 * @return void
	 */
	public function save(Comment $comment) {
		if($comment->isValid())
			$comment->isNew() ? $this->add($comment) : $this->modify($comment);
		else
			throw new \RuntimeException('Le commentaire doit être valide pour être enregistré');
	}
	
	/**
	 * Méthode permettant de supprimer un commentaire spécifique, ainsi que toutes les "réponses" à ce commentaire
	 * @param int $id L'identifiant du commentaire à supprimer
	 * @return void
	 */
	abstract public function delete($id);
	
	/**
	 * Méthode permettant de supprimer tous les commentaires associés à une news
	 * @param int $newsId L'idetifiant de la news associée
	 * @return void
	 */
	abstract public function deleteFromNews($newsId);
	
	/**
	 * Méthode retournant un commentaire spécique, et éventuellement tous ses "réponses"
	 * @param int $id L'idenifiant du commantaire demandé
	 * @param boolean $responses A vrai, le commentaire contiendra aussi ses réponses
	 * 
	 * @return Comment Le commentaire
	 */
	abstract public function get($id, $responses = false);
}