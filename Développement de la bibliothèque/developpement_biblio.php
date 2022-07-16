<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Développement de la bibliothèque</title>
</head>
<body>
<pre>
                                                      Développement de la bibiliothèque
                                                      
Le plus gros a été fait: je sais comment fonctionnera mon application, je sais où je veux aller. Maintenant, il ne reste plus qu'à 
me servir de tout cela pour construire diagrammes UML qui vont lier mes classes, me permettant ainsi de les écrire facilement.

Voici le lien permettant de visualiser <a href="http://www.victorthuillier.com/oc/poo/tp_app/?f=Partie+III%2FChapitre+2" 
title="Visualiser les schémas du projet">l'ensemble du projet</a> en cas de besoin.

<h3>
I./ L'application
</h3>
1.) L'application

Commençons par construire notre classe Application. Celle-ci possède pour l'instant une seule fonctionnalité : celle de s'exécuter.
Or, on n'a pas encore parlé des <strong>caractéristiques</strong> de l'application, autrement dit les attributs. 

Le premier attribut est le <strong>nom</strong> de l'application.

Les deux autres sont la <strong>requête</strong> ainsi que la <strong>réponse</strong> envoyée au client. Etudions donc tout d'abord
ces deux entités, avant de créer la classe Application.

2.) La requête du client
a./ Schématisons

Je vais représenter la requête du client au travers d'une instance de classe. Comme pour toute classe, intéressons-nous aux 
fonctionnalités attendues. Qu'est-ce qui m'intéresse dans la requête du client ? Quelles fonctionnalités seraient intéressantes ?
A partir de cette intstance, il serait pratique de pouvoir : 

	-	Obtenir une variable POST
	-	Obtenir une variable GET
	-	Obtenir un cookie
	-	Obtenir la méthode employée pour envoyer la requête (méthode GET ou POST)
	-	Obtenir l'URL entrée (utile pour que le routeur connaisse la page souhaitée)
	
Et pour la route, voici un petit diagramme : réf diag_projet.dia ==> HTTPRequest

b./ Codons

Cette classe, comme toute classe de mon framework, est à écrire dans un fichier situé dans le dossier /lib/OCFram (donc, je crée
un fichier /lib/OCFram/HTTPRequest.php.

Voici donc ma classe HTTPRequest : 

<?php 
//namespace OCFram;

class HTTPRequest extends ApplicationComponent {
	public function cookieData($key) {
		return isset($_COOKIE[$key]) ? $_COOKIE[$key] : null;
	}
	
	public function cookieExists($key) {
		return isset($_COOKIE[$key]);
	}
	
	public function getData($key) {
		return isset($_GET[$key]) ? $_GET[$key] : null;
	}
	
	public function getExists($key) {
		return isset($_GET[$key]);
	}
	
	public function postData($key) {
		return isset($_POST[$key]) ? $_POST[$key] : null;
	}
	
	public function postExists($key) {
		return isset($_POST[$key]);
	}
	
	public function method() {
		return $_SERVER['REQUEST_METHOD']; // renvoie la méthode de la requête (i.e. 'GET', 'POST', 'HEAD' ou 'PUT')
	}
	
	public function requestURI() {
		return $_SERVER['REQUEST_URI']; // renvoie l'url de la requête du client
	}	
}
?>

Un petit mot sur la toute première ligne, celle qui contient la déclaration du namespace. Ou plus tôt un rappel: toutes les classes
de mon projet sont déclarées dans des namespaces. Cela permet d'une part de structurer mon projet, et d'autre part, d'écrire un
autoload simple qui sait directement, grâce au namespace contenant la classe, le chemin du fichier contenant ladite classe.

Par exemple, si j'ai un contrôleur du module de news. Celui-ci sera placé dans le dossier /App/Frontend/Modules/News. La classe
représentant ce contrôleur (NewsController) sera donc dans le namespace App\Frontend\Modules\News !

Attention : Un fichier contenant un espace de noms doit déclarer l'espace de noms au début du fichier, avant tout autre code, 
avec une seule exception : le mot clé declare

3.) La réponse envoyée au client
a./ Schématisons

Là aussi, je vais représenter la réponse envoyée au client au travers d'une entité. Cette entité n'est autre qu'une instance
d'une classe. Quelles fonctionnalités aura cette classe ? Que veut-on envoyer au visiteur ? La réponse la plus évidente est
la <strong>page</strong>. Je veux assiger une <strong>page</strong> à la réponse. Cependant, il est bien beau d'assigner une
page, encore faudrait-il pouvoir l'envoyer ! Voici une deuxième fonctionnalité : celle <strong>d'envoyer</strong> la réponse
en <strong>générant</strong> la page. 

