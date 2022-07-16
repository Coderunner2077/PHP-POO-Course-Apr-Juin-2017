<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>D�veloppement de la biblioth�que</title>
</head>
<body>
<pre>
                                                      D�veloppement de la bibilioth�que
                                                      
Le plus gros a �t� fait: je sais comment fonctionnera mon application, je sais o� je veux aller. Maintenant, il ne reste plus qu'� 
me servir de tout cela pour construire diagrammes UML qui vont lier mes classes, me permettant ainsi de les �crire facilement.

Voici le lien permettant de visualiser <a href="http://www.victorthuillier.com/oc/poo/tp_app/?f=Partie+III%2FChapitre+2" 
title="Visualiser les sch�mas du projet">l'ensemble du projet</a> en cas de besoin.

<h3>
I./ L'application
</h3>
1.) L'application

Commen�ons par construire notre classe Application. Celle-ci poss�de pour l'instant une seule fonctionnalit� : celle de s'ex�cuter.
Or, on n'a pas encore parl� des <strong>caract�ristiques</strong> de l'application, autrement dit les attributs. 

Le premier attribut est le <strong>nom</strong> de l'application.

Les deux autres sont la <strong>requ�te</strong> ainsi que la <strong>r�ponse</strong> envoy�e au client. Etudions donc tout d'abord
ces deux entit�s, avant de cr�er la classe Application.

2.) La requ�te du client
a./ Sch�matisons

Je vais repr�senter la requ�te du client au travers d'une instance de classe. Comme pour toute classe, int�ressons-nous aux 
fonctionnalit�s attendues. Qu'est-ce qui m'int�resse dans la requ�te du client ? Quelles fonctionnalit�s seraient int�ressantes ?
A partir de cette intstance, il serait pratique de pouvoir : 

	-	Obtenir une variable POST
	-	Obtenir une variable GET
	-	Obtenir un cookie
	-	Obtenir la m�thode employ�e pour envoyer la requ�te (m�thode GET ou POST)
	-	Obtenir l'URL entr�e (utile pour que le routeur connaisse la page souhait�e)
	
Et pour la route, voici un petit diagramme : r�f diag_projet.dia ==> HTTPRequest

b./ Codons

Cette classe, comme toute classe de mon framework, est � �crire dans un fichier situ� dans le dossier /lib/OCFram (donc, je cr�e
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
		return $_SERVER['REQUEST_METHOD']; // renvoie la m�thode de la requ�te (i.e. 'GET', 'POST', 'HEAD' ou 'PUT')
	}
	
	public function requestURI() {
		return $_SERVER['REQUEST_URI']; // renvoie l'url de la requ�te du client
	}	
}
?>

Un petit mot sur la toute premi�re ligne, celle qui contient la d�claration du namespace. Ou plus t�t un rappel: toutes les classes
de mon projet sont d�clar�es dans des namespaces. Cela permet d'une part de structurer mon projet, et d'autre part, d'�crire un
autoload simple qui sait directement, gr�ce au namespace contenant la classe, le chemin du fichier contenant ladite classe.

Par exemple, si j'ai un contr�leur du module de news. Celui-ci sera plac� dans le dossier /App/Frontend/Modules/News. La classe
repr�sentant ce contr�leur (NewsController) sera donc dans le namespace App\Frontend\Modules\News !

Attention : Un fichier contenant un espace de noms doit d�clarer l'espace de noms au d�but du fichier, avant tout autre code, 
avec une seule exception : le mot cl� declare

3.) La r�ponse envoy�e au client
a./ Sch�matisons

L� aussi, je vais repr�senter la r�ponse envoy�e au client au travers d'une entit�. Cette entit� n'est autre qu'une instance
d'une classe. Quelles fonctionnalit�s aura cette classe ? Que veut-on envoyer au visiteur ? La r�ponse la plus �vidente est
la <strong>page</strong>. Je veux assiger une <strong>page</strong> � la r�ponse. Cependant, il est bien beau d'assigner une
page, encore faudrait-il pouvoir l'envoyer ! Voici une deuxi�me fonctionnalit� : celle <strong>d'envoyer</strong> la r�ponse
en <strong>g�n�rant</strong> la page. 

