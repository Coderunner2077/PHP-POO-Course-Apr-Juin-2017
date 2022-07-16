<?php header('Content-type: text/html; charset="iso-8859-1"');?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Les m�thodes magiques</title></head>
<body>
<pre>
													Les m�thods magiques
													
Les m�thodes magiques, une possibilit� offerte par le PHP, sont de petites bricoles bien pratiques dans certains cas. 

I./ Principe

Une m�thode magique est une m�thode qui, si elle est pr�sente dans ma classe, sera appel�e lors de tel ou tel �v�nement. Si la
m�thode n'existe pas et que l'�v�nement est ex�cut�, aucun effet "sp�cial" ne sera ajout�, l'�v�nement s'ex�cutera normalement. Le
but des m�thodes magiques est d'intercepter un �v�nement, dire de faire ceci ou cela et retourner une valeur utile pour l'�v�nement
si besoin il y a.

Par exemple, la m�thode __construct() est une m�thode magique ! L'�v�nement qui appelle la m�thode __construct() est la
cr�ation d'objet. 

Dans le m�me genre que __construct(), on peut citer __destruct() qui, elle, sera appel�e lors de la destruction de l'objet. Voici
un exemple au cas o� : 

<?php 
class MaClasse {
	
	public function __construct() {
		echo 'Cr�ation de ma classe';
	}
	
	public function __destruct() {
		echo 'Destruction de ma classe';
	}
	
}

$obj = new MaClasse();
echo '<p>Ici, utilisation de la classe...</p>';
?>

Au m�me titre que __construct(), __destruct() a lui aussi un petit nom : il s'agit du destructeur.

II./ La surcharge magique des attributs et m�thodes 

Parlons maintenant des m�thodes li�es � la surchage magique des attributs et m�thodes. 

De quoi s'agit-il ? La surcharge magique d'attributs ou m�thodes consiste � cr�er dynamiquement des attributs et m�thodes. Cela
est possible lorsque l'on tente d'acc�der � un �l�ment qui n'existe pas ou auquel on n'a pas acc�s (s'il est priv� ou qu'on tente
d'y acc�der depuis l'ext�rieur de la class par exemple). Dans ce cas l�, on a... 6 m�thodes magiques � notre disposition.

1.) "__set" et "__get"

Leur principe est le m�me, leur fonctionnement est � peu pr�s semblable, c'est juste l'�v�nement qui change. 

Commen�ons par __set. Cette m�thode est appel�e lorsque l'on tente d'assigner une valeur � un attribut auquel on n'a pas acc�s ou
qui n'existe pas. Cette m�thode prend deux param�tres : le 1er est le nom de l'attribut auquel on a tent� d'assigner une valeur, et 
le second param�tre est la valeur que l'on a tent� d'assigner justement. Cette m�thode ne retourne rien. Je peux simplement faire ce
que bon me semble : 

<?php 
class MaClasse2 {
	private $_attributPrive;
	public function __set($nom, $valeur) {
		echo 'Ah, on a tent� d\'assigner � l\'attribut <strong>', $nom, '</strong> la valeur <strong>', $valeur, '</strong> mais '
					. 'c\'est pas possible !<br />';
		$this->$nom = $valeur;
	}
	
	
}

$obj2 = new MaClasse2();

$obj2->attribut = 'Inexistant';
$obj2->_attributPrive = 'Valeur assign�e. '; 
echo $obj2->attribut; // j'ai donc r�ussi � ajouter un attribut � la vol�e en quelque sorte !!!
?>

Je vais aussi, stocker dans un tableau tous les attributs (avec leurs valeurs) que j'ai essay� de modifier ou cr�er. 

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
			return 'Impossible d\'acc�der � l\'attribut <strong>' . $nom . '</strong>, d�sol� !<br />Sinon, voici sa valeur : '
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
echo '<p>Et maintenat, tour de passe passe gr�ce � __get() : </p><br />';
echo $obj3->_attributPrive;
?>

