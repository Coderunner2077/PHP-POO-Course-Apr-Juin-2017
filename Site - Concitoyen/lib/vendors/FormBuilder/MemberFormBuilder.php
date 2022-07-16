<?php
namespace FormBuilder;

use \Concit\FormBuilder;
use \Concit\Form;
use \Entity\Member;
use \Validator\MaxLengthValidator;
use \Validator\NotNullValidator;
use \Validator\EmailValidator;
use \Validator\MinLengthValidator;

class RegisterFormBuilder extends FormBuilder {
	
	public function __construct(Member $member, array $namesValues) {
		$this->form = new Form($member, $namesValues);
	}
	
	public function build() {
		$entity = $this->form->entity();
		$this->form
		->add(new StringField([
				'label' => 'Pseudo',
				'name' => 'pseudo',
				'maxLength' => 20,
				'validators' => [
						new MaxLengthValidator('Le pseudo ne peut comporter plus de 20 caractères', 20),
						new NotNullValidator('Veuillez entrer un pseudo')
				]
		]));
		if(!$entity->isNew()) {
			$this->form->add(new StringField([
					'label' => 'Prénom',
					'name' => 'prenom',
					'maxLength' => 20,
					'validators' => [
							new MaxLengthValidator('Le prénom ne peut comporter plus de 20 caractères', 20)
					]
			]))
			->add(new StringField([
					'label' => 'Nom',
					'name' => 'nom',
					'maxLength' => 20,
					'validators' => [
							new MaxLengthValidator('Le nom de peut comporter plus de 20 caractères', 20)
					]
			]));
		}
		$this->form
		->add(new StringField([
				'label' => 'E-mail',
				'name' => 'email',
				'maxLength' => 40,
				'validators' => [
						new NotNullValidator('Veuillez entrer votre adresse e-mail'),
						new EmailValidator('L\'adresse e-mail entrée est invalide')
				]
		], 'email'))
		->add(new StringField([
				'label' => 'Mot de passe',
				'name' => 'pass',
				'validators' => [
						new NotNullValidator('Veuillez entrer '.($entity->isNew() ? 'un' : 'votre') . ' mot de passe'),
						new MinLengthValidator('Veuillez entrer un mot de passe composé d\'au moins 8 caractères ', 8)
				]
		], 'password'));
		if($form->entity()->isNew()) {
			$this->form->add(new StringField([
					'label' => 'Confirmation de mot de passe',
					'name' => 'pass_repeat',
					'validators' => [
							new NotNullValidator('Veuillez confirmer votre mot de passe'),
							new PasswordConfirmValidator('Les mots de passe ne sont pas identiques', $this->form->field('pass')->value())
					]
			], 'password'));
		}
		
	}
}