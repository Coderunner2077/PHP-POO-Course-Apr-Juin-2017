<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Les exceptions</title>
</head>
<body>
<pre>
													Les traits
													
Depuis sa version 5.4, PHP intègre un moyen de réutiliser le code d'une méthode dans deux classes indépendantes. Cette
fonctionnalité permet de repousser les limites de l'héritage simple (en PHP, une classe ne pouvant hériter que d'une seule
classe mère). On va donc se pencher sur les traits ici afin de pallier le problème de duplication de méthode. 

I./ Le principe des traits
1.) Posons le problème

Admettons que l'on ait deux classes, Writer et Mailer. La 1re est chargée d'écrire du texte dans un fichier, tandis que la 2nde
envoie un texte par mail. Cependant, il serait agréable de mettre en forme le texte. Pour cela, je décide de formater le texte
en HTML. Or, un problème se pose : je vais devoir effectuer la même opération (celle de formater en HTML) dans deux classes
complétement différentes et indépendantes : 

<?php 
/*
class Writer {
	public function write($text) {
		$text = '<p>Date : ' .date('d/m/Y'). '</p>' . "\n".
				'<p>'.nl2br($text).'</p>';
		
		file_put_contents('fichier.txt', $text);
	}
}

class Mailer {
	public function send($text) {
		$text = '<p>Date : ' . date('d/m/Y') . '</p>' . "\n" .
				'<p>' .nl2br($text).'</p>';
		mail('login@dldf.fr', 'Test de traits', $text);
	}
}
*/
?>

Ici, ce code est petit, et la duplication n'est donc pas énorme, mais elle est belle et bien présente. Dans une application
de plus grande envergure, ce code pourrait être dupliqué pas mal de fois. Si en plus il fallait formater le texte autrement :
ce serait la cata, car il faudrait modifier chaque partie du code qui formatait du texte. 

Justement, les traits permettent de résoudre ce genre de problème.

2.) Résoudre le problème grâce aux traits
a./ Syntaxe de base

Les traits sont un moyen <strong>d'externaliser</strong> du code. Plus précisément, les traits définissent des méthodes que
les classes peuvent utiliser. Avant de résoudre le problème évoqué, je vais me pencher sur la syntaxe des traits pour pouvoir
m'en servir.

<?php 
trait MonTrait {
	public function hello() {
		echo 'Hello !';
	}
}

class A {
	use MonTrait;
}

class B {
	use MotTrait;
}

$obj = new A;
$obj->hello();	// Affiche 'Hello !'

$obj2 = new B;
$obj2->hello();	// Affiche 'Hello !'

?>

Comme je peux le constater, un trait n'est autre qu'une mini-classe. Dedans, je n'ai déclaré qu'une seule méthode. L'utilisation
d'un trait dans une classe se fait grâce au mot-clé use. En utilisant ce mot-clé, toutes les méthodes du trait vont être
<strong>importées</strong> dans la classe. 

b./ Retour sur mon formateur

<?php 
trait HTMLFormater {
	protected function format($text) {
		return '<p>Date : ' . date('d/m/Y') . '</p>' . "\n" .
				'<p>' .nl2br($text).'</p>';
	}
}

class Writer {
	use HTMLFormater;
	public function write($text) {		
		file_put_contents('fichier.txt', $this->format($text));
	}
}

class Mailer {
	use HTMLFormater;
	public function send($text) {
		mail('login@dldf.fr', 'Test de traits', $this->format($text));
	}
}

$w = new Writer;
$w->write('Hello the world !');

$m = new Mailer;
$m->send('Hello the world !');
?>

On peut également utiliser plusieurs traits dans une classe.

3.) Utiliser plusieurs traits
a./ Syntaxe

Pour utiliser plusieurs traits, rien de plus simple. Il me suffit de lister tous les traits à utiliser séparés par des virgules. 
Comme ceci : 

<?php
trait HTMLFormater2
{
	public function formatHTML($text)
	{
		return '<p>Date : '.date('d/m/Y').'</p>'."\n".
				'<p>'.nl2br($text).'</p>';
	}
}

trait TextFormater
{
	public function formatText($text)
	{
		return 'Date : '.date('d/m/Y')."\n".$text;
	}
}

class Mailer2 {
	use HTMLFormater2, TextFormater;
	public function send($text) {
		mail('lodf@fdfl.fr', 'Objet bidon', $this->formatHTML($text));
	}
}

