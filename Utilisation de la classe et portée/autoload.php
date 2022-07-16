<?php
function chargerClasse($classe) {
	require $classe . '.php';
}
/*
chargerClasse('Personnage');
$perso = new Personnage(10, 10);
echo $perso->toString();
*/
// Ou alors, encore mieux, en automatisant ce mécanisme : 

spl_autoload_register('chargerClasse');

$perso = new Personnage(20, 20);

echo $perso->toString();

Personnage::bonjour();

$perso->bonjour(); // méthode statique appelée depuis une instance de classe (préférer l'autre façon)

$perso2 = new Personnage(Personnage::FORCE_MOYENNE); // attention, le bon constructeur est à définir...