Passons maintenant � __get(). Cette m�thode est appel�e lorsque l'on tente d'acc�der � un attribut qui n'existe pas ou auquel
on n'a pas acc�s. Elle prend un seul param�tre : le nom de l'attribut auquel on a tent� d'acc�der. Cette m�thode peut retourner
ce qu'elle veut (ou plus exactement ce que je veux bien qu'elle retourne...). 

R�f MaClasse3

Note : Etant donn� que tous mes attributs doivent �tre priv�s, je peux facilement les mettre en "lecture seule" gr�ce � __get. 
L'utilisateur aura acc�s aux attributs, mais ne pourra pas les modifier. 

2.) "__isset" et "__unset"

La 1re m�thode __isset() est appel�e losque l'on appelle la fonction isset() sur un attribut qui n'existe pas ou auquel on n'a pas
acc�s. Etant donn� que la fonction isset() renvoie true ou false, la m�thode __isset() doit aussi renvoyer un bool�en. 
Cette m�thode prend un param�tre : le nom de l'attribut que l'on a envoy� � la fonction isset(). Je peux par exemple utiliser la 
classe pr�c�dente en impl�mentant la m�thode __isset(), ce qui peut donner : 

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
			return 'Impossible d\'acc�der � l\'attribut <strong>' . $nom . '</strong>, d�sol� !<br />Sinon, voici sa valeur : '
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
echo '<p>Et maintenat, tour de passe passe gr�ce � __get() : </p><br />';
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
	
Remarque : la m�thode __isset() est appel�e ici � la place, ou en recours, en quelque sorte, de la fonction isset().

Pour __unset(), le principe est le m�me. Cette m�thode est appel�e lorsque l'on esssaye d'appeler la fonction unset() sur
un attribut inexistant ou auquel on n'a pas acc�s. On peut facilement impl�menter __unset() � la classe pr�c�dente de mani�re
� supprimer l'entr�e correspondante dans mon tableau $attributs. Cette m�thode ne doit rien retourner. 

R�f MaClasse4

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

Parlons maintenant des m�thodes que l'on appelle alors qu'on n'y a pas acc�s (soit parce que inexistantes, soit parce que priv�es).
La m�thode __call() sera appel�e lorsque l'on essayera d'appeler une telle m�thode. Elle prend deux arguments : le 1er est le nom
de la m�thode que l'on a essay� d'appeler et le second est la liste des arguments qui lui ont �t� pass�s (sous forme de tableau). 

Exemple  :

<?php 
class MaClasse5 {
	public function __call($nom, $arguments) {
		echo 'La m�thode <strong>', $nom, '</strong> a �t� appel�e alors qu\'elle n\'existe pas ! Ses arguments �taient les'
		.'suivants : <strong>', implode('</strong>, <strong>', $arguments), '</strong>';
	}
	
	public static function __callStatic($nom, $arguments) {
		echo 'La m�thode <strong>', $nom, '</strong> a �t� appel�e dans un contexte statique alors qu\'elle n\'existe pas'
		.' ! Ses arguments �taient les suivants : <strong>', implode ('</strong>, <strong>', $arguments), '</strong><br />';
	}
}

$obj = new MaClasse5();

$obj->method(123, 'test');
echo '<br />';
MaClasse5::methodeStatique(1123445, 'voila', 'test3');
?>

Essayons maintenant d'appeler une m�thode qui n'existe pas statiquement... Erreur fatale ! Sauf si j'utilise __callStatic. Cette
m�thode est appel�e lorsque j'appelle une m�thode dans un contexte statique alors qu'elle n'existe pas. La m�thode magique 
__callStatic doit obligatoirement �tre static ! 

R�f MaClasse5

III./ Lin�ariser ses objets
1.) Le principe de lin�arisation
a./ Serialize / unserialize

Lin�ariser consiste � enregistrer un array, par exemple, en int�gralit� (ie avec les clefs associ�es aux valeurs) en base de 
donn�es, ou encore dans un fichier. En fait, c'est la s�rialisation.

Il existe une fonction bien particuli�re et puissante en PHP (depuis la version 4) pour ce genre de cas : serialize(). 