Il existe de nombreuses autres fonctionnalit�s "accessoires", comme par exemple celle de pouvoir rediriger le visiteur vers une
page 404, lui �crire un cookie et d'ajouter un header sp�cifique. Pour r�sumer, ma classe me permettra : 

	-	D'assigner une page � la r�ponse
	-	D'envoyer la r�ponse en g�n�rant la page
	-	De rediriger l'utilisateur
	-	De le rediriger vers une erreur 404
	-	D'ajouter un cookie
	-	D'ajouter un header sp�cifique
	
R�f diag_projet.dia ==> HTTTResponse

b./ Codons

<?php 
//namespace OCFram;

class HTTPResponse extends ApplicationComponent {
	protected $page;
	
	public function setPage(Page $page) {
		$this->page = $page;
	}
	
	public function send() {
		exit($this->page->getGeneratedPage()); // cette ligne sera expliqu�e plus tard
		// exit affiche un message (si sp�cifi�) et termine le script courant
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
		// On a fait en sorte que le dernier param�tre soit � true
	}
	
}
?>

4.) Retour sur mon application
a./ Sch�matisons

Maintenant que j'ai vu comment sont repr�sent�es la requ�te du client et la r�ponse que l'on va lui envoyer, je peux r�flechir
pleinement sur ce qui compose ma classe. Elle poss�de une fonctionnalit� (celle de s'ex�cuter) et trois caract�ristiques : son
nom, la requ�te du client et la r�ponse que l'on va lui envoyer.

Il y a au moins deux classes qui h�riteront de cette classe Application, cela n'a donc aucun sens d'instancier cette classe. Par
cons�quent, cette classe sera abstraite. 

R�f diag_projet.dia ==> Application

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
			
			// On regarde si des variables sont pr�sentes dans l'URL
			if($route->hasAttribute('vars'))
				$vars = explode(',', $route->getAttribute('vars'));
				
			// On ajoute la route au routeur
			$router->addRoute(new Route($route->getAttribute('url'), $route->getAttribute('module'), $route->getAttribute('action'),
										$vars));
			try {
				// On ajoute la route correspondante � l'URL
				$matchedRoute = $router->getRoute($this->httpRequest->requestURI());
			} catch(\RuntimeException $e) {
				if($e->getCode() == Router::NO_ROUTE)
					//Si aucune route ne correspond, c'est que la page demand�e n'existe pas
					$this->httpResponse->redirect404();
			}
		}
		
		// On ajoute les variables de l'URL au tableau $_GET
		$_GET = array_merge($_GET, $matchedRoute->vars());
		
		// On instancie le contr�leur
		$controllerClass = 'App\\'.$this->name.'\\Modules\\'. $matchedRoute->module().'\\'.$matchedRoute->module().'Controller';
		return new $controllerClass($this, $matchedRoute->module(), $matchedRoute->action());
	}
}
?>
	
Dans le constructeur, on assigne une cha�ne de caract�res vide � l'attribut name. En fait, chaque application (qui h�ritera donc
de cette classe) sera charg�e de sp�cifier son nom en initialisant cet attribut.

5.) Les composants de l'application

Les deux premi�res classes (comme la plupart des classes que je vais cr�er) sont des <strong>composantes de l'application</strong>.
Toutes ces classes ont donc une nature en commun et doivent h�riter d'une m�me classe repr�sentant cette nature: classe que 
je nommerai ApplicationComponent.

Que me permettra de faire cette classe ? 

D'obtenir l'application � laquelle l'objet appartient. C'est tout !
Cette classe se chargera juste de stocker, pendant la construction de l'objet, l'instance de l'application ex�cut�e. J'ai donc
une simple classe ressemblant � ceci :

R�f diag_projet.dia ==> ApplicationComponent

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

Note : il faut penser � ajouter le lien de parent� aux classe HTTPRequest et HTTPResponse. Et aussi, passer l'instance de 
l'application lors de l'instanciation de ces deux classes dans le constructeur de Application.

<h3>
II./ Le routeur
</h3>
1.) Sch�matisons

Comme on l'a vu, le routeur est l'objet qui va me permettre de savoir quelle page je dois ex�cuter. Pour en �tre capable, le
routeur aura � sa disposition des <strong>routes</strong> pointant chacune vers un module et une action.

Rappel : une route, c'est une URL associ�e � un module et une action. Cr�er une route signifie donc assigner un module et une
action � une URL. 

