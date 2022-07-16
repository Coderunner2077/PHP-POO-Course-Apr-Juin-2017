<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Les exceptions</title>
</head>
<body>
<pre>
												Les exceptions
												
I./ Rappels et quelques nouveautés
1.) Lever et capturer une exception

Attention : il ne faut jamais lancer d'exception dans un destructeur. Si je le fais, j'aurai une erreur fatale : "Exception 
thrown without a stack frame in Unknown on line 0". Cette erreur peut aussi être lancée dans un autre cas évoqué à la fin. 

Toutes les classes spécialisées dans la levée d'une exception héritent de la classe Exception.

Voilà comment on lance et capture une nouvelle exception dans une fonction :

<?php 
function additionner($a, $b) {
	if(!is_numeric($a) || !is_numeric($b))
		throw new Exception('Les deux paramètres doivent être des nombres');
	return $a + $b;
}
try {
	echo additionner(10, 39) , '<br />';
	echo additionner(10, 'blabla');
	echo additionner(20, 20);
} catch(Exception $e) {
	//die('Une exception est levée : ' . $e->getMessage()); // die() interrompt le script
	echo 'Une exception est levée : ' . $e->getMessage(); // là, le script continue après le bloc try/bloc
}
echo '<p>script exécuté</p>';
?>

2.) La classe Exception

Voici la structure de la classe Exception : 

<?php 
/*
class Exception {
	protected $message = 'exception inconnue'; // Message de l'exception
	protected $code = 0; // Code de l'exception défini par l'utilisateur
	protected $file; // Nom du fichier source de l'exception
	protected $line; // Ligne de la source de l'exception
	
	final function getMessage(); // Message de l'exception
	final function getCode(); 	// Code de l'exception
	final function getFile(); 	// Nom du fichie source
	final function getLine(); 	// Ligne de la source de l'exception
	final function getTrace(); 	// Un tableau de backtrace()
	final function getTraceAsString(); // Chaîne formatée de trace
	
	// Les redéfinissables :
	function __construct($message = NULL, $code = 0);
	function __toString(); // Chaîne formatée pour l'affichage
}
*/
?>
En effet, il n'y a que __construct() et __toString() que l'on peut redéfinir en faisant hériter une classe de Exception. 

Par exemple, je vais créer une classe MonException qui rendra obligatoire le 1er argument du constructeur, et où la méthode 
__toString() n'affichera que le message d'erreur.

<?php 
class MonException extends Exception {
	public function __construct($message, $code = 0) {
		parent::__construct($message, $code);
	}
	
	public function __toString() {
		return $this->message;
	}
}

function additionner2($a, $b) {
	if(!is_numeric($a) || !is_numeric($b))
		throw new MonException('Les deux paramètres doivent être des nombres');
	return $a + $b;
}

try {
	echo additionner2(10, 39) , '<br />';
	echo additionner2(10, 'blabla');
	echo additionner2(20, 20);
} catch(MonException $e) {
	echo 'Une exception est levée : ' . $e; // là, je n'ai pas à écrire $e->getMessage();
}
echo '<p>script exécuté</p>';
?>

3.) Emboîter plusieurs blocs catch()

<?php 

function additionner3($a, $b) {
	if(!is_numeric($a) || !is_numeric($b)) 
		throw new MonException('Les deux paramètres doivent être nombres');
	if(func_num_args() > 2)
		throw new Exception('Trop d\'arguments');
	return $a + $b;
}

try {
	echo additionner3(2, 3);
	echo additionner3(43, 43, 10);
	echo additionner3(20, 'Voilà'); // ne sera même pas exécuté, car l'exception déjà levée interrompt le script du bloc try
} catch(MonException $e) {
	echo 'Exception levée : ' , $e;
} catch(Exception $e) {
	echo 'Exception levée : ' , $e->getMessage();
}

?>

4.) Exemple concret : la classe PDOException

La bibliothèque PDO a sa propre classe d'exception : PDOException. Celle-ci n'hérite pas directement de la classe Exception,
mais de RuntimeException (qui a juste la particularité d'être instanciée pour émettre une exception lors de l'exécution du script). 

Il existe une classe pour chaque type d'exception, comme en C++ et Java. 
Voici leur liste complète : http://fr2.php.net/manual/fr/spl.exceptions.php

Cette classe PDOException est donc la classe pour émettre une exception par la classe PDO ou PDOStatement. 

Note : si une extension orientée objet doit émettre une erreur, elle émettra une exception.

Bref, voici un exemple d'utilisation de PDOException : 

<?php
try
{
  $db = new PDO('mysql:host=localhost;dbname=tests', 'root', ''); // Tentative de connexion.
  echo 'Connexion réussie !'; // Si la connexion a réussi, alors cette instruction sera exécutée.
}

catch (PDOException $e) // On attrape les exceptions PDOException.
{
  echo 'La connexion a échoué.<br />';
  echo 'Informations : [', $e->getCode(), '] ', $e->getMessage(); // On affiche le n° de l'erreur ainsi que le message.
}
?>

5.) Exceptions prédéfinies

Il existe une quantité d'exceptions prédéfinies. Au lieu de lancer tout le temps une exception en instanciant Exception, il
est préférable d'instancier la classe adaptée à la situation. Par exemple, reprenons le code avec la fonction additionner(). La
classe à instancier ici est celle qui doit l'être lorsqu'un paramètre est invalide. On regarde la documentation, et on
tombe sur InvalidArgumentException. Le code donnerait donc : 

<?php 
function add($a, $b) {
	if(!is_numeric($a) || !is_numeric($b))
		throw new InvalidArgumentException('Les deux paramètres doivent être des nombres');
	
	return $a + $b;
}

