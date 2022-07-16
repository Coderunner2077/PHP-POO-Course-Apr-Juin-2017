<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Le frontend</title>
</head>
<body>
<pre>
                                                         Le frontend
                                                         
Je vais enfin aborder quelque chose de concret en construisant ma première application: le frontend. Cette application est la partie
visible par tout le monde. Je vais construire un module de news avec commentaires, autant le dire, il y a de quoi faire !

<h3>
I./ L'application
</h3>
1.) La classe FronendApplication

Je vais commencer par créer ma classe FronendApplication. Avant de commencer, je vais créer à l'intérieur du dossier '/App/Frontend'
le fichier contenant ma classe, à savoir FrontendApplication.php.

Bien. Je commence par écire le minimum de la classe avec le namespace correspondant (pour rappel: le namespace est identique au 
chemin menant vers le fichier contenant la classe) en implémenant les deux méthodes à écrire, à savoir __construct() (qui aura
pour simple contenu d'appeler le constructeur parent puis de spécifier le nom de l'application), et run(). Cette dernière méthode
écrira cette suite d'instruction :

	-	Obtention du contrôleur grâce à la méthode parente getController()
	-	Exécution du contrôleur
	-	Assignation de la page créée par le contrôleur à la réponse
	-	Envoi de la réponse
	
Ma classe devrait ressembler à ceci : 

<?php 
namespace App\Frontend;

use \OCFram\Application;

class FronendApplication extends Application {
	public function __construct() {
		parent::__construct();
		
		$this->name = 'Frontend';
	}
	
	public function run() {
		$controller = $this->getController();
		$controller->execute();
		
		$this->httpResponse->setPage($controller->page());
		$this->httpResponse->send();
	}
}
?>

Finalement, le déroulement est assez simple quand on regarde de plus près.

2.) Le layout

Tout site web qui se respecte se doit d'avoir un design. Je ne vais pas m'étaler ici sur ce type de création, ce n'est pas le sujet
qui m'occupe. Je vais donc me servir d'un pack libre anciennement disponible sur un site de designs: 

Réf Envision (dossier)

C'est un design très simple et facilement intégrable. Idéal pour ce que je veux faire ici.

Pour rappel, les fichiers de ce pack sont à placer dans le dossier /Web. 

Revenons-en au layout. Celui-ci est assez simple et respecte les contraintes imposées par le design. Pour rappel, le layout
est à placer dans :

<strong>/App/Frontend/Templates/layout.php</strong>

Réf layout.php

Note : la variable $user fait référence à l'instance de User. Elle doit être initilialisée dans la méthode getGeneratedPage() de la
classe Page.EA612 bm noir


<?php 
namespace OCFram;

class Page extends ApplicationComponent {
	// ...
	public function getGeneratedPage() {
		if(!file_exists($this->contentFile))
			throw new \RuntimeException('La vude spécifie n\'existe pas !');
		
		$user = $this->app->user();
		if($this->vars)
			export($this->vars);
		
		ob_start();
			require $this->contentFile;
		$content = ob_get_clean();
		
		ob_start();
			require __DIR__.'/../../App/'. $this->app->name() . '/Templates/layout.php';
		return ob_get_clean();
	}
	
	// ...
}
?>

Note : Si j'utilise la variable $this, elle fera référence à l'objet Page car le layout est inclus dans la méthode
Page::getGeneratedPage().
 
3.) Les deux fichiers de configuration

Je vais préparer le terrain en créant les deux fichiers de configuration  dont j'ai besoin: les fichiers app.xml et routes.xml,
pour l'instant quasi-vierges :

Réf app.xml
Réf routes.xml

4.) L'instanciation de FrontendApplication

Pour lancer mon application, j'ai au préalable besoin d'effectuer quelques opérations. En effet, je n'ai toujours pas parlé de 
l'endroit où je vais enregistrer mes autoloads. Je vais ainsi créer un fichier chargé d'enregistrer ces autoload puis de 
lancer l'application. En informatique, un tel programme est appelé <strong>bootsrap</strong>. Il s'agit de manière plus générale
d'un petit programme chargé d'en lancer un plus gros (ce plus gros programme est ici mon application). 

