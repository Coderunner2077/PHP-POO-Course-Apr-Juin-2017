<?php
namespace App\Backend;

use \Concit\Application;

class BackendApplication extends Application {
	public function __construct() {
		parent::__construct();
		$this->name = 'Backend';
	}
	
	public function run() {
		if(!$this->user->isAuthenticated()) 
			$controller = new Modules\Admin\AdminController($this, 'Admin', 'authenticate');
		else
			$controller = $this->getController();
		
		$controller->execute();
		$this->httpResponse->setPage($controller->page());
		$this->httpResponse->send();
	}
}