<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Les exceptions</title>
</head>
<body>
<pre>
												Les design patterns
												
Les design patterns sont des solutions inventées par les développeurs pour pallier différents problèmes de conception. Je ne 
reverrai pas ici le pattern observer, ni le pattern strategy (en cas de besoin, revoir le cours sur Java), mais j'en verrai d'autres,
nouveaux pour moi.
Par ailleurs, je découvrirai les classes anonymes en PHP. 

I./ Laisser une classe créer les objets : le pattern Factory
1.)Le problème

Admettons que je viens de créer une application relativement importante. J'ai construit cette application en associant plus ou
moins la plupart de mes classes entre elles. A présent, je souhaite modifier un petit morceau de code afin d'ajouter une 
fonctionnalité à l'application. 
Problème: étant donné que la plupart de mes classes sont plus ou moins liées, il va falloir modifier un tas de choses ! C'est 
là que le pattern Factory peut se revéler utile. 

Ce motif est très simple à construire. En fait, si j'implémente ce pattern, je n'aurai plus de new à placer dans la partie 
globale du script afin d'intancier une classe. En effet, ce ne sera pas à moi de le faire mais à une <strong>classe usine</strong>.
Cette classe aura pour rôle de charger les classes que je lui passe en argument. Ainsi, quand je modifierai mon code, je 
n'aurai qu'à modifier le masque d'usine pour que la plupart des modifications prennent effet. En gros, je ne me soucierai plus
de l'instanciation de mes classes, ce sera à l'usine de le faire.

Voici comment se présente une classe implémentant le pattern Factory :

&lt;?php 
class DBFactory {
	public static function load($sgbdr) {
		$classe = 'SGBDR_' . $sgbdr;
		
		if(file_exists($chemin = $classe . '.class.php')) {
			require $chemin;
			return new $classe;
		} 
		else 
			throw new Exception('La classe <strong>' .$classe . '</strong> n\'a pas pu être trouvée');
	}
}
?>

Dans mon script, je pourrai donc faire quelque chose de ce genre : 

<?php 
try {
	$mysql = DBFactory::load('MySQL');
} catch(Exception $e) {
	echo $e->getMessage();
}
?>

2.) Exemple concret 

Le but est de créer une classe qui distribuera les objets PDO facilement. Je vais partir du principe que j'ai plusieurs SGBDR, ou
plusieurs BDD qui utilisent des identifiants différents. Pour résumer, je vais tout centraliser dans une classe. 

<?php 
class DBFactory {
	public static function getMysqlConnexion() {
		$db = new PDO('mysql:host=localhost;dbname=tests', 'root', 'root');
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		return $db;
	}
	
	public static function getPgsqlConnexion() {
		$db = new PDO('pgsql:host=localhost;dbname=tests', 'root', 'root');
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		return $db;
	}
}
?>

Ceci me simplefiera énormément la tâche. Si j'ai besoin de modifier mes identifiants de connexion, je n'aurai pas à aller chercher
dans tous mes scripts : tout sera placé dans mon factory !

II./ L'injection de dépendances

Comme tout pattern, celui-ci est né à cause d'un problème souvent rencontré par les développeurs : le fait d'avoir de nombreuses
classes dépendantes les unes des autres. L'injection de dépendances consiste à découpler mes classes. Le pattern singleton,
lui, favorise les dépendances, et l'injection de dépendances palliant ce problème, il est intéressant d'étudier ce nouveau 
pattern tout en mettant en évidence les limites du pattern singleton.

Soit le code suivant : 

&lt;?php 
class NewsManager {
	public function get($id) {
		// On admet que MyPDO étend PDO et qu'il implémente un singleton
		$q = MyPDO::getInstance()->query('SELECT id, auteur, titre, contenu FROM news WHERE id = '.(int)$id);
		
		return $q->fetch(PDO::FETCH_ASSOC);
	}
}
?>

Je m'aperçois qu'ici, le singleton a introduit une dépendance entre deux classes n'appartenant pas au même module. Deux modules
ne doivent jamais être liés de cette façon, ce qui est le cas dans cet exemple. Deux modules doivent être indépendants l'un de
l'autre. D'ailleurs, en y regardant de plus près, cela ressemble fortement à une variable globale. En effet, un singleton n'est
jamais rien d'autre qu'une variable globale déguisée (il y a juste une étape en plus pour accéder à la variable) : 

