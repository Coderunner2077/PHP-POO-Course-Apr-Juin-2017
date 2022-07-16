<?php
namespace App\Backend\Modules\News;

use \OCFram\BackController;
use \OCFram\HTTPRequest;
use \Entity\News;
use \Entity\Comment;

class NewsController extends BackController {
	public function executeIndex(HTTPRequest $request) {
		$this->page->addVar('title', 'Gestion des news');
		$this->page->addVar('listeNews', $this->managers->getManagerOf('News')->getList());
		$this->page->addVar('nombreNews', $this->managers->getManagerOf('News')->count());
	}
	
	protected function processForm(HTTPRequest $request) {
		$news = new News([
				'auteur' => $request->postData('auteur'),
				'titre' => $request->postData('titre'),
				'contenu' => $request->postData('contenu')
		]);	
		
		if($request->postExists('id'))
			$news->setId($request->postData('id'));
	
		if($news->isValid()) {
			$this->managers->getManagerOf('News')->save($news);
			$this->app->user()->setFlash($news->isNew() ? 'La news a bien été ajouté !' : 'La news a bien été modifiée !');
			
			$this->app->httpResponse()->redirect('/admin/');
		} 
		else 
			$this->page->addVar('errors', $news->errors());
		
		$this->page->addVar('news', $news);
	
	}
	
	public function executeInsert(HTTPRequest $request) {
		if($request->postExists('auteur'))
			$this->processForm($request);
		
		$this->page->addVar('title', 'Ajout d\'une news');
	}
	
	public function executeUpdate(HTTPRequest $request) {
		if($request->postExists('auteur'))
			$this->processForm($request);
		else 
			$this->page->addVar('news', $this->managers->getManagerOf('News')->getUnique($request->getData('id')));
		
		$this->page->addVar('title', 'Modification d\'une news');
	}
	
	public function executeDelete(HTTPRequest $request) {
		$this->managers->getManagerOf('News')->delete($request->getData('id'));
		$this->managers->getManagerOf('Comments')->deleteFromNews($request->getData('id'));
		$this->app->user()->setFlash('La news a bien été supprimé');
		$this->app->httpResponse()->redirect('admin/');
	}
	
	public function executeUpdateComment(HTTPRequest $request) {
		$this->page->addVar('title', 'Modfication d\'un commentaire');
		if($request->postExists('auteur')) {
			$comment = new Comment([
					'id' => $request->getData('id'),
					'auteur' => $request->postData('auteur'),
					'contenu' => $request->postData('contenu')
			]);
			
			if($comment->isValid()) {
				$this->managers->getManagerOf('Comments')->save($comment);
				$this->app->user()->setFlash('Le commentaire a bien été ajouté !');
				$this->app->httpResponse()->redirect('.');
			}
			else 
				$this->page->addVar('errors', $comment->errors());
			
			$this->page->addVar('comment', $comment);
		}
		else 
			$this->page->addVar('comment', $this->managers->getManagerOf('Comments')->get($request->getData('id')));
	}
	
	public function executeDeleteComment(HTTPRequest $request) {
		$this->managers->getManagerOf('Comments')->delete($request->getData('id'));
		$this->app->user()->setFlash('Le commentaire a bien été supprimé !');
		$this->app->httpResponse()->redirect('.');
	}
}