Il existe de nombreuses autres fonctionnalités "accessoires", comme par exemple celle de pouvoir rediriger le visiteur vers une
page 404, lui écrire un cookie et d'ajouter un header spécifique. Pour résumer, ma classe me permettra : 

	-	D'assigner une page à la réponse
	-	D'envoyer la réponse en générant la page
	-	De rediriger l'utilisateur
	-	De le rediriger vers une erreur 404
	-	D'ajouter un cookie
	-	D'ajouter un header spécifique
	
Réf diag_projet.dia ==> HTTTResponse

b./ Codons

<?php 
//namespace OCFram;

class HTTPResponse extends ApplicationComponent {
	protected $page;
	
	public function setPage(Page $page) {
		$this->page = $page;
	}
	
	public function send() {
		exit($this->page->getGeneratedPage()); // cette ligne sera expliquée plus tard
		// exit affiche un message (si spécifié) et termine le script courant
	}
	
	public function redirect($location) {
		header('Location: '. $location);
	}
	
	public function redirect404() {
		$this->page = new Page($this->app);
		
		$this->page->setContentFile(__DIR__.'/../../Errors/404.html');
		header('HTTP/1.0 404 Not Found');
		
		$this->send();
	}
	
	public function addHeader($header) {
		header($header);
	}
	
	public function addCookie($name, $value = '', $expire = 0, $path = null, $domain = null, $secure = false, $httpOnly = true) {
		setcookie($name, $value, $expire, $path, $domain, $secure, $httpOnly);
		// On a fait en sorte que le dernier paramètre soit à true
	}
	
}
?>

4.) Retour sur mon application
a./ Schématisons

Maintenant que j'ai vu comment sont représentées la requête du client et la réponse que l'on va lui envoyer, je peux réflechir
pleinement sur ce qui compose ma classe. Elle possède une fonctionnalité (celle de s'exécuter) et trois caractéristiques : son
nom, la requête du client et la réponse que l'on va lui envoyer.

Il y a au moins deux classes qui hériteront de cette classe Application, cela n'a donc aucun sens d'instancier cette classe. Par
conséquent, cette classe sera abstraite. 

Réf diag_projet.dia ==> Application

b./ Codons

<?php 
//namespace OCFram;

abstract class Application {
	protected $name;
	protected $httpRequest;
	protected $httpResponse;
	protected $user;
	protected $config;
	
	public function __construct(User $user) {
		$this->httRequest = new HTTPRequest($this);
		$this->httpResponse = new HTTPResponse($this);
		$this->name = '';
		$this->user = $user;
		$this->config = new Config($this);
	}
	
	abstract public function run();
	
	public function httpRequest() {
		return $this->httpRequest;
	}
	
	public function httpResponse() {
		return $this->httpResponse;
	}
	
	public function name() {
		return $this->name;
	}
	
	public function getController() {
		$router = new Router();
		
		$xml = new \DOMDocument();
		$xml->load(__DIR__.'/../../App/'.$this->name.'/Config/routes.xml');
		
		$routes = $xml->getElementsByTagName('route');
		
		// On parcourt les routes du fichier XML
		foreach($routes as $route) {
			$vars = [];
			
			// On regarde si des variables sont présentes dans l'URL
			if($route->hasAttribute('vars'))
				$vars = explode(',', $route->getAttribute('vars'));
				
			// On ajoute la route au routeur
			$router->addRoute(new Route($route->getAttribute('url'), $route->getAttribute('module'), $route->getAttribute('action'),
										$vars));
			try {
				// On ajoute la route correspondante à l'URL
				$matchedRoute = $router->getRoute($this->httpRequest->requestURI());
			} catch(\RuntimeException $e) {
				if($e->getCode() == Router::NO_ROUTE)
					//Si aucune route ne correspond, c'est que la page demandée n'existe pas
					$this->httpResponse->redirect404();
			}
		}
		
		// On ajoute les variables de l'URL au tableau $_GET
		$_GET = array_merge($_GET, $matchedRoute->vars());
		
		// On instancie le contrôleur
		$controllerClass = 'App\\'.$this->name.'\\Modules\\'. $matchedRoute->module().'\\'.$matchedRoute->module().'Controller';
		return new $controllerClass($this, $matchedRoute->module(), $matchedRoute->action());
	}
}
?>
	
Dans le constructeur, on assigne une chaîne de caractères vide à l'attribut name. En fait, chaque application (qui héritera donc
de cette classe) sera chargée de spécifier son nom en initialisant cet attribut.

