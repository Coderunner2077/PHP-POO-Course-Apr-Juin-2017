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
			trigger_error('La position sp�cifi�e n\'est pas valide', E_USER_WARNING);
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
			trigger_error('La position sp�cifi�e n\'est pas valide', E_USER_WARNING);
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
		echo 'L\'attribut d a bien �t� supprim�';
}

echo '</pre>';
echo '<pre>';
var_dump($obj);
echo '</pre>';

echo count($obj);
$obj2 = new MyClass2(['premier', 'deuxi�me', 'troisi�me', 'quatri�me', 'cinqui�me']);
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
echo '<p>apr�s un seek(2) : ' , $obj2->current() , '</p>';

$obj2['d'] = 'dernier attribut';
$obj2->next();
echo '<br />' , $obj2->current();

echo '<p>Nouveaux tests pour voir les am�liorations...</p>';
$obj2 = new MyClass2(['premier', 'deuxi�me', 'troisi�me', 'quatri�me', 'cinqui�me']);
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

I./ Les quelques nouveaut�s

En tout cas, ce sont des nouveaut�s pour moi.

1.) Les constantes d'interfaces

Les constantes d'interfaces fonctionnent exactement comme les constantes de classes. Elles ne peuvent pas �tre �cras�es par des
classes qui impl�mentent l'interface. Exemple : 

<?php 
interface iInterface {
	const MA_CONSTANTE = 'Hello !';
}

echo iInterface::MA_CONSTANTE;	// Affiche 'Hello !'

class Cl implements iInterface {

}

echo Cl::MA_CONSTANTE;	// Affiche 'Hello! !'

?>

2.) H�riter ses interfaces

Je ne sais plus s'il y avait cette posibilit� en Java, je pense qu'il devrait y avoir. Bref, on peut faire h�riter en PHP les
interaces gr�ce � l'op�rateur extends. Mais on ne peut pas r��crire ni une m�thode, ni une constante qui a d�j� �t� list�e 
dans l'interface parente. 

Exemple : 

<?php 

interface iA
{
	public function test1();
}

interface iB extends iA
{
	public function test1 ($param1, $param2); // Erreur fatale : impossible de r��crire cette m�thode.
}

interface iC extends iA
{
	public function test2();
}

class MyClasse implements iC
{
	// Pour ne g�n�rer aucune erreur, on doit �crire les m�thodes de iC et aussi de iA.
	
	public function test1()
	{
		
	}
	
	public function test2()
	{
		
	}
}
?>

Contrairement aux classes, les interfaces peuvent h�riter de plusieurs interfaces � la fois. Il me suffit de s�parer leur nom
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

Dans cet exemple, une classe impl�mentant oC devra impl�menter � la fois les trois m�thodes test1(), test2() et test3().

II./ Les interfaces pr�d�finies

Gr�ce � certaines interfaces pr�d�finies, je vais pouvoir modifier le comportement de mes objets ou r�aliser plusieurs 
choses pratiques. Il y a bcp d'interfaces pr�d�finies, et seulement quatre seront pr�sent�es ici. 
Je vais ici cr�er un "tableau-objet", et pas que.

1.) D�finition d'un iterateur

Afin de comprendre un peu plus ce que l'on va faire, on va commencer par voir ce qu'est un iterateur. C'est tout vu.
On dit d'un objet que l'on peut parcourir, qu'il est iteratif. Pour rendre l'objet iteratif, je vais imposer un comportement
� mes objets afin qu'ils puissent �tre parcourus. Ce comportement � imposer se fera par le biais des interfaces ! L'interface
la plus basique pour rendre un objet iteratif est Iterator.

2.) L'interface Iterator

Commen�ons d'abord par l'interface Iterator. Si ma classe impl�mente cette interface, alors je pourrai modifier le comportement
de mon objet lorsqu'il est parcouru. Cette interface pr�sente cinq m�thodes :

	-	current() : renvoie l'�l�ment courant
	-	key() : retourne la cl� de l'�l�ment courant
	-	next() : d�place le pointeur sur l'�l�ment suvant
	-	rewind() : remet le pointeur sur le premier �l�ment
	-	valid() : v�rifie si la position courante est valide
	
