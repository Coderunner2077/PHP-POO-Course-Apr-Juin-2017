
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Les générateurs</title></head>
<body>
<pre>
													Les générateurs
													
Les générateurs sont une façon simple et rapide d'implémenter des itérateurs, permettant ainsi de résoudre des problèmes de performance
ou de code à rallonge. 
Les générateurs ont été implémentés depuis la version 5.5 de PHP.
<h3>
I./ Notions de base
</h3>
1.) Etude de cas

Imaginons que l'on ait à parcourir les lignes d'un fichier pour faire une quelconque opération sur chacune d'entre elles. Pour ce
faire, j'ai la fonction file() qui a pour rôle de lire le fichier puis  de retourner un tableau dont chaque entrée est une 
ligne différente. Son utilisation est ainsi plutôt simple :

&lt;?php
$lines = file('monfichier.txt');

foreach($lines as $line) 
{
	// Effectuer une opération sur line
}
?&gt;

Cela peut devenir vite embêtant si le fichier est trop gros, surtout s'il y a des milliers de lignes. En effet, si on stocke tout 
cela dans une seule variable, il y a de gros risques pour atteindre la limite de la mémoire allouée pour le script.

Ce qu'il faudrait donc faire, ce serait lire les lignes une par une, sans garder en mémoire la valeur de la précédente ligne. Si
l'on veut garder la boucle foreach pour conserver cet aspect pratique d'utilisation, on va donc devoir utiliser un itérateur. Voilà
à quoi pourrait ressembler notre classe : 

<?php 
class FileReader implements Iterator {

	protected $file;
	protected $currentLine;
	protected $currentKey;
	
	public function __construct($file) {
		if(!$this->file = fopen($file, 'r'))
			throw new RuntimeException('Impossible d\'ouvrir "' . $file. '"');
	}
	
	public function rewind() {
		fseek($this->file, 0);
		$this->currentLine = fgets($this->file);
		$this->currentKey = 0;
	}
	
	public function valid() {
		return $this->currentLine !== false;
	}
	
	public function key() {
		return $this->currentKey;
	}
	
	public function current() {
		return $this->currentLine;
	}
	
	public function next() {
		if($this->currentLine !== false) {
			$this->currentLine = fgets($this->file);
			$this->currentKey++;
		}
		
	}
}
?>

L'utilisation de cet itérateur est aussi simple qu'avec la fonction file : 

<?php 
$fileReader = new FileReader('serialized.txt');

foreach($fileReader as $line) {
	// Effectuer une opération sur line
}
?>

Ainsi, bien qu'on ait fait une grande optimisation au niveau de la mémoire utilisée, on a pourtant un code bien plus long : 
la création d'un itérateur telle que je le connais est longue, surtout pour ne faire qu'une petite opération comme c'est le cas
ici. C'est exactement à cela que remédient les générateurs : ils optimisent l'utilisation de la mémoire tout en conservant un
code clair et concis. 

2.) Les générateurs

Un générateur permet la création d'itérateur de manière simple et efficace. Concernant la classe FileReader, si l'on voulait 
résoudre ce problème de longueur, comment ferait-on ? La 1re idée serait de ne pas avoir à créer de classe. En effet, si l'on
s'attarde un peu sur le contenu des méthodes, seule la méthode next() est vraiment spécifique à notre cas : c'est dans cette 
méthode qu'on <strong>construit le tableau à parcourir</strong>. Les autres méthodes ne sont pas spéciques à ce qu'on fait. En
effet, elles permettent juste de traiter le tableau qu'on a construit, mais on ne fait rien d'exceptionnel : il s'agit d'un
tableau classique que PHP <strong>peut très bien parcourir tout seul.</strong>

C'est de cette idée que sont nés les générateurs : n'écrire qu'une seule fonction qui est chargée de construire le tableau, sans
se soucier de toutes les autres fonctions permettant d'obtenir l'entrée courante du tableau ou de savoir si le tableau contient
une autre entrée pour continuer son parcours par exemple. 

Pour créer un générateur, je ne vais ainsi écrire qu'une seule fonction. Dans cette fonction, on va parcourir les lignes du fichier
et, pour chaque ligne, on va indiquer à PHP qu'il s'agit de la valeur de la prochaine entrée du tableau grâce au mot clé :

	-	yield
	