5.) Les composants de l'application

Les deux premières classes (comme la plupart des classes que je vais créer) sont des <strong>composantes de l'application</strong>.
Toutes ces classes ont donc une nature en commun et doivent hériter d'une même classe représentant cette nature: classe que 
je nommerai ApplicationComponent.

Que me permettra de faire cette classe ? 

D'obtenir l'application à laquelle l'objet appartient. C'est tout !
Cette classe se chargera juste de stocker, pendant la construction de l'objet, l'instance de l'application exécutée. J'ai donc
une simple classe ressemblant à ceci :

Réf diag_projet.dia ==> ApplicationComponent

Et le code :

<?php 
//namespace OCFram;

abstract class ApplicationComponent {
	protected $app;
	
	public function __construct(Application $app) {
		$this->app = $app;
	}
	
	public function app() {
		return $this->app;
	}
}
?>

Note : il faut penser à ajouter le lien de parenté aux classe HTTPRequest et HTTPResponse. Et aussi, passer l'instance de 
l'application lors de l'instanciation de ces deux classes dans le constructeur de Application.

<h3>
II./ Le routeur
</h3>
1.) Schématisons

Comme on l'a vu, le routeur est l'objet qui va me permettre de savoir quelle page je dois exécuter. Pour en être capable, le
routeur aura à sa disposition des <strong>routes</strong> pointant chacune vers un module et une action.

Rappel : une route, c'est une URL associée à un module et une action. Créer une route signifie donc assigner un module et une
action à une URL. 

La question que l'on se pose alors est : <strong>où seront écrites les routes ?</strong> On peut être tenté de les écrire 
directement à l'intérieur de la classe Routeur et faire une sorte de switch / case sur les routes pour trouver laquelle correspond à
l'URL. Cette façon de faire présente un énorme inconvénient : ma classe représentant le routeur sera <strong>dépendante</strong>   
du projet que je développe. Par conséquent, je ne pourrais plus l'utiliser sur un autre site ! Il va donc falloir 
<strong>externaliser</strong>  ces définitions de routes.

Comment pourrait-on faire alors ? Je vais tout simplement placer ces routes dans un autre fichier. Ce fichier doit être placé
dans le dossier de l'application concernée, et puisque ça touche à la configuration de celle-ci, je le placerai dans un sous-dossier
Config. Il y a aussi un détail à régler : dans quel format vais-je écrire le fichier ? Un choix optimal, c'est le format XML car
ce langage est intuitif et simple à parser, notamment grâce à la bibliothèque native <a href="http://fr2.php.net/manual/fr/class.domdocument.php">
DOMDocument</a> de PHP. Le chemin complet vers ce fichier devient donc : 

<strong>/App/Nomdelapplication/Config/routes.xml</strong>

Comme pour tout fichier XML qui se respecte, celui-ci doit suivre une structure précise ==>

&lt;?xml version="1.0" encoding="utf-8" ?>
&lt;routes>
	&lt;route url="/news.html" module="News" action="index">&lt;/route>
&lt;/routes>

Le rôle de la troisième ligne consiste à permettre au routeur d'établir, lorsqu'on va sur la page <strong>news.html</strong>,
que le client veut accéder au module <strong>News</strong> et exécuter l'action <strong>index</strong>.

Un autre problème se pose. Par exemple, si je veux afficher une news spécifique en fonction de son identifiant, que faire ? Ou,
plus généralement, comment passer des variables GET ? L'idéal serait d'utiliser des expressions régulières en guise d'URL. Chaque
paire de parenthèses représentera une variable GET. Je spécifierai leur nom dans un quatrième attribut vars.

Voici le contenu de la balise &lt;route> revisité :

&lt;route url="/news-(.+)-([0-9]+)\.html" module="News" action="show" vars="slug,id" />

Ainsi, toute URL vérifiant cette expression pointera vers le module News et exécutera l'action show(). Les variables
$_GET['slug'] et $_GET['id'] seront créées et auront pour valeur le contenu des parenthèses capturantes.

On sait désormais que notre routeur a besoin de routes pour nous renvoyer celle qui correspond à l'URL. Cependant, s'il a
besoin de routes, il va falloir les lui donner !

Pourquoi ne peut-il pas aller les chercher lui-même ?

S'il allait les chercher lui-même, ma classe serait dépendante de l'architecture de l'application. Si je voulais utiliser ma
classe dans un autre projet, je ne pourrais pas, car le fichier contenat les routes (/App/Nomdelappli/Config/routes.xml) 
n'existerait tout simplement pas. De plus, dans ce projet, les routes ne seront peut-être pas stockées dans un fichier XML,
donc le parsage ne se fera peut-être pas de la même façon. Or, l'un des points forts de la POO est la réutilisabilité. Ainsi,
ma classe représentant le routeur ne dépendra ni d'une architecture, ni du format du fichier stockant les routes.

