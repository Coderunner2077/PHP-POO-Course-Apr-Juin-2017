<?php
header('Content-type: text/html; charset="iso-8859-1"');
class MaClasse implements SeekableIterator, ArrayAccess, Countable {
	private $_position;
	private $_tab = [];
	
	public function __construct(array $tableau) {
		$this->_position = 0;
		$this->_tab = $tableau;
	}
	
	public function current() {
		return $this->_tab[$this->_position];
	}
	
	public function key() {
		return $this->_position;
	}
	
	public function next() {
		$this->_position++;
	}
	
	public function rewind() {
		$this->_position = 0;
	}
	
	public function valid() {
		return isset($this->_tab[$this->_position]);
	}
	
	public function seek($pos) {
		$anciennePos = $this->_position;
		$this->_position = $pos;
		if(!$this->valid()) {
			trigger_error('La position spécifiée n\'est pas valide', E_USER_WARNING);
			$this->_position = $anciennePos;
		}
	}
	
	public function offsetExists($offset) {
		return isset($this->_tab[$offset]);
	}
	
	public function offsetGet($offset) {
		return $this->_tab[$offset];
	}
	
	public function offsetSet($offset, $value) {
		$this->_tab[$offset] = $value;
	}
	
	public function offsetUnset($offset) {
		unset($this->_tab[$offset]);
	}
	
	public function count() {
		return count($this->_tab);
	}
}

$objet = new MaClasse(['un', 'deux', 'trois', 'quatre']);

foreach($objet as $key => $value) {
	echo $key , '=>' , $value, '<br />';
}

echo 'La boucle while maintenant : <br />';
$objet->rewind();
while($objet->valid()) {
	echo $objet->current() , '<br />';
	$objet->next();
}

echo $objet[3]; // affiche "quatre" (contenu du obj->_tab[3])

class MyClass implements ArrayAccess, Countable {
	protected $a = 1;
	protected $b = 2;
	protected $c = 3;
	protected $d = 4;
	

	public function offsetExists($cle) {
		return isset($this->$cle);
	}
	
	public function offsetGet($cle) {
		if(isset($this->$cle))
			return $this->$cle;
	}
	
	public function offsetSet($cle, $valeur) {
		$this->$cle = $valeur;
	}
	
	public function offsetUnset($cle) {
		unset($this->$cle);
	}
	
	public function count() {
		$nb = 0;
		foreach($this as $cle)
			$nb++;
		return $nb;
	}
}
class MyClass2 extends MyClass implements SeekableIterator {
	protected $position = 0;
	protected $indexes = [];
	
	public function __construct(array $tableau) {
		$parent = new MyClass;
		foreach($parent as $cle => $value) {
			$this->indexes[] = $cle;
		}
		
		while($this->valid()) {
			if(!isset($tableau[$this->position]))
				break;
			$this[$this->key()] = $tableau[$this->position];
			$this->next();
		}
		$this->position = 0;
	}
	
	public function current() {
		if($this->valid())
			return $this[$this->indexes[$this->position]];
		else
			return 'out of bound';
	}
	
	public function next() {
		$this->position++;
	}
	
	public function key() {
		return $this->indexes[$this->position];
	}
	
	public function valid() {
		return isset($this->indexes[$this->position]);
	}
	
	public function rewind() {
		$this->position = 0;
	}
	
	public function seek($pos) {
		$anciennePos = $this->position;
		$this->position = $pos;
		if(!$this->valid()) {
			trigger_error('La position spécifiée n\'est pas valide', E_USER_WARNING);
			$this->position = $anciennePos;
		}
	}
	
}

$obj = new MyClass;
echo '<pre>';
echo $obj['a'] , ' ' , $obj['b'], ' ' , $obj['c'] , ' ' , $obj['d'];
$obj['a'] = 'premier attribut';
echo '<br />' , $obj[0] , '<br />';
if(isset($obj['d'])) {
	unset($obj['d']);
	if(empty($obj['d']))
		echo 'L\'attribut d a bien été supprimé';
}

