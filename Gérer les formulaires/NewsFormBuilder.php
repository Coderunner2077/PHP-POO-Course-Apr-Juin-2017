<?php
namespace FormBuilder;

use \OCFram\FormBuilder;
use \OCFram\StringField;
use \OCFram\TextField;
use \OCFram\MaxLengthValidator;
use \OCFram\NotNullValidator;

class NewsFormBuilder extends FormBuilder {
	public function build() {
		$this->form->add(new StringField([
				'label' => 'Auteur',
				'name' => 'auteur',
				'maxLength' => 20,
				'validators' => [
						new MaxLengthValidator('L\'auteur de la news sp?cifi? est trop long (20 caract?res maximum', 20),
						new NotNullValidator('Merci de sp?cifi? l\'auteur de la news')
				]
		]))
		->add(new StringField([
				'label' => 'Titre',
				'name' => 'titre',
				'maxLength' => 100,
				'validators' => [
						new MaxLengthValidator('Le titre sp?cifi? est trop long (100 caract?res maximum)', 100),
						new NotNullValidator('Merci de sp?cifier le titre de la news')
				]
		]))
		->add(new TextField([
				'label' => 'Contenu',
				'name' => 'contenu',
				'rows' => 60,
				'cols' => 8,
				'validators' => [
						new NotNullValidator('Veuillez entrer un contenu pour la news')
				]
		]));
	}
}