Voici la d�finition officielle de cette fonction : 

serialize() retourne une cha�ne de caract�res contenant une repr�sentation lin�aire de value, pour stockage. C'est une 
technique pratique pour stocker ou passer des valeurs de PHP entre scripts, sans perdre ni leur structure, ni leur type. 

Note : le principe de serialize() n'est pas de faire un transtypage classique, mais bien une lin�arisation  qui conserve le type (et
donc les informations) de la variable originale. 

Par exemple : 

<?php 
$tab = array(5, 6, 7, 8, 'string');
echo serialize($tab); ?>

Ce qui affiche :

a:5:{i:0;i:5;i:1;i:6;i:2;i:7;i:3;i:8;i:4;s:6:"string";}

Remarque : "a" pour array, le 4 pour la taille de l'array. i pour entier, s pour les cha�nes de caract�res. A chaque fois, l'index 
est d'abord sp�cifi�. 

On peut m�me lin�ariser les nombres (que ce soit int ou float, etc.), les bool�ens, les cha�nes de caract�res m�me, bref, tous
les types de variables. 

La fonction serialize() n'aurait eu que peu d'int�r�t, si on ne pouvait pas r�cup�rer les donn�es originales sous leur forme 
originale. C'est ce que permet de faire la fonction unserialize(). 
b./ L'enregistrement dans un fichier

Exemple :

<?php 
$srlzd = serialize(array(1, 2, 3, 'quatre' => 'valeur4'));
$fichier = fopen('serialized.txt', 'a+');

fwrite($fichier, $srlzd);
fclose($fichier);

?>

Note : il reste un probl�me, si le fichier trouve un caract�re sp�cial (\n, \t...), il va le traduire comme tel. Il faut s'assurer
de prot�ger de cela, par exemple en doubland le \.


c./ L'utilisation de serialize en barre d'adresse

Il faut faire attention en transmettant le r�sultat de serialize() via GET : certains caract�res n'�tant pas support�s dans
les URL ou ayant un sens bien particulier, notamment les ";" qui sont pr�sents dans un array serializ�, il faut encoder ces
caract�res via la m�thode :

urlencode() qui encode ces caract�res non support�s ou � sens particulier, pour le passage via URL.

Exemple : 

<?php 
//header('Location: magic_methods.php?data=' . urlencode($srlzd));
// exit;

?>

Ensuite, il faudra utiliser urldecode() pour effectuer l'op�ration inverse. 

d./ L'enregistrement en base de donn�es

Il faut passer par les requ�tes pr�par�es pour prot�ger les donn�es contre les guillemets, apostrophes ou encore les antislashes.
Ces caract�res (", ', \) sont les plus dangereux si non control�s. Et ce n'est pas parce que je n'ai pas d'erreur que tout va
bien...

2.) Lin�ariser des objets 

J'ai un sys�me de protection de sessions sur mon site avec une classe Connexion. Cette classe, comme son nom l'indique, 
aura pour r�le d'�tablir une connexion � la BDD. Comment faire pour stocker l'objet cr�� dans une variable $_SESSION ?

Eh bien, je fais : 

$_SESSION['connexion'] = $objetConnexion;

Cela fonctionne, mais est-ce que je sais ce qui se passe quand j'effectue ce genre d'op�ration ? Ou plut�t ce qui se passe <strong>
� la fin du script</strong> ? 

En fait, � la fin du script, le tableau de session est lin�aris� automatiquement. 

Pour bien comprendre, je vais lin�ariser moi-m�me mon objet. Voici ce que je vais faire :

	-	Cr�ation de l'objet $objetConnexion = new Connexion();
	-	transformation de l'objet en cha�ne de caract�res ($_SESSION['connexion'] = serialize($objetConnexionn););
	-	changement de page
	-	transformation de la cha�ne de caract�res en objet ($objetConnexion = unserialize($_SESSION['connexion']);)
	
En effet, on peut tr�s bien lin�riser un objet : et comment ?

