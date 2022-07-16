<?php
namespace Concit; 

use \Concit\HTTPRequest;
use \Concit\FormHandler;
use \Entity\Comment;
use \Entity\News;
use \FormBuilder\CommentFormBuilder;
use \FormBuilder\NewsFormBuilder;

trait NewsControllerTrait {
	protected function checkUser(HTTPRequest $request, $id = null) {
		$user = $this->app->user();
		if(empty($id))
			$id = $user->memberId();
			if(($request->getExists('mem') && $request->getData('mem') != $user->memberId())
					|| (!$request->getExists('mem') && !$user->isAuthenticated() && $id != $user->memberId()))
				$this->app->httpResponse()->redirect403();
				else
					return $user;
	}
	
	public function executeUpdateComment(HTTPRequest $request) {
		$this->page->addVar('title', 'Modification d\'un commentaire');
		$user = $this->app->user();
		if($request->postExists('contenu')) {
			$comment = new Comment([
					'news' => $request->postData('news'),
					'auteur' => $user->isAuthenticated() ? $request->postData('auteur') : $user->getAttribute('pseudo'),
					'contenu' => $request->postData('contenu')
			]);
			if($request->postExists('comm'))
				$comment->setComm($request->postData('comm'));
				if($request->postExists('mem'))
					$comment->setMem($request->postData('mem'));
		}
		else
			$comment = $this->managers->getManagerOf('Comments')->get($request->getData('id'));
			$this->checkUser($request, $comment->mem());
			
			$manager =  $this->managers->getManagerOf('Comments');
			$formBuilder = new CommentFormBuilder($comment);
			$formBuilder->build();
			$form = $formBuilder->form();
			$formHandler = new FormHandler($form, $manager, $request);
			if($formHandler->process()) {
				$manager->save($comment);
				$user->setFlash('Le commentaire a bien été modifié');
				$this->redirectToRoute(lcfirst($this->app->name()).'_news_show', ['id' => $comment->news()]);
			}
			$this->page->addVar('form', $form->createView());
	}
	
	public function executeDeleteComment(HTTPRequest $request) {
		$manager = $this->managers->getManagerOf('Comments');
		if($comment = $manager->get($request->getData('id'))) {
			$user = $this->checkUser($request, $comment->mem());
			$manager->delete($comment->id());
			$user->setFlash('Le commentaire a bien été supprimé');
			$this->redirectToRoute(lcfirst($this->app->name()).'_news_show', ['id' => $comment->news()]);
		}
		else
			$this->app->httpResponse()->redirect404();
	}
	
	public function executeDelete(HTTPRequest $request) {
		$manager = $this->managers->getManagerOf('News');
		$commentCommanager = $this->managers->getManagerOf('Comments');
		if($news = $manager->get($request->getData('id'))) {
			$user = $this->checkUser($request, $news->mem());
			$manager->delete($news->id());
			$commentManager->deleteFomNews($news->id());
			$user->setFlash('La news a bien été supprimée');
			$this->app->httpResponse()->redirect('.');
		}
		else
			$this->app->httpResponse()->redirect404();
	}
	public function executeInsert(HTTPRequest $request) {
		$this->processForm($request);
		$this->page->addVar('title', 'Ajout d\'une news');
	}
	
	public function executeUpdate(HTTPRequest $request) {
		$this->processForm($request);
		$this->page->addVar('title', 'Modification d\'une news');
	}
	
	protected function processForm(HTTPRequest $request) {
		$user = $this->app->user();
		$manager = $this->managers->getManagerOf('News');
		if($request->$postExists('contenu')) {
			$news = new News([
					'auteur' => $user->isAuthenticated() ? $request->postData('auteur') : $user->getAttribute('pseudo'),
					'titre' => $request->postData('titre'),
					'contenu' => $request->postData('contenu')
			]);
			if($request->getExists('id'))
				$news->setId($request->getData('id'));
				if($request->postExists('mem'))
					$news->setMem($request->postData('mem'));
		}
		else {
			if($request->getExists('id'))
				$news = $manager->getUnique($request->getData('id'));
				else
					$news = new News();
		}
		$this->checkUser($request, $news->mem());
		$formBuilder = new NewsFormBuilder($news);
		$formBuilder->build();
		$form = $formBuilder->form();
		$formHandler = new FormHandler($form, $manager, $request);
		$newsId = $news->id();
		if($formHandler->process()) {
			$user->setFlash(empty($newsId) ? 'L\'article a bien été ajouté' : 'L\'article a bien été modifié');
			$this->redirectToRoute(lcfirst($this->app->name()).'_news_show', ['id' => $news->id()]);
		}
		else
			$this->page->addVar('form', $form->createView());
	}
}