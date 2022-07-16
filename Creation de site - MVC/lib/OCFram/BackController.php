<?php
namespace OCFram;

abstract class BackController extends ApplicationComponent {
	protected $managers = null,
			  $module = '',
			  $action = '',
			  $view = '',
			  $page = null;
	
	public function __construct(Application $app, $module, $action) {
		parent::__construct($app);
		$this->page = new Page($app);
		$this->managers = new Managers('PDO', PDOFactory::getMysqlConnexion());
		$this->setModule($module);
		$this->setAction($action);
		$this->setView($action);
	}
	
	public function page() {
		return $this->page;
	}
	
	public function execute() {
		$method = 'execute'.ucfirst($this->action);
		if(!is_callable([$this, $method]))
			throw new \RuntimeException('L\'action ' . $this->action.' n\'est pas définie dans le module');
		
		$this->$method($this->app->httpRequest());
	}
	
	public function setModule($module) {
		if(!is_string($module) || !trim($module))
			throw new \RuntimeException('Le module doit être une chaîne de caractères non vide');
		$this->module = $module;
	}
	
	public function setAction($action) {
		if(!is_string($action) || !trim($action))
			throw new \RuntimeException('L\'action doit être une chaîne de caractères non vide');
		
		$this->action = $action;
	}
	
	public function setView($view) {
		if(!is_string($view) || !trim($view))
			throw new \RuntimeException('La vue doit être une chaîne de caractères valide');
		
		$this->view = $view;
		$this->page->setContentFile(__DIR__.'/../../App/'.$this->app->name().'/Modules/'.$this->module.'/Views/'.$this->view.'.php');
	}
	
}