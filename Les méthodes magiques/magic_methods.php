<?php header('Content-type: text/html; charset="iso-8859-1"');?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Les méthodes magiques</title></head>
<body>
<pre>
													Les méthods magiques
													
Les méthodes magiques, une possibilité offerte par le PHP, sont de petites bricoles bien pratiques dans certains cas. 

I./ Principe

Une méthode magique est une méthode qui, si elle est présente dans ma classe, sera appelée lors de tel ou tel événement. Si la
méthode n'existe pas et que l'événement est exécuté, aucun effet "spécial" ne sera ajouté, l'événement s'exécutera normalement. Le
but des méthodes magiques est d'intercepter un événement, dire de faire ceci ou cela et retourner une valeur utile pour l'événement
si besoin il y a.

Par exemple, la méthode __construct() est une méthode magique ! L'événement qui appelle la méthode __construct() est la
création d'objet. 

Dans le même genre que __construct(), on peut citer __destruct() qui, elle, sera appelée lors de la destruction de l'objet. Voici
un exemple au cas où : 

<?php 
class MaClasse {
	
	public function __construct() {
		echo 'Création de ma classe';
	}
	
	public function __destruct() {
		echo 'Destruction de ma classe';
	}
	
}

$obj = new MaClasse();
echo '<p>Ici, utilisation de la classe...</p>';
?>

Au même titre que __construct(), __destruct() a lui aussi un petit nom : il s'agit du destructeur.

II./ La surcharge magique des attributs et méthodes 

Parlons maintenant des méthodes liées à la surchage magique des attributs et méthodes. 

De quoi s'agit-il ? La surcharge magique d'attributs ou méthodes consiste à créer dynamiquement des attributs et méthodes. Cela
est possible lorsque l'on tente d'accéder à un élément qui n'existe pas ou auquel on n'a pas accès (s'il est privé ou qu'on tente
d'y accéder depuis l'extérieur de la class par exemple). Dans ce cas là, on a... 6 méthodes magiques à notre disposition.

1.) "__set" et "__get"

Leur principe est le même, leur fonctionnement est à peu près semblable, c'est juste l'événement qui change. 

Commençons par __set. Cette méthode est appelée lorsque l'on tente d'assigner une valeur à un attribut auquel on n'a pas accès ou
qui n'existe pas. Cette méthode prend deux paramètres : le 1er est le nom de l'attribut auquel on a tenté d'assigner une valeur, et 
le second paramètre est la valeur que l'on a tenté d'assigner justement. Cette méthode ne retourne rien. Je peux simplement faire ce
que bon me semble : 

<?php 
class MaClasse2 {
	private $_attributPrive;
	public function __set($nom, $valeur) {
		echo 'Ah, on a tenté d\'assigner à l\'attribut <strong>', $nom, '</strong> la valeur <strong>', $valeur, '</strong> mais '
					. 'c\'est pas possible !<br />';
		$this->$nom = $valeur;
	}
	
	
}

$obj2 = new MaClasse2();

$obj2->attribut = 'Inexistant';
$obj2->_attributPrive = 'Valeur assignée. '; 
echo $obj2->attribut; // j'ai donc réussi à ajouter un attribut à la volée en quelque sorte !!!
?>

Je vais aussi, stocker dans un tableau tous les attributs (avec leurs valeurs) que j'ai essayé de modifier ou créer. 

<?php 
class MaClasse3 {
	private $_attributs = [];
	private $_attributPrive = 'Private attribut';
	
	public function __set($nom, $valeur) {
		$this->_attributs[$nom] = $valeur;
	}
	
	public function afficherAttributs() {
		echo '<pre>' , print_r($this->_attributs, true), '</pre>'; // true ==> retourner la valeur au lieu de l'afficher
	}
	
	public function __get($nom) {
		if(isset($this->$nom))
			return 'Impossible d\'accéder à l\'attribut <strong>' . $nom . '</strong>, désolé !<br />Sinon, voici sa valeur : '
						. $this->$nom . '<br />';
		elseif(isset($this->_attributs[$nom]))
			return $this->_attributs[$nom];
		else 
			return 'C\'est mort';
			
		
	}
	
	
}