Un objet est un ensemble d'attributs, tout simplement. Les m�thodes ne sont pas stock�es dans l'objet, c'est la classe qui s'en
occupe. Ainsi, je pourrai conserver mon objet dans une variable sous forme de chaine de caract�res. 

La chose essentielle � comprendre est que la fonction serialize() est automatiquement appel�e sur l'array $_SESSION � la 
fin du script, mon objet est donc automatiquement lin�aris� � la fin du script. C'est uniquement dans un but didactique que je
l'ai lin�ris� manuellement ici.

La seconde fonction, unserialize(), retournera la cha�ne de caract�res pass�e en param�tre sous forme d'objet. En gros, cette
fonction lit la cha�ne de caract�res pass�e en param�tre sous forme d'objet. En gros, cette fonction lit la cha�ne de 
caract�res, cr�e une instance de la classe correspondante et assigne � chaque attribut la valeur qu'ils avaient. Ainsi,
je pourrai utiliser l'objet retourn� (appel des m�thodes, attributs et diverses op�rations) comme avant. Cette fonction est
automatiquement appel�e d�s le d�but du script pour conserver le tableau de sessions pr�c�demment enregistr� dans le fichier. 

Attention : si j'ai lin�aris� un objet manuellement, il ne sera JAMAIS restaur� automatiquement. 

Et le rapport avec les m�thodes magiques ?

En fait, les fonctions cit�es ci-dessus (serialize et unserialize) ne se contentent pas de transformer le param�tre qu'on
leur passe en autre chose : elles v�rifient si, dans l'objet pass� en param�tre (pour serealize()), il y a une m�thode __sleep(),
auquel cas celle-ci est ex�cut�e. Si c'est unserialize() qui est appel�e, la fonction v�rifie si l'objet obtenu comporte
une m�thode __wakeup(), auquel cas celle-ci est appel�e. 

3.) "serialize" et "__sleep"

La m�thode magique __sleep() est appel�e pour nettoyer l'objet ou pour sauver des attributs. Si la m�thode magique __sleep()
n'existe pas, tous les attributs seront sauv�s. Cette m�thode doit renvoyer un tableau avec les noms des attributs � sauver. 
Par exemple, si je veux sauver $serveur et $login, la fonction devra retourner ['serveur', 'login']. 

Voici � quoi pourrait ressembler ma classe Connexion :

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
		// Ici sont � placer les instructions � ex�cuter juste avant la lin�arisation. 
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

Maintenant, je vais simplement impl�menter la fonction __wakepup. Je vais y placer un appel � la m�thode connexionBDD() qui
se chargera de me connecter � ma base de donn�es puisque les identifiants, serveur et nom de la base ont �t� sauvegard�s et
ainsi restaur�s � l'appel de la fonction unserialize(). 

R�f Connexion

Mais bon, si l'on pr�f�re sauver tous les attributs, ce n'est pas la peine de tourner autour du pot, on peut directement 
enregistrer un objet dans une entr�e de session, sans appeler serialize, unserialize (et donc sans d�finir les m�thodes __sleep,
__wakeup). Ce code fonctionnera parfaitement : 

<?php 
//session_start();	//  � appeler au d�but de la page bien s�r

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

Ainsi, mon objet a bel et bien �t� sauvegard� comme il fallait, et tous les attributs ont �t� sauv�s. C'est magica!

Attention : �tant donn� que mon objet est restaur� automatiquement lors de l'appel de session_start(), la classe correspondante 
doit �tre d�clar�e <strong>avant</strong>, sinon l'objet d�s�rialis� sera une instance de __PHP_Incomplete_Class_Name, classe qui
ne contient aucune m�thode (cela produira donc un objet inutile). Si j'ai un autoload qui chargera la classe automatiquement, il
sera appel�.

IV./ Autres m�thodes magiques

Il s'agira ici de __toString(), __set_state(), __invoke() et __debugInfo()

1.) __toString()

La m�thode magique __toString() est appel�e lorsque l'objet est amen� � �tre converti en cha�ne de caract�res. Cette m�thode
doit retourner la cha�ne de caract�res souhait�e. 

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

