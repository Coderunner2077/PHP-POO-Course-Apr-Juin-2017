<?php
namespace Model;

use \OCFram\Manager;
use \Entity\News;

abstract class NewsManager extends Manager {
	//...
	
	/**
	 * Méthode renvoyant le nombre de news total
	 * @return int
	 */
	abstract public function count();
	
	/**
	 * Méthode permettant d'ajouter une news.
	 * @param $news News La news à ajouter
	 * @return void
	 */
	abstract public function add(News $news);
	
	/**
	 * Méthode permettant d'enregistrer une news.
	 * @param $news News La news à enregistrer
	 * @see self::add()
	 * @see self::modify()
	 * @return void
	 */
	public function save(News $news) {
		if($news->isValid())
			$news->isNew() ? $this->add($news) : $this->modify($news);
		else 
			throw new \RuntimeException('La news doit être valide pour être enregistrée.');
	}
	
	/**
	 * Méthode permettant de modifier une news
	 * @param $news News La news à modifier
	 * @return void
	 */
	abstract protected function modify(News $news);
	
	/**
	 * Méthode permettant de supprimer une news
	 * @param $id int L'identifiant de la news à supprimer
	 * @return void
	 */
	abstract public function delete($id);
	
	// ...
}