<?php
namespace FormBuilder;

use \OCFram\FormBuilder;
use \OCFram\StringField;
use \OCFram\TextField;
use \OCFram\MaxLengthValidator;
use \OCFram\NotNullValidator;

class CommentFormBuilder extends FormBuilder {
	public function build() {
		$this->form->add(new StringField([
				'label' => 'Auteur',
				'name' => 'auteur',
				'maxLength' => 50,
				'validators' => [
						new MaxLengthValidator('L\'auteur spécifié est trop long (50 caractères maximum)', 50),
						new NotNullValidator('Veuillez entrer le nom de l\'auteur du commentaire')
				]
		]))
		 ->add(new TextField([
		 		'label' => 'Contenu',
		 		'name' => 'contenu',
		 		'cols' => 50,
		 		'rows' => 7,
		 		'validators' => [
		 				new NotNullValidator('Veuillez entrer le contenu du commentaire')
		 		]
		 ]));
				  
	}
}