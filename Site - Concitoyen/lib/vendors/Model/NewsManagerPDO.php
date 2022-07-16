<?php
namespace Model;

use \Entity\News;

class NewsManagerPDO extends NewsManager {
	public function getList($offset = - 1, $limit = -1) {
		$sql = 'SELECT * FROM news ORDER BY dateModif DESC';
		if($limite > 0)
			$sql .= ' LIMIT '.(int) $limit;
		if($limite > 0 && $offset > -1)
			$sql .= ' OFFSET ' .(int) $offset;
		
		$q = $this->dao->query($sql);
		//$q->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\News');
		$q->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, 'Entity\News');
		$listeNews = $q->fetchAll();
		foreach($listeNews as $news) {
			$news->setDateAjout(new \DateTime($news->dateAjout()));
			$news->setDateModif(new \DateTime($news->dateModif()));
		}
		$q->closeCursor();
		
		return $listeNews;
	}
	
	public function count() {
		return $this->dao->query('SELECT COUNT(*) FROM news')->fetchColumn();
	}
	
	public function getUnique($id) {
		$req = $this->dao->prepare('SELECT * FROM news WHERE id = :id');
		$req->bindValue(':id', (int) $id, \PDO::PARAM_INT);
		$req->execute();
		$req->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\News');
		if($news = $req->fetch()) {
			$news->setDateAjout(new \DateTime($news->dateAjout()));
			$news->setDateModif(new \DateTime($news->dateMOdif()));
			$req->closeCursor();
			
			return $news;
		}
		$req->closeCursor();
		return null;
	}
	
	public function add(News $news) {
		$q = $this->dao->prepare('INSERT INTO news SET auteur=:auteur, titre=:titre, contenu=:contenu, mem=:mem, dateAJout=NOW(), '
				. 'dateModif=NOW()');
		$q->execute([
				':auteur' => $news->auteur(),
				':titre' => $news->titre(),
				':contenu' => $news->contenu(),
				':mem' => $news->mem()
		]);
		$news->setId($this->dao->lastInsertId());		
	}
	
	public function modify(News $news) {
		$q = $this->dao->prepare('UPDATE news SET auteur=:auteur, titre=:titre, contenu=:contenu, dateModif=NOW() WHERE id=:id');
		$q->execute([
				':auteur' => $news->auteur(),
				':titre' => $news->titre(),
				':contenu' => $news->contenu(),
				':id' => $news->id()
		]);
	}
	
	public function delete($id) {
		$this->dao->exec('DELETE FROM news WHERE id = '.(int) $id);
	}
}