La question que l'on se pose alors est : <strong>o� seront �crites les routes ?</strong> On peut �tre tent� de les �crire 
directement � l'int�rieur de la classe Routeur et faire une sorte de switch / case sur les routes pour trouver laquelle correspond �
l'URL. Cette fa�on de faire pr�sente un �norme inconv�nient : ma classe repr�sentant le routeur sera <strong>d�pendante</strong>   
du projet que je d�veloppe. Par cons�quent, je ne pourrais plus l'utiliser sur un autre site ! Il va donc falloir 
<strong>externaliser</strong>  ces d�finitions de routes.

Comment pourrait-on faire alors ? Je vais tout simplement placer ces routes dans un autre fichier. Ce fichier doit �tre plac�
dans le dossier de l'application concern�e, et puisque �a touche � la configuration de celle-ci, je le placerai dans un sous-dossier
Config. Il y a aussi un d�tail � r�gler : dans quel format vais-je �crire le fichier ? Un choix optimal, c'est le format XML car
ce langage est intuitif et simple � parser, notamment gr�ce � la biblioth�que native <a href="http://fr2.php.net/manual/fr/class.domdocument.php">
DOMDocument</a> de PHP. Le chemin complet vers ce fichier devient donc : 

<strong>/App/Nomdelapplication/Config/routes.xml</strong>

Comme pour tout fichier XML qui se respecte, celui-ci doit suivre une structure pr�cise ==>

&lt;?xml version="1.0" encoding="utf-8" ?>
&lt;routes>
	&lt;route url="/news.html" module="News" action="index">&lt;/route>
&lt;/routes>

Le r�le de la troisi�me ligne consiste � permettre au routeur d'�tablir, lorsqu'on va sur la page <strong>news.html</strong>,
que le client veut acc�der au module <strong>News</strong> et ex�cuter l'action <strong>index</strong>.

Un autre probl�me se pose. Par exemple, si je veux afficher une news sp�cifique en fonction de son identifiant, que faire ? Ou,
plus g�n�ralement, comment passer des variables GET ? L'id�al serait d'utiliser des expressions r�guli�res en guise d'URL. Chaque
paire de parenth�ses repr�sentera une variable GET. Je sp�cifierai leur nom dans un quatri�me attribut vars.

Voici le contenu de la balise &lt;route> revisit� :

&lt;route url="/news-(.+)-([0-9]+)\.html" module="News" action="show" vars="slug,id" />

Ainsi, toute URL v�rifiant cette expression pointera vers le module News et ex�cutera l'action show(). Les variables
$_GET['slug'] et $_GET['id'] seront cr��es et auront pour valeur le contenu des parenth�ses capturantes.

On sait d�sormais que notre routeur a besoin de routes pour nous renvoyer celle qui correspond � l'URL. Cependant, s'il a
besoin de routes, il va falloir les lui donner !

Pourquoi ne peut-il pas aller les chercher lui-m�me ?

S'il allait les chercher lui-m�me, ma classe serait d�pendante de l'architecture de l'application. Si je voulais utiliser ma
classe dans un autre projet, je ne pourrais pas, car le fichier contenat les routes (/App/Nomdelappli/Config/routes.xml) 
n'existerait tout simplement pas. De plus, dans ce projet, les routes ne seront peut-�tre pas stock�es dans un fichier XML,
donc le parsage ne se fera peut-�tre pas de la m�me fa�on. Or, l'un des points forts de la POO est la r�utilisabilit�. Ainsi,
ma classe repr�sentant le routeur ne d�pendra ni d'une architecture, ni du format du fichier stockant les routes.

De cette fa�on, ma classe Routeur pr�sente deux foncionnalit�s : 

	-	Celle d'ajouter une route � sa liste de routes
	-	Celle de renvoyer la route correspondante � l'URL.
	
Avec, bien entendu, une caract�ristique : la liste des routes attach�e au routeur. 
Cependant, une autre question se pose : je disais qu'on "passe une route" au routeur.  Ce qui ne peut raisonnablement se faire que
si la route est un objet.

Qu'est-ce qui caract�rise l'objet Route : 

	-	Une URL
	-	Un module
	-	Une action
	-	Un tableau comportant les noms des variables
	-	Un tableau cl�/valeur comportant les noms/valeurs des variables
	
Quelle diff�rence entre les deux derni�res caract�ristiques ? 

