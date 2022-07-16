<?php
namespace App\Frontend;

use \Concit\Application;

class FrontendApplication extends Application {
	public function __construct() {
		parent::__construct();
		$this->name = 'Frontend';
	}
	
	public function run() {
		$routeNames = ['frontend_home','frontend_news_show', 'frontend_news_insertcomment', 'frontend_member_signup', 'frontend_member_connect'];
		$matchedRoute = $this->getRoute();
		if(!in_array($matchedRoute->name(), $routeNames) && !$this->user->isConnected())
			$controller = $this->getController($this->router->route('frontend_member_connect'));
		else 
			$controller = $this->getController($matchedRoute);
		
		$controller->execute();
		$this->httpResponse->setPage($controller->page());
		$this->httpResponse->send();
	}
}