$obj3 = new MaClasse3();
$obj3->attribut = 'Premier test';
$obj3->_attributPrive = 'Autre test simple';
$obj3->afficherAttributs();
echo '<p>Et maintenat, tour de passe passe grâce à __get() : </p><br />';
echo $obj3->_attributPrive;
?>

Passons maintenant à __get(). Cette méthode est appelée lorsque l'on tente d'accéder à un attribut qui n'existe pas ou auquel
on n'a pas accès. Elle prend un seul paramètre : le nom de l'attribut auquel on a tenté d'accéder. Cette méthode peut retourner
ce qu'elle veut (ou plus exactement ce que je veux bien qu'elle retourne...). 

Réf MaClasse3

Note : Etant donné que tous mes attributs doivent être privés, je peux facilement les mettre en "lecture seule" grâce à __get. 
L'utilisateur aura accès aux attributs, mais ne pourra pas les modifier. 

2.) "__isset" et "__unset"

La 1re méthode __isset() est appelée losque l'on appelle la fonction isset() sur un attribut qui n'existe pas ou auquel on n'a pas
accès. Etant donné que la fonction isset() renvoie true ou false, la méthode __isset() doit aussi renvoyer un booléen. 
Cette méthode prend un paramètre : le nom de l'attribut que l'on a envoyé à la fonction isset(). Je peux par exemple utiliser la 
classe précédente en implémentant la méthode __isset(), ce qui peut donner : 

<?php 
class MaClasse4 {
	private $_attributs = [];
	private $_attributPrive = 'Private attribut';
	
	public function __set($nom, $valeur) {
		$this->_attributs[$nom] = $valeur;
	}
	
	public function afficherAttributs() {
		echo '<pre>' , print_r($this->_attributs, true), '</pre>'; // true ==> retourner la valeur au lieu de l'afficher
	}
	
	public function __get($nom) {
		if(isset($this->$nom))
			return 'Impossible d\'accéder à l\'attribut <strong>' . $nom . '</strong>, désolé !<br />Sinon, voici sa valeur : '
						. $this->$nom . '<br />';
		elseif(isset($this->_attributs[$nom]))
			return $this->_attributs[$nom];
		else 
			return 'C\'est mort';
	}
	
	public function __isset($nom) {
		return isset($this->_attributs[$nom]);
	}
	
	public function __unset($nom) {
		if(isset($this->_attributs[$nom]))
			unset($this->_attributs[$nom]);
	}
} 

$obj4 = new MaClasse4();
$obj4->attribut = 'Premier test';
$obj4->_attributPrive = 'Autre test simple';
$obj4->afficherAttributs();
echo '<p>Et maintenat, tour de passe passe grâce à __get() : </p><br />';
echo $obj3->_attributPrive;

if(isset($obj4->attribut))
	echo 'L\'attribut <strong>attribut</strong> existe ! <br />';
else 
	echo 'L\attribut <strong>attribut</strong> n\'existe pas ! <br />';

if (isset($obj4->_attributPrive))
	echo 'L\'attribut <strong>_attributPrive</strong> existe !';
else
	echo 'L\'attribut <strong>_attributPrive</strong> n\'existe pas !';
	
	?>
	
Remarque : la méthode __isset() est appelée ici à la place, ou en recours, en quelque sorte, de la fonction isset().

Pour __unset(), le principe est le même. Cette méthode est appelée lorsque l'on esssaye d'appeler la fonction unset() sur
un attribut inexistant ou auquel on n'a pas accès. On peut facilement implémenter __unset() à la classe précédente de manière
à supprimer l'entrée correspondante dans mon tableau $attributs. Cette méthode ne doit rien retourner. 

Réf MaClasse4

<?php 
unset($obj4->attribut);

if (isset($obj->attribut))
{
	echo 'L\'attribut <strong>attribut</strong> existe !<br />';
}
else
{
	echo 'L\'attribut <strong>attribut</strong> n\'existe pas !<br />';
}