On va donc "construire" petit à petit notre tableau en lui ajoutant des entrées au fur et à mesure.

<?php 
function readLines($fileName) {
	if(!$file = fopen($filename, 'r'))
		return;
	
	//Tant qu'il reste des lignes à parcourir
	while($line = fgets($file))
		//On dit à PHP que cette ligne du fichier fait office de "prochaine entrée du tableau"
		yield $line;
	
	fclose($file);
}
?>

Le but des générateurs étant de créer facilement des itérateurs, un générateur <strong>est</strong> un itérateur. Et qu'est-ce
qu'un itérateur ? C'est une instance d'une classe implémentant Iterator. Donc oui... un générateur est une instance d'une 
classe implémentant Iterator. Or, ma fonction est un générateur car elle contient le mot-clé yield dedans (c'est automatique : 
toute fonction contenant ce mot-clé est considérée comme un générateur en PHP). 

En effet, cette fonction readLines n'en est une qu'à première vue : en fait c'est une sorte de classe. Plus précisément, cette
fonction que j'ai écrite est "transformée"  par PHP en une instance de la classe Generator. 

Pour preuve, essayons ceci : 

<?php 
var_dump(readLines('serialized.txt'));
?>

Attention : il est impossible de cloner un générateur.

Note : le fait d'avoir appelé ma fonction readLines() n'a en rien lancé l'exécution de celle-ci. Il est impossible de l'invoquer:
si je l'appele comme on l'a fait, j'obtiendrai juste l'instance de Generator associée à ce générateur. 

Cette fonction n'étant qu'un itérateur, on va la parcourir en tant que tel : 

<?php 
$generator = readLines('serialized.txt');

foreach($generator as $line) {
	//Code
}?>

Si on détaille ce script, voici ce que ça donne :

On commence par récupérer l'instance de Generator associée au générateur. La variable $generator est donc un itérateur. <strong>La
fonction n'a pas encore été exécutée.</strong> Vient ensuite le parcours  de l'itérateur grâce à la boucle foreach() : 

1. Première itération : la fonction commence à s'exécuter. PHP continue l'exécution de la fonction jusqu'à ce qu'il rencontre un
	yield.
2. PHP rencontre le yield suivi d'une chaîne de caractères (il s'agit de la ligne actuelle du fichier). Il va donc dire à la boucle
foreach() : "tiens, la prochaîne valeur est cette chaîne de caractères"
3. PHP arrête l'exécution de la fonction  (il ne va pas plus loin que le yield rencontré)
4. La boucle foreach() peut donc commencer, et la valeur courante du tableau (ici représentée par la variable $line) n'est autre
que la valeur spécifiée avec le yield dans la fonction.
5. Une fois l'itération de la boucle foreach terminée, on recommence : PHP va continuer l'exécution de la fonction là où il
s'est arrêté, puis s'arrêtera de nouveau lorsqu'il rencontrera un yield. On retourne ainsi à l'étape 2, et ainsi de suite jusqu'à
ce que la fonction se termine. 

Bien sûr, puisque readLines() renvoie un identifiant objet, je n'ai pas besoin de passer par une variable : je peux directement
parcourir ce résultat avec foreach : 

&lt;?php
foreach(readLines('fichier.txt') as $line) 
{
	// code
}
?&gt;

De cette façon, on a réglé le problème de la mémoire saturée (PHP ne garde en mémoire qu'une ligne à la fois; quand il passe à la
ligne suivante, il a oublié la précédente), ainsi que le problème du code à rallonge. 

<h3>
II./ Zoom sur les valeurs retournées
</h3>
1.) Retourner des clés avec les valeurs 

