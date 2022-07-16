<?php
namespace FormBuilder;

use \Concit\FormBuilder;
use \Concit\Form;
use \Entity\Member;
use \Validator\NotNullValidator;
use \Validator\MinLengthValidator;

class ChangeFormBuilder extends FormBuilder {
	
	public function __construct(Member $membre, array $namesValues) {
		$this->form = new Form($membre, $namesValues);
	}
	
	public function build() {
		$this->form
		->add(new StringField([
				'label' => 'Mot de passe actuel',
				'name' => 'pass',
				'validators' => [
						new NotNullValidator('Veuillez entrer le mot de passe actuel')
				]
		], 'password'))
		->add(new StringField([
				'label' => 'Nouveau mot de passe',
				'name' => 'newPass',
				'validators' => [
						new NotNullValidator('Veuillez entrer un nouveau mot de passe'),
						new MinLengthValidator('Le mot de passe doit comporter au moins 8 caractères', 8)
				]
		], 'password'))
		->add(new StringField([
				'label' => 'Nouveau mot de passe (confirmation)',
				'name' => 'pass_repeat',
				'validators' => [
						new NotNullValidator('Veuillez confirmer votre mot de passe'),
						new PasswordConfirmValidator('Les mots de passe ne sont pas identiques', $this->form->field('newPass')->value())
				]
		], 'password'));
	}
}