Je vais donc créer un fichier <strong>bootstrap.php</strong> situé dans le dossier /Web. Celui-ci devra être capable
de connaître l'application à lancer. Je vais lui transmettre cette donnée par une variable GET. Bien entendu, il faudra
vérfier que cette variable existe et que l'application existe bien avant de procéder aux diverses opérations. Voici le fichier
que je devrai obtenir: 

<?php 
const DEFAULT_APP = 'Frontend';
// Si l'application n'est pas valide, on va charger l'application par défaut qui générera une erreur 404
if(!isset($_GET['app']) || !file_exists(__DIR__.'/../App/'.$_GET['app'])) 
	$_GET['app'] = DEFAULT_APP;
	
// Je commence par inclure la classe me permettant d'enregistrer mes autolaod
require __DIR__.'/../lib/OCFram/SplClassLoader.php';

// On va ensuite enregistrer les autoloads correspondant  à chaque vendor (OCFram, App, Model, etc.)
$OCFramLoader = new SplClassLoader('OCFram', __DIR__.'/../lib');
$OCFramLoader->register();

$appLoader = new SplClassLoader('App', __DIR__.'/..');
$appLoader->register();

$modelLoader = new SplClassLoader('Model', __DIR__. '/../lib/vendors');
$modelLoader->register();

$entityLoader = new SplClassLoader('Entity', __DIR__.'/../lib/vendors');
$entityLoader->register();

// Il ne me suffit plus qu'à déduire le nom de la classe et l'instancier
$appClass = 'App\\'.$_GET['app'].'\\'.$_GET['app'].'Application';
$app = new $appClass();
$app->run();
?>

5.) Réécrire toutes les URL

Il faut que toutes les URL pointent vers ce fichier. Pour cela, je vais me pencher vers l'URL rewriting. Voici le contenu du
.htaccess : 

RewriteEngine On

# Si le fichier auquel on tente d'accéder existe (si on veut accéder à une image par exemple).
# Alors on ne réécrit pas l'URL
RewriteCond %{REQUET_FILENAME} !-f
RewriteRule ^(.*)$ bootsrap.php?app=Frontend [QSA,L]

<h3>
II./ Le module de news
</h3>
Je vais commencer en douceur par un système de news. Pourquoi en douceur ? Car j'ai déjà fait cet exercice lors du précédent TP !
Ainsi, je vais voir comment <strong>l'intégrer</strong> au sein de l'application, et je verrai ainsi plus clair sur la manière
dont elle fonctionne. 

Mais il s'agira d'abord de rappeler ce qu'on attend du système de news

1.) Fonctionnalités

Il doit être possible d'exécuter deux actions différentes sur le module de news: 

	-	Afficher l'index du module. Cela aura pour effet de dévoiler les cinq dernières news avec le titre et l'extrait du 
			contenu (seuls les 200 premiers caractères seront autorisés).
	-	Afficher une news spécifique en cliquant sur son titre. L'auteur apparaîtra, ainsi que la date de modification si la 
			news a été modifiée
			
Comme pour tout module, je commence par créer les dossiers et fichiers de base, à savoir: 

	-	Le dossier <strong>App/Frontend/Modules/News</strong> qui contiendra mon module
	-	Le fichier <strong>App/Frontend/Modules/NewsController.php</strong> qui contiendra mon contrôleur
	-	Le dossier <strong>App/Frontend/Modules/News/Views</strong> qui contiendra les vues
	-	Le fichier <strong>lib/vendors/Model/NewsManager.php</strong> qui contiendra mon manager de base
	-	Le fichier <strong>lib/vendors/Model/NewsManagerPDO.php</strong> qui contiendra mon manager utilisant PDO
	-	Le fichier <strong>lib/vendors/Entity/News.php</strong> qui contiendra la classe représentant un enregistrement
	
2.) Structure de la table news

Une news est constitée d'un titre, d'un auteur et d'un contenu. Aussi, il faut stocker la date d'ajout de news ainsi que sa 
date de modification. Cela me donne une table <strong>news</strong> ressemblant à ceci : 

