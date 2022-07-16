<?php
namespace Concit;
use Yaml\Yaml as YAML;

abstract class Application {
	protected $httpRequest,
			  $httpResponse,
			  $name,
			  $matchedRoute,
			  $user,
			  $config,
			  $router;
	
	public function __construct() {
		$this->httpRequest = new HTTPRequest($this);
		$this->httpResponse = new HTTPResponse($this);
		$this->name = '';
		$this->user = new User();
		$this->config = new Config($this);
	}
	
	abstract public function run();
	
	public function httpRequest() { return $this->httpRequest; }
	
	public function httpResponse() { return $this->httpResponse; }
	
	public function name() { return $this->name; }
	
	public function user() { return $this->user; }
	
	public function config() { return $this->config; }
	
	public function router() { return $this->router; }
	
	public function getRoute() {
		if(!isset($this->router)) {
			$this->router = new Router();
			$routes = Yaml::parse(file_get_contents(__DIR__.'/../../App/'.$this->name.'/Config/routes.yml'));
			foreach($routes as $nom => $route) {
				$path = $route['path'];
				$vars = [];
				if(preg_match('#{(.+)}#U', $path, $vars))
					array_shift($vars);
				$requirements = isset($route['requirements']) ? $route['requirements'] : [];
				$this->router->addRoute(new Route($nom, $route['path'], $route['defaults'], $vars, $requirements));
			}
		}
		
		try {
			$this->matchedRoute = $this->router->getRoute($this->httpRequest->requestURI());
		} catch(\RuntimeException $e) {
			if($e->getCode() === Router::NO_ROUTE)
				$this->httpResponse->redirect404();
		}
		$_GET = array_merge($_GET, $this->matchedRoute->vars());
		
		return $this->matchedRoute;
	}
	
	public function getController(Route $matchedRoute = null) {
		if(empty($matchedRoute))
			$this->getRoute();
		else 
			$this->matchedRoute = $matchedRoute;
		
		$controller = 'App\\'.$this->name.'\\Modules\\'.$this->matchedRoute->module().'\\'.$this->matchedRoute->module().'Controller';
		return new $controller($this, $this->matchedRoute->module(), $this->matchedRoute->action());
	}
}