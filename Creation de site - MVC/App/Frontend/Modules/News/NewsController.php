<?php
namespace App\Frontend\Modules\News;

use \OCFram\BackController;
use \OCFram\HTTPRequest;
use \Entity\News;
use \Entity\Comment;

class NewsController extends BackController {
	public function executeIndex(HTTPRequest $request) {
		$nombreNews = $this->app->config()->get('nombre_news');
		$nombreCaracteres = $this->app->config()->get('nombre_caracteres');
		
		// On ajoute une définition pour le titre
		$this->page->addVar('title', 'Liste des '.$nombreNews . ' dernières news');
		
		// on obtient la liste des News grâce au manager
		$listeNews = $this->managers->getManagerOf('News')->getList(0, $nombreNews);
		
		foreach($listeNews as $news) {
			if($news->contenu() > $nombreCaracteres) {
				$debut = substr($news->contenu(), 0, $nombreCaracteres);
				$debut = substr($debut, 0, strripos($debut, ' ')) . '...';
				
				$news->setContenu($debut);
			}
		}
		// On ajoute la variable $listeNews à la vue
 		$this->page->addVar('listeNews', $listeNews);
	}
	
	public function executeShow(HTTPRequest $request) {
		$news = $this->managers->getManagerOf('News')->getUnique($request->getData('id'));
		
		if(empty($news))
			$this->app->httpResponse()->redirect404();
		
		$this->page->addVar('title', $news->titre());
		$this->page->addVar('news', $news);
		$this->page->addVar('comments', $this->managers->getManagerOf('Comments')->getListOf($news->id()));
	}
	
	public function executeInsertComment(HTTPRequest $request) {
		$this->page->addVar('title', 'Ajout d\'un commentaire');
		if($request->postExists('auteur')) {
			$comment = new Comment([
					'news' => $request->getData('news'),
					'auteur' => $request->postData('auteur'),
					'contenu' => $request->postData('contenu')
			]);
			if($comment->isValid()) {
				$this->managers->getManagerOf('Comments')->save($comment);
				$this->app->user()->setFlash('Le commentaire a bien été enregistré');
				$this->app->httpResponse()->redirect('/news-'. $request->getData('news').'.html');
			}
			else 
				$this->page->addVar('errors', $comment->errors());
			
			$this->page->addVar('comment', $comment);
		} 
	}
}