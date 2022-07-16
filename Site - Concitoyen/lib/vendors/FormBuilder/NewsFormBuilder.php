<?php
namespace FormBuilder;

use \Concit\FormBuilder;
use \Validator\NotNullValidator;
use \Validator\MaxLengthValidator;

class NewsFormBuilder extends FormBuilder {
	public function build() {
		$this->form->add(new StringField([
				'label' => 'Auteur',
				'name' => 'auteur',
				'maxLength' => 20,
				'validators' => [
						new NotNullValidator('L\'auteur doit être une chaîne de caractères non vide'),
						new MaxLengthValidator('L\'auteur ne peut comporter plus de 20 caractères', 100)
				]
		]))
		->add(new StringField([
				'label' => 'Titre',
				'name' => 'titre',
				'maxLength' => 100,
				'validators' => [
						new  NotNullValidator('Le titre doit être une chaîne de caractères non vide'),
						new MaxLengthValidator('Le titre ne peut comporter plus de 100 caractères', 100)
				]
		]))
		->add(new TextField([
				'label' => 'Contenu',
				'name' => 'contenu',
				'cols' => 60,
				'rows' => 8,
				'validators' => [
						new NotNullValidator('Le contenu doit être une chaîne de caractères non vide')
				]
		]));
		$entity = $this->form->entity();
		if(!$entity->isNew())
			$this->form->add(new StringField([
					'name' => 'id',
					'value' => $entity->id()
			], 'hidden'));
		if(!empty($entity->mem()))
			$this->form->add(new StringField([
					'name' => 'mem',
					'value' => $entity->mem()
			], 'hidden'));
	}
}