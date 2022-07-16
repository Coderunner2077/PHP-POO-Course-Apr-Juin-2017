<?php
namespace OCFram;

class Page extends ApplicationComponent {
	protected $contentFile,
			  $vars;
	
	public function addVar($var, $value) {
		if(!is_string($var) || is_numeric($var) || !trim($var))
			throw new \RuntimeException('Le nom de la variable est invalide !');
		$this->vars[$var] = $value;
	}
	
	public function getGeneratedPage() 	{
		if(!file_exists($this->contentFile))
			throw new \RuntimeException('La vue spécifiée ('.$this->contentFile .') n\'existe pas');
		
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

	public function setContentFile($contentFile) {
		if(!is_string($contentFile) || !trim($contentFile))
			throw new \RuntimeException('L\'url doit être une chaîne de caractères valide');
		$this->contentFile = $contentFile;
	}
}