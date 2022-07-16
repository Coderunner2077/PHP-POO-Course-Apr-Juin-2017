<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Les closures</title>
</head>
<body>
<pre>
												Les closures
												
Les closures, représentant ce qu'on appelle des fonctions anonymes, sont un moyen simple de créer des fonctions à la volée et sont
impélmentées à PHP dans sa version 5.3. Elles sont souvent utilisées en tant que fonctions de rappel comme on va le voir. Depuis
sa version 5.4, PHP propose de lier les closures à des objets ou à des classes, rendant leur utilisation encore plus pratique.

<h3>
I./ Création de closures
</h3>
1.) Syntaxe

Une closure est une fonction particulière qui n'est pas nommée. Elle se déclare plutôt facilement, comme ceci : 

<?php 
function() {
	echo 'Hello world!';
};
?>

En fait, ceci semble être une fonction, mais il s'agit d'un objet. En réalité, il s'agit d'une instance de la classe Closure. 

Essayons ce code pour s'en convaincre : 

<?php 
$maFonction = function() {
	echo 'Hello world !';
};

var_dump($maFonction);	// On découvre ici qu'il s'agit bien d'un objet de type Closure
?>

Cette classe Closure possède une méthode magique que l'on a vue : __invoke(). Pour rappel, cette méthode est invoquée lorsque
l'on se sert de son objet comme une fonction. Exemple :

<?php 
$maFonction = function() {
	echo 'Hello world !';
};

$maFonction(); // Affiche 'Hello world !';
?>

2.) Exemple d'utilisation

Les closures sont principalement utilisées en tant que fonctions de rappels. Les fonctions de rappels sont des fonctions demandées
par d'autres fonctions pour effecuer des tâches spécifiques. 

Prenons l'exemple de la fonction array_map(). Cette fonction permet d'appeler la fonction qu'on lui passe en argument sur chaque
élément du tableau passé en deuxième argument (comme on le voit, on peut passer une infinité de tableaux à traiter, mais
on va rester sur un seul tableau pour l'exemple). 

La fonction que l'on doit donner à array_map() doit posséder 1 paramètre : la valeur actuelle du tableau traitée par array_map() 
(cette fonction va appeler ma closure sur chaque élément du tableau). Ma fonction doit renvoyer la nouvelle valeur. Si l'on
a un tableau de nombres et que l'on souhaite ajouter 5 à chacun d'entre eux, mon code ressemblerait à ceci : 

<?php
$monAdditionneur = function($val) {
	return $val += 5;
};

$newTav = array_map($monAdditionneur, [1, 2, 3, 4, 5]);
?>
</pre>

3.) Utilisation de variables extérieures

Actuellement, mon additionneur est assez limité. En effet, si je veux pouvoir ajouter 4 à chacun de mes nombres, je devrais créer
une autre closure. Il serait donc intéressant de rendre variable le nombre ajouté (ici, 5). POur cela, j'ai le mot-clé :

	-	use
	
...qui permet d'importer au sein de ma fonction une variable extérieure. 

Son utilisation est plutôt intuitive : 

<?php 
$quantite = 5;

$myFonction = function($val) use ($quantite) {
	 return $val + $quantite;
};

$listeNbr = array_map($maFonction, [1, 2, 3, 4, 5]);
?>

Un problème se pose maintenant : la quantité que j'ai fixée à 5 ne peut être changée, car cette variable a été importé dans 
ma closure dès la création de cette dernière. Ainsi, avec le code suivant, le résultat obtenu ne sera pas celui escompté :

<?php 
var_dump($listeNbr); // On a : [6, 7, 8, 9, 10]

$quantite = 4;

$listeNbr = array_map($myFonction, $listeNbr);

var_dump($listeNbr); // On a : [11, 12, 13, 14, 15] au lieu de [10, 11, 12, 13, 14]
?>

Dans ce cas-là, je vais passer par une fonction chargée de renvoyer une closure. Cette fonction prendra un argument : la quantité
à ajouter. J'importerai ainsi cette quantité dans ma closure, puis la retournerai. Ainsi, array_map() revevra une nouvelle
closure, dont la quantité importée sera à chaque fois différente (si on spécifie une différente bien entendu).

Tadam : 

<?php 
function creerAdditionneur($quantity) {
	return function($val) use ($quantity) {
		return $val + $quantity;
	};
}

$listeNbr = [1, 2, 3, 4, 5];
$listeNbr = array_map(creerAdditionneur(5), $listeNbr);

var_dump($listeNbr);	// On a : [6, 7, 8, 9, 10]

$listeNbr = array_map(creerAdditionneur(4), $listeNbr);

var_dump($listeNbr);	// On a bien : [10, 11, 12, 13, 14]
?>

Ici, j'ai une fonction creerAdditionneur() chargée de renvoyer l'additionneur que array_map() utilisera. Cet additionneur importe 
la quantité à additionner dès sa création. Or, cet additionneur est créé lorsque l'on appelle creerAdditionneur avec la quantité
à additionner. Suivant l'argument donné à creerAdditionneur, la quantité additionnée ne sera pas la même, d'où le résultat
final !

