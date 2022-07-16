<?php
namespace OCFram;

class Config extends ApplicationComponent {
	protected $vars = [];
	
	public function get($var) {
		if(!is_string($var) || is_numeric($var) || !trim($var))
			throw new \InvalidArgumentException('Le nom de variable configuration doit être une chaîne de caractère valide');
		if(!$this->vars) {
			$xml = new \DOMDocument();
			$xml->load(__DIR__.'/../../App/'.$this->app->name().'/Config/app.xml');
			$definitions = $xml->getElementsByTagName('define');
			foreach($definitions as $define) 
				$this->vars[$define->getAttribute('var')] = $define->getAttribute('value');
		}
		
		return isset($this->vars[$var]) ? $this->vars[$var] : null;
	}
}