echo '</pre>';
echo '<pre>';
var_dump($obj);
echo '</pre>';

echo count($obj);
$obj2 = new MyClass2(['premier', 'deuxième', 'troisième', 'quatrième', 'cinquième']);
echo '<br />Et pour la nouvelle instance, le nombre d\'attributs: ' , count($obj2);
echo '<br />Suspense...<br />';
echo '<pre>' , var_dump($obj2, true) , '</pre>';
$obj2->rewind();
echo '<p>Boucle while : </p>';
while($obj2->valid()) {
	echo $obj2->current() , '<br />';
	$obj2->next();
}
$obj2->seek(2);
echo '<p>après un seek(2) : ' , $obj2->current() , '</p>';

$obj2['d'] = 'dernier attribut';
$obj2->next();
echo '<br />' , $obj2->current();

echo '<p>Nouveaux tests pour voir les améliorations...</p>';
$obj2 = new MyClass2(['premier', 'deuxième', 'troisième', 'quatrième', 'cinquième']);
foreach($obj2 as $cle => $value) {
	echo $cle , '=>' , $value , '<br />';
}

echo '<hr>';
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Les interfaces</title></head>
<body>
<pre>
														Les interfaces
														
Les interfaces comme je les ai vu en Java, apparemment. 

I./ Les quelques nouveautés

En tout cas, ce sont des nouveautés pour moi.

1.) Les constantes d'interfaces

Les constantes d'interfaces fonctionnent exactement comme les constantes de classes. Elles ne peuvent pas être écrasées par des
classes qui implémentent l'interface. Exemple : 

<?php 
interface iInterface {
	const MA_CONSTANTE = 'Hello !';
}

echo iInterface::MA_CONSTANTE;	// Affiche 'Hello !'

class Cl implements iInterface {

}

echo Cl::MA_CONSTANTE;	// Affiche 'Hello! !'

?>

2.) Hériter ses interfaces

Je ne sais plus s'il y avait cette posibilité en Java, je pense qu'il devrait y avoir. Bref, on peut faire hériter en PHP les
interaces grâce à l'opérateur extends. Mais on ne peut pas réécrire ni une méthode, ni une constante qui a déjà été listée 
dans l'interface parente. 

Exemple : 

<?php 

interface iA
{
	public function test1();
}

interface iB extends iA
{
	public function test1 ($param1, $param2); // Erreur fatale : impossible de réécrire cette méthode.
}

interface iC extends iA
{
	public function test2();
}

class MyClasse implements iC
{
	// Pour ne générer aucune erreur, on doit écrire les méthodes de iC et aussi de iA.
	
	public function test1()
	{
		
	}
	
	public function test2()
	{
		
	}
}
?>

Contrairement aux classes, les interfaces peuvent hériter de plusieurs interfaces à la fois. Il me suffit de séparer leur nom
par une virgule. Exemple : 

<?php
interface oA
{
  public function test1();
}

interface oB
{
  public function test2();
}

interface oC extends oA, oB
{
  public function test3();
}
?>

Dans cet exemple, une classe implémentant oC devra implémenter à la fois les trois méthodes test1(), test2() et test3().

II./ Les interfaces prédéfinies

Grâce à certaines interfaces prédéfinies, je vais pouvoir modifier le comportement de mes objets ou réaliser plusieurs 
choses pratiques. Il y a bcp d'interfaces prédéfinies, et seulement quatre seront présentées ici. 
Je vais ici créer un "tableau-objet", et pas que.

1.) Définition d'un iterateur

Afin de comprendre un peu plus ce que l'on va faire, on va commencer par voir ce qu'est un iterateur. C'est tout vu.
On dit d'un objet que l'on peut parcourir, qu'il est iteratif. Pour rendre l'objet iteratif, je vais imposer un comportement
à mes objets afin qu'ils puissent être parcourus. Ce comportement à imposer se fera par le biais des interfaces ! L'interface
la plus basique pour rendre un objet iteratif est Iterator.

2.) L'interface Iterator