Il y a différentes façons de retourner des valeurs avec yield. Dans l'exemple précédent, je n'ai retournée qu'une simple valeur
(il s'agissait d'une chaîne de caractères, mais je peux retourner n'importe quoi). Or, dans une boucle foreach, il est possible
de retourner la clé associée à l'entrée actuellement parcourue du tableau. Par défaut, lorsque je fais un yield, PHP va
incrémenter son compteur, de sorte à fournir des clés numériques, comme pour les tableaux (la 1re valeur est disponible à la
clé 0, la 2nde à la clé 1, etc.). Voyons cela :

<?php 
function generator() {
	for($i = 0; $i < 10; $i++)
		yield 'Itération N°' . $i;
}

foreach($generator() as $key => $val)
	echo $key , ' => ' , $val , '<br />';
?>

Il est également possible de modifier la clé associée à la valeur que l'on retourne. Pour cela, on doit suivre cette syntaxe :

&lt;?php
yield $key => $val
?&gt;

Voici un exemple simple, mettant en application cette nouvelle syntaxe :

<?php 
function generateur() {
	//On retourne ici des chaînes de caractères assignées à des clés 
	yield 'a' => 'Itération 1';
	yield 'b' => 'Itération 2';
	yield 'c' => 'Itération 3';
	yield 'd' => 'Itération 4';
	yield 'e' => 'Itération 5';
}

foreach($generateur() as $key => $val) {
	echo $key , ' => ', $val , '<br />';
}
?>

Je viens ainsi de voir une deuxième utilisation de yield. Il ne me restera ainsi qu'une troisème façon que je verrai dans la 
prochaine partie. 

2.) Retourner une référence

Pour pouvoir parcourir un tableau tout en modifiant ses valeurs, il faut passer ces valeurs par <strong>référence</strong>. 
Commeçons par un exemple simple exempt de références : 

<?php 
class SomeClass {
	protected $attr;
	public function __construct() {
		$this->attr = ['Un', 'deux', 'trois', 'quatre'];
	}
	
	public function generator() {
		foreach($this->attr as $val)
			yield $val;
	}
}

// Et la boucle foreach de mon générateur : 

$obj = new SomeClass();

foreach($obj->generator() as $val) 
	var_dump($val);
?>

Le but de la manoeuvre sera de faire en sorte que l'on puisse modifier $val dans ma boucle foreach. Pour ce faire, je vais dire
à ma fonction qu'elle doit renvoyer des références. 
Et pour qu'une fonction renvoie une référence, on doit placer un & devant son nom. Pour les générateurs, on fait pareil : lorsqu'on
ajoute un & devant son nom, cela voudra dire que toutes les variables retournées par yield seront retournées par référence. C'est
cette étape la plus importante. Et c'est celle-là qu'il faut retenir. 

Si l'on revient à notre exemple, cela ne suffira pas. En effet, dans la boucle foreach de mon générateur, il faut récupérer les
variables du tabeau par référence aussi. Bien sûr, il en va de même pour la boucle foreach parcourant mon générateur. On obtiendra
donc un code ressemblant à ceci : 

<?php 
class SomeClass2 {

	protected $attr;
	
	public function __construct() {
		$this->attr = ['Un', 'deux', 'trois', 'quatre'];
	}
	
	// Le & devant le nom du générateur indique que les valeurs retournées sont des références
	public function &generator() {
		// On cherche ici à obtenir les références des valeurs du tabeau pour les retourner
		foreach($this->attr as &$val)
			yield $val;
	}
	
	public function getAttr() {
		return $this->attr;
	}
}

$obj = new SomeClass2();

// On parcourt le générateur en récupérant les entrées par référence
foreach($obj->generator() as &$val) {
	// On peut donc modifier notre valeur
	$val = strrev($val);
}

echo '<pre>';
var_dump($obj->getAttr());
echo '</pre>';
?>

Ce qui prouve bien que le tableau a été modifié en dehors de la classe, mettant ainsi en avant l'utilisation des références.

<h3>
III./ Les coroutines
</h3>
Si l'on jette un oeil à la classe Generator, on peut s'apercevoir qu'elle dispose de 3 méthodes de plus que Iterator : 

	-	send()
	-	throw()
	-	__wakeup()
	
Je vais donc me focaliser sur les deux premières dans ce chapitre.

1.) La méthode send()