&lt;?php 
class NewsManager {
	public function get($id) {
		global $db;
		// revient EXACTEMENT au même que :
		$db = MyPDO::getInstance();
		
		// suite des opérations...
	}
}
?>
Effectivement, l'un des poins forts de la POO étant le fait de pouvoir redistribuer sa classe et la réutiliser, cela est justement
impossible dans le cas présent parce que ma classe NewsManager dépend de MyPDO. Qu'est-ce qui me dit que la personne utilisant
NewsManager aura cette dernière ? Rien du tout, et c'est normal. On est ici face à une dépendance créée par le singleton. De plus,
la classe dépend aussi de PDO : il y avait donc déjà une dépendance au début, et le pattern Singleton en a créé une autre. Il faut
donc supprimer ces deux dépendances. 

Comment fait-on alors ?

Ce qu'il faut, c'est passer mon DAO au constructeur, sauf que ma classe ne doit pas être dépendante d'une quelconque bibliothèque.
Ainsi, mon objet peut très bien utiliser PDO, MySQLi ou que sait-je encore, la classe se servant de lui doit fonctionner de la
même manière. Alors, comment procéder ? Il faut imposer un <strong>comportement spécifique à mon objet</strong> en l'obligeant
à implémenter certaines méthodes. Justement, les interfaces sont là pour ça. Je vais donc créer une interface iDB ne contenant
(pour faire simple) qu'une seule méthode : query()

<?php 
interface iDB {
	public function query($query);
}
?>

Pour que l'exemple soit parlant, je vais créer deux classes utilisant cette structure, l'une utilisant PDO et l'autre MySQLi.
Cependant, un problème se pose : le résultat retourné par la méthode query() des classes PDO et MySQLi sont des instances de
deux classes différentes, les méthodes disponibles ne sont, par conséquent, pas les mêmes. Il va donc falloir créer d'autres
classes pour gérer les résultats qui suivent eux aussi une structure définie par une interface (admettons iResult). 

<?php 
interface iResult {
	public function fetchAssoc();
}
?>

Je peux donc à présent écrire mes quatre classes : MyPDO, MyMySQLi, MyPDOStatement et MyMySQLiResult :

<?php 
class MyPDO extends PDO implements iDB {
	public function query($query) {
		return new MyPDOStatement(parent::query($query));
	}
}

class MyPDOStatement implements iResult {
	protected $st;
	public function __construct(PDOStatement $st) {
		$this->st = $st;
	}
	public function fetchAssoc() {
		return $this->st->fetch(PDO::FETCH_ASSOC);
	}
}

class MyMySQLi extends MySQLi implements iDB {
	public function query($query) {
		return new MyMySQLiResult(parent::query($query));
	}
}

class MyMySQLiResult implements iResult {
	protected $st;
	public function __construct(MySQLi_Result $st) {
		$this->st = $st;
	}
	
	public function fetchAssoc() {
		return $this->st->fetch_assoc();
	}
}

?>

Je peux donc maintenant écrire ma classe NewsManager. Attention à ne pas oublier de vérifier que les objets sont bien des 
instances de classes implémentant les interfaces désirées !

<?php 
class NewsManager {
	protected $dao;
	
	// On souhaite un objet instanciant une classe qui implémente iDB
	public function __construct(iDB $dao) {
		$this->dao = $dao;
	}
	
	public function get($id) {
		$q = $this->dao->query('SELECT id, auteur, titre, contenu FROM news WHERE id = ' .(int)$id);
		// On vérifie que le résultat implémente bien iResult
		if(!$q instanceof iResult)
			throw new Exception('Le résultat d\'une requête doit être un objet implémentant iResult');
			
		return $q->fetchAssoc();
	}
}
?>

Testons maintenant notre code : 

<?php 
$dao = new MyPDO('mysql:host=localhost;dbname=mybdd', 'root', 'root');
// $dao = new MyMySQLi('localhost', 'root', 'root', 'news');

$manager = new NewsManager($dao);
print_r($manager->get(2));
?>

Ainsi, j'ai bel et ben découplé mes classes ! Il n'y a plus aucune dépendance entre ma classe NewsManager et une quelconque autre
classe. 

Note : le problème, dans mon cas, c'est qu'il est difficile de faire de l'injection de dépendances pour qu'une classe supporte
toutes les bibliothèques d'accès aux BDD (PDO, MySQLi, etc.) à cause des résultats des requêtes. De son côté, PDO a la classe
PDOStatement, tandis que MySQLi a MySQLi_STMT pour les requêtes préparées et MySQLi_Result pour les résultats des requêtes 
classiques. Cela est donc difficile de les conformer au même modèle. Je vais donc, dans le TP à venir, utiliser une autre
technique pour découpler mes classes. 

III./ Un exemple concret de pattern strategy

<?php 
interface Formater {
	public function format($text);
}

abstract class Writer {
	// Attribut contenant l'instance du formateur que l'on veut utiliser
	protected $formater;
	// Je veux une instance d'une classe implémentant Formater en paramètre
	public function __construct(Formater $formater) {
		$this->formater = $formater;
	}
	
