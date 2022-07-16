<?php
namespace Concit;

class Route {
	protected $name,
			  $path,
			  $module,
			  $action,
			  $varNames,
			  $vars = [],
			  $requirements = [],
			  $defaults = [],
			  $appName;
	
	public function __construct($name, $path, array $defaults = array(), array $varNames = array(), array $requirements = array()) {
		$this->setName($name);
		$this->setDefaults($defaults);
		$this->setVarNames($varNames);
		$this->setRequirements($requirements);		
		$this->setPath($path);
	}
	
	public function hasVars() {
		return !empty($this->varNames);
	}
	
	public function generateURL(array $params = []) {
		$url = $this->path;
		foreach($params as $key => $param) {
			$url = preg_replace('#{' .$key.'}#U', $param, $url);
		}
		
		if(empty($params) && count($this->defaults) > 1) {
			foreach($this->defaults as $key => $value) {
				if($key == '_controller')
					continue;
				$url = preg_replace('#{'.$key.'}#U', $value, $url);
			}
		}
		
		return $url;
	}
	
	public function match($url) {
		if(count($this->defaults) > 1) {
			$defPath = preg_replace('#{.+}#U', '', $this->path);
			if(preg_match('#^'.$defPath.'$#', $url)) {
				$defaults = [];
				foreach($this->defaults as $key => $value) {
					if($key == '_controller')
						continue;
						$defaults[] = $value;
				}
				return $defaults;
			}
		}
		
		$path = $this->path;
		
		foreach($this->varNames as $varName) {
			$requirement = isset($this->requirements[$varName]) ? '('.$this->requirements[$varName].')' : '(.*)';
			$path = preg_replace('#{'.$varName.'}#U', $requirement, $path);
		}
		
		if(preg_match('#^'.$path.'$#', $url, $matches))
			return $matches;
		else 
			return false;
	}
	
	public function setAppName($appName) { 
		if(is_string($appName))
			$this->appName = $appName;
	}
	
	public function setRequirements(array $requirements = []) { 		
		$this->requirements = $requirements;
	}
	
	public function setDefaults(array $defaults) { 
		if(!isset($defaults['_controller']))
			throw new RuntimeException('Le paramètre _controller est manquant');
		
		$this->defaults = $defaults;
		if(preg_match('#^([a-z]+):([a-z]+):([a-z]+)$#iU', $defaults['_controller'], $matches)) {
			$this->setAppName($matches[1]);
			$this->setModule($matches[2]);
			$this->setAction($matches[3]);
		}
	}
	
	public function setName($name) {
		if(is_string($name)) 
			$this->name = $name;
	}
	
	public function setPath($path) { 
		if(!is_string($path))
			throw new InvalidArgumentException('Le chemin de la route doit être une chaîne de caractères');
		
		if(preg_match('#\.#', $path))
			$path = preg_replace('#\.#', '\.', $path);
		$this->path = $path;
	} 
	
	public function setModule($module) {
		if(is_string($module))
			$this->module = $module;
	}
	
	public function setAction($action) {
		if(is_string($action)) 
			$this->action = $action;
	}
	
	public function setVarNames(array $varNames) {
		$this->varNames = $varNames;
	}
	
	public function setVars(array $vars) {
		$this->vars = $vars;
	}
	
	public function appName() { return $this->appName; }
	
	public function name() { return $this->name; }
	
	public function path() { return $this->path; }
	
	public function module() { return $this->module; }
	
	public function action() { return $this->action; }
	
	public function varNames() { return $this->varNames; }
	
	public function vars() { return $this->vars; }
	
}