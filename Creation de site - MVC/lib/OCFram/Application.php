<?php
namespace OCFram;

abstract class Application {
	protected $name,
			  $httpRequest,
			  $httpResponse,
			  $user,
			  $config;
	
	public function __construct() {
		$this->name = '';
		$this->httpRequest = new HTTPRequest($this);
		$this->httpResponse = new HTTPResponse($this);
		$this->user = new User();
		$this->config = new Config($this);
	}
	
	abstract public function run();
	
	public function name() {
		return $this->name;
	}
	
	public function httpRequest() {
		return $this->httpRequest;
	}
	
	public function httpResponse() {
		return $this->httpResponse;
	}
	
	public function user() {
		return $this->user;
	}
	
	public function config() {
		return $this->config;
	}
	
	public function getController() {
		$router = new Router();
		$xml = new \DOMDocument();
		$xml->load(__DIR__.'/../../App/'.$this->name.'/Config/routes.xml');
		$routes = $xml->getElementsByTagName('route');
		foreach($routes as $route) {
			$vars = [];
			
			if($route->hasAttribute('vars'))
				$vars = explode(',', $route->getAttribute('vars'));
				
				$router->addRoute(new Route($route->getAttribute('url'), $route->getAttribute('module'), $route->getAttribute('action'),
						$vars));
		}
		try {
			$matchedRoute = $router->getRoute($this->httpRequest->requestURI());
		} catch(\RuntimeException $e) {
			if($e->getCode() == Router::NO_ROUTE)
				$this->httpResponse->redirect404();
		}
		
		// On ajoute les variables de l'URL au tableau $_GET
		$_GET = array_merge($_GET, $matchedRoute->vars());
		
		$controller = 'App\\'.$this->name.'\\Modules\\'.$matchedRoute->module().'\\'.$matchedRoute->module().'Controller';
		return new $controller($this, $matchedRoute->module(), $matchedRoute->action());		
	}
}
