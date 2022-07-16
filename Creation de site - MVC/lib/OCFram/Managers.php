<?php
namespace OCFram;

class Managers {
	protected $dao = null,
			  $api = null,
			  $managers = array();
	
	public function __construct($api, $dao) {
		$this->api = $api;
		$this->dao = $dao;
	}
	
	public function getManagerOf($module) {
		if(!is_string($module) || !trim($module))
			throw new \RuntimeException('Le module doit être une chaîne de caractères valide');
		if(!isset($this->managers[$module])) {
			$manager = '\\Model\\'.$module.'Manager'.$this->api;
			$this->managers[$module] = new $manager($this->dao);
		}
		
		return $this->managers[$module];
	}
}