En �crivant ces m�thodes, on pourra renvoyer la valeur qu'on veut, et pas forc�ment la valeur de l'attribut actuellement lu. 
Imaginons qu'on ait un attribut qui soit un tableau. On pourrait tr�s bien cr�er un petit script qui, au lieu de parcourir 
l'objet, parcourt le tableau ! J'aurai besoin d'un attribut $position qui stocke la position actuelle. 

R�f MaClasse

3.) L'interface SeekableIterator 

Cette interface h�rite de l'interface Iterator, je n'aurai donc pas besoin d'impl�menter les deux � la fois � ma classe. 

SeekableIterator ajoute une m�thode � la liste des m�thodes d'Iterator : la m�thode seek(). Cette m�thode permet de placer le 
curseur interne � une position pr�cise. Elle demande donc un argument : la position du curseur � laquelle il faut le placer. Il
faudra prendre des pr�cautions au cas o� la valeur de l'argument ne soit pas valide. 

R�f Maclasse

4.) L'interface ArrayAccess

Je vais enfin, gr�ce � cette m�thode, pouvoir placer des crochets � la suite de mon objet avec la cl� � laquelle acc�der,
comme sur un vrai tableau. L'interface ArrayAccess liste quatre m�thodes : 

	-	offsetExists() : m�thode qui v�rifiera l'existence de la cl� entre crochets lorsque l'objet est pass� � la fonction
							isset() ou empty() (cette valeur entre crochets est pass�e � la m�thode en param�tre)
	-	offsetGet() : m�thode appel�e lorsqu'on fait un simple $obj['cl�']. La valeur 'cl�' est donc pass�e � la m�thode offsetGet()
	-	offsetSet() : m�thode appel�e lorsqu'on assigne une valeur � une entr�e. Cette m�thode re�oit donc deux arguments, la valeur
						de la cl� et la valeur qu'on veut lui assigner.
	-	offsetUnset() : m�thode appel�e lorsqu'on appelle la fonction unset()  sur l'objet avec une valeur entre crochets. Cette
						m�thode re�oit un argument, la valeur qui sera mise entre les crochets. 
						
R�f MaClasse

On se rapproche vraiment du comportement de tableau, il manque juste un petit quelque chose pour que ce soit absolument parfait...

5.) L'interface Countable

Cette interface contient une m�thode : la m�thode count(). Celle-ci doit obligatoirement renvoyer un entier qui sera la valeur
renvoy�e par la fonction count() appel�e sur mon objet. Cette m�thode n'est pas bien compliqu�e � impl�menter, il suffit juste
de retourner le nombre d'entr�es de mon tableau. 

R�f MaClasse

6.) La classe ArrayIterator

Une classe comme MaClasse que j'ai cr��e pour pouvoir cr�er des "objets-tableaux", existe d�j�. En effet, PHP poss�de 
nativement une classe nomm�e ArrayIterator. Comme ma pr�c�dente classe, celle-ci impl�mente les quatre interfaces que l'on a vues
ici. 

Il y aura toutefois une diff�rence par rapport � MaClasse : cette classe impl�mente un constructeur qui accepte un tableau en guise
d'argument. Et c'est ce tableau qui sera "transform�" en objet. Ainsi, si je fais un $instanceArrayIterator['cle'], alors � l'�cran
s'affichera l'entr�e qui a pour cl� "cl�" du tableau pass� en param�tre.

Remarque : je suis all� plus loin que ce qui est propos� dans ce chapitre. A savoir, j'ai r�ussi � manier un
objet comme s'il s'agissait vraiment d'un tableau, dans le sens o� ce sont les noms des attributs m�mes de l'objet que je mets entre 
crochets. R�f MyClass, MyClass2

Remarque2 : j'ai m�me am�lior� MaClasse pour qu'il accepte un tableau en param�tre de son constructeur.

Remarque3 : ...mais c'est pas encore top.
</pre>
</body>
</html>
	