?>

3.) "__call" et "__callStatic"

Parlons maintenant des méthodes que l'on appelle alors qu'on n'y a pas accès (soit parce que inexistantes, soit parce que privées).
La méthode __call() sera appelée lorsque l'on essayera d'appeler une telle méthode. Elle prend deux arguments : le 1er est le nom
de la méthode que l'on a essayé d'appeler et le second est la liste des arguments qui lui ont été passés (sous forme de tableau). 

Exemple  :

<?php 
class MaClasse5 {
	public function __call($nom, $arguments) {
		echo 'La méthode <strong>', $nom, '</strong> a été appelée alors qu\'elle n\'existe pas ! Ses arguments étaient les'
		.'suivants : <strong>', implode('</strong>, <strong>', $arguments), '</strong>';
	}
	
	public static function __callStatic($nom, $arguments) {
		echo 'La méthode <strong>', $nom, '</strong> a été appelée dans un contexte statique alors qu\'elle n\'existe pas'
		.' ! Ses arguments étaient les suivants : <strong>', implode ('</strong>, <strong>', $arguments), '</strong><br />';
	}
}

$obj = new MaClasse5();

$obj->method(123, 'test');
echo '<br />';
MaClasse5::methodeStatique(1123445, 'voila', 'test3');
?>

Essayons maintenant d'appeler une méthode qui n'existe pas statiquement... Erreur fatale ! Sauf si j'utilise __callStatic. Cette
méthode est appelée lorsque j'appelle une méthode dans un contexte statique alors qu'elle n'existe pas. La méthode magique 
__callStatic doit obligatoirement être static ! 

Réf MaClasse5

III./ Linéariser ses objets
1.) Le principe de linéarisation
a./ Serialize / unserialize

Linéariser consiste à enregistrer un array, par exemple, en intégralité (ie avec les clefs associées aux valeurs) en base de 
données, ou encore dans un fichier. En fait, c'est la sérialisation.

Il existe une fonction bien particulière et puissante en PHP (depuis la version 4) pour ce genre de cas : serialize(). 

Voici la définition officielle de cette fonction : 

serialize() retourne une chaîne de caractères contenant une représentation linéaire de value, pour stockage. C'est une 
technique pratique pour stocker ou passer des valeurs de PHP entre scripts, sans perdre ni leur structure, ni leur type. 

Note : le principe de serialize() n'est pas de faire un transtypage classique, mais bien une linéarisation  qui conserve le type (et
donc les informations) de la variable originale. 

Par exemple : 

<?php 
$tab = array(5, 6, 7, 8, 'string');
echo serialize($tab); ?>

Ce qui affiche :

a:5:{i:0;i:5;i:1;i:6;i:2;i:7;i:3;i:8;i:4;s:6:"string";}

Remarque : "a" pour array, le 4 pour la taille de l'array. i pour entier, s pour les chaînes de caractères. A chaque fois, l'index 
est d'abord spécifié. 

On peut même linéariser les nombres (que ce soit int ou float, etc.), les booléens, les chaînes de caractères même, bref, tous
les types de variables. 

La fonction serialize() n'aurait eu que peu d'intérêt, si on ne pouvait pas récupérer les données originales sous leur forme 
originale. C'est ce que permet de faire la fonction unserialize(). 
b./ L'enregistrement dans un fichier

Exemple :

<?php 
$srlzd = serialize(array(1, 2, 3, 'quatre' => 'valeur4'));
$fichier = fopen('serialized.txt', 'a+');

fwrite($fichier, $srlzd);
fclose($fichier);

?>

Note : il reste un problème, si le fichier trouve un caractère spécial (\n, \t...), il va le traduire comme tel. Il faut s'assurer
de protéger de cela, par exemple en doubland le \.


c./ L'utilisation de serialize en barre d'adresse

Il faut faire attention en transmettant le résultat de serialize() via GET : certains caractères n'étant pas supportés dans
les URL ou ayant un sens bien particulier, notamment les ";" qui sont présents dans un array serializé, il faut encoder ces
caractères via la méthode :