class Writer2 {
	use HTMLFormater2, TextFormater;
	public function write($text) {
		file_put_contents('fichierto.txt', $this->formatText($text));
	}
}

?>

4.) Résolution des conflits

Le code donné plus haut est bien beau, mais que se passerait-il si mes traits avait tous les deux une méthode nommée format() ?
Eh bien, une erreur fatale en résulterait (avec le message "Trait method format has not been applied, because there are collisions
with other trait methods"). Pour pallier ce problème, je vais donner une priorité à une méthode d'un trait afin de lui permettre
d'écraser la méthode de l'autre trait s'il y en a une identique.

Par exemple, si dans ma classe Writer, je veux formater le message en HTML, je pourrais faire : 

<?php 
trait HTMLFormateur
{
	public function format($text)
	{
		return '<p>Date : '.date('d/m/Y').'</p>'."\n".
				'<p>'.nl2br($text).'</p>';
	}
}

trait TextFormateur
{
	public function format($text)
	{
		return 'Date : '.date('d/m/Y')."\n".$text;
	}
}

class WriterBis {
	use HTMLFormateur, TextFormateur {
		HTMLFormateur::format insteadof TextFormateur;
	}
	
	public function write($text) {
		file_put_contents('fichier.txt', $this->format($text));
	}
}
?>

Première chose à noter : il y a une paire d'accolades suivant les noms des traits à utiliser. A l'intérieur de cette paire 
d'accolades se trouve la liste des "méthodes prioritaires". Chaque déclaration de priorité se fait en se terminant par un
point-virgule. Cette ligne signifie donc : "La méthode format() du trait HTMLFormateur écrasera la méthode du même nom du 
trait TextFormateur (si elle y est définie)".

5.) Méthodes de traits vs méthodes de classes
a./ La classe plus forte que le trait

Si une classe déclare une méthode et utilise un trait possédant cette même méthode, alors la méthode déclarée dans la classe 
l'emportera sur la méthode déclarée dans le trait. 

Exemple : 

<?php
trait MonTrait2
{
  public function sayHello()
  {
    echo 'Hello !';
  }
}

class MaClasse
{
  use MonTrait2;
  
  public function sayHello()
  {
    echo 'Bonjour !';
  }
}

$objet = new MaClasse;
$objet->sayHello(); // Affiche « Bonjour ! ».

?>

b./ Le trait plus fort que sa mère

A l'inverse, si une classe utilise un trait possédant une méthode déjà implémentée dans la classe mère de la classe utilisant ce
trait, alors ce sera la méthode du trait qui sera utilisée (la méthode du trait écrasera celle de la méthode de la classe mère).

Exemple : 

<?php
trait Tr
{
  public function speak()
  {
    echo 'Je suis un trait !';
  }
}

class Mere
{
  public function speak()
  {
    echo 'Je suis une classe mère !';
  }
}

class Fille extends Mere
{
  use Tr;
}

$fille = new Fille;
$fille->speak(); // Affiche « Je suis un trait ! »

?>

II./ Plus loin avec les traits
1.) Définition d'attributs
a./ Syntaxe

J'ai vu que les traits servaient à isoler des méthodes afin de pouvoir les utiliser dans deux classes totalement 
indépendantes. Si le besoin s'en fait sentir, je peux même définir des attributs dans mon trait. Ils seront à leur tour
<strong>importés</strong> dans la classe qui utilsera ce trait. 

<?php 
trait TraitAttr {
	protected $attr = 'Hello !';
	public function showAttr() {
		echo $this->attr;
	}
}

class WhatAClass {
	use TraitAttr;
}

$fifi = new WhatAClass;
$fifi->showAttr();
?>

Attention : une propriété de trait peut être statique. Mais attention, dans ce cas, chaque classe utilisant ce trait aura 
une instance indépendante de cette propriété. 

b./ Conflit entre attributs

Si un attribut est défini dans un trait, alors la classe utilisant ce trait ne peut pas définir d'attribut possédant le même nom.
Suivant la déclaration de l'attribut, deux cas peuvent se présenter : 

	-	Si l'attribut déclaré dans la classe a le même nom mais pas la même valeur initiale ou pas la même visibilité, une
			erreur fatale est levée.
	-	Si l'attribut déclaré dans la classe a le même nom, une valeur initiale identique et la même visibilité, une erreur
			stricte est levée (il est possible, suivant ma configuration, que PHP n'affiche pas ce genre d'erreur).
			
