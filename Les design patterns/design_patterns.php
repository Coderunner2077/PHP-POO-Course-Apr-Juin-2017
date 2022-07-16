<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Les exceptions</title>
</head>
<body>
<pre>
												Les design patterns
												
Les design patterns sont des solutions invent�es par les d�veloppeurs pour pallier diff�rents probl�mes de conception. Je ne 
reverrai pas ici le pattern observer, ni le pattern strategy (en cas de besoin, revoir le cours sur Java), mais j'en verrai d'autres,
nouveaux pour moi.
Par ailleurs, je d�couvrirai les classes anonymes en PHP. 

I./ Laisser une classe cr�er les objets : le pattern Factory
1.)Le probl�me

Admettons que je viens de cr�er une application relativement importante. J'ai construit cette application en associant plus ou
moins la plupart de mes classes entre elles. A pr�sent, je souhaite modifier un petit morceau de code afin d'ajouter une 
fonctionnalit� � l'application. 
Probl�me: �tant donn� que la plupart de mes classes sont plus ou moins li�es, il va falloir modifier un tas de choses ! C'est 
l� que le pattern Factory peut se rev�ler utile. 

Ce motif est tr�s simple � construire. En fait, si j'impl�mente ce pattern, je n'aurai plus de new � placer dans la partie 
globale du script afin d'intancier une classe. En effet, ce ne sera pas � moi de le faire mais � une <strong>classe usine</strong>.
Cette classe aura pour r�le de charger les classes que je lui passe en argument. Ainsi, quand je modifierai mon code, je 
n'aurai qu'� modifier le masque d'usine pour que la plupart des modifications prennent effet. En gros, je ne me soucierai plus
de l'instanciation de mes classes, ce sera � l'usine de le faire.

Voici comment se pr�sente une classe impl�mentant le pattern Factory :

