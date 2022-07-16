<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Les objets en profondeur</title>
</head>
<body>
<pre>
												Les objets en profondeur
												
I./ Un objet, un identifiant
1.) Une histoire de référence et de clonage

Un peu comme en JavaScript, lors d'une assignation, les objets se font passer par référence, et non pas par copie.

Exemple : 

<?php 
class MaClasse {
	public $attribut1;
	public $attribut2;
}

$a = new MaClasse;

$b = $a;	// $b est une référence de $a;

$a->attribut1 = 'Hello';
$b->attribut2 = 'the world !';
echo $b->attribut1 . ' ' . $a->attribut2; // affiche 'Hello the world !'
?>

On me parle ici d'identifiant d'objet.

Afin de copier un objet en créant un nouvel objet à part entière, voilà comment on procède : 

$copie = clone $origine;	// On copie le contenu de l'objet $origine dans l'objet $copie

2.) La méthode magique __clone()

Lorsque je clone un objet, la méthode __clone() du nouvel objet sera appelée (du moins, si je l'ai définie). Je ne peux pas appeler
cette méthode directement. C'est la méthode __clone() du nouvel objet créé qui est appelée, et non pas la méthode __clone()
de l'objet à cloner. 

Je peux utiliser cette méthode pour modifier certains attributs pour le nouvel objet, ou alors incrémenter un compteur
d'instances par exemple : 

<?php 
class MyClass {
	public static $instances = 0;
	
	public function __construct() {
		self::$instances++;
	}
	
	public function __clone() {
		self::$instances++;
	}
	
	public static function getInstances() {
		return self::$instances;
	}
}

$a = new MyClass;
$b = clone $a;

echo 'Nombre d\'instances de MyClass : ' , MyClass::getInstances();

?>

II./ Comparaison des objets

On peut comparer deux instances d'objet grâce à l'opérateur '==' . Pour que la condition renvoyée soit vérifiée, il faut que les deux
instances aient les mêmes attributs et les mêmes valeurs, mais également que les deux instances soient des instances de la même
classe.

Il y a aussi l'autre opérateur de comparaison : '==='. Ce dernier renverra true uniquement dans le cas où les deux instances 
sont strictement identiques, dans le sens où elles doivent pointer vers le même objet. Bien sûr, l'une ou l'autre peut être
une référence de l'autre, ou alors elles peuvent être toutes les deux des références pointant vers le même objet. 

III./ Parcourir mes objets

Le fait de parcourir un objet consiste à lire tous les attributs visibles de l'objet. Evidemment, la question de la visibilité
dépendra beaucoup, pour ne pas dire uniquement, du contexte dans lequel on se place pour effectuer ce parcours.

Cela se fait avec un foreach, comme pour les tableaux. 

Exemple : 

<?php 
class MaClasse2 {
	public $attribut1 = 'Premier attribut public';
	public $attribut2 = 'Deuxième attribut public';
	
	protected $attributProtege1 = 'Premier attribut protégé';
	protected $attributProtege2 = 'Deuxième attribut protégé';
	
	private $attributPrive1 = 'Premier attribut privé';
	private $attributPrive2 = 'Deuxième attribut privé';
	
	function listeAttributs()
	{
		foreach ($this as $attribut => $valeur)
		{
			echo '<strong>', $attribut, '</strong> => ', $valeur, '<br />';
		}
	}
}

class Enfant extends MaClasse2
{
	function listeAttributs() // Redéclaration de la fonction pour que ce ne soit pas celle de la classe mère qui soit appelée.
	{
		foreach ($this as $attribut => $valeur)
		{
			echo '<strong>', $attribut, '</strong> => ', $valeur, '<br />';
		}
	}
}

$classe = new MaClasse2;
$enfant = new Enfant;

echo '---- Liste les attributs depuis l\'intérieur de la classe principale ----<br />';
$classe->listeAttributs(); // affichera tous les attributs

echo '<br />---- Liste les attributs depuis l\'intérieur de la classe enfant ----<br />';
$enfant->listeAttributs();	// affichera tous les attributs sauf les attributs privés de la classe mère

echo '<br />---- Liste les attributs depuis le script global ----<br />';

foreach ($classe as $attribut => $valeur)
{
	echo '<strong>', $attribut, '</strong> => ', $valeur, '<br />';	// affichera uniquement les attributs publics
}
?>

IV./ Obtenir le nom de la classe

Dans toutes les méthodes d'une classe, il est possible d'accéder à son nom grâce à ::class. Pour ce qui est à placer avant ce
double deux points, cela dépend de ce que l'on veut obtenir. Généralement, on a le choix entre self et static. On révient
à la résolution statique à la volée en ce qui concerne static::. 

Exemple : 

<?php 
class Mere {
	public function __construct() {
		echo static::class;
	}
	
	public function afficherClasseMere() {
		echo self::class;
	}
}

class Fille extends Mere {
	
}

echo '<br />';
$a = new Fille;	// affiche "Fille"
echo '<br />';
$a->afficherClasseMere();	// affiche "Mere"

?>
</pre>
</body>
</html>