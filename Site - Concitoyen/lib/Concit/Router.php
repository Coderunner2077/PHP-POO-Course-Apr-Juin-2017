<?php
namespace Concit;

class Router {
	protected $routes = [];
	
	const NO_ROUTE = 1;
	
	public function route($name) {
		return isset($this->routes[$name]) ? $this->routes[$name] : null;
	}
	
	public function addRoute(Route $route) {
		if(!in_array($route, $this->routes)) {
			$this->routes[$route->name()] = $route;
		}
	}
	
	public function getRoute($url) {
		foreach($this->routes as $route) {
			// If there is a match for the URI
			if(($varValues = $route->match($url)) !== false) {
				// If there are variables
				if($route->hasVars()) {
					$varNames = $route->varNames();
					$listVars = [];
					
					// Creating a new array key/value
					// (key = variable's name, value = its value)
					foreach($varValues as $key => $match) {
						// The first key the entirely captured string
						if($key !== 0)
							$listVars[$varNames[$key - 1]] = $match;
					}
					$route->setVars($listVars);					
				}
				return $route;
			}
		}
		throw new \RuntimeException('Aucune route ne correspond à l\'URL', self::NO_ROUTE);
	}
	
	public function generateURL($routeName, array $params = []) {
		if(!isset($this->routes[$routeName]))
			throw new \InvalidArgumentException('Le nom de la route spécifié est introuvable');
		
		return $this->routes[$routeName]->generateURL($params);
	}
}