	abstract public function write($text);
}

class FileWriter extends Writer {
	protected $file;
	
	public function __construct(Formater $formater, $file) {
		parent::__cunstruct($formater);
		$this->file = $file;
	}
	
	public function write($text) {
		$fichier = fopen($this->file, 'w');
		fwrite($fichier, $this->formater->format($text));
		fclose($fichier);		
	}
}

class DBWriter extends Writer {
	protected $db;
	
	public function __construct(Formater $formater, PDO $db) {
		parent::__construct($formater);
		$this->db = $db;
	}
	
	public function write($text) {
		$q = $this->db->exec('INSERT INTO news SET text = :text');
		$q->bindValue(':text', $this->formater->format($text));
		$q->execute();
	}
}

$xmlFormater = new class implements Formater {
	public function format($text) {
		return '<?xml version="1.0" text/xml encoding="iso-8859-1" ?>' . "\n" .
				'<message>' . "\n" .
				"\t" . '<date>' . time() . '</date>' . "\n" .
				"\t" . '<texte>' . $text . '</texte>' . "\n" .
				'</message>';
	}
};

$textFormater = new class implements Formater {
	public function format($text) {
		return 'Date : ' . time()  . "\n" . 'Texte : ' . $text;
	}
};

$htmlFormater = new class implements Formater {
	public function format($text) {
		return '<p>Date : ' . time() . '<br />' . "\n" . 'Texte : ' . $text . '</p>';
	}
};

function autoload($classe) {
	if(file_exists($chemin = $classe . 'class.php')) {
		require $chemin;
	}
}

spl_autoload_register('autoload');

$db = new PDO('mysql:host=localhost;dbname=mybdd;charset=utf8', 'root', 'root');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$writer = new DBWriter($htmlFormater, $db);
$writer->write('Voilà voilà');
$fileWriter = new FileWriter($htmlFormater, 'file.html');
// Sans classe anonyme mais une classe normale : $fileWriter = new FileWriter(new HTMLFormater, 'file.html');
$fileWriter->write('Et oui, voilà voilà !\n Hello the world !');

?>

J'ai utilisé ici les classes anonymes pour rendre le tout plus léger. Classes anonymes ?
En effet, depuis la version 7, PHP m'offre une fonctionnalité intéressante : les classes anonymes. Une classe anonyme est une
classe ne possédant pas de nom. Je serai amené à en utiliser lorsque la classe que j'écris n'est clairement destinée qu'à
une seule utilisation précise ou qu'elle n'a pas besoin d'être documentée. Dans ces cas-là, il n'est pas utile de déclarer cette
classe dans un fichier dédié (ça en vient même à alourdir inutilement le code). Voici un exemple très simple d'une classe 
anonyme : 

&lt;?php 
$monObjet = new class {
	public function sayHello() {
		echo 'Hello world !';
	}
};

Une classe anonyme suit les mêmes règles qu'une classe normale : il est possible de procéder à des héritages, d'implémenter
des interfaces, d'utiliser des traits, etc. 

Note : si le constructeur de la classe possède un paramètre, je passe l'argument juste après le mot-clé class (comme ceci :
$monObjet = new class('login@live.fr') {
	// etc.
}

?>

CONCLUSION : 

Le principal problème du singleton est de favoriser les dépendances entre deux classes. Il faut donc être très méfiant de ce
côté-là, car mon application deviendra difficilement modifiable et l'on perd alors les avantages de la POO. Il est donc recommandé
d'utiliser le singleton uniquement en dernier recours : si je décide d'implémenter ce pattern, c'est pour garantir que cette
classe ne doit être instanciée qu'une seule fois. Si je me rends compte que deux instances ou plus ne causent pas de problème
à l'application, alors je n'implémenterai pas le singleton. Mais surtout : <strong>ne jamais implémenter un singleton pour
l'utiliser comme une variable globale ! </strong>C'est la pire des choses à faire car cela favorise les dépendances entre
classes comme on l'a vu.

EN RESUMé : 

Un design pattern est un moyen de conception répondant à un problème récurrent.
Le pattern factory a pour but de laisser des classes usine créer les instances à ma place. 
Le pattern observer permet de lier certains objets à des "écouteurs" eux-mêmes chargés de notifier les objets auquels ils sont
rattachés.
Le pattern strategy sert à délocaliser une partie algorithmique d'une méthode afin de le rendre réutilisable, évitant ainsi 
toute duplication de cet algorithme. 
Le pattern singleton permet de pouvoir instancier une classe une et une seule fois, ce qui présente quelques soucis au niveau
des dépendances entre classes.
Le pattern injection de dépendances a pour but de rendre le plus indépendantes possibles les classes. 



</pre>
</body>
</html>