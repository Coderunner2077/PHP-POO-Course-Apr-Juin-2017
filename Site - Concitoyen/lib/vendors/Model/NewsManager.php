<?php
namespace Model;

use \Concit\Manager;
use \Entity\News;

abstract class NewsManager extends Manager {
	/**
	 * Méthode retournant une liste de news demandée
	 * @param $offset int La première news à sélectionner
	 * @param $limit int Le nombre de news à sélectionner
	 * @return array La liste de news. Chaque entrée est une instance de news
	 */
	abstract public function getList($offset = -1, $limit = -1);
	
	/**
	 * Méthode retournant le nombre de news
	 * @return int Le nombre de news
	 */
	abstract public function count();
	
	/**
	 * Méthode retournant une news précise
	 * @param $id int L'identfiant de la news demandée
	 * @return News La news demandée
	 */
	abstract public function getUnique($id);
	
	/**
	 * Méthode permettant d'ajouter une news
	 * @param News $news La news à ajouter
	 * @return void
	 */
	abstract public function add(News $news);
	
	/**
	 * Méthode permettant de modifier une news
	 * @param News $news La news à modifier
	 * @return void
	 */
	
	abstract public function modify(News $news);
	
	/**
	 * Méthode permettant d'enregistrer une news
	 * @param News $news La news à enregistrer
	 * @return void
	 */
	public function save(News $news) {
		if($news->isValid())
			$news->isNew()? $this->add($news) : $this->modify($news);
		else 
			throw new \RuntimeException('La news doit être valide pour être enregistrée');
	}
	
	/**
	 * Méthode permettant de supprimer une news
	 * @param int $id La news à supprimer
	 * @return void
	 */
	abstract public function delete($id);
}