&lt;?php 
class DBFactory {
	public static function load($sgbdr) {
		$classe = 'SGBDR_' . $sgbdr;
		
		if(file_exists($chemin = $classe . '.class.php')) {
			require $chemin;
			return new $classe;
		} 
		else 
			throw new Exception('La classe <strong>' .$classe . '</strong> n\'a pas pu �tre trouv�e');
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

Le but est de cr�er une classe qui distribuera les objets PDO facilement. Je vais partir du principe que j'ai plusieurs SGBDR, ou
plusieurs BDD qui utilisent des identifiants diff�rents. Pour r�sumer, je vais tout centraliser dans une classe. 

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

Ceci me simplefiera �norm�ment la t�che. Si j'ai besoin de modifier mes identifiants de connexion, je n'aurai pas � aller chercher
dans tous mes scripts : tout sera plac� dans mon factory !

II./ L'injection de d�pendances

Comme tout pattern, celui-ci est n� � cause d'un probl�me souvent rencontr� par les d�veloppeurs : le fait d'avoir de nombreuses
classes d�pendantes les unes des autres. L'injection de d�pendances consiste � d�coupler mes classes. Le pattern singleton,
lui, favorise les d�pendances, et l'injection de d�pendances palliant ce probl�me, il est int�ressant d'�tudier ce nouveau 
pattern tout en mettant en �vidence les limites du pattern singleton.

Soit le code suivant : 

&lt;?php 
class NewsManager {
	public function get($id) {
		// On admet que MyPDO �tend PDO et qu'il impl�mente un singleton
		$q = MyPDO::getInstance()->query('SELECT id, auteur, titre, contenu FROM news WHERE id = '.(int)$id);
		
		return $q->fetch(PDO::FETCH_ASSOC);
	}
}
?>

Je m'aper�ois qu'ici, le singleton a introduit une d�pendance entre deux classes n'appartenant pas au m�me module. Deux modules
ne doivent jamais �tre li�s de cette fa�on, ce qui est le cas dans cet exemple. Deux modules doivent �tre ind�pendants l'un de
l'autre. D'ailleurs, en y regardant de plus pr�s, cela ressemble fortement � une variable globale. En effet, un singleton n'est
jamais rien d'autre qu'une variable globale d�guis�e (il y a juste une �tape en plus pour acc�der � la variable) : 

&lt;?php 
class NewsManager {
	public function get($id) {
		global $db;
		// revient EXACTEMENT au m�me que :
		$db = MyPDO::getInstance();
		
		// suite des op�rations...
	}
}
?>
Effectivement, l'un des poins forts de la POO �tant le fait de pouvoir redistribuer sa classe et la r�utiliser, cela est justement
impossible dans le cas pr�sent parce que ma classe NewsManager d�pend de MyPDO. Qu'est-ce qui me dit que la personne utilisant
NewsManager aura cette derni�re ? Rien du tout, et c'est normal. On est ici face � une d�pendance cr��e par le singleton. De plus,
la classe d�pend aussi de PDO : il y avait donc d�j� une d�pendance au d�but, et le pattern Singleton en a cr�� une autre. Il faut
donc supprimer ces deux d�pendances. 

Comment fait-on alors ?

Ce qu'il faut, c'est passer mon DAO au constructeur, sauf que ma classe ne doit pas �tre d�pendante d'une quelconque biblioth�que.
Ainsi, mon objet peut tr�s bien utiliser PDO, MySQLi ou que sait-je encore, la classe se servant de lui doit fonctionner de la
m�me mani�re. Alors, comment proc�der ? Il faut imposer un <strong>comportement sp�cifique � mon objet</strong> en l'obligeant
� impl�menter certaines m�thodes. Justement, les interfaces sont l� pour �a. Je vais donc cr�er une interface iDB ne contenant
(pour faire simple) qu'une seule m�thode : query()

<?php 
interface iDB {
	public function query($query);
}
?>

Pour que l'exemple soit parlant, je vais cr�er deux classes utilisant cette structure, l'une utilisant PDO et l'autre MySQLi.
Cependant, un probl�me se pose : le r�sultat retourn� par la m�thode query() des classes PDO et MySQLi sont des instances de
deux classes diff�rentes, les m�thodes disponibles ne sont, par cons�quent, pas les m�mes. Il va donc falloir cr�er d'autres
classes pour g�rer les r�sultats qui suivent eux aussi une structure d�finie par une interface (admettons iResult). 

<?php 
interface iResult {
	public function fetchAssoc();
}
?>

Je peux donc � pr�sent �crire mes quatre classes : MyPDO, MyMySQLi, MyPDOStatement et MyMySQLiResult :

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

Je peux donc maintenant �crire ma classe NewsManager. Attention � ne pas oublier de v�rifier que les objets sont bien des 
instances de classes impl�mentant les interfaces d�sir�es !

<?php 
class NewsManager {
	protected $dao;
	
	// On souhaite un objet instanciant une classe qui impl�mente iDB
	public function __construct(iDB $dao) {
		$this->dao = $dao;
	}
	
	public function get($id) {
		$q = $this->dao->query('SELECT id, auteur, titre, contenu FROM news WHERE id = ' .(int)$id);
		// On v�rifie que le r�sultat impl�mente bien iResult
		if(!$q instanceof iResult)
			throw new Exception('Le r�sultat d\'une requ�te doit �tre un objet impl�mentant iResult');
			
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

Ainsi, j'ai bel et ben d�coupl� mes classes ! Il n'y a plus aucune d�pendance entre ma classe NewsManager et une quelconque autre
classe. 

Note : le probl�me, dans mon cas, c'est qu'il est difficile de faire de l'injection de d�pendances pour qu'une classe supporte
toutes les biblioth�ques d'acc�s aux BDD (PDO, MySQLi, etc.) � cause des r�sultats des requ�tes. De son c�t�, PDO a la classe
PDOStatement, tandis que MySQLi a MySQLi_STMT pour les requ�tes pr�par�es et MySQLi_Result pour les r�sultats des requ�tes 
classiques. Cela est donc difficile de les conformer au m�me mod�le. Je vais donc, dans le TP � venir, utiliser une autre
technique pour d�coupler mes classes. 

III./ Un exemple concret de pattern strategy

<?php 
interface Formater {
	public function format($text);
}

abstract class Writer {
	// Attribut contenant l'instance du formateur que l'on veut utiliser
	protected $formater;
	// Je veux une instance d'une classe impl�mentant Formater en param�tre
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
$writer->write('Voil� voil�');
$fileWriter = new FileWriter($htmlFormater, 'file.html');
// Sans classe anonyme mais une classe normale : $fileWriter = new FileWriter(new HTMLFormater, 'file.html');
$fileWriter->write('Et oui, voil� voil� !\n Hello the world !');

?>

J'ai utilis� ici les classes anonymes pour rendre le tout plus l�ger. Classes anonymes ?
En effet, depuis la version 7, PHP m'offre une fonctionnalit� int�ressante : les classes anonymes. Une classe anonyme est une
classe ne poss�dant pas de nom. Je serai amen� � en utiliser lorsque la classe que j'�cris n'est clairement destin�e qu'�
une seule utilisation pr�cise ou qu'elle n'a pas besoin d'�tre document�e. Dans ces cas-l�, il n'est pas utile de d�clarer cette
classe dans un fichier d�di� (�a en vient m�me � alourdir inutilement le code). Voici un exemple tr�s simple d'une classe 
anonyme : 

&lt;?php 
$monObjet = new class {
	public function sayHello() {
		echo 'Hello world !';
	}
};

Une classe anonyme suit les m�mes r�gles qu'une classe normale : il est possible de proc�der � des h�ritages, d'impl�menter
des interfaces, d'utiliser des traits, etc. 

Note : si le constructeur de la classe poss�de un param�tre, je passe l'argument juste apr�s le mot-cl� class (comme ceci :
$monObjet = new class('login@live.fr') {
	// etc.
}

?>

CONCLUSION : 

Le principal probl�me du singleton est de favoriser les d�pendances entre deux classes. Il faut donc �tre tr�s m�fiant de ce
c�t�-l�, car mon application deviendra difficilement modifiable et l'on perd alors les avantages de la POO. Il est donc recommand�
d'utiliser le singleton uniquement en dernier recours : si je d�cide d'impl�menter ce pattern, c'est pour garantir que cette
classe ne doit �tre instanci�e qu'une seule fois. Si je me rends compte que deux instances ou plus ne causent pas de probl�me
� l'application, alors je n'impl�menterai pas le singleton. Mais surtout : <strong>ne jamais impl�menter un singleton pour
l'utiliser comme une variable globale ! </strong>C'est la pire des choses � faire car cela favorise les d�pendances entre
classes comme on l'a vu.

EN RESUM� : 

Un design pattern est un moyen de conception r�pondant � un probl�me r�current.
Le pattern factory a pour but de laisser des classes usine cr�er les instances � ma place. 
Le pattern observer permet de lier certains objets � des "�couteurs" eux-m�mes charg�s de notifier les objets auquels ils sont
rattach�s.
Le pattern strategy sert � d�localiser une partie algorithmique d'une m�thode afin de le rendre r�utilisable, �vitant ainsi 
toute duplication de cet algorithme. 
Le pattern singleton permet de pouvoir instancier une classe une et une seule fois, ce qui pr�sente quelques soucis au niveau
des d�pendances entre classes.
Le pattern injection de d�pendances a pour but de rendre le plus ind�pendantes possibles les classes. 



</pre>
</body>
</html>