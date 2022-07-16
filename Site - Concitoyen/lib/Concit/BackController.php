<?php
namespace Concit;

abstract class BackController extends ApplicationComponent {
	protected $module = '',
			  $action = '',
			  $page = null,
			  $view = '',
			  $managers = null;
	
	public function __construct(Application $app, $module, $action) {
		parent::__construct($app);
		$this->managers = new Managers('PDO', PDOFactory::getMysqlConnexion());
		$this->page = new Page($app);
		
		$this->setModule($module);
		$this->setAction($action);
		$this->setView($action);
	}
	
	public function setModule($module) {
		if(!is_string($module) || !trim($module))
			throw new \InvalidArgumentException('Le module doit être une chaîne de caractères valide');
		$this->module = $module;
	}
	
	public function setAction($action) {
		if(!is_string($action) || !trim($action)) 
			throw new \InvalidArgumentException('L\'action doit être une chaîne de caractères valide');
		$this->action = $action;
	}
	
	public function setView($view) {
		if(!is_string($view) || !trim($view)) 
			throw new \InvalidArgumentException('La vue doit être une chaîne de caratères valide');
		$this->view = $view;
		$this->page->setContentFile(__DIR__.'/../../App/'.$this->app->name().'/Modules/'.$this->module.'/Views/'.$view.'.php');
	}
	
	public function page() { return $this->page; }
	
	public function execute() {
		$method = 'execute'.ucfirst($this->action);
		if(!is_callable([$this, $method]))
			throw new \RuntimeException('L\'action '.$this->action.' n\'est pas définie sur ce module');
		
		$this->$method($this->app->httpRequest());
	}
	
	public function generateURL($name, array $params = []) {
		return $this->app->router()->generateURL($name, $params);
	}
	
	public function redirectToRoute($name, array $params = []) {
		$this->app->httpResponse()->redirect($this->app->router()->generateURL($name, $params));
	}
}