De cette façon, ma classe Routeur présente deux foncionnalités : 

	-	Celle d'ajouter une route à sa liste de routes
	-	Celle de renvoyer la route correspondante à l'URL.
	
Avec, bien entendu, une caractéristique : la liste des routes attachée au routeur. 
Cependant, une autre question se pose : je disais qu'on "passe une route" au routeur.  Ce qui ne peut raisonnablement se faire que
si la route est un objet.

Qu'est-ce qui caractérise l'objet Route : 

	-	Une URL
	-	Un module
	-	Une action
	-	Un tableau comportant les noms des variables
	-	Un tableau clé/valeur comportant les noms/valeurs des variables
	
Quelle différence entre les deux dernières caractéristiques ? 

En fait, lorsque je crée les routes, je vais assigner les quatre premières caractéristiques. C'est donc cette dernière liste
de variables que je vais assigner à ma route. Ensuite, mon routeur va parcourir ces routes et c'est lui qui assignera les
valeurs des variables. C'est donc à ce moment-là que le tableau comportant les nom/valeurs des variables sera créé
et assigné à l'attribut correspondant.

Je peux maitenant dresser la liste  des fonctionnalités de mon objet représentant une route : 

	-	Celle de savoir si la route correspond à l'URL
	-	Celle de savoir si la route possède des variables (utile, je le verrai, dans le routeur).
	
Pour résumer, voici le diagramme UML représentant mes classes :

Réf diag_projet.dia ==> Router
Réf diag_projet.dia ==> Route

Codons :

<?php 
// namespace OCFram;

class Router {
	protected $routes;
	
	const NO_ROUTE = 1;
	
	public function addRoute(Route $route) {
		if(!in_array($route, $this->routes))
			$routes[] = $route;
	}
	
	public function getRoute($url) {
		foreach($this->routes as $key => $route) {
			// Si la route correspond à l'URL
			if($varValues = $route->match($url)) {
				// Si elle a des variables
				if($route->hasVars()) {
					$vars = [];
					$varNames = $route->varNames();
					foreach($varValues as $key => $value) {
						// Une première valeur contient entièrement la chaîne capturée
						if($key) 
							$vars[$varNames[$key - 1]] = $value;
						
					}
					$route->setVars($vars);
				}
				
				return $route;				
			}	
		}
		
		throw new \RuntimeException('Aucune route ne correspond à l\'URL', self::NO_ROUTE);
	}
}?>

<?php 
//namespace OCFram;

class Route {
	protected $url;
	protected $module;
	protected $action;
	protected $varNames;
	protected $vars = [];
	
	public function __construct($url, $module, $action, array $varNames) {
		$this->setUrl($url);
		$this->setModule($module);
		$this->setAction($action);
		$this->setVarNames($varNames);
	}
	
	public function hasVars() {
		return !empty($this->varNames);
	}
	
	public function match($url) {
		if(preg_match('#^' . $this->url .'$#', $url, $matches))
			return $matches;
		else 
			return false;
	}
	
	public function setUrl($url) {
		if(is_string($url))
			$this->url = $url;
	}
	
	public function setAction($action) {
		if(is_string($action))
			$this->action = $action;
	}
	
	public function setModule($module) {
		if(is_string($module))
			$this->module = $module;
	}
	
	public function setVarNames(array $varNames) {
		$this->varNames = $varNames;
	}
	
	public function setVars(array $vars) {
		$this->vars = $vars;
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
}
?>

Tout cela est bien beau, mais il serait tout de même intéressant d'exploiter mon routeur afin de l'intégrer dans mon application.
Pour cela, je vais implémenter une méthode dans ma classe Application qui sera chargée de me donner le contrôleur correspondant
à l'URL. Pour cela, cette méthode va parcourir le fichier XML pour ajouter les routes au routeur. Ensuite, elle va
récupérer la route correspondante à l'URL (si une exception est levée, on lèvera une erreur 404). Enfin, la méthode instanciera
le contrôleur correspondant à la route et le renverra.

Réf Application ==> getController()

<h3>
III./ Le back controller
</h3>
Il est dans la logique des choses de construire maintenant le back controller de base.

1.) Réfléchissons, schématisons

J'ai vu que l'objet BackController n'offrait qu'une seule fonctionnalité : celle de s'exécuter. Mais quelles sont ses 
caractéristiques ? 
Je sais qu'une vue devrait être associée au back controller : ce sera donc l'une de ses caractéristiques. 

