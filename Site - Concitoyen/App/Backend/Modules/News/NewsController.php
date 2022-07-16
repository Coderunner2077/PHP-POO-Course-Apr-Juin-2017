<?php
namespace App\Backend\Modules\News;

use \Concit\BackController;
use \Concit\HTTPRequest;
use \Entity\News;


class NewsController extends BackController {
	
	use \Concit\NewsControllerTrait;
	
	public function executeIndex(HTTPRequest $request) {
		$nombreCaracteres = $this->app->config()->get('nombre_caracteres');
		$listeNews = $this->managers->getManagerOf('News')->getList();
		foreach($listeNews as $news) {
			$titre = substr($news->contenu(), 0, $nombreCaracteres);
			$titre = substr($titre, 0, strripos(' ')) . '...';
			
			$news->setTitre($titre);
		}
		$this->page->addVar('title', 'Gestion des news');
		$this->page->addVar('listeNews', $listeNews);	
		$this->page->addVar('nombreNews', $this->managers->getManagerOf('News')->count());
	}
}