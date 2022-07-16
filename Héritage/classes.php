<?php
abstract class Personnage {
	private $_force = 0;
	
	public function afficherForce() {
		echo $this->_force;
	}
	
	protected function getForce() {
		return $this->_force;
	}
	
	abstract public function setForce($force);
	
	public static function lancerTest() {
		self::quiEstCe();
		echo '<br />Résolution statique à la volée : ';
		static::quiEstCe();
	}
	
	public static function quiEstCe() {
		echo 'Je suis la classe <strong>Mère</strong>';
	}
	
	public function whoIsIt() {
		echo 'Je suis ta <strong>Mère</strong>';
	}
	
	public function test() {
		static::whoIsIt();
	}
}

class Guerrier extends Personnage {
	protected $force = 0;
	public function afficherForce() {
		parent::afficherForce();	// appelle la méthode de la classe parente	
	}
	
	protected function getForce() {
		$retour = parent::getForce();
		return 'On peut ainsi stocker le résultat de la méthode parente : ' . $retour;
	}
	
	public function setForce($force) {
		if(is_int($force)) 
			$this->force = $force;
	}
	
	public static function quiEstCe() {
		echo 'Je suis la classe <strong>Fille</strong>';
	}
	
	public function whoIsIt() {
		echo 'Je suis pas ta <strong>Mère</strong>';
	}
	
}

$guerrier = new Guerrier();
$guerrier->afficherForce();

// Comme en Java, les attributs et méthodes private sont inaccessibles aux classes filles

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Résolution statique à la volée 

Guerrier::lancerTest(); // cette méthode appelera la méthode quiEstCe() de la classe mère (car self:: fait appel à la méthode statique de la classe dans laquelle est
						// contenu self::, donc de la classe parente ici
echo '<br />';
$guerrier->test();

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Cas complexes

class A {
	
	public function quiEstCe() {
		static::ceki();
	}
	
	public function ceki() {
		echo 'A';
	}
}

class B extends A {
	public function ceki() {
		echo 'B';
	}
	
	public static function test() {
		parent::ceki();
		echo '<br />Et la classe en cours d\'exécution : ';
		static::ceki();
		echo '<br />Et maintenant : ';
		self::quiEstCe();
		echo '<br />And finally : ';
		A::quiEstCe();
	}
}

class C extends B {
	public function ceki() {
		echo 'C';
	}
}

echo "<br />";

C::test();	// Affiche 'A', 'C', 'C', 'A'

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Dernier exemple : Utilisation de static:: dans un contexte non statique

class TestParent {
	public function __construct() {
		static::qui();
	}
	
	public static function qui() {
		echo 'TestParent';
	}
}

class TestChild extends TestParent {
	public function __construct() {
		static::qui();
	}
	
	public function test() {
		$o = new TestParent();
	}
	
	public static function qui() {
		echo 'TestChild';
	}
}

echo '<br />';
$o = new TestChild(); 	// Affiche 'TestChild'
$o->test();		// Affiche 'TestParent'

/*
 * Voilà ce qui s'est passé : 
 * 
 	-	Création d'une instance de la classe TestChild;
	-	appel de la méthode qui() de la classe TestChild puisque c'est la méthode __construct de la classe TestChild qui a été appelée ;
	-	appel de la méthode test de la classe TestChild;
	-	création d'une instance de la classe TestParent;
	-	appel de la méthode qui de la classe TestParent puisque c'est la méthode __construct de cette classe qui a été appelée.

 */