Maintenant, pensons à la nature d'un back controller. Celui-ci est propre à un <strong>module</strong>, et si on l'a instancié, 
c'est qu'on veut qu'il exécute une <strong>action</strong>. Cela fait donc deux autres caractéristiques : le module et l'action.

Enfin, il y en a une autre : la page associée au contrôleur. C'est à travers cette instance représentant la page envoyée par
la suite au visiteur que le contrôleur  transmettra des données à la vue. Pour l'instant, je dois juste mémoriser l'idée que
le contrôleur est associé à une page stockée en tant qu'instance dans un attribut de la classe BackController.

Une instance de BackController me permettra donc :

	-	D'exécuter une action (donc une méthode)
	-	D'obtenir la page associée au contrôleur
	-	De modifier le module, l'action et la vue associées au contrôleur
	
Cette classe est une classe de base dont héritera chaque contrôleur. Par conséquent, elle se doit d'être abstraite. Aussi,
il s'agit d'un <strong>composant</strong> de l'application, donc un lien de parenté avec ApplicationComponent est à créer. 

J'arrive donc à une classe ressemblant à cela :

Réf diag_projet.dia ==> BackController

Mon constructeur se chargera dans un premier temps d'appeler le constructeur de son parent. Dans un second temps, il créera une
instance de la classe  Page qu'il stockera dans l'attribut correspondant. Enfin, il assignera les valeurs au module, à l'action
et à la vue (par défaut, la vue a la même valeur que l'action).

Concernant la méthode execute(), comment fonctionnera-t-elle ? Son rôle est d'invoquer la méthode correspondant à l'action
assignée à mon objet. Le nom de la méthode suit une logique qui est de se nommer executeNomdelaction(). Par exemple, si j'ai
une action show sur mon module, je devrai implémenter la méthode executeShow() dans mon contrôleur. Aussi, pour une question
de simplicité, je passerai la requête du client à la méthode. En  effet, dans la plupart des cas, les méthodes auront besoin
de la requête du client pour obtenir une donnée (que ce soit une variable GET, POST ou un cookie).

Codons : 

<?php 
// namespace OCFram;

abstract class BackController extends ApplicationComponent {
	protected $action = '';
	protected $module = '';
	protected $page = null;
	protected $view = '';
	protected $managers = null;
	
	public function __construct(Application $app, $module, $action) {
		parent::__construct($app);
		
		$this->page = new Page($app);
		$this->managers = new Managers('PDO', PDOFactory::getMysqlConnection());
		
		$this->setModule($module);
		$this->setAction($action);
		$this->setView($action);
	}
	
	public function execute() {
		$method = 'execute' . ucfirst($this->action);
		if(!is_callable($this, $method)) 
			throw new \RuntimeException('L\'action '.$this->action.' n\'est pas définie dans ce module');
			
		$this->$method($this->app->httpRequest());
	}
	
	public function page() {
		return $this->page;
	}
	
	public function setModule($module) {
		if(!is_string($module)|| empty($module)) 
			throw new \InvalidArgumentException('Le module doit être une chaîne de caractères valide');
		
		$this->module = $module;
	}
	
	public function setAction($action) {
		if(!is_string($action) || empty($action))
			throw new \InvalidArgumentException('L\'action doit être une chaîne de caractères valide');
			
		$this->action = $action;
	}
	
	public function setView($view) {
		if(!is_string($view) || empty($view))
			throw new \InvalidArgumentException('La vue doit être une chaîne de caractères valide');
		
		$this->view = $view;
		$this->page->setContentFile(__DIR__.'/../../App/'.$this->app->name().'/Modules/'.$this->module.'/Views/'.$this->view.'.php');
	}
}?>

2.) Accéder aux managers depuis le contrôleur

Un petit souci se pose : comment le contrôleur accédera aux managers ? On pourrait les instancier directement dans la méthode,
mais les managers exigent le DAO lors de la construction de l'objet et ce DAO n'est pas accessible depuis le contrôleur. Je
vais donc créer une classe qui gérera les managers: classe que j'ai nommé Managers. J'instancierai donc cette classe au sein
de mon contrôleur en lui passant le DAO. Les méthodes filles auront accès à cet objet et pourront accéder aux managers facilement.

a./ Petit rappel sur la structure d'un manager

