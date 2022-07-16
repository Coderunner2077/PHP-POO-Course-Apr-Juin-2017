<?php 
namespace Model;

use \OCFram\Manager;
use \Entity\Comment;

abstract class CommentsManager extends Manager {
	// ...
	
	/**
	 * Méthode permettant de modifier un commentaire
	 * @param $comment Comment Le commentaire à modifier
	 * @return void
	 */
	abstract protected function modify(Comment $comment);
	
	/**
	 * Méthode permettant d'obtenir un commentaire spécifique 
	 * @param $id int L'identifiant du commentaire
	 * @return Comment
	 */
	abstract public function get($id);
	
	/**
	 * Méthode permettant de supprimer un commentaire
	 * @param $id int L'idenifiant du commentaire à supprimer
	 * @return void
	 */
	abstract public function delete($id);
	
	/**
	 * Méthode permettant de supprimer tous les commentaires liés à une news
	 * @param $news L'identifiant de la news dont les commentaires doivent être supprimés
	 * @return void
	 */
	abstract public function deleteFromNews($news);
	// ...
}