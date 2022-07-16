<?php
/*
 * 												Manipulation de données stockées
 * 
Une requéte renvoie le plus souvent un tableau. Tout l'enjeu est de transformer ce tableau en objet que l'on peut
manier à sa guise. 

Lorsqu'on veut construire une classe, il faut systématiquement se poser deux questions : 

	-	Quelles seront les caractéristiques de mes objets ?
	-	Quelles seront les fonctionnalités de mes objets ? 
	
Il ne faudra pas oublir de v�rifier l'intégrité des attributs dans les setters.

Ici, la classe Membre repr�sentera une entrée de mon tableau MySQL membres.

Réf Membre

I./ L'hydratation
1.) La théorie de l'hydratation est un point essentiel dans le domaine de la POO, notamment lorsqu'on utilise des objets 
représentant des données stockées. Cette notion peut vite devenir compliquée et créer des interrogations pour les débutants.

Il faut savoir que lorsqu'on parle d'hydratation, on parle d'objet "à hydrader". Hydrater un objet, c'est tout simplement lui
apporter ce dont il a besoin pour fonctionner. En d'autres termes, hydrader un objet revient à lui fournir des données 
correspondant à ses attributs pour qu'il assigne les valeurs souhaitées à ces derniers. L'objet aura ainsi des attributs valides
et sera en lui-même valide. On dit que l'objet a ainsi été hydraté. 

Réf plus bas

Au début, j'ai un objet Membre dont les attributs sont vides. Et l'hydratation consiste à assigner des valeurs aux attributs. Ainsi,
l'objet est fonctionnel car il contient des attributs valides : j'ai donc bien hydraté l'objet.

2.) L'hydratation en pratique

Hydrater un objet revient à assigner des valeurs à des attributs. Qu'à-t-on besoin de faire pour réaliser une telle chose ? Il faut
ajouter à l'objet l'action de s'hydrater. Et qui dit action dit méthode !

Qu'est-ce qu'on doit mettre dans la méthode hydrate() ?  ==> IL ne faut surtout pas assigner les valeurs aux attributs directement
car on violerait ainsi le principe d'encapsulation. En effet, de cette manière, je ne contrôle pas l'intégrité des valeurs.
Effectivement, il n'y a aucune garantie que le tableau contiendra un nom valide. Il faut donc obligatoirement passer par les setters.
Il faut donc les appeler au lieu d'assigner les valeurs directement. 
Mais faire ceci est un long : 

public function hydrate(array $donnees) {
	if(isset($donnees['membre_id']) 
		$this->setMembre_id($donnees['id']);
	if(isset($donnees['pseudo'])
		$this->setPseudo($donnees['pseudo']);
	// etc.
}

On peut le faire, bien sûr, mais non seulement c'est assez long et répétitif, cela manque de flexibilité. En effet, si j'ajoute un
attribut (et donc un setter correspondant), il faudra modifier ma méthode hydrate() pour ajouter la possibilité d'assigner cette
valeur.

Je vais donc procéder plus rapidement, en créant une boucle foreach qui va parcourir le tableau passé en paramètre. 

En récupérant le nom de l'attribut, il est facile de déterminer le setter correspondant. Chaque setter a pour nom setnomDeLAttributN.

Remarque : dans mon tableau membres les noms de colonnes sont de cette forme là ==> nom_de_l_attribut  (ce qui m'a amené à adapter
les setters).

Attention : il est important de préciser que la 1re lettre du nom de l'attribut doit être en majuscule. Par exemple, le setter
correspondant à nom est setNom. Je ferai donc appel à la fonction ucfirst() qui transforme la 1re lettre en maj.

Pour vérifier si la méthode existe, je vais utiliser la fonction : 

	-	bool method_exists(mixed $object, string $nomDeLaMethode) 
	
La méthode renvoie true si la méthode existe, et false si elle n'existe pas.

Ensuite, pour appeler le setter à l'intérieur de la condition if(method_existe($donnees, $method)), il y a une petite astuce. Il est
possible d'appeler une méthode dynamiquement, ie appeler une méthode dont le nom n'est pas connu d'avance (en d'autres termes, 
le nom est connu seuleement pendant l'exécution et est donc stocké dans une variable). Pour ce faire, voici un petit exemple de code :

class A {

	public function hello() {
		echo 'Hello le monde';
	}
}

A $a = new A;
$method = 'hello';

$a->$method(); // On affiche 'Hello le monde';

Note : il est bien entendu possible de passer des arguments à la méthode. 

Réf Membre

Cette fonction est très importante, je la retrouverai dans de nombreux codes (parfois sous des formes différentes), provenant
de pluseurs développeurs. 

Note : il est courant d'implémenter un constructeur à ces classes demandant un tableau de valeurs pour qu'il appelle ensuite
la fonction d'hydratation afin que l'objet soit hydraté dès sa création.

II./ Gérer sa BDD correctement

On vient de voir jusqu'à présent comment gérer les données que les requêtes nous renvoient, mais où placer ces requêtes ? Mon but
est de programmer orienté objet, donc je veux le moins de code possible  en-dehors des classes pour mieux l'organiser. Il ne faut
surtout pas placer les requêtes dans les méthodes de la classe représentat une entité de la BDD. 

1.) Une classe, un rôle

En POO, il y a une phrase qui revient souvent : "une classe, un rôle". Un objet instanciant une classe comme Membre a pour
rôle de représenter une ligne présente en BDD. Représenter ne veut pas dire gérer. 

J'aurai donc besoin d'un autre objet qui va gérer les objets Membre représentant les données du BDD. Un objet gérant des entités
issues d'une BDD est généralement appelé un "manager". 

Comme un manager ne fonctionne pas sans support de stockage (dans mon cas, une BDD), je vais prendre un exemple concret en 
créant un gestionnaire pour mes membres, qui va donc se charger d'en ajouter, d'en modifier, d'en supprimer ou d'en récupérer. 
Puisque ma classe est un gestionnaire de membres, je vais la nommer MembresManager. Cependant, il faut se rappeler des deux questions
que l'on doit se poser en créant une classe :

	-	Quelles seront les caractéristiques de mes objets ?
	-	Quelles seront les fonctionnalités de mes objets ?
	
2.) Les caractéristiques d'un manager

Mon manager a besoin d'une connexion à la BDD pour fonctionner et pouvoir effectuer des requêtes. En utilisant PDO, je sais
que la connexion à la BDD est représentée par un objet, un objet d'accès à la BDD (ou DAO, pour Database Access Object). 
Mon manager a donc besion de cet objet pour fonctionner, donc ma classe aura un attribut stockant cet objet. 

Ma classe n'a besoin de rien d'autre pour fonctionner. 

Je peux donc commencer à écire ma classe : 

Réf MembresManager

Pour tester si cela fonctionne, je vais créer un Membre

Réf plus bas;

Un manager peut stocker des objets en BDD, mais peut tout à fait les stocker sur un autre support (fichier XML, fichier test, etc.).

*/