Un manager, comme on l'a fait pour le TP de news, est divisé en deux parties. La première partie est une classe abstraite
listant toutes les méthodes que le manager doit implémenter. La seconde partie est constituée de classes qui vont implémenter
ces méthodes, <strong>spécifiques à chaque DAO</strong>. Pour reprendre l'exemple de news, la première partie était constituée
de la classe abstraite NewsManager et la seconde partie était constituée de NewsManagerPDO et NewsManagerMySQLi.

En plus du DAO, il faudra donc spécifier à ma classe gérant ces managers l'API que l'on souhaite utiliser. Suivant ce qu'on lui
demande, ma classe me retournera une instance de NewsManagerPDO ou NewsManagerMySQLi par exemple. 

b./ La classe Managers

Schématiquement, voilà à quoi ressemble la classe Managers : réf diag_projet.dia

Cette instance de Managers sera stockée dans un attribut de l'objet BackController comme $managers par exemple. L'attribution
d'une intsance de managers à cet attribut se fait dans le constructeur de la manière suivante :

Réf BackController ==> $managers

Et voici le code de la classe Managers : 

<?php 
// namespace OCFram;
class Managers {
	protected $api = null;
	protected $dao = null;
	protected $managers = array();
	
	public function __construct($api, $dao) {
		$this->api = $api;
		$this->dao = $dao;
	}
	
	public function getManagerOf($module) {
		if(!is_string($module) || empty($module)) 
			throw new \InvalidArgumentException('Le module doit être une chaîne de caractères valide');
		if(!isset($this->managers[$module])) {
			$manager = '\\Model\\'.$module.'Manager'.$this->api;
			$this->managers[$module] = new $manager($this->dao);
		}
		
		return $this->managers[$module];
	}
}
?>

Et maintenant, comment je passe l'instance de PDO au constructeur de Managers ? 

Je ne l'instancie surtout pas directement car cela m'obligerait à modifier la classe BackController à chaque modification, ce
qui n'est pas très flexible. Il est donc recommandé d'utiliser le pattern factory et de créer une classe PDOFactory :

<?php 
// namespace OCFram;
class PDOFactory {
	public static function getMysqlConnexion() {
		$db = new PDO('mysql:host=localhost;dbname=mybdd', 'root', 'root');
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		return $db;
	}
}
?>

3.) A propos des managers

La classe Manager ainsi que la classe Entity seront des classes abstraites. La classe Manager se chargera d'implémenter
un constructeur qui demandera le DAO par le biais d'un paramètre, comme ceci :

<?php 
//namespace OCFram;

abstract class Manager {
	protected $dao;
	
	public function __construct($dao) {
		$this->dao = $dao;
	}
}
?>

Par contre, la classe Entity est légèrement plus complexe. En effet, celle-ci offre quelques fonctionnalités :

	-	Implémentation d'un constructeur qui hydratera l'objet si un tableau de valeurs lui est fourni. 
	-	Implémentation d'une méthode qui permet de vérifier si l'enregistrement est nouveau ou pas. Pour cela, on vérifie si
				l'attribut $id est vide ou non (ce qui inclut le fait que toutes les tables devront posséder un champ nommé id)
	-	Implémentation des getters / setters
	-	Implémentation de l'interface ArrayAccess (ce n'est pas obligatoire, mais cela permet d'utiliser l'objet comme un tableau	
				dans les vues)
				
Le code obtenu devrait s'apparenter à celui-ci : 

<?php 
//namespace OCFram;

abstract class Entity implements \ArrayAccess {
	protected $erreurs = [],
			  $id;
	
	public function __construct(array $donnees = []) {
		if(!empty($donnees))
			hydrate($donnees);
	}
	
	public function isNew() {
		return empty($this->id);
	}
	
	public function erreurs() {
		return $this->erreurs;
	}
	
	public function id() {
		return $this->id;
	}
	
	public function setId($id) {
		$this->id = (int) $id;
	}
	
	public function hydrate(array $donnees) {
		foreach($donnees as $key => $value) {
			$method = 'set'.ucfirst($value);
			if(is_callable([$this, $method]))
				$this->$method($value);
		}
	}
	
	public function offsetGet($var) {
		if(isset($this->$var) && is_callable([$this, $var]))
			return $this->$var();
	}
	
	public function offsetSet($var, $value) {
		$method = 'set'.ucfirst($var);
		if(isset($this->$var) && is_callable([$this, $method]))
			$this->$method($value);
	}
	
	public function offsetExists($var) {
		return isset($this->$var) && is_callable([$this, $var]);
	}
	
	public function offsetUnset($var) {
		throw new \Exception('Impossible de supprimer une quelconque valeur');
	}
}
?>

<h3>
IV./ La page
</h3>