urlencode() qui encode ces caractères non supportés ou à sens particulier, pour le passage via URL.

Exemple : 

<?php 
//header('Location: magic_methods.php?data=' . urlencode($srlzd));
// exit;

?>

Ensuite, il faudra utiliser urldecode() pour effectuer l'opération inverse. 

d./ L'enregistrement en base de données

Il faut passer par les requêtes préparées pour protéger les données contre les guillemets, apostrophes ou encore les antislashes.
Ces caractères (", ', \) sont les plus dangereux si non controlés. Et ce n'est pas parce que je n'ai pas d'erreur que tout va
bien...

2.) Linéariser des objets 

J'ai un sysème de protection de sessions sur mon site avec une classe Connexion. Cette classe, comme son nom l'indique, 
aura pour rôle d'établir une connexion à la BDD. Comment faire pour stocker l'objet créé dans une variable $_SESSION ?

Eh bien, je fais : 

$_SESSION['connexion'] = $objetConnexion;

Cela fonctionne, mais est-ce que je sais ce qui se passe quand j'effectue ce genre d'opération ? Ou plutôt ce qui se passe <strong>
à la fin du script</strong> ? 

En fait, à la fin du script, le tableau de session est linéarisé automatiquement. 

Pour bien comprendre, je vais linéariser moi-même mon objet. Voici ce que je vais faire :

	-	Création de l'objet $objetConnexion = new Connexion();
	-	transformation de l'objet en chaîne de caractères ($_SESSION['connexion'] = serialize($objetConnexionn););
	-	changement de page
	-	transformation de la chaîne de caractères en objet ($objetConnexion = unserialize($_SESSION['connexion']);)
	
En effet, on peut très bien linériser un objet : et comment ?

Un objet est un ensemble d'attributs, tout simplement. Les méthodes ne sont pas stockées dans l'objet, c'est la classe qui s'en
occupe. Ainsi, je pourrai conserver mon objet dans une variable sous forme de chaine de caractères. 

La chose essentielle à comprendre est que la fonction serialize() est automatiquement appelée sur l'array $_SESSION à la 
fin du script, mon objet est donc automatiquement linéarisé à la fin du script. C'est uniquement dans un but didactique que je
l'ai linérisé manuellement ici.

La seconde fonction, unserialize(), retournera la chaîne de caractères passée en paramètre sous forme d'objet. En gros, cette
fonction lit la chaîne de caractères passée en paramètre sous forme d'objet. En gros, cette fonction lit la chaîne de 
caractères, crée une instance de la classe correspondante et assigne à chaque attribut la valeur qu'ils avaient. Ainsi,
je pourrai utiliser l'objet retourné (appel des méthodes, attributs et diverses opérations) comme avant. Cette fonction est
automatiquement appelée dès le début du script pour conserver le tableau de sessions précédemment enregistré dans le fichier. 

Attention : si j'ai linéarisé un objet manuellement, il ne sera JAMAIS restauré automatiquement. 

Et le rapport avec les méthodes magiques ?

En fait, les fonctions citées ci-dessus (serialize et unserialize) ne se contentent pas de transformer le paramètre qu'on
leur passe en autre chose : elles vérifient si, dans l'objet passé en paramètre (pour serealize()), il y a une méthode __sleep(),
auquel cas celle-ci est exécutée. Si c'est unserialize() qui est appelée, la fonction vérifie si l'objet obtenu comporte
une méthode __wakeup(), auquel cas celle-ci est appelée. 

3.) "serialize" et "__sleep"

La méthode magique __sleep() est appelée pour nettoyer l'objet ou pour sauver des attributs. Si la méthode magique __sleep()
n'existe pas, tous les attributs seront sauvés. Cette méthode doit renvoyer un tableau avec les noms des attributs à sauver. 
Par exemple, si je veux sauver $serveur et $login, la fonction devra retourner ['serveur', 'login']. 

Voici à quoi pourrait ressembler ma classe Connexion :

<?php 
class Connexion {
	protected $pdo, $serveur, $utilisateur, $motDePasse, $dataBase;
	