Commençons d'abord par l'interface Iterator. Si ma classe implémente cette interface, alors je pourrai modifier le comportement
de mon objet lorsqu'il est parcouru. Cette interface présente cinq méthodes :

	-	current() : renvoie l'élément courant
	-	key() : retourne la clé de l'élément courant
	-	next() : déplace le pointeur sur l'élément suvant
	-	rewind() : remet le pointeur sur le premier élément
	-	valid() : vérifie si la position courante est valide
	
En écrivant ces méthodes, on pourra renvoyer la valeur qu'on veut, et pas forcément la valeur de l'attribut actuellement lu. 
Imaginons qu'on ait un attribut qui soit un tableau. On pourrait très bien créer un petit script qui, au lieu de parcourir 
l'objet, parcourt le tableau ! J'aurai besoin d'un attribut $position qui stocke la position actuelle. 

Réf MaClasse

3.) L'interface SeekableIterator 

Cette interface hérite de l'interface Iterator, je n'aurai donc pas besoin d'implémenter les deux à la fois à ma classe. 

SeekableIterator ajoute une méthode à la liste des méthodes d'Iterator : la méthode seek(). Cette méthode permet de placer le 
curseur interne à une position précise. Elle demande donc un argument : la position du curseur à laquelle il faut le placer. Il
faudra prendre des précautions au cas où la valeur de l'argument ne soit pas valide. 

Réf Maclasse

4.) L'interface ArrayAccess

Je vais enfin, grâce à cette méthode, pouvoir placer des crochets à la suite de mon objet avec la clé à laquelle accéder,
comme sur un vrai tableau. L'interface ArrayAccess liste quatre méthodes : 

	-	offsetExists() : méthode qui vérifiera l'existence de la clé entre crochets lorsque l'objet est passé à la fonction
							isset() ou empty() (cette valeur entre crochets est passée à la méthode en paramètre)
	-	offsetGet() : méthode appelée lorsqu'on fait un simple $obj['clé']. La valeur 'clé' est donc passée à la méthode offsetGet()
	-	offsetSet() : méthode appelée lorsqu'on assigne une valeur à une entrée. Cette méthode reçoit donc deux arguments, la valeur
						de la clé et la valeur qu'on veut lui assigner.
	-	offsetUnset() : méthode appelée lorsqu'on appelle la fonction unset()  sur l'objet avec une valeur entre crochets. Cette
						méthode reçoit un argument, la valeur qui sera mise entre les crochets. 
						
Réf MaClasse

On se rapproche vraiment du comportement de tableau, il manque juste un petit quelque chose pour que ce soit absolument parfait...

5.) L'interface Countable

Cette interface contient une méthode : la méthode count(). Celle-ci doit obligatoirement renvoyer un entier qui sera la valeur
renvoyée par la fonction count() appelée sur mon objet. Cette méthode n'est pas bien compliquée à implémenter, il suffit juste
de retourner le nombre d'entrées de mon tableau. 

Réf MaClasse

6.) La classe ArrayIterator

Une classe comme MaClasse que j'ai créée pour pouvoir créer des "objets-tableaux", existe déjà. En effet, PHP possède 
nativement une classe nommée ArrayIterator. Comme ma précédente classe, celle-ci implémente les quatre interfaces que l'on a vues
ici. 

Il y aura toutefois une différence par rapport à MaClasse : cette classe implémente un constructeur qui accepte un tableau en guise
d'argument. Et c'est ce tableau qui sera "transformé" en objet. Ainsi, si je fais un $instanceArrayIterator['cle'], alors à l'écran
s'affichera l'entrée qui a pour clé "clé" du tableau passé en paramètre.

Remarque : je suis allé plus loin que ce qui est proposé dans ce chapitre. A savoir, j'ai réussi à manier un
objet comme s'il s'agissait vraiment d'un tableau, dans le sens où ce sont les noms des attributs mêmes de l'objet que je mets entre 
crochets. Réf MyClass, MyClass2

Remarque2 : j'ai même amélioré MaClasse pour qu'il accepte un tableau en paramètre de son constructeur.

Remarque3 : ...mais c'est pas encore top.
</pre>
</body>
</html>
	