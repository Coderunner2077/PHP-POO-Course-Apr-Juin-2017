<?php
namespace Model;

use \Entity\Comment;

class CommentsManagerPDO extends CommentsManager {
	public function getListOf($news, $responses = true) {
		if(!\ctype_digit($news) && !\is_int($news))
			throw new \InvalidArgumentException('L\'identifiant de la news doit être un nombre entier valide : ' . $news . ', ');
		
		$q = $this->dao->query('SELECT * FROM comments WHERE news = '.$news.' ORDER BY date DESC');
		$q->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\Comment');
		if($responses === true) {
			$comments = array();
			$comms = array(array());
			while($comment = $q->fetch()) {
				$comment->setDate(new \DateTime($comment->date()));
				
				if(empty($comment->comm()))
					$comments[] = $comment;
				else {
					$comms['id-'.$comment->comm()][] = $comment;
				}
			}
			foreach($comments as $comment) {
				if($key = array_key_exists('id-' . $comment->id(), $comms)) 
					$comment->setComms($comms[$key]);
			}
			$q->closeCursor();
			
			return $comments;
		}
		$comments = $q->fetchAll();
		foreach($comments as $comment)
			$comment->setDate(new \DateTime($comment->date()));
		
		$q->closeCursor();
		return $comments;
	}
	
	public function add(Comment $comment) {
		$q = $this->dao->prepare('INSERT INTO comments SET news=:news, auteur=:auteur, contenu=:contenu, mem=:mem, comm=:comm, '
				.'date=NOW()');
		$q->bindValue(':news', $comment->news(), \PDO::PARAM_INT);
		$q->bindValue(':auteur', $comment->auteur());
		$q->bindValue(':contenu', $comment->contenu());
		$q->bindValue(':mem', (int)$comment->mem(), \PDO::PARAM_INT);
		$q->bindValue(':comm', (int)$comment->mem(), \PDO::PARAM_INT);
		$q->execute();
		
		$$comment->setId($this->dao->lastInsertId());
	}
	
	public function modify(Comment $comment) {
		$q = $this->dao->prepare('UPDATE comments SET news=:news, auteur=:auteur, contenu=:contenu, mem=:mem, comm=:comm, '
				.'date=NOW() WHERE id=:id');
		$q->bindValue(':news', $comment->news(), \PDO::PARAM_INT);
		$q->bindValue(':auteur', $comment->auteur());
		$q->bindValue(':contenu', $comment->contenu());
		$q->bindValue(':mem', (int)$comment->mem(), \PDO::PARAM_INT);
		$q->bindValue(':comm', (int)$comment->mem(), \PDO::PARAM_INT);
		$q->bindValue(':id', (int) $comment->id(), \PDO::PARAM_INT);
		$q->execute();
	}
	
	public function delete($id) {
		$this->dao->exec('DELETE FROM comments WHERE id='.(int) $id.' OR comm='.(int) $id);
	}
	
	public function deleteFromNews($news) {
		$this->dao->exec('DELETE FROM comments WHERE news='.(int)$news);
	}
	
	public function get($id, $responses = false) {
		$q = $this->dao->prepare('SELECT FROM comments WHERE id = :id');
		$q->bindValue(':id', (int) $id, \PDO::PARAM_INT);
		$q->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\Comment');
		if($comment = $q->fetch()) {
			$comment->setDate(new \DateTime($comment->date()));
			$q->closeCursor();
			if($responses) {
				$q = $this->dao->query('SELECT FROM comments WHERE comms='.$comment->id());
				$q->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\Comment');
				$comms = $q->fetchAll();
				foreach($comms as $comm) {
					$comm->setDate(new \DateTime($comm->date()));
				}
				if(!empty($comms))
					$comment->setComms($comms);
				$q->closeCursor();
			}
			
			return $comment;
		}
		return null;
	}
}