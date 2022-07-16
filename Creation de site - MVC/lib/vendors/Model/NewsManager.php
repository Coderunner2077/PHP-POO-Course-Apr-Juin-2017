<?php
namespace Model;

use \OCFram\Manager;
use \Entity\News;

abstract class NewsManager extends Manager {
	/**
	 * Méthode retournant une liste de news demandée
	 * @param $debut int La première news à sélectionner
	 * @param $limite int Le nombre de news à sélectionner
	 * @return array La liste de news. Chaque entrée est une instance de news.
	 */ 
	abstract public function getList($debut = -1, $limite = -1);
	
	/**
	 * Méthode retournant une news précise
	 * @param $id int Identifiant de la news demandée
	 * @return News La news demandée.
	 */
	abstract public function getUnique($id);
	
	/**
	 * Méthode retournant le nombre de news
	 * @return int
	 */
	abstract public function count();
	
	/**
	 * Méthode permettant d'enregistrer une news
	 * @param $news News La news à enregistrer 
	 * @see self::add()
	 * @see self::modify()
	 * @return void
	 */
	public function save(News $news) {
		if($news->isValid())
			$news->isNew() ? $this->add($news) : $this->modify($news);
		else 
			throw new \RuntimeException('La news doit être valide pour être enregisrée');
	}
	
	/**
	 * Méthode permettant d'ajouter une news
	 * @param $news News La news à ajouter
	 * @return void
	 */
	abstract public function add(News $news);
	
	/** 
	 * Méthode permettant de modifier une news
	 * @param $news News La news à modifier
	 * @return void
	 */
	abstract public function modify(News $news);
	
	/**
	 * Méthode permettant de supprimer une news
	 * @param $id int La news à supprimer
	 * @return void
	 */
	abstract public function delete($id);
}