En fait, lorsque je cr�e les routes, je vais assigner les quatre premi�res caract�ristiques. C'est donc cette derni�re liste
de variables que je vais assigner � ma route. Ensuite, mon routeur va parcourir ces routes et c'est lui qui assignera les
valeurs des variables. C'est donc � ce moment-l� que le tableau comportant les nom/valeurs des variables sera cr��
et assign� � l'attribut correspondant.

Je peux maitenant dresser la liste  des fonctionnalit�s de mon objet repr�sentant une route : 

	-	Celle de savoir si la route correspond � l'URL
	-	Celle de savoir si la route poss�de des variables (utile, je le verrai, dans le routeur).
	
Pour r�sumer, voici le diagramme UML repr�sentant mes classes :

R�f diag_projet.dia ==> Router
R�f diag_projet.dia ==> Route

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
			// Si la route correspond � l'URL
			if($varValues = $route->match($url)) {
				// Si elle a des variables
				if($route->hasVars()) {
					$vars = [];
					$varNames = $route->varNames();
					foreach($varValues as $key => $value) {
						// Une premi�re valeur contient enti�rement la cha�ne captur�e
						if($key) 
							$vars[$varNames[$key - 1]] = $value;
						
					}
					$route->setVars($vars);
				}
				
				return $route;				
			}	
		}
		
		throw new \RuntimeException('Aucune route ne correspond � l\'URL', self::NO_ROUTE);
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

Tout cela est bien beau, mais il serait tout de m�me int�ressant d'exploiter mon routeur afin de l'int�grer dans mon application.
Pour cela, je vais impl�menter une m�thode dans ma classe Application qui sera charg�e de me donner le contr�leur correspondant
� l'URL. Pour cela, cette m�thode va parcourir le fichier XML pour ajouter les routes au routeur. Ensuite, elle va
r�cup�rer la route correspondante � l'URL (si une exception est lev�e, on l�vera une erreur 404). Enfin, la m�thode instanciera
le contr�leur correspondant � la route et le renverra.

R�f Application ==> getController()

<h3>
III./ Le back controller
</h3>
Il est dans la logique des choses de construire maintenant le back controller de base.

1.) R�fl�chissons, sch�matisons

J'ai vu que l'objet BackController n'offrait qu'une seule fonctionnalit� : celle de s'ex�cuter. Mais quelles sont ses 
caract�ristiques ? 
Je sais qu'une vue devrait �tre associ�e au back controller : ce sera donc l'une de ses caract�ristiques. 

Maintenant, pensons � la nature d'un back controller. Celui-ci est propre � un <strong>module</strong>, et si on l'a instanci�, 
c'est qu'on veut qu'il ex�cute une <strong>action</strong>. Cela fait donc deux autres caract�ristiques : le module et l'action.

Enfin, il y en a une autre : la page associ�e au contr�leur. C'est � travers cette instance repr�sentant la page envoy�e par
la suite au visiteur que le contr�leur  transmettra des donn�es � la vue. Pour l'instant, je dois juste m�moriser l'id�e que
le contr�leur est associ� � une page stock�e en tant qu'instance dans un attribut de la classe BackController.

Une instance de BackController me permettra donc :

	-	D'ex�cuter une action (donc une m�thode)
	-	D'obtenir la page associ�e au contr�leur
	-	De modifier le module, l'action et la vue associ�es au contr�leur
	
Cette classe est une classe de base dont h�ritera chaque contr�leur. Par cons�quent, elle se doit d'�tre abstraite. Aussi,
il s'agit d'un <strong>composant</strong> de l'application, donc un lien de parent� avec ApplicationComponent est � cr�er. 

J'arrive donc � une classe ressemblant � cela :

R�f diag_projet.dia ==> BackController