Toujours dans la continuité du déroulement de l'application, je vais m'intéresser maintenant à la page qui, on vient de le voir,
était attachée à mon contrôleur.

1.) Réfléchissons, schématisons 

Commençons par nous intéresser aux fonctionnalités de notre classe Page. Une page est composée de la vue et du layout afin
de générer le tout. Ainsi, j'ai une première fonctionnalité : celle de <strong>générer</strong> une page. De plus, le contrôleur
doit pouvoir transmettre des variables à la vue, stockées dans cette page: <strong>ajouter une variable à la page</strong> est
donc une autre fonctionnalité. Enfin, la page doit savoir quelle vue elle doit générer pour l'ajouter ensuite au layout: il est
donc possible d'<strong>assigner une vue à la page</strong> (réf assigner_vue_page.png). 

Pour résumer, une instance de ma classe Page doit permettre :

	-	D'ajouter une variable à la page (le contrôleur aura besoin de passer des données à la vue)
	-	D'assigner une vue à la page
	-	De générer la page avec le layout de l'application
	
Avant de commencer à coder cette classe, voici le diagramme la représentant :
	
2.) Codons

La classe est, semble-t-il, plutôt facile à écrire. Cependant, on peut se demander comment écrire la méthode getGeneratedPage().
En fait, il faut inclure les pages pour générer leur contenu, et stocker ce contenu dans une variable grâce aux 
<a href="http://fr2.php.net/manual/fr/ref.outcontrol.php">fonctions de tamporisation de sortie</a> pour pouvoir s'en servir 
plus tard. Pour la transformation du tableau stocké dans l'attribut $vars en variables, il faut regarder du côté de la 
fonction <a href="http://fr2.php.net/manual/fr/function.extract.php">extract</a>.

<?php 
// namespace OCFram;

class Page extends ApplicationComponent {
	protected $contentFile,
			  $vars = [];
	
	public function addVar($var, $value) {
		if(!is_string($var) || is_numeric($var) || empty($var))
			throw new \InvalidArgumentException('Le nom de la variable doit être une chaîne de caractères non nulle');
		
		$this->vars[$var] = $value;
	}
	
	public function setContentFile($contentFile) {
		if(!is_string($contentFile) || empty($contentFile))
			throw new \InvalidArgumentException('La vue spécifiée est invalide');
		
		$this->contentFile = $contentFile;
	}
	
	public function getGeneratedPage() {
		if(!file_exists($this->contentFile))
			throw new \RuntimeException('La vue spécifiée n\'existe pas !');
		
		extract($this->vars);
		ob_start();
		   require $this->contentFile;
		$content = ob_get_clean();
		
		ob_start();
		   require __DIR__.'/../../App/'. $this->app->name().'/Templates/layout.php';
		return ob_get_clean();
	}
}
?>

3.) Retour sur la classe BackController

Maintenant que j'ai écrit la classe Page, je vais modifier la méthode setView($view) de la classe BackController, de façon
à informer la page concernée du changement de vue.

Réf BackController ==> setView($view)

4.) Retour sur la méthode HTTPResponse::redirect404()

Etant donné que j'ai compris comment fonctionne un objet Page, je peux écrire cette méthode laissée vide jusqu'à présent.

Réf HTTPResponse::redirect404()

<h3>
V./ Bonus: utilisateur
</h3>
Cette classe est un "bonus", ie elle n'est pas indispensable à l'application. Cependant, je vais m'en servir plus tard !

1.) Réflechissons, schématisons

L'utilisateur, cé quoi ? L'utilisateur est celui qui visite mon site. Comme tout site Web qui se respecte, j'ai besoin 
d'enregistrer temporairement l'utilisateur dans la mémoire du serveur afin de stocker les informations le concernant. Je 
créerai donc une <strong>session</strong> pour l'utilisateur (système de sessions quoi...). Ma classe, que je nommerai User,
devra me permettre de gérer facilement la session de l'utilisateur. Je pourrai donc, par le biais d'un objet User : 

	-	Assigner un attribut à l'utilisateur
	-	Obtenir la valeur d'un attribut
	-	Authentifier l'utilisateur (cela me sera utile lorsque je ferai un formulaire de connexion pour l'espace d'administration)
	-	Savoir si l'utilisateur est authentifié
	-	Assigner un message informatif à l'utilisateur que l'on affichera sur la page
	-	Savoir si l'utilisateur a un tel message
	-	Et enfin, récupérer ce message
	
Cela donne naissance à une classe de ce genre : 

Réf diag_projet.dia ==> User

2.) Codons

