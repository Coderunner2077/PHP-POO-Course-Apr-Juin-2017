<?php
namespace Model;

use \Entity\Comment;

class CommentsManagerPDO extends CommentsManager {
	public function add(Comment $comment) {		
		$q = $this->dao->prepare('INSERT INTO comments SET news=:news, auteur = :auteur, contenu = :contenu, date = NOW()');
		$q->bindValue(':news', (int) $comment->news(), \PDO::PARAM_INT);
		$q->bindValue(':auteur', $comment->auteur());
		$q->bindValue(':contenu', $comment->contenu());
		$q->execute();
		
		$comment->setId($this->dao->lastInsertId());
	}
	
	public function getListOf($news) {
		if(!ctype_digit($news))
			throw new \InvalidArgumentException('L\'identifiant de la news passé doit être un nombre entier valide !');
		
		$q = $this->dao->prepare('SELECT * FROM comments WHERE news = :news ORDER BY date DESC');
		$q->bindValue(':news', $news, \PDO::PARAM_INT);
		$q->execute();
		$q->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\Comment');
		$comments = $q->fetchAll();
		
		foreach($comments as $comment)
			$comment->setDate(new \DateTime($comment->date()));
			
		return $comments;
	}
	
	public function modify(Comment $comment) {
		$req = $this->dao->prepare('UPDATE comments SET auteur=:auteur, contenu=:contenu, date=NOW() WHERE id=:id');
		$req->bindValue(':auteur', $comment->auteur());
		$req->bindValue(':contenu', $comment->contenu());
		$req->bindValue(':id', $comment->id(), \PDO::PARAM_INT);
		
		$req->execute();
	}
	
	public function get($id) {
		$req = $this->dao->prepare('SELECT * FROM comments WHERE id= :id');
		$req->bindValue(':id', (int) $id, \PDO::PARAM_INT);
		
		$req->execute();
		
		$req->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\Comment');
		if($comment = $req->fetch()) {
			$comment->setDate(new \DateTime($comment->date()));
			
			return $comment;
		}
		
		return null;
	}
	
	public function delete($id) {
		$this->dao->exec('DELETE FROM comments WHERE id = '.(int)$id);
	}
	
	public function deleteFromNews($newsId) {
		$this->dao->exec('DELETE FROM comments WHERE news = ' . (int)$newsId);
	}
}