Mon constructeur se chargera dans un premier temps d'appeler le constructeur de son parent. Dans un second temps, il cr�era une
instance de la classe  Page qu'il stockera dans l'attribut correspondant. Enfin, il assignera les valeurs au module, � l'action
et � la vue (par d�faut, la vue a la m�me valeur que l'action).

Concernant la m�thode execute(), comment fonctionnera-t-elle ? Son r�le est d'invoquer la m�thode correspondant � l'action
assign�e � mon objet. Le nom de la m�thode suit une logique qui est de se nommer executeNomdelaction(). Par exemple, si j'ai
une action show sur mon module, je devrai impl�menter la m�thode executeShow() dans mon contr�leur. Aussi, pour une question
de simplicit�, je passerai la requ�te du client � la m�thode. En  effet, dans la plupart des cas, les m�thodes auront besoin
de la requ�te du client pour obtenir une donn�e (que ce soit une variable GET, POST ou un cookie).

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
			throw new \RuntimeException('L\'action '.$this->action.' n\'est pas d�finie dans ce module');
			
		$this->$method($this->app->httpRequest());
	}
	
	public function page() {
		return $this->page;
	}
	
	public function setModule($module) {
		if(!is_string($module)|| empty($module)) 
			throw new \InvalidArgumentException('Le module doit �tre une cha�ne de caract�res valide');
		
		$this->module = $module;
	}
	
	public function setAction($action) {
		if(!is_string($action) || empty($action))
			throw new \InvalidArgumentException('L\'action doit �tre une cha�ne de caract�res valide');
			
		$this->action = $action;
	}
	
	public function setView($view) {
		if(!is_string($view) || empty($view))
			throw new \InvalidArgumentException('La vue doit �tre une cha�ne de caract�res valide');
		
		$this->view = $view;
		$this->page->setContentFile(__DIR__.'/../../App/'.$this->app->name().'/Modules/'.$this->module.'/Views/'.$this->view.'.php');
	}
}?>

2.) Acc�der aux managers depuis le contr�leur

Un petit souci se pose : comment le contr�leur acc�dera aux managers ? On pourrait les instancier directement dans la m�thode,
mais les managers exigent le DAO lors de la construction de l'objet et ce DAO n'est pas accessible depuis le contr�leur. Je
vais donc cr�er une classe qui g�rera les managers: classe que j'ai nomm� Managers. J'instancierai donc cette classe au sein
de mon contr�leur en lui passant le DAO. Les m�thodes filles auront acc�s � cet objet et pourront acc�der aux managers facilement.

a./ Petit rappel sur la structure d'un manager

Un manager, comme on l'a fait pour le TP de news, est divis� en deux parties. La premi�re partie est une classe abstraite
listant toutes les m�thodes que le manager doit impl�menter. La seconde partie est constitu�e de classes qui vont impl�menter
ces m�thodes, <strong>sp�cifiques � chaque DAO</strong>. Pour reprendre l'exemple de news, la premi�re partie �tait constitu�e
de la classe abstraite NewsManager et la seconde partie �tait constitu�e de NewsManagerPDO et NewsManagerMySQLi.

En plus du DAO, il faudra donc sp�cifier � ma classe g�rant ces managers l'API que l'on souhaite utiliser. Suivant ce qu'on lui
demande, ma classe me retournera une instance de NewsManagerPDO ou NewsManagerMySQLi par exemple. 

b./ La classe Managers

Sch�matiquement, voil� � quoi ressemble la classe Managers : r�f diag_projet.dia

Cette instance de Managers sera stock�e dans un attribut de l'objet BackController comme $managers par exemple. L'attribution
d'une intsance de managers � cet attribut se fait dans le constructeur de la mani�re suivante :

R�f BackController ==> $managers

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
			throw new \InvalidArgumentException('Le module doit �tre une cha�ne de caract�res valide');
		if(!isset($this->managers[$module])) {
			$manager = '\\Model\\'.$module.'Manager'.$this->api;
			$this->managers[$module] = new $manager($this->dao);
		}
		
		return $this->managers[$module];
	}
}
?>

Et maintenant, comment je passe l'instance de PDO au constructeur de Managers ? 

Je ne l'instancie surtout pas directement car cela m'obligerait � modifier la classe BackController � chaque modification, ce
qui n'est pas tr�s flexible. Il est donc recommand� d'utiliser le pattern factory et de cr�er une classe PDOFactory :

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

La classe Manager ainsi que la classe Entity seront des classes abstraites. La classe Manager se chargera d'impl�menter
un constructeur qui demandera le DAO par le biais d'un param�tre, comme ceci :

<?php 
//namespace OCFram;

abstract class Manager {
	protected $dao;
	
	public function __construct($dao) {
		$this->dao = $dao;
	}
}
?>

