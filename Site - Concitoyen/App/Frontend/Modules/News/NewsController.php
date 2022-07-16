<?php

namespace App\Frontend\Modules\News;

use \Concit\BackController;
use \Concit\HTTPRequest;
use \Entity\News;
use \FormBuilder\CommentFormBuilder;

class NewsController extends BackController {
	
	use \Concit\NewsControllerTrait;
	
	public function executeIndex(HTTPRequest $request) {
		$nombreNews = $this->app->config()->get('nombre_news');
		$nombreCaracteres = $this->app->config()->get('nombre_caracteres');
		if($request->getExists('page')) {
			$pageCourante = $request->getData('page');
			$offset = ($pageCourante - 1) * $nombreNews;
		}
		else {
			$offset = 0;
			$pageCourante = 1;
		}
		$manager = $this->managers->getManagerOf('News');
		$listeNews = $manager->getList($offset, $nombreNews);
		foreach($listeNews as $news) {
			if(\strlen($news->contenu()) > $nombreCaracteres) {
				$contenu = \substr($news->contenu(), 0, $nombreCaracters);
				$contenu = \substr($contenu, 0, \strripos(' ')) . '...';
			}
			$news->setContenu($contenu);
		}
		if(($total = $manager->count()) > $nombreNews) {
			$pages = [];
			$lastPage = \ceil($total / $nombreNews);
			for($i = 1; $i <= $lastPage; $i++)
				$pages[] = $i;
			$this->page->addVar('pages', $pages);
		}
		$this->page->addVar('nombreNews', $nombreNews);
		$this->page->addVar('pageCourante', $pageCourante);
		$this->page->addVar('title', 'Liste des '.$nombreNews.(($offset == 0) ? ' dernières news' : (' news de '
				.$listeNews[0]->dateModif()->format('d/m/Y'). ' à ' 
				.$listeNews[$nombreNews - 1]->dateModif()->format('d/m/Y'))));
		$this->page->addVar('listeNews', $listeNews);		
	}
	
	public function executeShow(HTTPRequest $request) {
		$news = $this->managers->getManagerOf('News')->getUnique($request->getData('id'));
		$comments = $this->managers->getManagerOf('Comments')->getListOf($request->getData('id'));
		if(empty($news))
			$this->app->httpResponse()->redirect404();
		$this->page->addVar('title', $news->titre());
		$this->page->addVar('news', $news);
		$this->page->addVar('comments', $comments);
		
	}
	
	public function executeInsertComment(HTTPRequest $request) {
		$this->page->addVar('title', 'Ajout d\'un commentaire');
		if($request->postExists('contenu')) {
			$comment = new Comment([
					'news' => $request->getData('news'),
					'auteur' => ($this->app->user()->isConnected() ? $this->app->user()->getAttribute('pseudo') : 
								$this->app->user()->isAuthenticated() ? $request->postData('auteur') : 'Anonymous'),
					'contenu' => $request->postData('contenu'),
			]);
			if($request->getExists('comm'))
				$comment->setComm($request->getData('comm'));
		}
		else 
			$comment = new Comment();
		if($this->app->user()->isConnected())
			$comment->setMem($this->app->user()->memberId());
		$formBuilder = new CommentFormBuilder($comment);
		$formbuilder->build();
		$form = $formBuilder->form();
		$formHandler = new FormHandler($form, $this->managers->getManagerOf('Comments'), $request);
		if($formHandler->process()) {
			$this->app->user()->setFLash('Le commentaire a bien été ajouté');
			$this->redirectToRoute('frontend_news_show', ['id' => $request->getData('news')]);
		}
		$this->page->addVar('form', $form->createView());
	}	
}