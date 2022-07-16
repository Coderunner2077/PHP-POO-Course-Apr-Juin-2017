<?php
namespace Concit;

class Managers {
	protected $managers = [],
			  $api = null,
			  $dao = null;
	
	public function __construct($api, $dao) {
		$this->api = $api;
		$this->dao = $dao;
	}
	
	public function getManagerOf($module) {
		if(!is_string($module) || !trim($module))
			throw new \InvalidArgumentException('Le module spécifié est invalide');
		if(!isset($this->managers[$module])) {
			$manager = 'Model\\'.$module.'Manager'.$this->api;
			$this->managers[$module] = new $manager($this->dao);
		}
		return $this->managers[$module];
	}
}