<?php
namespace Model;

use \Entity\News;

class NewsManagerPDO extends NewsManager {
	public function getList($debut = -1, $limite = -1) {
		$sql = 'SELECT * FROM news ORDER BY id DESC';
		if($limite != -1 )
			$sql .= ' LIMIT '. (int) $limite ;
		if($debut != - 1)
			$sql .= ' OFFSET '. (int) $debut;
		
		$q = $this->dao->query($sql);
		$q->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\News');
		$listeNews = $q->fetchAll();
		
		foreach($listeNews as $news) {
			$news->setDateAjout(new \DateTime($news->dateAjout()));
			$news->setDateModif(new \DateTime($news->dateModif()));
		}
		$q->closeCursor();
		
		return $listeNews;
	}
	
	public function getUnique($id) {
		$requete = $this->dao->prepare('SELECT * FROM news WHERE id=:id');
		$requete->bindValue(':id', (int) $id, \PDO::PARAM_INT);
		$requete->execute();
		$requete->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\News');
		
		if($news = $requete->fetch()) {
			$news->setDateAjout(new \DateTime($news->dateAjout()));
			$news->setDateModif(new \DateTime($news->dateModif()));
			
			$requete->closeCursor();
			return $news;
		}
		$requete->closeCursor();
		
		return null;		
	}
	
	public function count() {
		return $this->dao->query('SELECT COUNT(*) FROM news')->fetchColumn();
	}
	
	public function add(News $news) {
		$req = $this->dao->prepare('INSERT INTO news SET auteur=:auteur, titre = :titre, contenu = :contenu, dateAjout = NOW(), '
				. 'dateModif=NOW()');
		$req->bindValue(':auteur', $news['auteur']);
		$req->bindValue(':titre', $news['titre']);
		$req->bindValue(':contenu', $news['contenu']);
		
		$req->execute();
	}
	
	public function modify(News $news) {
		$req = $this->dao->prepare('UPDATE news SET auteur=:auteur, titre=:titre, contenu=:contenu, dateModif=NOW() WHERE id=:id');
		$req->bindValue(':auteur', $news['auteur']);
		$req->bindValue(':titre', $news['titre']);
		$req->bindValue(':contenu', $news['contenu']);
		$req->bindValue(':id', (int) $news['id'], \PDO::PARAM_INT);
		
		$req->execute();
	}
	
	public function delete($id) {
		$this->dao->exec('DELETE FROM news WHERE id = '. (int) $id);
	}
}