Par contre, la classe Entity est l�g�rement plus complexe. En effet, celle-ci offre quelques fonctionnalit�s :

	-	Impl�mentation d'un constructeur qui hydratera l'objet si un tableau de valeurs lui est fourni. 
	-	Impl�mentation d'une m�thode qui permet de v�rifier si l'enregistrement est nouveau ou pas. Pour cela, on v�rifie si
				l'attribut $id est vide ou non (ce qui inclut le fait que toutes les tables devront poss�der un champ nomm� id)
	-	Impl�mentation des getters / setters
	-	Impl�mentation de l'interface ArrayAccess (ce n'est pas obligatoire, mais cela permet d'utiliser l'objet comme un tableau	
				dans les vues)
				
Le code obtenu devrait s'apparenter � celui-ci : 

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

Toujours dans la continuit� du d�roulement de l'application, je vais m'int�resser maintenant � la page qui, on vient de le voir,
�tait attach�e � mon contr�leur.

1.) R�fl�chissons, sch�matisons 

Commen�ons par nous int�resser aux fonctionnalit�s de notre classe Page. Une page est compos�e de la vue et du layout afin
de g�n�rer le tout. Ainsi, j'ai une premi�re fonctionnalit� : celle de <strong>g�n�rer</strong> une page. De plus, le contr�leur
doit pouvoir transmettre des variables � la vue, stock�es dans cette page: <strong>ajouter une variable � la page</strong> est
donc une autre fonctionnalit�. Enfin, la page doit savoir quelle vue elle doit g�n�rer pour l'ajouter ensuite au layout: il est
donc possible d'<strong>assigner une vue � la page</strong> (r�f assigner_vue_page.png). 

Pour r�sumer, une instance de ma classe Page doit permettre :

	-	D'ajouter une variable � la page (le contr�leur aura besoin de passer des donn�es � la vue)
	-	D'assigner une vue � la page
	-	De g�n�rer la page avec le layout de l'application
	
Avant de commencer � coder cette classe, voici le diagramme la repr�sentant :
	
2.) Codons

La classe est, semble-t-il, plut�t facile � �crire. Cependant, on peut se demander comment �crire la m�thode getGeneratedPage().
En fait, il faut inclure les pages pour g�n�rer leur contenu, et stocker ce contenu dans une variable gr�ce aux 
<a href="http://fr2.php.net/manual/fr/ref.outcontrol.php">fonctions de tamporisation de sortie</a> pour pouvoir s'en servir 
plus tard. Pour la transformation du tableau stock� dans l'attribut $vars en variables, il faut regarder du c�t� de la 
fonction <a href="http://fr2.php.net/manual/fr/function.extract.php">extract</a>.

<?php 
// namespace OCFram;

class Page extends ApplicationComponent {
	protected $contentFile,
			  $vars = [];
	
	public function addVar($var, $value) {
		if(!is_string($var) || is_numeric($var) || empty($var))
			throw new \InvalidArgumentException('Le nom de la variable doit �tre une cha�ne de caract�res non nulle');
		
		$this->vars[$var] = $value;
	}
	
	public function setContentFile($contentFile) {
		if(!is_string($contentFile) || empty($contentFile))
			throw new \InvalidArgumentException('La vue sp�cifi�e est invalide');
		
		$this->contentFile = $contentFile;
	}
	
	public function getGeneratedPage() {
		if(!file_exists($this->contentFile))
			throw new \RuntimeException('La vue sp�cifi�e n\'existe pas !');
		
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

Maintenant que j'ai �crit la classe Page, je vais modifier la m�thode setView($view) de la classe BackController, de fa�on
� informer la page concern�e du changement de vue.

R�f BackController ==> setView($view)

4.) Retour sur la m�thode HTTPResponse::redirect404()

Etant donn� que j'ai compris comment fonctionne un objet Page, je peux �crire cette m�thode laiss�e vide jusqu'� pr�sent.

R�f HTTPResponse::redirect404()

<h3>
V./ Bonus: utilisateur
</h3>
Cette classe est un "bonus", ie elle n'est pas indispensable � l'application. Cependant, je vais m'en servir plus tard !

1.) R�flechissons, sch�matisons

