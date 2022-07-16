<?php
namespace FormBuilder;

use \Concit\FormBuilder;
use \Concit\StringField;
use \Concit\TextField;
use \Validator\NotNullValidator;
use \Validator\MaxLengthValidator;

class CommentFormBuilder extends FormBuilder {
	public function build() {
		$this->form->add(new StringField([
				'label' => 'Pseudo',
				'name' => 'auteur',
				'maxLength' => 20,
				'validators' => [
						new NotNullValidator('L\'auteur doit être une chaîne de caractères non nulle'),
						new MaxLengthValidator('L\'auteur ne peut comporter au maximum que 20 caractères', 20)
				]
		]))
		->add(new TextField([
				'label' => 'Contenu',
				'name' => 'contenu',
				'rows' => 7,
				'cols' => 50,
				'validators' => [
						new NotNullValidator('Le contenu doit être une chaîne de caractères non null')
				]
		]));
		$entity = $this->form->entity();
		if(!empty($entity->news()))
			$this->form->add(new StringField([
				'name' => 'news',
				'value' => $entity->news()
			], 'hidden'));
		if(!empty($entity->mem()))
			$this->form->add(new StringField([
					'name' => 'mem',
					'value' => $entity->mem()
			], 'hidden'));
		if($entity->isResponse())
			$this->form->add(new StringField([
					'name' => 'comm',
					'value' => $entity->comm()
			], 'hidden'));
	}
}