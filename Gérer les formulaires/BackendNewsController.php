<?php
namespace App\Backend\Modules\News;

use \OCFram\BackController;
use \OCFram\HTTPRequest;
use \OCFram\FormHandler;
use \FormBuilder\CommentFormBuilder;
use \FormBuilder\NewsFormBuilder;
use \Entity\Comment;
use \Entity\News;

class BackendNewsController extends BackController {
	public function executeUpdateComment(HTTPRequest $request) {
		if($request->method() == 'POST') {
			$comment = new Comment([
					'id' => $request->getData('id'),
					'auteur' => $request->postData('auteur'),
					'contenu' => $request->postData('contenu')
			]);
		}
		else 
			$comment = $this->managers->getManagerOf('Comments')->get($request->getData('id'));
		
		$formBuilder = new CommentFormBuilder($comment);
		$formBuilder->build();
		$form = $formBuilder->form();
		if($request->method() == 'POST' && $form->isValid()) {
			$this->managers-getManagerOf('Comments')->save($comment);
			$this->app->user()->setFlash('Le commentaire a bien été modifié');
			$this->app->httpResponse()->redirect('/admin/');
		}
		
		$this->page->addVar('comment', $comment);
		$this->page->addVar('form', $form->createView());
		$this->page->addVar('title', 'Modification d\'un commentaire');
	}
	
	protected function processForm(HTTPRequest $request) {
		if($request->method() == 'POST') {
			$news = new News([
					'auteur' => $request->postData('auteur'),
					'titre' => $request->postData('titre'),
					'contenu' => $request->postData('contenu')
			]);
			if($request->getExists('id'))
				$news->setId($request->getData('id'));
		}
		else {
			if($request->getExists('id')) {
				$news = $this->managers->getManagerOf('News')->getUnique($this->request->getData('id'));
			}
				
			else 
				$news = new News();
		}
		
		$formBuilder = new NewsFormBuilder($news);
		$formBuilder->build();
		$form = $formBuilder->form();
		
		if($request->method() == 'POST' && $form->isValid()) {
			$this->managers->getManagerOf('News')->save($news);
			$this->app->user()->setFlash($news->isNew() ? 'La news a bien été ajouté !' : 'La news a bien été modifiée !');
			$this->app->httpResponse()->redirect('/admin/');
		}
		
		/// $this->page->addVar('news', $news); apparement, je n'ai plus besoin de le transmettre...
		$this->page->addVar('form', $form->createView());
	}
	
	public function executeInsert(HTTPRequest $request) {
		$this->processForm($request);
		$this->page->addVar('Ajout d\'une news');
	}
	
	public function executeUpdate(HTTPRequest $request) {
		$this->processForm($request);
		$this->page->addVar('title', 'Modification d\'une news');
	}
}