<h3>
II./ Lier une closure
</h3>
1.) Lier une closure à un objet

Actuellemnt, je suis capable de créer des closures dénuées de tout contexte. Mais il est possible, une fois ma closure créée, 
d'en obtenir une copie qui sera liée à un objet. En d'autres termes, ma closure fera partie de mon objet, lui permettant 
un accès à ses attributs et méthodes. 
 
Reprenons l'additionneur précédent pour le modifier afin qu'il ajoute 5  à un attribut d'un objet. Voilà une première ébauche
du code : 

<?php 
$myAdditionneur = function() {
	$this->attr += 5;
};

class SomeClass {
	protected $attr = 0;
	
	public function getAttr() {
		return $this->attr;
	}
}

$obj = new SomeClass;
?>

Le but de la manoeuvre sera donc d'ajouter cette fonction à mon objet $obj afin de pouvoir modifier ce nombre. Pour cela,
je vais me servir d'une méthode de la classe Closure, la voici  : 

	-	bintTo() : cette méthode accepte deux arguments. Le premier est l'objet auquel je veux lier ma closure (ici, ce sera donc
					$obj). Le second argument est le <strong>contexte dans lequel la méthode sera invoquée</strong>. Ici, je 
					souhaite modifier un attribut protégé, il est donc important que la méthode soit invoquée au sein de la classe
					SomeClass. Il y a deux façons de l'indiquer : soit par le biais d'une chaîne de caractères valant "SomeClass",
					soit par un objet du type SomeClass (comme $obj tout simplement).
				
Voilà le complément du code :

<?php 
// On obtient une copie de notre closure qui sera liée  à notre objet $obj
// Cette nouvelle closure sera appelée en tant que méthode de SomeClass
// On aurait tout aussi bien pu passer $obj en second argument
$myAdditionneur = $myAdditionneur->bindTo($obj, 'SomeClass');

$myAdditionneur();
echo $obj->getAttr();	// Affiche bien 5
?>

Il est important de comprendre cette histoire de contexte. En effet, si j'avais mis autre chose en second argument, comme ceci :
<?php 
/*
$myAdditionneur = $myAdditionneur->bindTo($obj, 'AutreClasse');
$myAdditionneur();
*/
echo $obj->getAttr();
?>
En effet, l'appel à getAttr() générera maintenant une erreur stipulant que j'accède à $attr alors que je n'ai pas le droit.
Le contexte n'est pas un argument obligatoire. Si je ne le spécifie pas, le contexte sera inchangé, c'est-à-dire que le contexte 
de la nouvelle closure sera le même que celui de l'ancienne. 

2.) Lier temporairement une closure à un objet

Pour de petites opérations, la solution précédente peut s'avérer lourde. Depuis la version 7 de PHP, il est possible de lier
la closure à un objet <strong>le temps d'un appel.</strong> Considérons l'exemple suivant :

<?php 
class  Nombre {
	private $_nb;
	
	public function __construct($nb) {
		$this->_nb = $nb;
	}
}

$closure = function () {
	var_dump($this->_nb + 5);
};

$two = new Nombre(2);
$three = new Nombre(3);

$closure->call($two);	// donne 7
$closure->call($three);	// donne 8
?>

Que s'est-il passé ? 

	1. Une closure a été créée, ayant pour rôle d'afficher le résultat de l'addition de l'attribut _nb avec 5
	2. J'ai deux instances différentes de la même classe. L'attribut _nb de la 1re instance vaut 2 et celui de l'autre vaut 3
	3. J'invoque la closure la liant au premier objet. De cette façon, l'expression $this->_nb contenue dans la closure vaut 2. Le
			résultat est donc de 7
	4. J'invoque ensuite la closure la liant au second objet. De cette façon, l'expression $this->_nb contenue dans la closure
			vaut 3. Le résultat est donc de 8

3.) Lier une closure à une classe

Lier une closure à une classe n'a de sens que si elle est utilisée dans un contexte statique, c'est-à-dire qu'elle servira uniquement
à afficher ou modifier les attributs statiques de la classe. 

Adaptons donc notre closure pour qu'elle fonctionne dans un contexte statique :

<?php 
// Je déclare ici une closure statique
$additionneur = static function() {
	self::$_nb += 5;
};

class MaClasse {
	private static $_nb = 0;
	
	public static function getNb() {
		return self::$_nb;
	}
}

$additionneur = $additionneur->bindTo(null, 'MaClasse');
$additionneur();

MaClasse::getNb(); // affiche 5
?>

Cette fois-ci, l'appel de bindTo() a été légérement modifié. En effet, je ne souhaite plus lier ma closure à un objet. J'ai donc
passé null en premier argument. Le second argument, lui, est resté inchangé, car je souhaite invoquer ma closure en tant que méthode
de MaClasse.