Imaginons un système inversé. Actuellement, le générateur <strong>fournit</strong> les données. Mais il est également possible 
de faire l'inverse, c'est-à-dire <strong>envoyer</strong> des données au générateur (on verra l'intérêt plus tard). 

Pour ce faire, voyons d'abord un exemple tout simple : 

&lt;?php

function generator() {
	echo yield;
}

$gen = generator();
$gen->send('Hello world ! ');

?&gt;

En testant ce code, la phrase 'Hello world !' s'affiche sur l'écran. Alors, que s'est-il passé ? 

Je commence par simplement récupérer le générateur dans ma variable $gen. Ensuite, j'invoque la méthode send en lui envoyant
'Hello world ! ' en argument. Lorsque j'invoque send() pour la 1re fois, PHP va commencer l'exécution de la fonction jusqu'au 
prochain yield qu'il rencontre. Lorsqu'il en rencontre un (peu importe si j'ai spécifié une valeur à retourner ou non), PHP 
"remplacera" ce yield par la valeur spécifiée dans la méthode send(). Une fois fait, la fonction <strong>continue son
exécution jusqu'au prochaine yield</strong>, puis PHP la met en pause <strong>juste avant</strong> le prochain yield. S'il ne
s'agit pas du premier appel de la méthode send(), PHP reprendra l'exécution de la fonction là où il s'était arrêté, puis
refera la même opération que précédemment, etc. 

Avant d'aller plus loin, on va faire une petite parenthèse sur la syntaxe à respecter. En effet, il y a deux cas d'utilisation
du mot-clé yield : soit il est utilisé dans une expression, c'est-à-dire qu'on s'intéresse au résultat qu'il retournera (comme
on vient juste de le faire avec echo yield), soit il est utilisé seul et constitue lui-même une instruction (comme on faisait 
avant, ie en faisant yield $data; par exemple).

Dans le cas où yield est utilisé dans le contexte d'une expression, des parenthèses sont requises autour de lui, sauf si yield
est utilisé seul. Dans l'autre cas, les parenthèses ne sont pas nécessaires. 

Voici un petit exemple mettant en oeuvre les 3 cas différents à distinguer : 

&lt;?php

// Le yield n'est pas utilisé dans une expression : pas de parenthèses
yield 'Hello worldish!';

// Le yield est ici utilié dans une expression, mais il est utilisé seul : pas de parenthèses
$data = yield;

// Le yield est ici utilisé dans une expression : les parenthèses sont requises
$data = (yield 'Hello worldish !');

?&gt;

Voici un exemple concret : 

<?php 
function gener() {
	echo (yield 'Hello world !');
	echo yield;
}

$gen = gener();

// On envoie le message 'Message 1'
// PHP va donc l'afficher grâce au premier echo du générateur
$gen->send('Message 1');

// On envoie "Message 2"
// PHP reprend l'exécution du générateur et affiche le message grâce au 2ème echo
$gen->send('Message 2');

// On envoie "Message 3"
// La fonction générateur s'était déjà terminée, donc rien ne se passe
$gen->send('Message 3');
?>

Note : si je parcours mon générateur et qu'il fait face à "yield;" (l'instruction qu'on vient juste de voir), alors la valeur 
retournée sera NULL

Lorsque l'on utilise les générateurs de cette façon (ie on les utilise pour prendre des valeurs et non les retourner), on parle
de <strong>générateur inverse</strong> ou encore de <strong>coroutine</strong>. 

Voyons maintenant un exemple d'utilisation. 

2.) Cas pratique : un système multitâche

Je vais ici voir comment on pourrait faire un système <strong>multitâche</strong>. Concrètement, on va faire en sorte de pouvoir
exécuter des fonctions "en parallèle", ie des fonctions qui se mettent en pause chacune leur tout afin qu'une autre puisse 
poursuivre son exécution. 

On va donc créer une classe dont le rôle sera de gérer ces tâches, c'est-à-dire qu'elle possédera une liste de tâches et sera
capable de les exécuter en parallèle. Pour des raisons pratiques, cette liste sera sous la forme d'une instance de :

	-	SplQueue
	
Cette classe me permettra de gérer facilement ma liste des tâches grâce aux méthodes enqueue(), dequeue() et isEmpty(), permettant
respectivement d'ajouter un élément en fin de liste, de supprimer le premier élément de la liste et de savoir si la liste est
vide. 

Commençons par écrire la base de ma classe TaskRunner dont on vient de parler : 

<?php 
class TaskRunner {
	
	protected $tasks;
	
	public function __construct() {
		// On initialise la liste des tâches
		$this->tasks = new SplQueue();
	}
	
	public function addTask(Generator $task) {
		// On ajoute la tâche à la fin de la liste
		$this->tasks->enqueue($task);
	}
	
	public function run() {
		// On verra ici ce qu'on mettra
		// Tant qu'il y a toujours au moins une tâche à exécuter
		while(!$this->tasks->isEmpty()) {
			// On enlève la première tâche et on la récupère au passage
			$task = $this->tasks->dequeue();
			
			// On exécute la prochaine étape de la tâche
			$task->send('Hello world !');
			
			// Si la tâche n'est pas finie, on la replace en fin de liste
			if($task->valid()) {
				$this->addTask($task);
			}
		}
	}
}
?>

Comment va-t-on exécuter deux fonctions (ou plus) en parallèle ? L'astuce ici se fera bien entendu à l'aide de yield dans les 
fonctions représentant les tâches, afin de les mettre en pause régulièrement. Le principe sera donc le suivant.

On a une liste de tâches qu'on parcourt tant qu'elle n'est pas vide  (on a la méthode isEmpty() à notre dispostion). A chaque
tâche, on va invoquer la méthode send() sur cette tâche en lui envoyant les données dont elle a besoin (dans mon cas, je vais
me contenter d'envoyer "Hello world ! "). Ensuite, il faut enlever cette tâche du haut de la liste grâce à dequeue (cette méthode
renvoie la valeur de l'élément supprimé, donc on va d'abord l'appeler afin de récupérer la tâche actuelle puis ensuite appeler
send()). Enfin, on va voir si la tâche est finie. POur cela, les générateurs étant des itérateurs, j'aurai à ma disposition la
méthode valid() permettant de vérifier s'il y a une prochaine valeur (dans mon cas, cela revient à vérifier s'il y a un
prochain yield, donc de vérifier  si la tâche a encore quelque chose à faire). 

