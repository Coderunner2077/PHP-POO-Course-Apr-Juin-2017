<?php
namespace FormBuilder;

use \Concit\FormBuilder;
use \Entity\Member;
use \Validator\NotNullValidator;
use \Validator\MaxLengthValidator;

class ConnexionFormBuilder extends FormBuilder {
	public function __construct(Member $membre) {
		parent::__construct($membre);
	}
	
	public function build() {
		$this->form
		->add(new StringField([
				'label' => 'Identifiant',
				'name' => 'pseudo',
				'maxLength' => 20,
				'validators' => [
						new NotNullValidator('L\'identifiant doit être une chaîne de caractères valide'),
						new MaxLengthValidator('L\'identifiant ne peut comporter plus de 20 caractères', 20)
				]
		]))
		->add(new StringField([
				'label' => 'Mot de passe',
				'name' => 'pass',
				'validators' => [
						new NotNullValidator('Veuillez entrer le mot de passe'),
				]
		], 'password'));
	}
}