Il reste un dernier petit détail à régler. Actuellement, ma closure peut toutjours être liée à un objet. Si je souhaite imposer
à ma closure de ne pouvoir  être liée  qu'à une classe, il faut la déclarer statique avec le mot-clé... static !

Dorénavant, si j'essaye de la lier à un objet, PHP me dira gentillement que ce n'est pas possible.

4.) Les liaisons automatiques

Jusqu'à maintenant, j'ai toujours déclaré mes closures dans la partie globale du script, et je devais  les lier manuellement aux
objets ou classes souhaités. En fait, si je déclare une closure à l'intérieur d'une méthode, cette closure adoptera le contexte dans
lequel a été appelée cette méthode, et sera directement liée à l'objet concernée si la méthode n'est pas statique. 

<?php 
class MyClass {
	private $_nb = 0;
	
	public function getAdditionneur() {
		return function() {
			$this->_nb += 5;
		};
	}
	
	public function getNb() {
		return $this->_nb;
	}
}

$obj = new MyClass();
$additionneur = $obj->getAdditionneur();
$additionneur();

echo $obj->getNb(); // affiche bien 5 car ma closure est bien liée  à $obj depuis MyClass
?>

Bien entendu, le principe reste exactement le même pour un contexte statique : 

<?php 
class LaClasse {
	private static $_nb = 0;
	
	public static function getAdditionneur() {
		return function() {
			self::$_nb += 5;
		};
	}
	
	public static function getNb() {
		return self::$_nb;
	}
}

$additionneur = LaClasse::getAdditionneur();
$additionneur();

echo LaClasse::getNb(); // affiche bien 5
?>

5.) Implémentation du pattern Observer à l'aide de closures

Je vais m'appuyer sur cet exemple de classe implémentant le pattern Observer : 

<?php 
class Observed implements SplSubject {
	protected $name;
	protected $observers = [];
	
	public function attach(SplObserver $observer) {
		$this->observers[] = $observer;
		return $this;
	}
	
	public function detach(SplObserver $observer) {
		if(is_int($key = array_search($observer, $this->observers, true)))
			unset($this->observers[$key]);
	}
	
	public function notify() {
		foreach($this->observers as $obs) 
			$obs->update($this);
	}
	
	public function name() {
		return $this->name;
	}
	
	public function setName($name) {
		$this->name = $name;
		$this->notify();
	}
}
?>

On pourrait compléter ce code en écrivant une classe implémentant l'interface SplObserver. Et au final, cela donnerait un code assez
lourd dans la mesure où la seule partie qui changeait était le contenu de la méthode update(), alors même que l'on crée une classe
à chaque fois. Le but ici est de faire en sorte de n'avoir à créer qu'une classe générique implémentant l'interface SplObserver
et à laquelle on donnera la closure à notifier.

Chaque nouvelle closure sera liée à un nouvel objet observateur. Voici la classe dont les objets observateurs seront des 
instances : 

<?php 
class Observer implements SplObserver {
	protected $closure;
	protected $name;
	
	public function __construct(Closure $closure, $name) {
		// On lie la closure à l'objet actuel  et on lui spécifie le contexte à utiliser
		// ici il s'agit du même contexte que $this
		$this->closure = $closure->bindTo($this, $this);
		$this->name = $name;
	}
	
	public function update(SplSubject $subject) {
		// en cas de notification, on récupère la closure et on l'appelle
		$closure = $this->closure;
		$closure($subject);
	}
}
?>

Et maintenant, il n'y a plus qu'à tester mon système. Pour cela, je vais créer une instance de Observed ainsi que deux closures
à notifier. Une fois rattachées à l'objet observé, je vais changer l'attribut $name de mon objet observé afin de déclencher
les notifications. 

Voici le code de test : 

<?php 
$observed = new Observed();

$closure1 = function(SplSubject $subject) {
	echo 'L\'objet ', $this->name , ' a été notifié du nouveau nom : ' , $subject->name() , "\n"; 
};

$closure2 = function(SplSubject $subject) {
	echo 'L\'objet ', $this->name , ' a été notifié du nouveau nom : ' , $subject->name() , "\n"; 
};



$observed->attach(new Observer($closure1, 'Observer1'))
		 ->attach(new Observer($closure2, 'Observer2'))
		 ->attach(new Observer(function(SplSubject $subject) {
	echo 'L\'objet ', $this->name , ' a été notifié ! Nouvelle valeur de name : ' , $subject->name() , "\n"; 
}, 'Observer3'));


$observed->setName('Patouche');
// Ce qui affiche :
// Observer1 a été notifié ! Nouvelle valeur de name : Patouche
// Observer2 a été notifié ! Nouvelle valeur de name : Patouche
// Observer3 a été notifié ! Nouvelle valeur de name : Patouche
?>

En résumé : 

Les closures permettent de représenter  des <strong>fonctions anonymes</strong>.
Les closures sont souvent utilisées en tant que <strong>fonctions de rappels</strong>.
Il est possible de lier une closure à un objet ou à une classe grâce à bindTo().

</body>
</html>