Ecrivons donc la méthode run :

Réf TaskRunner

Je peux essayer ce code avec des tâches simples  comme celles-ci : 

<?php 
$taskRunner = new TaskRunner();

function task1() {
	for($i = 1; $i <= 2; $i++) {
		$data = yield;
		echo 'Tâche 1, itération ' , $i, ', valeur envoyée : ', $data, '<br />';
	}
}

function task2() {
	for($i = 1; $i <= 6; $i++) {
		$data = yield;
		echo 'Tâche 2, itération ' , $i , ', valeur envoyée : ', $data , '<br />';
	}
}

function task3() {
	for($i = 1; $i <= 4; $i++) {
		$data = yield;
		echo 'Tâche 3, itération ', $i , ', valeur envoyée : ' , $data , '<br />';
	}
}

$taskRunner->addTask(task1());
$taskRunner->addTask(task2());
$taskRunner->addTask(task3());

$taskRunner->run();
?>

Ainsi, chaque tâche s'est exécutée en parallèle. 

Voilà donc cet exemple terminé. On peut bien sûr améliorer ce système pour en refaire un autre plus poussé (envoyer des données
spécifiques aux tâches, définir un ordre de priorité, etc.).

3.) La méthode throw()

La méthode throw() permet de lancer une exception à l'emplacement du yield dans le générateur. L'idée est la même que pour send() :
lorsque throw() est appelée, le PHP démarre (ou continue) l'exécution du générateur jusqu'au prochain yield, et lancera une
exception à cet endroit précis. Cette méthode  accepte un seul et unique argument : l'exception à lancer (donc une instance de 
Exception ou une instance d'une classe héritant d'Exception).

Exemple : 

<?php 
function generatorr() {
	echo "Début\n";
	yield;
	echo 'Fin';
}

$gen = generatorr();
$gen->throw(new Exception('test'));
?>

Cela montre que ma fonction s'est bien exécutée jusqu'au premier yield, et dès que  PHP y est arrivé, il a lancé l'exception. Pour
l'attraper, on entoure le yield du bloc try / catch. 

Pour me familiariser un peu avec ce concept, voici un petit exemple qui devrait m'aider à comprendre : 

