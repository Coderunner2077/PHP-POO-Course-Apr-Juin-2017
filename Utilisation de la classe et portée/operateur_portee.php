<?php
/*
 * 											L'opérateur de résolution de portée
 * 
 * L'opérateur de résolution de portée ("::"), appelé "double deux-points" ("Scope Resolution Operator"), est
 * utilisé pour appeler des éléments appartenant à telle classe et non à tel objet. 
 * Cet opérateur est aussi appelé "Paamayim Nekudoyatim".
 * 
 * I./ Les constantes de classe
 * 
 * Les constantes de classe permettent d'éviter tout code muet. Exemple de code muet : 
 * 
 * $perso = new Personnage(40);
 * 
 * ==> muet parce qu'on ne sait pas, de prime abord, à quoi 40 correspond. 
 * 
 * Pour déclarer une constante :
 * 
 * const FORCE_PETITE = 20;
 * const FORCE_MOYENNE = 50;
 * const FORCE_GRANDE = 80;
 * 
 * Attention : une constante ne prend pas de "$" devant son nom ! 
 * 
 * Contrairement aux attributs, je ne peux pas accéder à ces valeurs via l'opérateur "->" depuis une instance
 * d'objet (ni $this ni $perso ne fonctionneront), mais avec l'opérateur "::" car une constante appartient
 * à la classe et non à un quelconque objet.
 * 
 * Pour accéder à une constante, je dois spécifier le nom de la classe suivi du symbole double deux points,
 * suivi du nom de la constante. Ainsi, on pourrait imaginer un code comme celui-ci (dans Personnage) : 
 * 
 * public function setForce($force) {
 * 		if(in_array($force, [self::FORCE_PETITE, self::FORCE_MOYENNE, self::FORCE_GRANDE]) {
 * 			$this->_force = $force;
 * 		}
 * }
 * 
 * Et lors de la création de mon instance : 
 * 
 * $perso = new Personnage(Personnage::FORCE_MOYENNE);
 * 
 * Note : les constantes sont en majuscules. La même convention qu'en C.
 * 
 * II./ Les attributs et méthodes statiques
 * 
 * 1.) Les méthodes statiques
 * 
 * Tout comme en C++, pas de $this ni implicite ni encore moins explicite dans les méthodes statiques. Et je peux 
 * également l'appeler depuis une instance d'objet, même s'il est plus concevable de l'appeler tout comme les
 * constantes de classe, avec le nom de la classe et l'opérateur double deux points.
 * 
 * Comme dans les autres langages, c'est le mot-clé static qui entre en jeu : 
 * 
 * public static function parler() {
 * 		echo 'Hello le monde !';
 * }
 * 
 * Note : le mot-clé static peut être placé avant la visibilité (ici public), ça ne change rien (?).
 * 
 * Et à la suite, je pourrai faire : 
 * 
 * Personnage::parler();
 * 
 * Ou encore : 
 * 
 * $person = new Personnage(Personnage::FORCE_GRANDE);
 * $person->parler();
 * 
 * Cependant, il faut préférer la méthode avec "::" car on voit mieux de quelle classe il s'agit. De plus, appeler
 * de cette façon une méthode statique évitera une erreur de degré E_STRICT.
 * 
 * 2.) Les attributs statiques
 * 
 * Rappel : les éléments statiques sont créés au début du chargement de la classe. C'est vrai aussi pour les
 * attributs.
 * 
 * La seule chose nouvelle ici (tout comme pour les constantes d'ailleurs) est le mot-clé self qui permet d'accéder
 * à la classe (et non pas à l'instance d'objet comme $this) à l'intérieur des méthodes. 
 * 
 * Exemple : 
 * 
 * private static $_hello = 'Hello le monde !';
 * 
 * 
 * public static function parler() {
 * 		echo self::$_hello;
 * }
 * 
 * Note : il ne faut pas oublier le "$" pour les attributs statiques. 
 * 
 * Réf Personnage
 * 
 * Exemple de classe comptant le nombre d'instances : 
*/

class Compteur 
{
	private static $_compteur = 0;
	public function __construct() {
		self::$_compteur++;
	}
	
	public static function getCompteur() {
		return self::$_compteur;
	}
}

$compt = new Compteur();
$compt2 = new Compteur();
$compteur = new Compteur();
echo Compteur::getCompteur();