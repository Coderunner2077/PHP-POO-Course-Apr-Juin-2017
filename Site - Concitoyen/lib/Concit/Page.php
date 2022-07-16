<?php
namespace Concit;

class Page extends ApplicationComponent {
	protected $contentFile,
			  $vars = [];
	
	public function setContentFile($contentFile) {
		if(!is_string($contentFile) || !trim($contentFile))
			throw new \InvalidArgumentException('La vue spécifiée est invalide');
		$this->contentFile = $contentFile;
	}
	
	public function addVar($key, $value) {
		if(!is_string($key) || is_numeric($key) || !trim($key))
			throw new \InvalidArgumentException('Le nom de la variable doit être une chaîne de caractères non nulle');
		
		$this->vars[$key] = $value;
	}
	
	public function getGeneratedPage() {
		if(!file_exists($this->contentFile))
			throw new \RuntimeException('La vue spécifiée n\'existe pas');
		
		$user = $this->app->user();
		if($this->vars)
			extract($this->vars);
		ob_start();
			require $this->contentFile;
		$content = ob_get_clean();
		
		ob_start();
			require __DIR__.'/../../App/'.$this->app->name().'/Templates/layout.php';
		return ob_get_clean();
	}
}