<?php 
function generatore() {
	
	// On fait une boucle de 5 yield pour garder quelque chose de simple
	for($i = 0; $i < 5; $i++) {
		// On indique qu'on vient de rentrer dans la ième itération 
		echo "Début $i<br />";
		
		// On essaye "d'attraper" la valeur qu'on nous a donnée
		try {
			yield;
		} catch (Exception $e) {
			// Si on a une exception levée, on indique son numéro : 
			echo "Exception $i<br />";
		}
		
		// Enfin, on indique qu'on vient de finir la ième itération
		echo "Fin $i<br />";
	}
}

$gen = generatore();

foreach($gen as $i => $val) {
	// On décide de lancer l'exception  pour l'itération n°3
	if($i == 3) 
		$gen->throw(new Exception('Petit test'));
}
?>

Ainsi, seul le yield de la trosième itération a mené une exception, qui d'ailleurs a été attrapée sans souci avec le bloc try. 

Lancer une exception sans aucune raison comme on vient de le faire a très peu d'intérêt. En fait, cette méthode  throw va
de paire avec  send(). Lorsque j'ai un système de coroutine comme on vient de le voir, c'est à moi de fournir les données
au générateur. Si quelque chose ne va pas, je peux lancer une exception pour le prochain yield au lieu de lui envoyer des
données avec send(). 

Pour reprendre mon système multitâche précédent, on pourrait ajouter la possibilité de tuer une tâche en cours 
d'exécution. Pour ce faire, il me suffirait d'envoyer une exception à ma tâche pour lui indiquer qu'il faut qu'elle se termine. 

Exemple (dans lequel je décide de tuer la tâche 2 lors de la 5ème itération) : 

<?php 
class TaskLanceur {
	protected $tasks;
	
	public function __construct() {
		// On initialise la liste des tâches. 
		$this->tasks = new SplQueue();
	}
	
	public function addTask($task) {
		// On ajoute la tâche à la fin de la liste
		$this->tasks->enqueue($task);
		
		return $this;
	}
	
	public function run() {
		$i = 1;
		
		// Tant qu'il y a toujours au moins une tâche à exécuter
		while(!$this->tasks->isEmpty()) {
			$task = $this->tasks->dequeue();
			
			// POur l'exemple, on va arrêter la tâche N°2 lors de son 2ème appel
			if($i == 5) {
				$task->throw(new Exception('Tâche interrompue'));
			}
			
			$task->send('Hello world !');
			
			if($task->valid())
				$this->addTask($task);
			
			$i++;
		}
	}
}

$taskRunner = new TaskLanceur();

function taskUn() {
	for($i = 1; $i <= 2; $i++) {
		try {
			$data = yield;
			echo 'Tâche 1, itération ', $i , ', valeur envoyée : ', $data , '<br />';
		} catch (Exception $e) {
			echo 'Erreur tâche 1 : ', $e->getMessage() , '<br />';
			return;
		}
	}
}

function taskDeux() {
	for($i = 1; $i <= 6; $i++) {
		try {
			$data = yield;
			echo 'Tâche 2, itération ' , $i, ', valeur envoyée ' , $data , '<br />';
		} catch (Exception $e) {
			echo 'Erreur tâche 2 : ' , $e->getMessage() , '<br />';
		}
	}
}

function taskTrois () {
	for($i = 1; $i <= 4; $i++) {
		try {
			$data = yield;
			echo 'Tâche 3, itération ', $i , ', valeur envoyée : ' , $data , '<br />';
		} catch (Exception $e) {
			echo 'Erreur tâche 3 : ' , $e->getMessage() , '<br />';
			return;
		}
	}
}

$taskRunner->addTask(taskUn())
		   ->addTask(taskDeux())
		   ->addTask(taskTrois())
		   ->run();
?>

En résumé : 

Les générateurs sont une façon simple de créer des itérateurs.

Toute fonction contenant le mot-clé yield est automatiquement considérée comme un générateur.

Un générateur peut renvoyer une valeur simple mais aussi une clé qui lui sera associée. 

Pour renvoyer une référence via un yield, il faut placer un & avant le nom du générateur.

La méthode send() permet de créer des <strong>coroutines</strong>, ce qui consiste à <strong>consommer</strong> des valeurs 
et non à les <strong>retourner</strong>.
</pre>
</body>
</html>