echo 'Test InvalidArgumentException :  <br />';
try {
	echo add(20, 'voilavoila') , '<br />';
	echo add(2, 8) , '<br />';
} catch(InvalidArgumentException $e) {
	echo 'Exception : ' , $e->getMessage();
}

?>

6.) Exécuter un code même si l'exception n'est pas attrapée

Si jamais une exception est levée dans un bloc try mas non attrapée dans un bloc catch, alors une erreur fatale est levée. C'est
dans ce cas de figure précis que l'on peut recourir au bloc finally pour faire exécuter un code donné (opération de nettoyage,
fermeture de fichier ou de connexion...).

II./ Gérer les erreurs facilement
1.) Convertir les erreurs en exceptions

Il est possible de convertir les erreurs fatales, alertes et notices en exceptions. Pour cela, je vais avoir besoin de la fonction :

set_error_handler()

Celle-ci permet d'enregistrer une fonction en callback qui sera appelée à chaque fois que l'une de ces trois erreurs sera lancée. Il
n'y a pas de rapport direct avec les exceptions : c'est à moi de l'établir.

Ma fonction, que l'on nommera error2exception() par exemple, doit demander entre deux et cinq paramètres : 

	-	le numéro de l'erreur (obligatoire)
	-	le message de l'erreur (obligatoire)
	-	le nom du fichier dans lequel l'erreur a été lancée
	-	le numéro de la ligne à laquelle l'erreur a été identifiée
	-	un tableau avec toutes variables qui existaient jusqu'à ce que l'erreur soit rencontrée
	
Je ne vais pas prêter attention au dernier paramètre, juste aux quatre premiers. Je vais créer ma propre classe MonException qui
hérite non pas de Exception mais de ErrorException (qui hérite d'Exception néanmoins). 

La fonction set_error_handler demande deux paramètres. Le 1er est le nom de la fonction à appeler, et le deuxième, les erreurs à 
intercepter. Par défaut, ce paramètre intercepte toutes les erreurs, y compris les erreurs strictes. 

Le constructeur de la classe ErrorException demande cinq paramètres, tous facultatifs : 

	-	le message d'erreur
	-	le code de l'erreur
	-	la sévérité de l'erreur (erreur fatale, alerte, notice, etc.) représentées par des constantes prédéfinies
	-	le fichier où l'erreur a été rencontrée
	-	la ligne à laquelle l'erreur a été rencontrée
	
Voilà à quoi pourrait ressembler le code de base : 

<?php 
class MyException extends ErrorException {
	public function __toString() {
		switch($this->severity) {
			case E_USER_ERROR:	// Si l'utilisateur émet une erreur fatale
				$type = 'Erreur fatale';
				break;
			case E_WARNING:	// Si PHP émet une alerte
			case E_USER_WARNING: 	// Si l'utilisateur émét une alerte
				$type = 'Attention';
				break;
			case E_NOTICE:	// Si PHP émet une notice
			case E_USER_NOTICE:	// Si l'utilsateur émet une notice
				$type = 'Note';
				break;
			default : // Erreur inconnue
				$type = 'Erreur inconnue';
				break;				
		}
		return '<strong>' . $type . '</strong> : [' . $this->code . '] ' . $this->message . '<br /><strong>' . $this->file
						. '</strong> à la ligne <strong>' . $this->line . '</strong>';
	}
}

function error2exception($code, $message, $file, $line) {
	// Le code fait office de sévérité 
	throw new MyException($message, 0, $code, $file, $line);
}

function customException($e) {
	echo 'Ligne ' , $e->getLine() , ' dans le fichier ', $e->getFile() , '<br /><strong>Exception lancée</strong> : ' 
				, $e->getMessage();
}
set_error_handler('error2exception');

set_exception_handler('customException');
?>

Note : dans le switch de __toString() de la classe MyException, je n'ai pas mentionné les erreur E_ERROR (ie erreur fatales générées
par PHP), car celles-ci ne peuvent pas être interceptées.

Ce code fonctionne à merveille (normalement). Mais attention, avec ce code, toutes les erreurs (même les notices) qui ne sont pas
dans un bloc try <strong>interrompront le script</strong> car elles émettront une exception !

On aurait très bien pu utiliser la classe Exception, mais ErrorException a été conçue exactement pour ce genre de chose. Je 
n'ai pas besoin de créer d'attribut stockant la sévérité de l'erreur ou de réécrire le constructeur pour y stocker le nom du fichier
et la ligne à laquelle s'est produite l'erreur.

2.) Personnaliser les exceptions non attrapées

J'ai réussi à transformer toutes mes erreurs en exceptions en les interceptant grâce à set_error_handler. Etant donné que la moindre
erreur lèvera une exception, il serait intéressant de personnaliser l'erreur générée par PHP. Ce que je veux dire par là, c'est
qu'une exception non attrapée génère une longue et laide erreur fatale. Je vais donc, comme pour les erreurs, intercepter les
exceptions grâce à set_exception_handler. Cette fonction demande un seul argument : le nom de la fonction à appeler si une
exception est lancée. La fonction de callback doit accepter un argument : un objet représentant l'exception.

Réf plus haut (customException(), set_exception_handler())

Attention : La fonction set_exception_handler() permet simplement d'intercepter l'exception et non l'attraper. Cela signifie que
l'on attrape l'exception, qu'on effectue des opérations puis qu'on la relâche. Le script, une fois customException appelée,
est automatiquement interrompu.

Attention2 : Il ne faut jamais lancer d'exception dans mon gestionnaire d'exception (ici customException). En effet, cela
créerait une boucle infinie mon gestionnaire lançant lui-même une exception. L'erreur lancée est la même que celle vue
précédemment : il s'agit d'une erreur fatale "Exception thrown without a stack frame in Unknown on line 0". 

</pre>
</body>
</html>