Avant de commencer à coder la classe, il faut que j'ajoute l'instruction invoquant session_start() au début du fichier, en dehors
de la classe. Ainsi, dès l'inclusion du fichier par l'autoload, la session démarrera et l'objet sera fonctionnel.

Ceci étant, voilà le code :

<?php 
// namespace OCFram;

session_start();

class User {
	public function getAttribute($attr) {
		return isset($_SESSION[$attr]) ? $_SESSION[$attr] : null;
	}
	
	public function setAttribute($attr, $value) {
		$_SESSION[$attr] = $value;
	}
	
	public function getFlash() {
		$flash = $_SESSION['flash'];
		unset($_SESSION['flash']);
		
		return $flash;
	}
	
	public function hasFlash() {
		return isset($_SESSION['flash']);
	}
	
	public function setFlash($value) {
		$_SESSION['flash'] = $value;
	}
	
	public function isAuthenticated() {
		return isset($_SESSION['auth']) && $_SESSION['auth'] === true;
	}
	
	public function setAuthenticated($authenticated = true) {
		if(!is_bool($autenticated))
			throw new \InvalidArgumentException('La valeur spécifiée à la méthode User::setAuthenticated() doit être un booléen');
		
		$_SESSION['auth'] = $authenticated;
	}
}
?>

Note : il faut penser à modifier ma classe Application afin d'ajouter un attribut $user et à créer l'objet User dans le constructeur
que je stockerai dans l'attribut créé.

<h3>
VI./ Bonus 2 : la configuration
</h3>
Cette classe est également un bonus dans la mesure où elle n'est pas essentielle pour que l'application fonctionne. 

1.) Réflechissons, schématisons

Tout site web bien conçu se doit d'être configurable à souhait. Par conséquent, il faut que chaque application possède un 
<strong>fichier de configuration</strong> déclarant les paramètres propres à ladite application. Par exemple, si je veux
afficher un nombre de news précis sur l'accueil, il serait préférable de spécifier un paramètre nombre_de_news à l'application
que je mettrai par exemple à 5 plutôt que d'insérer ce nombre en dur dans le code. De cette façon, j'aurai à modifier 
uniquement ce nombre dans le fichier de configuration pour faire varier le nombre de news sur la page d'accueil. 

a./ Un format pour le fichier

Le format de fichier sera le même que pour le fichier contenant les routes, à savoir le format XML. La base du fichier
sera celle-ci : 

&lt;?xml version="1.0" encoding="utf-8">
&lt;definitions>
&lt;/definitions>

Chaque paramètre se déclarera avec une balise define comme ceci :

&lt;define var="nombre_news" value="5" />

b./ Emplacement du fichier

Le fichier de configuration est propre à chaque application. Par conséquent, il devra être placé aux côtés du fichier routes.xml 
sous le doux nom de <strong>app.xml</strong>. Son chemin complet sera donc :

<strong>/App/Nomdelapplication/Config/app.xml</strong>

c./ Fonctionnement de la classe

J'aurai donc une classe s'occupant de gérer la configuration. Pour faire simple, je ne vais lui implémenter qu'une seule 
fonctionnalité : celle de récupérer un paramètre. Il faut également garder à l'esprit qu'il s'agit d'un <strong>composant
de l'application</strong>, donc il faut un lien de parenté avec ApplicationComponent.

La méthode get($var) (qui sera chargée de récupérer la valeur d'un paramètre) ne devra pas parcourir à chaque fois le fichier
de configuration, cela serait bien trop lourd. S'il s'agit du premier appel de la méthode, il faudra ouvrir le ficher XML
en instanciant la classe DOMDocument et stocker tous les paramètres dans un attribut (admettons $vars). Ainsi, à chaque fois que 
la méthode get() sera invoquée, je n'aurai qu'à retourner le paramètre précédemment enregistré.

Ma classe, plutôt  simple, ressemble à ceci : 

Réf diag_projet.dia ==> Config

2.) Codons 

<?php 
//namespace OCFram;

class Config extends ApplicationComponent {
	protected $vars = [];
	
	public function get($var) {
		if(!$this->vars) {
			$xml = new \DOMDocument();
			$xml->load(__DIR__.'/../../App/'.$this->app->name().'/Config/app.xml');
			$definitions = $xml->getElementsByTagName('define');
			foreach($definitions as $define) {
				$this->vars[$define->getAttribute('var')] = $define->getAttribute('value');
			}
		}
		
		if(isset($this->vars[$var]))
			return $this->vars[$var];
		
		return null;
	}
}?>

Note : il faut, comme pour la classe User, ajouter un nouvel attribut à ma classe Application qui stockera l'instance de Config.
</pre>
</body>
</html>