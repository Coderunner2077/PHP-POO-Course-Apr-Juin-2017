<?php
namespace OCFram;

class Route {
	protected $url,
			  $module,
			  $action,
			  $varNames,
			  $vars = [];
	
	public function __construct($url, $module, $action, array $varNames) {
		$this->url = $url;
		$this->module = $module;
		$this->action = $action;
		$this->varNames = $varNames;
	}
	
	public function url() {
		return $this->url;
	}
	
	public function module() {
		return $this->module;
	}
	
	public function action() {
		return $this->action;
	}
	
	public function varNames() {
		return $this->varNames;
	}
	
	public function vars() {
		return $this->vars;
	}
	
	public function setUrl($url) {
		if(is_string($url) && trim($url))
			$this->url = $url;
	}
	
	public function setModule($module) {
		if(is_string($module) && trim($module))
			$this->module = $module;
	}
	
	public function setAction($action) {
		if(is_string($action) && trim($action))
			$this->action = $action;
	}
	
	public function setVarNames(array $varNames) {
		$this->varNames = $varNames;
	}
	
	public function setVars(array $vars) {
		$this->vars = $vars;
	}
	
	public function hasVars() {
		return !empty($this->varNames);
	}
	
	public function match($url) {
		if(preg_match('`^'.$this->url.'$`', $url, $varValues))
			return $varValues;
		else 
			return false;
	}
}