<?php
namespace Model;

use \Entity\Comment;

class CommentsManagerPDO extends CommentsManager {
	// ... 
	protected function modify(Comment $comment) {
		$req = $this->dao->prepare('UPDATE comments SET auteur=:auteur, contenu=:contenu WHERE id=:id');
		$req->bindValue(':pseudo', $comment->auteur());
		$req->bindValue(':contenu', $comment->contenu());
		$req->bindValue(':id', $comment->id(), \PDO::PARAM_INT);
		
		$req->execute();
	}
	
	public function get($id) {
		$req = $this->dao->prepare('SELECT * FROM comments WHERE id=:id');
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
		$this->dao->exec('DELETE FROM comments WHERE id = ' . (int) $id);
	}
	
	public function deleteFromNews($news) {
		$this->dao->exec('DELETE FROM comments WHERE news = ' . (int) $news);
	}
	//...
}