	public function __construct($serveur, $utilisateur, $motDePasse, $dataBase) {
		$this->serveur = $serveur;
		$this->utilisateur = $utilisateur;
		$this->motDePasse = $motDePasse;
		$this->dataBase = $dataBase;
		
		$this->connexionBDD();
	}
	
	protected function connexionBDD() {
		$this->pdo = new PDO('mysql:host='. $this->serveur . ';dbname='. $this->dataBase, $this->utilisateur, $this->motDePasse);
	}
	
	public function __sleep() {
		// Ici sont à placer les instructions à exécuter juste avant la linéarisation. 
		// On retourne ensuite la liste des attributs qu'on veut sauver.
		return ['serveur', 'utilisateur', 'motDePasse', 'dataBase'];
		
	}
	
	public function __wakepup() {
		$this->connexionBDD();
	}
}

// Et je peux faire ceci : 

$connexion = new Connexion('localhost', 'root', 'root', 'mybdd');

$_SESSION['connexion'] = serialize($connexion);

// ensuite, ne faudrait-il pas faire, dans une autre page : $connexion = unserialize($_SESSION['connexion']);  // (ya pense que oui)
?>

4.) "unserialize" et "__wakeup"

Maintenant, je vais simplement implémenter la fonction __wakepup. Je vais y placer un appel à la méthode connexionBDD() qui
se chargera de me connecter à ma base de données puisque les identifiants, serveur et nom de la base ont été sauvegardés et
ainsi restaurés à l'appel de la fonction unserialize(). 

Réf Connexion

Mais bon, si l'on préfère sauver tous les attributs, ce n'est pas la peine de tourner autour du pot, on peut directement 
enregistrer un objet dans une entrée de session, sans appeler serialize, unserialize (et donc sans définir les méthodes __sleep,
__wakeup). Ce code fonctionnera parfaitement : 

<?php 
//session_start();	//  à appeler au début de la page bien sûr

if(!isset($_SESSION['connexion'])) {
	$connexion = new Connexion('localhost', 'root', 'root', 'mybdd');
	$_SESSION['connexion'] = $connexion;
	
	echo 'Actualisez la page pour voir ! ';
} 
else {
	echo '<pre>';
	var_dump($_SESSION['connexion']); // On affiche les infos concernant notre objet
	echo '</pre>';
}
?>

Ainsi, mon objet a bel et bien été sauvegardé comme il fallait, et tous les attributs ont été sauvés. C'est magica!

Attention : étant donné que mon objet est restauré automatiquement lors de l'appel de session_start(), la classe correspondante 
doit être déclarée <strong>avant</strong>, sinon l'objet désérialisé sera une instance de __PHP_Incomplete_Class_Name, classe qui
ne contient aucune méthode (cela produira donc un objet inutile). Si j'ai un autoload qui chargera la classe automatiquement, il
sera appelé.

IV./ Autres méthodes magiques

Il s'agira ici de __toString(), __set_state(), __invoke() et __debugInfo()

1.) __toString()

La méthode magique __toString() est appelée lorsque l'objet est amené à être converti en chaîne de caractères. Cette méthode
doit retourner la chaîne de caractères souhaitée. 

Exemple : 

<?php 
class MyClass {
	protected $texte;
	
	public function __construct($texte) {
		$this->texte = $texte;
	}
	
	public function __toString() {
		return $this->texte;
	}
}

$obj = new MyClass('Hello the worldish !');
// Solution 1 : cast

$texte = (string) $obj;

echo $texte;	// affiche 'Hello the worldish !';
var_dump($texte); // affiche string(20) : 'Hello the worldish !'

// Solution 2 : directement dans un echo ==>
echo $obj;	// Affiche 'Hello the worldish !'
?>

2.) __set_state()

La méthode __set_state() est appelée lorsque j'appelle la fonction var_export()  en passant mon objet à exporter en paramètre. Cette
fonction var_export() a pour rôle d'exporter la variable passée en paramètre sous forme de code PHP (chaîne de caractères). Si 
je ne spécifie pas de méthode __set_state() dans ma classe, une erreur fatale sera levée. 

