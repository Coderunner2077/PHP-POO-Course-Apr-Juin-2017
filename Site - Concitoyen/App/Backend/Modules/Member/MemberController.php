<?php
namespace App\Backend\Modules\Member;

use \Concit\BackController;
use \Concit\HTTPRequest;
use \Entity\Member;
use \FormBuilder\ConnexionFormBuilder;
use \Concit\FormHandler;
use \Concit\FormBuilder;
use \Concit\StringField;
use \Validators\NotNullValidator;

class MemberController extends BackController {
	public function executeConnect(HTTPRequest $request) {
		$this->page->addVar('title', 'Espace-membre: Connexion');
		$user = $this->app->user();
		if($request->postExists('pseudo')) {
			$membre = new Member([
					'pseudo' => $request->postData('pseudo'),
					'pass' => sha1($request->postData('pass'))
			]);
		}
		else
			$membre = new Member;
			$formBuilder = new ConnexionFormBuilder($membre);
			$formBuilder->build();
			$form = $formBuilder->form();
			$formHandler = new FormHandler($form, $this->managers->getManagerOf('Members'), $request);
			if($formHandler->processConnection()) {
				$user->setFlash('La connexion est effectuée avec succès');
				$this->page->addVar('membre', $membre);
				$user->setMember(true);
				$user->setMemberId($membre->id());
				$user->setAttribute('pseudo', $membre->pseudo());
				$this->redirectToRoute('frontend_member_index');
			}
			else
				$user->setFlash('Le login ou le mot de passe est incorrect');
				$this->page->addVar('form', $form->createView());
	}
	
	public function executeDisconnect(HTTPRequest $request) {
		$user = $this->app->user();
		if($user->isConnected()) {
			$user->disconnectMember('Vous vous êtes bien déconnecté de votre espace perso');
			$this->redirectToRoute('frontend_home');
		}
		else
			$this->app->httpResponse()->redirect403();
	}
	
	public function executeIndex(HTTPRequest $request) {
		$this->page->addVar('title', 'Espace membre');
		$user = $this->app->user();
		$membre = $this->managers->getManagerOf('Members')->getUnique($user->memberId());
		$listeNews = $this->managers->getManagerOf('News')->getNewsOfMember($membre->id());
		$this->page->addVar('membre', $membre);
		$this->page->addVar('listeNews', $listeNews);
		$this->page->addVar('nombreNews', \count($listeNews));
	}
	
	public function executeChangePassword(HTTPRequest $request) {
		$user = $this->app->user();
		if($request->method() == 'POST') {
			$membre = new Member([
					'pseudo' => $user->getAttribute('pseudo'),
					'pass' => sha1($request->postData('pass'))
			]);
			$namesValues = array(
					'newPass' => $request->postData('newPass'),
					'pass_repeat' => $request->postData('pass_repeat')
			);
		}
		else {
			$membre = new Member();
			$namesValues = array(
					'newPass' => null,
					'pass_repeat' => null
			);
		}
		$membre->setId($user->memberId());
		$formBuilder = new ChangePasswordFormBuilder($membre, $namesValues);
		$formBuilder->build();
		$form = $formBuilder->form();
		$formHandler = new FormHandler($form, $this->managers->getManagerOf('Members'), $request);
		
		if($formHandler->processChangePassword()) {
			$user->setFlash('Le mot de passe a bien été changé');
			$this->redirectToRoute('frontend_member_index');
		}
		else
			$this->page->addVar('form', $form->createView());
	}
	
	public function executeUpdateMember(HTTPRequest $request) {
		$user = $this->app->user();
		if($request->method() == 'POST') {
			$membre = new Member([
					'pseudo' => $request->postData('pseudo'),
					'pass' => sha1($request->postData('pass')),
					'prenom' => $request->postData('prenom'),
					'nom' => $request->postData('nom'),
					'email' => $request->postData('email')
			]);
		}
		else
			$membre = new Member();
			
			$membre->setId($user->memberId());
			$formBuilder = new MemberFormBuilder($membre);
			$formBuilder->build();
			$form = $formBuilder->form();
			$formHandler = new FormHandler($form, $this->managers->getManagerOf('Members'), $request);
			
			if($formHandler->processUpdateMember($user)) {
				$user->setFlash('Le profil a bien été modifié');
				$user->setAttribute('pseudo', $member->pseudo());
				$this->redirectToRoute('frontend_member_index');
			}
			else
				$this->page->addVar('form', $form->createView());
	}
	
	public function executeDelete(HTTPRequest $request) {
		$user = $this->app->user();
		if($request->method() == 'POST') {
			$membre = new Member([
					'id' => $user->memberId(),
					'pseudo' => $user->getAttribute('pseudo'),
					'pass' => sha1($request->postData('pass'))
			]);
		}
		else
			$membre = new Member();
			
			$formBuilder = new class ($membre) extends FormBuilder {
				public function build() {
					$this->form->add(new StringField([
							'label' => 'Mot de passe',
							'name' => 'pass',
							'validators' => [
									new NotNullValidator('Veuillez vous authentifier en entrant votre mot de passe')
							]
					], 'password'));
				}
			};
			$formBuilder->build();
			$form = $formBuilder->form();
			$formHandler = new FormHandler($form, $this->managers->getManagerOf('Members'), $request);
			if($formHandler->processDeleteMember()) {
				$user->disconnectMember('Votre profil membre a bien été supprimé');
				$this->redirectToRoute('frontend_home');
			}
			else
				$this->page->addVar('form', $form->createView());
	}
}