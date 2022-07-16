<?php
namespace App\Backend\Modules\Admin;

use \Concit\BackController;
use \Concit\HTTPRequest;

class AdminController extends BackController {
	public function executeAuthenticate(HTTPRequest $request) {
		$this->page->addVar('title', 'Authentification');
		$user = $this->app->user();
		if($request->postExists('login')) {
			if($request->postData('login') == $this->app->config()->get('login') 
					&& sha1($request->postData('pass')) == $this->app->config()->get('pass')) {
						$user->setAuthenticated(true);
						$user->setFlash('Authentification réussie !');
						$this->app->httpResponse()->redirect('/admin/');
					}
			else 
				$user->setFlash('Le login ou le mot de passe est incorrect');
		}
	}
	
	public function executeDeauthenticate(HTTPRequest $request) {
		$user = $this->app->user();
		if($user->isAuthenticated()) {
			$user->setAuthenticated(false);
			$this->app->user()->setFlash('Déconnexion effectuée avec succès');
			$this->app->httpResponse()->redirect('.');
		}
		else 
			$this->app->httpResponse()->redirect403();
	}
}