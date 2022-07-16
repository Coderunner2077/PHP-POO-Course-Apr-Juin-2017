<?php
namespace App\Backend\Modules\Connexion;

use \OCFram\BackController;
use \OCFram\HTTPRequest;

class ConnexionController extends BackController {	
	public function executeIndex(HTTPRequest $request) {
		$this->page->addVar('title', 'Connexion');
		if($request->postExists('login')) {
			if($request->postData('login') == $this->app->config()->get('login') && $request->postData('pass')
					== $this->app->config()->get('pass'))
			{
				$this->app->user()->setAuthenticated(true);
				$this->app->httpResponse()->redirect('.');
			}
			else
				$this->app->user()->setFlash('Le login ou le mot de passe est incorrect !');
		}
	}
	
	public function executeDisconnect(HTTPRequest $request) {
		$this->app->user()->setAuthenticated(false);
		$this->app->user()->setFlash('Déconnexion effectuée avec succès');
		$this->app->httpResponse()->redirect('.');
		
	}
}