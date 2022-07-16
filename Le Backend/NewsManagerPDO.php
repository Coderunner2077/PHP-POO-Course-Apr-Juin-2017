<?php
namespace Model;

use \Entity\News;

class NewsManagerPDO extends NewsManager {
	//...
	
	public function count() {
		return $this->dao->query('SELECT COUNT(*) FROM news')->fetchColumn();
	}
	
	public function add(News $news) {
		$q = $this->dao->prepare('INSERT INTO news SET auteur=:auteur, titre=:titre, contenu=:contenu, dateAjout=NOW(), dateModif=NOW()');
		$q->bindValue(':auteur', $news->auteur());
		$q->bindValue(':titre', $news->titre());
		$q->bindValue(':contenu', $news->contenu());
		
		$q->execute();
	}
	
	protected function modify(News $news) {
		$req = $this->dao->prepare('UPDATE news SET auteur=:auteur, titre=:titre, contenu=:contenu, dateModif=NOW() WHERE id=:id');
		$req->bindValue(':auteur', $news->auteur());
		$req->bindValue(':titre', $news->titre());
		$req->bindValue(':contenu', $news->contenu());
		$req->bindValue(':id', $news->id(), \PDO::PARAM_INT);
		
		$req->execute();
	}
	
	public function delete($id) {
		$this->dao->exec('DELLETE FROM news WHERE id = '.(int)$id);
	}
	
	//...
}
