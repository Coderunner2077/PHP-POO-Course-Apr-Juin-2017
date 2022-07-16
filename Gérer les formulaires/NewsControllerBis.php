<?php
namespace App\Frontend\Modules\News;

use \OCFram\BackController;
use \Entity\Comment;
use \FormBuilder\CommentsFormBuilder;
use \OCFram\HTTPRequest;
use \OCFram\FormHandler;

class NewsControllerBis extends BackController {
	public function executeInsertComment(HTTPRequest $request) {
		// Si le form a été envoyé
		if($request->method() == 'POST') {
			$comment = new Comment([
					'news' => $request->getData('news'),
					'auteur' => $request->postData('auteur'),
					'contenu' => $request->postData('contenu')
			]);
		}
		else 
			$comment = new Comment();
		$formBuilder = new CommentFormBuilder($comment);
		$formBuilder->build();
		$form = $formBuilder->form();
		if($request->method() == 'POST' && $form->isValid()) {
			$this->managers->getManagerOf('Comments')->save($comment);
			$this->app->user()->setFlash('Le commentaire a bien été ajouté');
			$this->app->httpResponse()->redirect('news-'.$request->getData('news').'.html');
		}
		
		$this->page->addVar('form', form()->createView());
		$this->page->addVar('comment', $comment);
		$this->page->addVar('title', 'Ajout d\'un commentaire');
	}
}