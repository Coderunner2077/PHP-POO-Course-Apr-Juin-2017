<?php
class Personnage
{
	// Les attributs (privés)
	private $_force;	// Notation PEAR ==> chaque nom d'élément privé précédé d'un "_"
	private $_experience; // Par défaut, les attributs ont pour valeurs null
	private $_degats;
	
	// Les constantes : 
	const FORCE_PETITE = 20;
	const FORCE_GRANDE = 80;
	const FORCE_MOYENNE = 50;
	
	// Variable statique PRIVEE
	private static $_hello = 'Hello the world !';
	
	public function __construct() {
		$this->_force = 1;
		$this->_experience = 0;
		$this->_degats = 0;
	}
	
	/*
	public function __construct($force, $degats) {
		echo 'Instanciation du personnage !';
		$this->setForce($force);
		$this->setDegats($degats);
		$this->_experience = 1;
	}
	*/
	public function frapper(Personnage $persoAFrapper) {
		$persoAFrapper->_degats += $this->_force;
	}
	
	public function gagnerExperience() {
		$this->_experience++;	// Note : pas de '$' ici
	}
	/*
	// Mutateur chargé de modifier l'attribut $_force
	public function setForce($force) {
		if(!is_int($force)) { // Vérification de l'intégrité des données
			trigger_error('La force doit etre un entier', E_USER_WARNING); // trigger_error() génère un message
			return;											//...d'information, d'erreur ou encore d'avertissement
		}
		if($force > 100) {
			trigger_error('La force ne peut dépasser 100', E_USER_WARNING);
			return;
		}
		$this->_force = $force;
	}
	*/
	
	public function setForce($force) {
		if(in_array($force, [self::FORCE_PETITE, self::FORCE_MOYENNE, self::FORCE_GRANDE])){
			$this->_force = $force;
		}
	}
	public function setExperience($experience) {
		if(!is_int($experience)) {
			trigger_error('L\'experience doit être un nombre entier', E_USER_WARNING);
			return;
		}
		if($experience > 100) {
			trigger_error('L\expérience ne peut pas dépasser 100', E_USER_WARNING);
			return;
		}
		$this->_experience = $experience;
	}
	
	public function setDegats($degats) {
		if(!is_int($degats)) {
			trigger_error('Le dégat doit être un nombre entier', E_USER_WARNING);
			return;
		}
		$this->_degats = $degats;
	}
	//Ceci est la méthode degats(), elle se charge de renvoyer le contenu de l'attribu $_degats
	public function degats() {
		return $this->_degats;
	}
	
	// Etc.
	public function force() {
		return $this->_force;
	}
	
	public function experience() {
		return $this->_experience;
	}
	
	public function toString() {
		return 'Ce personnage a ' . $this->_force . ' de force, ' . $this->_experience . ' d\'expérience et '
				. $this->_degats . ' de degats';
	}
	
	public static function bonjour() {
		echo self::$_hello; // Note : j'écris le "$"
	}
}
/*
 * Le constructeur ne peut pas prendre n'importe quel nom ==> __construct (avec deux underscores au début...)
 * précèdé d'une portée donc. J'ai d'abord créé une classe sans constructeur, ce qui veut dire 
 * qu'il y a en PHP aussi, tout comme en C++, les constructeurs synthétiques...
 * 
 * Attention : dans le constructeur, les valeurs sont initialisées en appelant les mutateurs correspondant.
 * En effet, si on assignait directement ces valeurs avec les arguments, le principe d'encapsulation ne serait 
 * plus respecté et n'importe quel type de valeur pourrait être assigné. 
 * 
 * Attention : je ne dois mettre la méthode __construct avec le type de visibilité private que dans des cas
 * vraiment particuliers (notamment, l'instanciation normale de ma classe sera empêchée). 
 * 
 * Remarque : je n'ai pas pu définir plusieurs constructeurs.
 * 
 * 
 */

/*
$perso = new Personnage(10, 5);
if(is_null($perso->force()))
	echo 'voilà : ' . $perso->force();

$perso2 = new Personnage(35, 10);
// $perso2->setForce('string');
$perso->setForce(20);
$perso->frapper($perso2);
$perso->gagnerExperience();

$perso2->setForce(10);
$perso2->frapper($perso);
$perso2->gagnerExperience();

$perso3 = new Personnage(50, 10);
$perso3->frapper($perso2);

echo 'Personnage 1 : ' . $perso->toString() . '<br />'; // je dois appeler la méthode toString() explicitement
echo 'Personnage 2 : ' . $perso2->toString() . '<br />';
echo 'Personnage 3 : ' . $perso3->toString() . '<br />';

*/

/*
 * --------------------------------------------------------------------------------------------------------------
 * L'auto-chargement de classes
 * 
 * Pour une question d'organisation, il vaut mieux créer un fichier par classe. Il vaut mieux l'appeler,
 * par exemple "MaClasse.php" si je veux utiliser la classe "MaClasse". Je n'aurai qu'à inclure ce fichier
 * contenant ma classe, de cette manière : 
 * 
 * <?php 
 * require 'MaClasse.php';	// j'inclus ma classe
 * 
 * $object = new MaClasse();	// Et seulement après, je me sers de ma classe
 * 
 * Remarque : require est identique à include, mis à part le fait que si une erreur survient, il produit 
 * également une erreur fatale de niveau E_COMPILE_ERROR. En d'autres termes, il stoppera le script alors
 * que include émettra seulement une alerte de niveau E_WARNING qui n'empêchera pas le script de continuer.
 * 
 * Voici les grandes étapes pour pouvoir automatiser ce mécanisme (pour ne pas avoir à répéter require bcp de
 * fois) ==> 
 * 
 * Etape 1 : 
 * 
 *function chargerClasse($classe) {
 *		require $classe . '.php';	// On inclut la classe correspondante au paramètre passé
 *}
 *
 *Etape 2 : 
 *	// On enregistre la fonction en autoload pour qu'elle soit appelée dès qu'on instanciera une classe non
 *	// déclarée ==>
 *	spl_autoload_register('chargerClasse');
 * 
 * Voilà, je peux maintenant instancier des objets sans inclure manuellement leur classe.
 * 
 * Explication : En PHP, il y a ce qu'on appelle une "pile d'autoloads". Cette pile contient une liste de 
 * fonctions. Chacune d'entre elles sera appelée automatiquement par PHP lorsqu'on essaye d'instancier une classe
 * non déclarée. J'ai donc ici ajouté ma fonction à la pile d'autoloads afin qu'elle soit appelée à chaque fois
 * qu'on essaye d'instancier une classe non déclarée. PHP va donc appeler chaque fonction de la pile d'autoload
 * jusqu'à ce que la classe que l'on souhaite instancier soit chargée.
 * 
 * Note : je peux enregistrer autant de fonctions en autoload que je veux ave spl_autoload_register(). Si j'en
 * enregistre plusieurs, elles seront appelées dans l'ordre de leur enregistrement jusqu'à ce que la classe
 * soit chargée. Pour y parvenir, il suffit d'appeler spl_autoload_register() pour chaque fonction à enregistrer.
 * 
 * 
 * 