La m�thode __set_state() est appel�e lorsque j'appelle la fonction var_export()  en passant mon objet � exporter en param�tre. Cette
fonction var_export() a pour r�le d'exporter la variable pass�e en param�tre sous forme de code PHP (cha�ne de caract�res). Si 
je ne sp�cifie pas de m�thode __set_state() dans ma classe, une erreur fatale sera lev�e. 

Ma m�thode __set_state() prend un param�tre, la liste des attributs ainsi que leur valeur dans un tableau associatif ('attribut' =>
'valeur'). Ma m�thode magique devra retourner l'objet � exporter. Il faudra donc cr�er un nouvel objet et lui assigner les
valeurs qu'on souhaite, puis le retourner. 

Attention : Il ne faut jamais retourner $this, car cette variable n'existera pas dans cette m�thode ! var_export() reportera donc
une valeur nulle.

Puisque le code var_export() retourne du code PHP valide, on peut utiliser la fonction eval() qui ex�cute du code PHP sous forme
de cha�ne de caract�re qu'on lui passe en param�tre.

Par exemple, pour retourner un objet en sauvant ses attributs, on pourrait faire : 

<?php 
class Export {
	protected $chaine1, $chaine2;
	
	public function __construct($param1, $param2) {
		$this->chaine1 = $param1;
		$this->chaine2 = $param2;
	}
	
	public function __set_state($valeurs) {	// Liste des attributs de l'objet en param�tre
		$obj = new Export($valeurs['chaine1'], $valeurs['chaine2']);	// On cr�e un objet avec les attributs de l'objet que 
																		// l'on veut exporter;
		return $obj;	// on retourne l'objet cr��.
	}
}

$obj1 = new Export('Hello', 'world !');

eval('$obj2 = ' . var_export($obj1, true) . ';');	// On cr�e un autre objet, celui-ci ayant les m�mes attributs que l'objet
													// pr�c�dent
echo '<pre>', print_r($obj2, true), '</pre>';
?>

3.) __invoke()

La m�thode __invoke() permet d'utiliser un objet comme une fonction. A peu pr�s ainsi :

&lt;?php
$obj = new MyClass;

$obj('Petit test');

Par contre, ce code g�n�rera une erreur fatale... Pour r�soudre ce probl�me, je vais devoir utiliser une m�thode magique __invoke().
Elle est appel�e d�s qu'on essaye d'utiliser l'objet comme fonction (comme je viens de le faire). Cette m�thode comprend 
autant de param�tres que d'arguments pass�s � la fonction. 

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

Cette m�thode magique est invoqu�e sur mon objet lorsque l'on appelle la fonction var_dump(). Pour info, cette fonction permet 
d'obtenir des informations sur la variable qu'on lui donne. Si on lui donne un objet, var_dump() va afficher les d�tails de 
tous les attributs de l'objet, qu'ils soient publics, prot�g�s ou priv�s. La m�thode magique __debugInfo() permet de modifier
ce comportement en ne s�lectionnant que les attributs � afficher ainsi que ce qu'il faut afficher. Pour ce faire, cette
m�thode renverra sous forme de tableau associatif la liste des attributs � afficher avec leurs valeurs. Voici un exemple
d'utilisation (simplifi� au max) : 

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
var_dump($f);	// Affiche les informations retourn�es par fstat
?>

J'ai ici un gestionnaire de fichiers qui me permet de g�rer facilement la lecture et l'�criture d'un fichier (du moins, on imagine
que �a le fait). Un var_dump() simple sur mon objet ne serait pas tr�s r�v�lateur. Par contre, obtenir les informations sur le
fichier actuellement ouvert le serait plus, et c'est pr�cis�ment ce que l'on fait en �crivant la m�thode __debugInfo() avec l'appel
de fstat().

En r�sum� :

Les m�thodes magiques dont je me servirai le plus souvent sont : __construct(), __set(), __get() et __call(). Les autres sont plus
"gadget" et je les rencontrerai moins souvent. 
</pre>
</body>
</html>