require 'Membre.php';

try {
	$bdd = new PDO('mysql:host=localhost;dbname=mybdd; charset=utf8', 'root', 'root',
			array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
} catch(Exception $e) {
	die('Erreur : ' . $e->getMessage());
}

$req = $bdd->query('SELECT membre_id, pseudo, pass, adresse_mail, travail, passions, date_inscription, date_naissance, ' 
		. 'ville, avatar_url FROM membres');
while($donnees = $req->fetch(PDO::FETCH_ASSOC)) {	// Chaque entrée est récupérée et placée dans un array
	// On place les données (stockées dans un tableau) conernant le personnage au constructeur de la classe
	// On admet que le constructeur de la classe appelle chaque setter pour assigner les valeurs qu'on lui a données
	$membre = new Membre($donnees);
	echo $membre->toString() . '<br />';
}

$member = new Membre([
		'membre_id' => 3,
		'pseudo' => 'Ben',
		'pass' => sha1('historia'),
		'adresse_mail' => 'abcd@free.fr',
		'travail' => 'Mécano', 
		'passions' => 'opéra', 
		'date_naissance' => '1977-09-04',
		'ville' => 'Saint-Thropez', 
		'avatar_url' => '3.jpg'
]);


require 'MembresManager.php';
$manager = new MembresManager($bdd);
$manager->update($member);
//$manager->add($member);
echo $manager->get(3)->toString();
