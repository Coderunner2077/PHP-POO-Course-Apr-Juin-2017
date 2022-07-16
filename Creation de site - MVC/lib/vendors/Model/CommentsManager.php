<?php
namespace Model;

use \OCFram\Manager;
use \Entity\Comment;

abstract class CommentsManager extends Manager {
	/**
	 * Méthode permettant d'ajouter un commentaire
	 * @param comment Comment Le commentaire à ajouter
	 * @return void
	 */
	abstract public function add(Comment $comment);
	
	/**
	 * Méthode permettant d'enregistrer un commentaire
	 * @param comment Comment Le commentaire à enregistrer
	 * @return void
	 */
	public function save(Comment $comment) {
		if($comment->isValid()) 
			$comment->isNew() ? $this->add($comment) : $this->modify($comment);
		else
			throw new \RuntimeException('Le commentaire doit être valide pour être enregistré !');
	}
	
	/**
	 * Méthode permettant de récupérer une liste de commentaires
	 * @param $news La news sur la quelle on veut récupérer les commentaires
	 * @return array La liste des news. 
	 */
	abstract public function getListOf($news);
	
	/**
	 * Méthode permettant de modifier un commentaire
	 * @param $comment Comment Le commentaire à modifer
	 * @return void
	 */
	abstract public function modify(Comment $comment);
	
	/**
	 * Méthode retournant un commentaire spécifique
	 * @param $id int L'identifiant du commentaire
	 * @return Comment
	 */
	abstract public function get($news_id);
	
	/**
	 * Méthode permettant de supprimer un commentaire
	 * @param $id int L'identifiant du commentaire
	 * @return Comment
	 */
	abstract public function delete($id);
	
	/**
	 * Méthode permettant de supprimer tous les commentaires associés à une news
	 * @param $newsId int L'identifiant de la news dont les commentaires sont à supprimer
	 * @return void
	 */
	
	abstract public function deleteFromNews($newsId);
}