Malheureusement, il est impossible, contrairement à ce que j'ai fait avec les méthodes, de définir des attributs prioritaires.

2.) Traits composés d'autres traits

Au même titre que les classes, les traits peuvent eux aussi utiliser des traits. La façon de procéder est la même qu'avec les 
classes, tout comme la gestion des conflits entre méthodes. Voici un exemple : 

<?php 

trait A {
	public function saySomething() {
		echo 'Je suis le trait A';
	}
}

trait B {
	use A;
	
	public function saySomethingElse() {
		echo 'Je suis le trait B';
	}
}
class MaC {
	use B;
}

$o = new MaC;
$o->saySomething(); // Affiche « Je suis le trait A ! »
$o->saySomethingElse(); // Affiche « Je suis le trait B ! »
?>

3.) Changer la visibilité le nom des méthodes

Si un trait implémente une méthode, toute classe utilisant ce trait a la capacité de changer sa visibilité, ie passer en privé,
protégé ou public. Pour cela, je vais à nouveau me servir des accolades qui ont suivi la déclaration de use pour y glisser 
une instruction. Cette instruction fait appel à l'opérateur as, que j'ai déjà (peut-être) rencontré dans l'utilisation des 
namespaces. Le rôle est ici le même : créer un alias. En effet, je peux aussi changer le nom des méthodes. 
Dans ce dernier cas, la méthode ne sera pas renommée, mais copiée sous un autre nom, ce qui signifie que je pourrai toujours y 
accéder sous son ancien nom. 

Quelques petits exemples :

<?php 

trait Tr {
	public function sayAThing() {
		echo 'I am a Berliner';
	}
}

class Waooh {
	use Tr {
		sayAThing as protected;
	}
}

$o = new Waooh;
$o->sayAThing(); // Une erreur fatale est levée car on tente d'accéder à une méthode protégée. ?>

Autre exmeple : 
<?php 
trait Tr2 {
	public function sayAThing() {
		echo 'Ich bin ein Berliner';
	}
}

class Classe2 {
	use Tr2 {
		sayAThing as whoiam;
	}
}

$o = new  Classe2;
$o->sayAThing();    // Affichera « Je suis le trait A ! »
$o->whoiam();    // Affichera « Je suis le trait A ! »

?>

Ou encore : 

<?php 
trait MonPetitTrait {
	public function hello() {
		echo 'Hello the world !';
	}
}

class ClasseBelle {
	use MonPetitTrait {
		hello as protected bonjour;
	}
}

$o = new ClasseBelle;
$o->helo(); // Affichera « Hello the wolrd ! ».
$o->bonjour(); // Lèvera une erreur fatale, car l'alias créé est une méthode protégée.?>

4.) Méthodes abstaites dans les traits 

Enfin, il faut aussi savoir que l'on peut forcer la classe utilisant le trait à implémenter certaines méthodes au moyen 
de méthodes abstraites. Ainsi, ce code lèvera une erreur fatale : 

<?php 
trait AbstTrait {
	abstract public function sayADamnThing();
}

class NewClass {
	use AbstTrait;
}
?>

Cependant, si la classe utilisant le trait déclarant une méthode abstraite est elle aussi abstraite, alors ce sera à ses classes
filles d'implémenter les méthodes abstraites du trait (elle peut le faire, mais elle n'est pas obligée). Exemple : 

<?php 
trait Tret {
	abstract public function test();
}

abstract class Meretchik {
	use A;
}

// Jusque là, aucune erreur n'est levée

class Pistontchik extends Meretchik {
	// Par contre, une erreur fatale est ici levée, car la méthode test() n'a pas été implémentée.
}
?>


En résumé : 

	-	Les traits sont un moyen pour éviter la duplication de méthodes
	-	Un trait s'utilise grâce au mot-clé use
	-	Il est possible d'utiliser une infinité de traits dans une classe en résolvant les conflits éventuels avec insteadof
	-	Un trait peut lui-même utiliser un autre trait
	-	Il est possible de changer la visibilité  d'une méthode ainsi que son nom grâce au mot-clé as.
	

</pre>
</body>
</html>