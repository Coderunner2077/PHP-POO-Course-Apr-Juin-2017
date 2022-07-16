<?php
namespace Concit;

class Config extends ApplicationComponent {
	protected $vars;
	
	public function get($var) {
		if(!is_string($var));
		if(!isset($this->vars[$var])) {
			$xml = new \DOMDocument();
			$xml->load(__DIR__.'/../../App/'.$this->app->name().'/Config/app.xml');
			$definitions = $xml->getElementsByTagName('define');
			foreach($definitions as $define) {
				$this->vars[$define->getAttribute('var')] = $define->getAttribute('value');
			}
		}
		
		return isset($this->vars[$var]) ? $this->vars[$var] : null;
	}
}