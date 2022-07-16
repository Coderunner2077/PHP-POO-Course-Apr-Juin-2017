<?php
namespace App\Frontend\Modules\News;

use \OCFram\BackController;
use \Entity\Comment;

class NewsController extends BackController {
	public function executeInsertComment(HTTPRequest $request) {
		if($request->method() == 'POST') {
			$comment = new \Entity\Comment([
					'news' => $request->getData('news'),
					'auteur' => $request->postData('auteur'),
					'contenu' => $request->postData('contenu')
			]);
		}
		else 
			$comment = new Comment();
		
		$form = new Form($comment);
		
		$form->add(new StringField([
				'label' => 'Auteur',
				'name' => 'auteur',
				'maxLength' => 50
			]))
			->add(new TextField([
				'label' => 'Contenu',
				'name' => 'contenu',
				'rows' => 7, 
				'cols' => 60
			]));
		
		if($form->isValid()) {
			// ...
		}
		
		$this->page->addVar('comment', $comment);
		$this->page->addVar('form', $form->createView()); // On passe le formulaire généré à la vue
		$this->page->addVar('title', 'Ajout d\'un commentaire');
	}
}