CREATE TABLE IF NOT EXISTS `news` (
`id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
auteur varchar(30) NOT NULL,
titre varchar(100) NOT NULL,
contenu text NOT NULL,
dateAjout datetime NOT NULL,
dateModif datatime NOT NULL,
PRIMARY KEY(id)
) DEFAULT CHARSET=utf8;

Je peux désormais écrire la classe représentant cette entité: 

<?php 
namespace Entity;

use \OCFram\Entity;

class News extends Entity {
	protected $auteur,
			  $titre,
			  $contenu,
			  $dateAjout,
			  $dateModif;
	
	const AUTEUR_INDALIDE = 1;
	const TITRE_INVALIDE = 2;
	const CONTENU_INVALIDE = 3;
	
	public function isValid() {
		// return trim($this->auteur) && trim($this->titre && trim($this->contenu); // c'est mieux ya pense !
		return !(empty($this->auteur)) || empty($this->titre) || empty($this->contenu);
	}
	
	// SETTERS
	public function setAuteur($auteur) {
		if(!is_string($auteur) || !trim($auteur))
			$this->errors[] = self::AUTEUR_INVALIDE;
		$this->auteur = $auteur;
	}
	
	public function setTitre($titre) {
		if(!is_string($titre) || !trim($titre))
			$this->errors[] = self::TITRE_INVALIDE;
		$this->titre = $titre;
	}
	
	public function setContenu($contenu) {
		if(!is_string($contenu) || !trim($contenu))
			$this->errors[] = self::CONTENU_INVALIDE;
		$this->contenu= $contenu;
	}
	
	public function setDateAjout(\DateTime $date) {
		$this->dateAjout = $date;
	}
	
	public function setDateModif(\Datetime $date) {
		$this->dateModif = $date;
	}
	
	// GETTERS
	public function auteur() {
		return $this->auteur;
	}
	
	public function titre()	{
		return $this->titre;
	}
	
	public function contenu() {
		return $this->contenu;
	}
	
	public function dateAjout()	{
		return $this->dateAjout;
	}
	
	public function dateModif() {
		return $this->dateModif;
	}
}
?>

3.) L'action index
a./ La route

Commençons par implémenter cette action. La première chose à faire est de créer une nouvelle route: quelle URL pointera vers
cette action ? Eh bien, ce cera la racine du site web, donc ce cera l'URL/. Pour créer cette route, il va falloir modifier mon
fichier de configuration  et y ajouter cette ligne: 

&lt;route> url="/" module="News" action="index" />

Vient ensuite l'implémentation de l'action dans le contrôleur.

b./ Le contrôleur

Qui dit nouvelle action dit nouvelle méthode, et cette méthode c'est executeIndex(). Cette méthode  devra récupérer les cinq 
dernières news (le nombre cinq devra être stocké dans le fichier de configuration de l'application, i.e. App/Frontend/Config/app.xml).
Il faudra parcourir cette liste de news afin de n'assigner aux news qu'un contenu de 200 caractères au maximum. Ensuite, il
faut passer la liste de news à la vue : 

<?php 
namespace App\Frontend\Modules\News;

use \OCFram\BackController;
use \OCFram\HTTPRequest;

class NewsController extends BackController {
	public function executeIndex(HTTPRequest $request) {
		$nombreNews = $this->app->config()->get('nombre_vues');
		$nombreCaractères = $this->app->config()->get('nombre_caracters');
		
		// On ajoute une définition pour le titre
		$this->page->addVar('title', 'Liste des '.$nombreNews.' dernières news');
		
		// On récupère le manager des news
		$manager = $this->managers->getManagerOf('News');
		
		// Ligne comportant une méthode qui sera implémentée plus tard
		$listeNews = $manager->getList(0, $nombreNews);
		
		foreach($listNews as $news) {
			if(strlen($news->contenu()) > $nombreCaracteres) {
				$debut = substr($news->contenu(), 0, $nombreCaracteres);
				$debut = substr($debut, 0, strrpos($debut, ' ')) . '...';
				
				$news->setContenu($debut);
			}
		}
		
		// On ajoute la variable $listNews à la vue
		$this->page->addVar('listNews', $listeNews);
	}
}
?>
 
En effet, j'ai utilisé le fichier de configuration pour récupérer le nombre de news à afficher et le nombre maximum de caractères. 
Voici le fichier de configuration : 

Réf app.xml

c./ La vue

A toute action correspond une vue du même nom. Ici, la vue à créer sera :

<strong>/App/Frontend/Modules/News/Views/index.php</strong>

Voici un exemple très simple de cette vue :

Réf index.php

A noter : l'utilisation des news comme des tableaux grâce à l'implémentation de l'interface ArrayAccess.

d./ Le modèle

Je vais modifier deux classes  faisant partie du modèle, à savoir NewsManager et NewsManagerPDO. Je vais implémenter à cette 
dernière classe une méthode : getList(). Sa classe parente doit donc aussi être modifiée pour déclarer cette méthode : 

<?php 
namespace Model;

use \OCFram\Manager;

abstract class NewsManager extends Manager {
	/**
	 * Méthode retournant une liste de news demandée
	 * @param $debut int La première news à sélectionner
	 * @param $limite int Le nombre de news à sélectionner
	 * @return array  La list de news. Chaque entrée  est une instance de news.
	 */
	abstract public function getList($listeNews = -1, $limite = -1);
}
?>

<?php 
namespace Model;

use \Entity\News;

class NewsManagerPDO extends NewsManager {
	public function getList($debut = - 1, $limite = -1) {
		$sql = 'SELECT * FROM news ORDERY BY id DESC';
		
		if($debut != -1 || $limite != -1)
			$sql .= ' LIMIT ' . (int) $limite . ' OFFSET ' . (int) $debut;
		
		$requete = $this->dao->query($sql);
		$requete = setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\News');
		
		$listeNews = $requete->fetchAll();
		
		foreach($listeNews as $news) {
			$news->setDateAjout(new \DateTime($news->dateAjout()));
			$news->setDateModif(new \DateTime($news->dateModif()));
		}
		
		$requete->closeCursor();
		return $listeNews;
	}
}
?>

Par contre, si je fais le test en accédant à la racine de mon site, je découvrirai... un gros blanc, car aucune news n'est
présente en BDD. Donc, à ajouter...

4.) L'action show
a./ La route

Les URL du type <strong>news-id.html</strong> vont pointer vers cette action. Je dois donc modifier le fichier de configuration
des routes pour y ajouter celle-ci :

Réf routes.html

b./ Le contrôleur

Le contrôleur implémentera la méthode executeShow(). Son contenu est simple : le contrôleur ira demander au manager la news
correspondant à l'identifiant puis, il passera cette news à la vue, en ayant pris soin de remplacer les sauts de lignes par des
balises &lt;br /> dans le contenu de la news.

Note : si la news n'existe pas, il faudra rediriger l'utilisateur vers une erreur 404.

<?php 
namespace App\Frontend\Modules\News;

use \OCFram\BackController;
use \OCFram\HTTPRequest;

class NewsController extends BackController {
	public function executeIndex(HTTPRequest $request) {
		$nombreNews = $this->app->config()->get('nombre_news');
		$nombreCaracteres = $this->app->config()->get('nombre_caracteres');
		
		// on ajoute une définition pour le titre
		$this->page->addVar('title', 'Liste des '.$nomreNews.' dernières news');
		
		// On récupère le manager de news
		$manager = $this->managers->getManagerOf('News');
		
		$listeNews = $manager->getList(0, $nombreNews);
		
		foreach($listNews as $news) {
			if(strlen($news->contenu()) > $nombreCaracters) {
				$debut = substr($news->contenu(), 0,$nombreCaracteres);
				$debut = substr($debut, 0, strrpos($debut, ' ')) . '...';
				
				$news->setContenu($debut);
			}
		}
		
		// On ajoute la variable listNews à la page
		$this->page->addVar('listeNews', $listeNews);
	}
	
	public function executeShow(HTTPRequest $request) {
		$news = $this->managers->getManagerOf('News')->getUnique($request->getData('id'));
		
		if(empty($news))
			$this->app->httpResponse()->redirect404();
		
		$this->page->addVar('title', $news->titre());
		$this->page->addVar('news', $news);
	}
}
?>

c./ La vue

La vue se contente de générer l'affichage de la news. Voici le code de la vue : 

Réf show.php

d./ Le modèle

Je vais là aussi toucher mes classes NewsManager et NewsManagerPDO en ajoutant la méthode getUnique() :

<?php 
namespace Model;

use \OCFram\Manager;

abstract class NewsManager extends Manager {
	/**
	 * Méthode retournant une liste de news demandée.
	 * @param $debut int La première news à sélectionner
	 * @param $limite int Le nombre de news à sélectionner
	 * @return array La liste des news. Chaque entrée est une instance de News.
	 */
	abstract public function getList($debut = -1, $limite = -1);
	
	/**
	 * Méthode retournant une news précise
	 * @param $id int L'identifiant de la news à récupérer
	 * @return News La news demandée.
	 */
	abstract public function getUnique($id);
}
?>

<?php 
namespace Model;

use \Entity\News;

class NewsManagerPDO extends NewsManager {
	public function getList($debut = -1, $limite = - 1) {
		$sql = 'SELECT * FROM news ORDER BY id DESC';
		if($debut != -1 || $limite != -1) 
			$sql .= ' LIMITE '.(int) $limite. ' OFFSET '.(int) $debut;
		
		$requete = $this->dao->query($sql);
		$requete->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\News');
		
		$listeNews = $requete->fetchAll();
		
		foreach($listeNews as $news) {
			$news->setDateAjout(new \DateTime($news->dateAjout()));
			$news->setDateModif(new \DateTime($news->dateModif()));
		}
		
		$requete->closeCursor();
		
		return $listeNews;
	}	
	
	public function getUnique($id) {
		$requete = $this->dao->prepare('SELECT * FROM news WHERE id = :id');
		
		$requete->bindValue(':id', (int) $id, \PDO::PARAM_INT);
		$requete->execute();
		$requete->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\News');
		
		if($news = $requete->fetch()) {
			$news->setDateAjout(new \DateTime($news->dateAjout()));
			$news->setDateModif(new \DateTime($news->dateModif()));
			
			return $news;
		}
		
		return null;		
	}
}
?>

<h3>
III./ Ajoutons des commentaires
</h3>
1.) Cahier des charges

Je vais ajouter une action à mon module de news : l'ajout de commentaire. Il ne faudra pas oublier de modifier mon module de
news, et plus spécialement l'action <strong>show</strong> pour laisser apparaître  la liste des commentaires ainsi qu'un lien 
menant au formulaire d'ajout de commentaire. 

Avant toute chose, il va falloir créer les modèles me permettant d'interagir avec la BDD pour accéder aux commentaires : 

	-	Le fichier <strong>/lib/vendors/Model/CommentsManager.php</strong> qui contiendra mon manager de base
	-	Le fichier <strong>/lib/vendors/Model/CommentsManagerPDO.php</strong> qui inclura mon manager utilisant PDO
	-	Le fichier <strong>/lib/vendors/Entity/Comment.php</strong> qui comportera la classe représentant un enregistrement
	
2.) Structure de la table <em>comments</em>

Un commentaire est assigné à une news. Il est constitué d'un auteur et d'un contenu, ainsi que de sa date d'enregistrement. Ma 
table <strong>comments</strong> doit donc être constituée de la sorte : 

CREATE TABLE IF NOT EXISTS `comments` (
	`id` mediumint(9) NOT NULL AUTO_INcREMENT,
	news smallint(6) NOT NULL,
	auteur varchar(50) NOT NULL,
	contenu text NOT NULL,
	date datetime NOT NULL, 
	PRIMARY KEY(id)
) DEFAULT CHARSET=utf8;

Puisque je connais la structure d'un commentaire, je peux écrire la classe représentant son entité, à savoir la classe Comment :

<?php 
namespace Entity;

use \OCFram\Entity;

class Comment extends Entity {
	protected $news,
			  $auteur,
			  $contenu,
			  $date;
	
	const AUTEUR_INVALIDE = 1;
	const CONTENU_INVALIDE = 2;
	
	public function isValid() {
		return !(empty($this->auteur) || empty($this->contenu));
	}
	
	public function setNews($news) {
		$this->news = (int) $news;
	}
	
	public function setAuteur($auteur) {
		if(!is_string($auteur) || !trim($auteur))
			$this->errors[] = self::AUTEUR_INVALIDE;
		
		$this->auteur = $auteur;
	}
	
	public function setContenu($contenu) {
		if(!is_string($contenu) || !trim($contenu))
			$this->errors[] = self::CONTENU_INVALIDE;
		
		$this->contenu = $contenu;
	}
	
	public function setDate(DateTime $date) {
		$this->date = $date;
	}
	
	public function news() {
		return $this->news;
	}
	
	public function auteur() {
		return $this->auteur;
	}
	
	public function contenu() {
		return $this->contenu;
	}
	
	public function date() {
		return $this->date;
	}
}
?>

3.) L'action <em>insertComment</em>
a./ La route

Je ne vais pas faire dans la fantaisie pour cette action,je vais prendre une URL basique: 

<strong>commenter-idnews.html</strong>

...je rajoute donc cette nouvelle route dans le fichier de configuration des routes

Réf routes

b./ La vue

Dans un premier temps, je vais m'attarder sur la vue car c'est à l'intérieur de celle-ci que je vais construire le formulaire. Cela
me permettra donc de savoir quels champs seront à traiter par le contrôleur. Voici donc la vue : 

Réf insertComment.php

L'identifiant de la news est stocké dans l'URL. Pusque je vais envoyer le formulaire sur cette même page, l'identifiant de la news
sera toujours présent dans l'URL et donc accessible <em>via</em> le contrôleur. 

c./ Le contrôleur

Ma méthode executeInsertComment() se chargera dans un premier temps de vérifier si le formulaire a été envoyé en vérifiant si la 
variable POST <em>pseudo</em> existe. Ensuite, elle procédera à la vérification des données et insérera le commentaire
en BDD si toutes les données sont valides :

<?php 
namespace App\Frontend\Modules\News;

use \OCFram\BackController;
use \OCFram\HTTPRequest;
use \Entity\Comment;

class NewsController extends BackController {
	//... 
	
	public function executeInsertComment(HTTPRequest $request) {
		$this->page->addVar('title', 'Ajout d\'un commentaire');
		
		if($request->postExists('pseudo')) {
			$comment = new Comment([
					'news' => $request->getData('news'),
					'auteur' => $request->postData('pseudo'),
					'contenu' => $request->postData('contenu')
			]);
			
			if($comment->isValid()) {
				$this->managers->getManagerOf('Comments')->save($comment);
				
				$this->app->user()->setFlash('Le commentaire a bien été ajouté, merci !');
				
				$this->app->httpResponse()->redirect('news-'.$request->getData('news').'.html');
			}
			else {
				$this->page->addVar('errors', $comment->errors());
			}
			
			$this->page->addVar('comment', $comment);
		}
	}
	//...
}
?>

d./ Le modèle

J'aurai besoin d'implémenter une méthode dans ma classe CommentsManager: save(). En fait, il s'agit d'un "raccourci", cette
méthode appelle elle-même une autre méthode : add() ou modify() selon si le commentaire est déjà présent en BDD. Mon manager
peut savoir si l'enregistrement est déjà enregistré ou pas grâce à la méthode isNew().

<?php 
namespace Model;

use \OCFram\Manager;
use \Entity\Comment;

abstract class CommentsManager extends Manager {
	/**
	 * Méthode permettant d'ajouter un commentaire
	 * @param $comment Le commentaire à ajouter
	 * @return void
	 */
	abstract protected function add(Comment $comment);
	
	/**
	 * Méthode permettant d'enregistrer un commentaire. 
	 * @param $comment Le commentaire à enresgistrer
	 * @return void
	 */
	public function save(Comment $comment) {
		if($comment->isValid())
			$comment->isNew() ? $this->add($comment) : $this->modify($comment);
		else 
			throw new \RuntimeException('Le commentaire doit être valide pour être enregistré');
	}
}
?>

<?php 
namespace Model;

use \Entity\Comment;

class CommentsManagerPDO extends CommentsManager {
	protected function add(Comment $comment) {
		$q = $this->dao->prepare('INSERT INTO comments SET news = :news, auteur = :auteur, contenu = :contenu, date = NOW()');
		
		$q->bindValue(':news', $comment->news(), \PDO::PARAM_INT);
		$q->bindValue(':auteur', $comment->auteur());
		$q->bindValue(':contenu', $comment->contenu());
		
		$q->execute();
		
		$comment->setId($this->dao->lastInsertId());
		
	}
}
?>

L'implémentation de la méthode modify() se fera lors de la construction de l'espace d'administration

4.) Affichage des commentaires
a./ Modification du contrôleur

Il suffit simplement de passer la liste des commentaires à la vue. Une seule instruction suffit donc : 

<?php 
namespace App\Frontend\Modules\News;

use \OCFram\BackController;
use \OCFram\HTTPRequest;
use \Entity\Comment;

class NewsController extends BackController {
	// ...
	
	public function executeShow(HTTPRequest $request) {
		$news = $this->managers->getManagerOf('News')->getUnique($request->getData('id'));
		
		if(empty($news))
			$this->app->httpResponse()->redirect404();
		
		$this->page->addVar('title', $news->titre());
		$this->page->addVar('news', $news);
		$this->page->addVar('comments', $this->managers->getManagerOf('Comments')->getListOf($news->id()));
	}
}
?>

b./ Modification de la vue affichant la news

La vue devra parcourir la liste des commentaires passés pour les afficher. Les liens portant vers l'ajout d'un commentaire
devront aussi figurer sur la page. 

Réf show.php

c./ Modification du manager des commentaires

Le manager des commentaires devra implémenter la méthode getListOf() dont a besoin mon contrôleur pour bien fonctionner. Voici
le code : 

<?php 
namespace Model;

use \OCFram\Manager;
use \Entity\Comment;

abstract class CommentsManager extends Manager {
	/**
	 * Méthode permettant d'ajouter un commentaire.
	 * @param $comment Le commentaire à ajouter
	 * @return void
	 */
	abstract protected function add(Commment $comment);
	
	/**
	 * Méthode permettant d'enregistrer un commentaire.
	 * @param $comment Le commentaire à enregistrer
	 * @return void
	 */
	public function save(Comment $comment) {
		if($comment->isValid())
			$comment->isNew() ? $this->add($comment) : $this->modify($comment);
		else
			throw new \RuntimeException('Le commentaire doit être validé pour être enregistré');
	}
	
	/**
	 * Méthode permettant de récupérer une liste de commentaires
	 * @param $news La news sur laquelle on veut récupérer les commentaires
	 * @return array
	 */
	abstract public function getListOf($news);
}
?>

<?php 
namespace Model;

use \Entity\Comment;

class CommentsManagerPDO extends CommentsManager {
	protected function add(Comment $comment) {
		$q = $this->dao->prepare('INSERT INTO comments SET news = :news, auteur = :auteur, contenu = :contenu, date = NOW()');
		$q->bindValue(':news', $comment->news(), \PDO::PARAM_INT);
		$q->bindValue(':auteur', $comment->auteur());
		$q->bindValue('contenu', $comment->contenu());
		$q->execute();
		
		$comment->setId($this->dao->lastInsertId());
	}
	
	public function getListOf($id) {
		if(!ctype_digit($id))
			throw new \InvalidArgumentException('L\'identifiant de la news passé doit être un nombre entier valide');
		
		$q = $this->dao->prepare('SELECT * FROM comments WHERE news=:news ORDER BY date DESC');
		$q->bindValue(':news', $id, \PDO::PARAM_INT);
		$q->execute();
		
		$q->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\Comment');
		
		$comments = $q->fetchAll();
		
		foreach($comments as $comment) 
			$comment->setDate(new \DateTime($comment->date()));
		
		return $comments;
	}
}
?>








</pre>
</body>
</html>