Ma méthode __set_state() prend un paramètre, la liste des attributs ainsi que leur valeur dans un tableau associatif ('attribut' =>
'valeur'). Ma méthode magique devra retourner l'objet à exporter. Il faudra donc créer un nouvel objet et lui assigner les
valeurs qu'on souhaite, puis le retourner. 

Attention : Il ne faut jamais retourner $this, car cette variable n'existera pas dans cette méthode ! var_export() reportera donc
une valeur nulle.

Puisque le code var_export() retourne du code PHP valide, on peut utiliser la fonction eval() qui exécute du code PHP sous forme
de chaîne de caractère qu'on lui passe en paramètre.

Par exemple, pour retourner un objet en sauvant ses attributs, on pourrait faire : 

<?php 
class Export {
	protected $chaine1, $chaine2;
	
	public function __construct($param1, $param2) {
		$this->chaine1 = $param1;
		$this->chaine2 = $param2;
	}
	
	public function __set_state($valeurs) {	// Liste des attributs de l'objet en paramètre
		$obj = new Export($valeurs['chaine1'], $valeurs['chaine2']);	// On crée un objet avec les attributs de l'objet que 
																		// l'on veut exporter;
		return $obj;	// on retourne l'objet créé.
	}
}

$obj1 = new Export('Hello', 'world !');

eval('$obj2 = ' . var_export($obj1, true) . ';');	// On crée un autre objet, celui-ci ayant les mêmes attributs que l'objet
													// précédent
echo '<pre>', print_r($obj2, true), '</pre>';
?>

3.) __invoke()

La méthode __invoke() permet d'utiliser un objet comme une fonction. A peu près ainsi :

&lt;?php
$obj = new MyClass;

$obj('Petit test');

Par contre, ce code génèrera une erreur fatale... Pour résoudre ce problème, je vais devoir utiliser une méthode magique __invoke().
Elle est appelée dès qu'on essaye d'utiliser l'objet comme fonction (comme je viens de le faire). Cette méthode comprend 
autant de paramètres que d'arguments passés à la fonction. 

Exemple :

<?php 
class MyClass2 {
	public function __invoke($argument) {
		echo $argument;
	}
}

$obj = new MyClass2();
$obj(5); 	// affiche 5

?>

4.) __debugInfo()

Cette méthode magique est invoquée sur mon objet lorsque l'on appelle la fonction var_dump(). Pour info, cette fonction permet 
d'obtenir des informations sur la variable qu'on lui donne. Si on lui donne un objet, var_dump() va afficher les détails de 
tous les attributs de l'objet, qu'ils soient publics, protégés ou privés. La méthode magique __debugInfo() permet de modifier
ce comportement en ne sélectionnant que les attributs à afficher ainsi que ce qu'il faut afficher. Pour ce faire, cette
méthode renverra sous forme de tableau associatif la liste des attributs à afficher avec leurs valeurs. Voici un exemple
d'utilisation (simplifié au max) : 

<?php 
class FileReader {
	protected $f;
	
	public function __construct($path) {
		$this->f = fopen($path, 'c+');
	}
	
	public function __debugInfo() {
		return ['f' => fstat($this->f)];
	}
}

$f = new FileReader('serialized.txt');
var_dump($f);	// Affiche les informations retournées par fstat
?>

J'ai ici un gestionnaire de fichiers qui me permet de gérer facilement la lecture et l'écriture d'un fichier (du moins, on imagine
que ça le fait). Un var_dump() simple sur mon objet ne serait pas très révélateur. Par contre, obtenir les informations sur le
fichier actuellement ouvert le serait plus, et c'est précisément ce que l'on fait en écrivant la méthode __debugInfo() avec l'appel
de fstat().

En résumé :

Les méthodes magiques dont je me servirai le plus souvent sont : __construct(), __set(), __get() et __call(). Les autres sont plus
"gadget" et je les rencontrerai moins souvent. 
</pre>
</body>
</html>