L'utilisateur, c� quoi ? L'utilisateur est celui qui visite mon site. Comme tout site Web qui se respecte, j'ai besoin 
d'enregistrer temporairement l'utilisateur dans la m�moire du serveur afin de stocker les informations le concernant. Je 
cr�erai donc une <strong>session</strong> pour l'utilisateur (syst�me de sessions quoi...). Ma classe, que je nommerai User,
devra me permettre de g�rer facilement la session de l'utilisateur. Je pourrai donc, par le biais d'un objet User : 

	-	Assigner un attribut � l'utilisateur
	-	Obtenir la valeur d'un attribut
	-	Authentifier l'utilisateur (cela me sera utile lorsque je ferai un formulaire de connexion pour l'espace d'administration)
	-	Savoir si l'utilisateur est authentifi�
	-	Assigner un message informatif � l'utilisateur que l'on affichera sur la page
	-	Savoir si l'utilisateur a un tel message
	-	Et enfin, r�cup�rer ce message
	
Cela donne naissance � une classe de ce genre : 

R�f diag_projet.dia ==> User

2.) Codons

Avant de commencer � coder la classe, il faut que j'ajoute l'instruction invoquant session_start() au d�but du fichier, en dehors
de la classe. Ainsi, d�s l'inclusion du fichier par l'autoload, la session d�marrera et l'objet sera fonctionnel.

Ceci �tant, voil� le code :

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
			throw new \InvalidArgumentException('La valeur sp�cifi�e � la m�thode User::setAuthenticated() doit �tre un bool�en');
		
		$_SESSION['auth'] = $authenticated;
	}
}
?>

Note : il faut penser � modifier ma classe Application afin d'ajouter un attribut $user et � cr�er l'objet User dans le constructeur
que je stockerai dans l'attribut cr��.

<h3>
VI./ Bonus 2 : la configuration
</h3>
Cette classe est �galement un bonus dans la mesure o� elle n'est pas essentielle pour que l'application fonctionne. 

1.) R�flechissons, sch�matisons

Tout site web bien con�u se doit d'�tre configurable � souhait. Par cons�quent, il faut que chaque application poss�de un 
<strong>fichier de configuration</strong> d�clarant les param�tres propres � ladite application. Par exemple, si je veux
afficher un nombre de news pr�cis sur l'accueil, il serait pr�f�rable de sp�cifier un param�tre nombre_de_news � l'application
que je mettrai par exemple � 5 plut�t que d'ins�rer ce nombre en dur dans le code. De cette fa�on, j'aurai � modifier 
uniquement ce nombre dans le fichier de configuration pour faire varier le nombre de news sur la page d'accueil. 

a./ Un format pour le fichier

Le format de fichier sera le m�me que pour le fichier contenant les routes, � savoir le format XML. La base du fichier
sera celle-ci : 

&lt;?xml version="1.0" encoding="utf-8">
&lt;definitions>
&lt;/definitions>

Chaque param�tre se d�clarera avec une balise define comme ceci :

&lt;define var="nombre_news" value="5" />

b./ Emplacement du fichier

Le fichier de configuration est propre � chaque application. Par cons�quent, il devra �tre plac� aux c�t�s du fichier routes.xml 
sous le doux nom de <strong>app.xml</strong>. Son chemin complet sera donc :

<strong>/App/Nomdelapplication/Config/app.xml</strong>

c./ Fonctionnement de la classe

J'aurai donc une classe s'occupant de g�rer la configuration. Pour faire simple, je ne vais lui impl�menter qu'une seule 
fonctionnalit� : celle de r�cup�rer un param�tre. Il faut �galement garder � l'esprit qu'il s'agit d'un <strong>composant
de l'application</strong>, donc il faut un lien de parent� avec ApplicationComponent.

La m�thode get($var) (qui sera charg�e de r�cup�rer la valeur d'un param�tre) ne devra pas parcourir � chaque fois le fichier
de configuration, cela serait bien trop lourd. S'il s'agit du premier appel de la m�thode, il faudra ouvrir le ficher XML
en instanciant la classe DOMDocument et stocker tous les param�tres dans un attribut (admettons $vars). Ainsi, � chaque fois que 
la m�thode get() sera invoqu�e, je n'aurai qu'� retourner le param�tre pr�c�demment enregistr�.

Ma classe, plut�t  simple, ressemble � ceci : 

R�f diag_projet.dia ==> Config

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

Note : il faut, comme pour la classe User, ajouter un nouvel attribut � ma classe Application qui stockera l'instance de Config.
</pre>
</body>
</html>