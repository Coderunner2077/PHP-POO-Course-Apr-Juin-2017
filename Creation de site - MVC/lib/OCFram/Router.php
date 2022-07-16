<?php
namespace OCFram;

class Router {
	protected $routes = array();
	
	const NO_ROUTE = 1;
	
	public function addRoute(Route $route) {
		if(!in_array($route, $this->routes))
			$this->routes[] = $route;
	}
	
	public function getRoute($url) {
		foreach($this->routes as $route) {
			if($varValues = $route->match($url)) {
				if($route->hasVars()) {
					$varNames = $route->varNames();
					$vars = [];
					foreach($varValues as $key => $value) {
						if($key)
							$vars[$varNames[$key - 1]] = $value;
					}
					$route->setVars($vars);
				}
				return $route;
			}
		}
		throw new \RuntimeException('Aucune route ne correspond à l\'URL demandée', self::NO_ROUTE);
	}
}