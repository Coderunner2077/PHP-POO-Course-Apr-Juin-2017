<!DOCTYPE html>
<html>
<head>
<meta charset="ISO-8859-1">
<title>L'API de réflexivité</title>
</head>
<body>
<pre>

													L'API de réflexivité
													
Une fois que j'aurai les bases de la réflexivité, je me pencherai sur un exemple d'utilisation. Pour cela, j'utiliserai une 
bibliothèque qui se sert de l'API de réflexivité pour exploiter les <strong>annotations</strong>.

I./ Obtenir des informations sur les classes
1.) Instanciation de la classe ReclectionClass
Qui dit "Réflexivité" dit "Instanciation de classe". Je vais donc instancier une classe  qui me fournira des informations sur une
telle classe. Dans cette section, il s'agira de la classe ReflectionClass. Quand je l'instancierai, je devrai spécifier le nom
de la classe sur laquelle on veut obtenir des informations. Je vais prendre la classe Membre de mon précédent TP.

Pour obtenir des infos la concernant, je vais procéder comme suit : 

&lt;?php 
$membre = new ReclectionClass('Membre'); // Le nom de la classe doit être en apostrophes ou guillemets
?>

Note : la classe ReflectionClass possède bcp de méthodes qui ne seront pas toutes vues ici.

Il est également possible d'obtenir des info sur une classe grâce à un objet. Je vais pour cela instancier la classe 
ReflectionObject en fournissant l'instance en guise d'argument. Cette classe hérite de toutes les méthodes de ReflectionClass : elle
ne réécrit que deux méthodes (dont le constructeur). Cette classe n'implémente pas de nouvelles méthodes.

Exemple d'utilisation très simple : 

&lt;?php 
$membre = new Membre(['pseudo' => 'vyk123', 'pass' => sha1('voilavoila')]);
$classeMembre = new ReflectionObject($membre);
?>

2.) Informations propres à la classe 
a./ Les attributs

Pour savoir si la classe possède un attribut, je me tourne vers 

ReflectionClass::hasProperty($attributName)

Cette méthode retourne vrai si l'attribut dont le nom est passé en paramètre existe, et faux s'il n'existe pas. Exemple :

&lt;?php 
if($classeMembre->hasProperty('date_naissance')) 
	echo 'La classe Membre possède un attribut date_naissance';
else 
	echo 'La classe Membre ne possède pas cet attribut';
?>

Je peux aussi récupérer cet attribut afin d'obtenir des infos le concernant (à voir plus loin).

b./ Les méthodes

Si je veux savoir si la classe implémente telle méthode, alors il va falloir regarder du côté de :

ReflectionClass::hasMethod($methodName)

Celle-ci retourne vrai si la méthode est impémentée et faux si elle ne l'est pas. Exemple : 

&lt;?php 
if($classeMembre->hasMethod('getPseudo'))
	echo 'La classe Membre possède la méthode getPseudo()';
?>

c./ Les constantes

Dans ce cas également, il est possible de savoir si telle ou telle classe possède telle constante. Ceci grâce à la méthode :

ReflectionClass::hasConstant($constantName)

Exemple : 
&lt;?php 
if($classeMembre->hasConstant('NOM_DE_CONSTANTE'));
else 
	echo 'Baa noon';
?>

Je peux aussi récupérer la valeur de la constante grâce à :

ReflectionClass::getConstant($constantName)

Exemple :
&lt;?php 
if($classeMembre->hasConstant('NOUVEAU'))
	echo 'La valeur de la constante NOUVEAU : ' , $classeMembre->getConstant('NOUVEAU');
?>

Je peux également retrouver la liste complète des constantes d'une classe sous forme de tableau grâce à :

ReflectionClass::getConstants();

Exemple : 
&lt;?php 
echo '<pre>' , print_r($classeMembre->getConstants(), true) , '</pre>';
?>

3.) Relations entre classes
a./ L'héritage

Pour récupérer la classe parente de ma classe, je vais regarder du côté de :

ReflectionClas::getParentClass()

Cette méthode me renvoie la classe parente s'il y en a une : la valeur de retour sera une instance de la classe ReflectionClass
qui représentera la classe parente ! Si la classe ne possède pas de parent, alors la valeur de retour sera false.

Exemple : 

&lt;?php 
class Admin extends Membre {

}

$classeAdmin = new ReflectionClass('Admin');

if($parent = $classeAdmin->getParentClass()) 
	echo 'La classe Admin a un parent : il s\'agit de la classe : ', $parent->getName();

?>

Voici une belle occasion pour évoquer la méthode 

ReflectionClass::getName() : méthode qui se contente de renvoyer la nom de la classe. 

Cette méthode est utile quand on n'a pas dans les lignes de code le nom de la classe alors qu'on en a besoin.

Dans le domaine de l'héritage, je peux également citer : 

Reflectionclass::isSubclassOf($className) : cette méthode renvoie vrai si la classe dont le nom est spécifié en paramètre est
											le parent de ma classe. 
											
Exemple :
&lt;?php 
if($classeAdmin->isSubclassOf('Membre'))
	echo 'La classe Admin a pour parent la classe Membre';
?>

Les deux prochanes méthodes à présenter ne sont pas en rapport direct avec l'héritage, mais sont cependant utilisées lorsque
cette relation existe : il s'agit de savoir si la classe est abstraite ou finale. J'ai pour cela les méthodes :

	-	ReflectionClass::isAbstract() 
	-	ReflectionClass::isFinal()
	
Exemple :

?php 
$classeMembre = new ReflectionClass('Membre');

if(!$classeMembre->isAbstract())
	echo 'la classe Membre n\'est pas abstraite';
if(!$classeMembre->isFinal())
	echo 'La classe Membre n\'est pas finale';
?> 

Dans le même genre, on a :

RéflectionClass::isInstantiable() : permettant de savoir si la classe est instanciable. Comme la classe Membre n'est pas abstraite,
elle l'est.

Vérifions cela : 
&lt;?php 
if($classeMembre->isInstantiable())
	echo 'La classe Membre est instanciable';
?>

Bref, pas de grosse surprise.

4.) Les interfaces

Voyons maintenant les méthodes en rapport avec les interfaces. Comme cela a été dit précédemment : une interface n'est autre
qu'une classe entièrement abstraite : je peux donc instancier la classe ReflectionClass en spécifiant le nom d'une interface
en paramètre et vérifier ci celle-ci est bien une interface grâce à la méthode : 

ReflectionClass::isInterface()

Note : Dans les exemples qui suivent, je vais admettre que la classe Membre implémente une interface iMembre

&lt;?php 
$classeIMembre = new ReflectionClass('iMembre');
if($classeIMembre->isInterface()) 
	echo 'La classe iMembre est une interface';
else 
	echo 'La classe iMembre n\est pas une interface';
?>

Je peux aussi savoir si telle classe implémente telle interface grâce à la méthode :

ReflectionClass::implementsInterface($interfaceName).

Il est aussi possible de récupérer toutes les interfaces implémentées, interfaces contenues dans un tableau. Pour cela,
ces deux méthodes sont à ma disposition : 

	-	ReflectionClass::getInterfaces() : renvoie autant d'instances de la classe ReflectionClass qu'il y a d'interfaces, chacune
											représentant une interface
	-	ReflectionClass::getInterfaceNames() : renvoie un tableau contenant les noms de toutes les interfaces impélementées.
	
II./ Obtenir des informations sur les attributs de ses classes

La classe permettant de savoir un peu plus sur les attributs est ReflectionProperty. Il y a deux moyens d'utiliser cette
classe : l'instancier directement ou utiliser une méthode de ReflectionClass qui me renverra alors une instance de ReflectionProperty.

1.) Instanciation directe

L'appel du constructeur se fait en lui passant deux arguments : le nom de la classe et le nom de l'attribut. Exemple : 

&lt;?php $attributMembre = new ReflectionProperty('Membre', 'pseudo'); ?>

2.) Récupération d'attribut de classe
a./ Récupérer un attribut

Pour récupérer un attribut d'une classe, j'aurai besoin de la méthode :

ReflectionClass::getProperty($attrName)

&lt;?php 

$attributPseudo = $classeMembre->getProperty('pseudo'); ?>

b./ Récupérer tous les attributs

Si je souhaite récupérer tous les attributs d'une classe, il va falloir me servir de : 

ReflectionClass::getProperties() : retourne un tableau contenant autant d'instances de ReflectionProperty que d'attributs

&lt;?php 
$attributsMembre = $classeMembre->getProperties(); 
?>

Voyons maintenant ce que l'on peut faire avec les attributs récupérés.

3.) Le nom et valeur des attributs

Afin de récupérer le nom d'un attribut, j'ai toujours la méthode : 

ReflectionProperty::getName()

Pour obtenir la valeur de l'attribut, j'utiliserai la méthode :

ReflectionProperty::getValue($object) : en spécifiant l'instance dont on veut obtenir la valeur de l'attribut.

En effet, chaque attribut est propre à chaque instance, ça n'aurait pas de sens de demander la valeur de l'attribut d'une classe,
sauf quand il s'agit des attributs statiques.

Pour m'exercer, je liste tous les attributs de Membre :

&lt;?php 
$membre = new Membre(['pseudo' => 'jesouis', 'pass' => 'motdepasse', 'passions' => 'cheval']);
foreach($classeMembre->getProperties() as $property) 
	echo $property->getName() , ' => ', $property->getValue($membre) , '<br />';
?>

Et là, sbim ! Une erreur fatale ! Car j'ai appelé la méthode ReflectionProperty::getValue() sur un attribut non public. Il faut
donc rendre l'attribut <strong>accessible</strong> grâce à la méthode : 

ReflectionProperty::setAccessible($bool) 	: 	vrai ou faux selon si je veux rendre l'attribut accessible ou non.

&lt;?php 
foreach($classeMembre->getProperties() as $property) {
	$property->setAccessible(true);
	echo $property->getName() , ' = > ', $property->getValue($membre) , '<br />';
}
?>

Attention : quand je rends un attribut accessible, je peux modifier sa valeur grâce à ReflectionProperty::setValue($objet, $valeur),
ce qui est contre le principe d'encapsulation. Il faut donc penser à rendre l'attribut inaccessible après sa lecture en 
faisant $attribut->setAccessible(false);

4.) Portée de l'attribut

Il est tout à fait possible de savoir si un attribut est privé, protégé ou public grâce aux méthodes suivantes : 

	-	ReflectionProperty::isPrivate()
	-	ReflectionProperty::isProtected()
	-	ReflectionProperty::isPublic()
	
&lt;?php 
$uneClasse = new ReflectionClass('MaClasse');
foreach($uneClasse->getProperties() as $attribut) {
	echo $attribut->getName() ,' est un attribut ';
	if($attribut->isPrivate())
		echo 'privé';
	elseif($attribut->isProtected())
		echo 'protegé';
	else 
		echo 'public';
	
	if($attribut->isStatic())
		echo ' (attribut statique)';
}
?>

Il existe aussi une méthode permettant de savoir si l'attribut est statique ou non grâce à la méthode :

ReflectionProperty::isStatic()

5.) Les attributs statiques

Le traitement d'attributs statiques diffère un peu dans le sens où ce n'est pas un attribut d'une <strong>instance</strong>
mais un attribut de la classe. Ainsi, je ne suis pas obligé de spécifier d'instance lors de l'appel de la méthode :

ReflectionProperty->getValue()

...car un attribut statique n'appartient à aucune instance.

<?php 
class A {
	public static $attr = 'Hello world !';
}

$classeA = new ReflectionClass('A');
echo $classeA->getProperty('attr')->getValue();
?>

Au lieu d'utiliser cette façon de faire, je peux directement appeler la méthode :

ReflectionClass::getStaticPropertyValue($attributName)  : où $attributName est le nom de l'attribut

Dans le même genre, on peut citer aussi :

ReflectionClass::setStaticPropertyValue($attributName, $value) : où $value est la nouvelle valeur de l'attribut.

<?php 
// Toujours par rapport à la classe A
$classeA = new ReflectionClass('A');
echo 'Valeur de l\'attribut attr : ' , $classeA->getStaticPropertyValue('attr');
$classeA->setStaticPropertyValue('attr', 'Bonjour le monde !');

echo $classA->getStaticPropertyValue('attr');	// Affiche Bonjour le monde !

?>

J'ai aussi la possibilité d'obtenir tous les attributs statiques grâce à la méthode :

ReflectionClass::getStaticProperties() : retourne un tableau avec <strong>uniquement les valeurs de chaque attribut</strong>, et non
										pas des instances de ReflectionProperty (/!\).
								
<?php 
class B {
	public static $attr1 = 'Hello world !';
	public static $attr2 = 'Bonjour le monde !';
}

$classeB = new ReflectionClass('B');

foreach($classeB->getStaticProperties() as $attr) {
	echo $attr , ' ';
}
// A l'écran s'affichera "Hello world ! Bonjour le monde ! "
?>
										
III./ Obtenir des informations sur les méthodes de ses classes

La classe ReflectionMethod permet d'obtenir des informations concernant telle ou telle méthode. Je pourrai connaître la portée
de la méthode, si elle est statique ou non, abstraite ou finale, s'il s'agit du constructeur ou du destructeur et on pourra
même l'appeler sur un objet. 

1.) Création d'une instance de ReflectionMethod
a./ Instanciation directe

Le constructeur de ReflectionMethod demande deux arguments, le nom de la classe et le nom de la méthode. 

Exemple : 

<?php 
class AB {
	public function hello($arg1, $arg2, $arg3 = 1, $arg4 = 'Hello world !') {
		echo 'Hello world !';
	}
}

$method = new ReflectionMethod('AB', 'hello');
?>

b./ Récupération d'une méthode d'une classe

La seconde façon de procéder est de récupérer la méthode de la classe grâce à 

ReflectionClass::getMethod($methodName) : qui renvoie une instance de ReflectionMethod représentant la méthode

<?php 
$classAB = new ReflectionClass('AB');
$method = $classAB.getMethod('hello');
?>

2.) Publique, protégée ou privée ?

Comme pour les attributs, j'ai les trois méthodes suivantes : 

	-	ReflectionMethod::isPublic()
	-	ReflectionMethod::isPrivate()
	-	ReflectionMethod::isProtected()
	
Et il ya la quatrième méthode pour savoir si la méthode est statique ou pas :

ReflectionMethod::isStatic()

3.) Absraite, finale ?

Voici les méthodes :

	-	ReflectionMethod::isAbstract()
	-	ReflectionMethod::isFinal()
	
4.) Constructeur, destructeur

Dans le même genre : 

	-	ReflectionMethod::isConstructor() : permet de savoir s'il s'agit d'un constructeur
	-	ReflectionMethod::isDestructor() : permet de savoir s'il s'agit d'un destructeur
	
Note : Pour que la méthode ReflectionMethod::isConstructor() renvoie vrai, il ne faut pas obligatoirement que la méthode soit
nommée __construct. En effet, si la méthode a le même nom que la classe, celle-ci est considérée comme le constructeur de la
classe car, sous PHP 4, c'était de cette façon que l'on implémentait le constructeur : il n'y avait jamais de __construct. Pour
que les scripts développés sous PHP 4 soient aussi compatibles sous PHP 5, le constructeur peut également être implémenté de
cette manière, mais il est clairement préférable d'utiliser la méthode magique créée à cet effet. 

5.) Appeler la méthode sur un objet

Pour réaliser ce genre de choe, je vais utiliser la méthode : 

ReflectionMethod::invoke($objet, $args) : le 1er argument est l'objet sur lequel on veut appeler la méthode. Viennent ensuite
tous les arguments que la méthode appelée en exige. Exemple :

<?php 
class ABC {
	public function hello($arg1, $arg2, $arg3 = 1, $arg4 = 'Hello world !') {
		var_dump($arg1, $arg2, $arg3, $arg4);
	}
}

$hello = new ReflectionMethod('ABC', 'hello');
$abc = new ABC;
$hello->invoke($abc, 'Couc', 'Oulala');

// A l'écran s'affichera donc :
// string(4) "test" string(10) "autre test" int(1) string(13) "Hello world !"

?>
Une méthode semblable à ReflectionMethod::invoke($object, $args) existe. Il s'agit de :

ReflectionMethod::invokeArgs($object, [$tablo])

La différence entre ces deux méthodes est que la seconde demandera les arguments listés dans un tableau au lieu de les lister
en paramètres. L'équivalent du code précédent, avec ReflectMethod::invokeArgs() seraient donc le suivant :

<?php
class AD
{
  public function hello($arg1, $arg2, $arg3 = 1, $arg4 = 'Hello world !')
  {
    var_dump($arg1, $arg2, $arg3, $arg4);
  }
}

$a = new AD;
$hello = new ReflectionMethod('AD', 'hello');

$hello->invokeArgs($a, ['test', 'autre test']); // Les deux arguments sont cette fois-ci contenus dans un tableau.

// Le résultat affiché est exactement le même.

?>

Si je n'ai pas accès à la méthode à cause de sa portée restreinte, je peux la rendre accessible comme on l'a fait avec
les attributs, grâce à la méthode: :

 ReflectionMethod::setAccessible($bool)		: si $bool vaut true, alors la méthode sera accessible, sinon elle ne le sera pas. 
 
IV./ Utiliser des annotations
 
On peut utiliser des annotations pour mes classes, méthodes ou attributs, mais surtout y accéder durant l'exécution du script. 

Les annotations sont des méta-données relatives à la classe, méthode ou attribut, qui apportent des infos sur l'entité 
souhaitée. Elles sont inséréés dans des commentaires utilisant la syntaxe doc block, comme ceci : 

<?php 
/**
 * @version 2.0
 */
class Somebody {
	// ...
}
?>

Les annotations s'insèrent à peu prés de la même façon, mais la syntaxe est un peu différente. En effet, la syntaxe
doit être précise pour qu'elle puisse être parsée par la bibliothèque que je vais utiliser pour récupérer les données souhaitées.

1.) Présentation de l'addendum

Cette section aura pour but de présenter les annotations par le biais de la bibliothèque addendum qui parsera les codes pour en
extraire les informations. Pour cela, je commence par télécharger addendum pour le décompresser dans le dossier de mon projet.

Je commence par créer une classe sur laquelle je vais travailler tout au long de cette partie, comme Personnage. Avec addendum, 
toutes les annotations sont des classes héritant d'une classe de base : Annotation. Si je veux ajouter une annotation, Table
par exemple, à ma classe pour spécifier à quelle table un objet Personnage correspond, alors il faudra au préalable créer une
classe Table.

Note : pour travailler simplement, je crée sur mon ordi un dossier annotations contenant un fichier Personnage.php qui contiendra
ma classe, un fichier MyAnnotations.php qui contiendra mes annotations, et enfin le dossier addendum.

Dans MyAnnotations.php : 

class Table extends Annotation {}

A toute annotation correspond une <strong>valeur</strong>, valeur à spécifier lors de la déclaration d'une annotation (dans 
Personnage.php) : 

/**
 * @Table("personnages")
 *
 */
 class Personnage
 {
 
 }
 
 Je viens donc de créer une annotation basique, mais concrètement, je n'ai pas fait grand-chose. Je vais maintenant voir comment
 récupérer cette annotation, et plus précisément, la valeur qui lui est assignée, grâce à addendum.
 
 Mais d'abord, à quoi servent les annotations ? Eh bien, les annotations sont surtout utilisées pour les frameworks, comme
 PHPUnit (framework de tests utilitaires) ou Zend Framework par exemple, ou bien les ORM tel que Doctrine, qui apportent ici 
 des informations pour le mapping des classes. Je n'aurai donc peut-être pas à utiliser des annotations dans mes scripts,
 mais il est important  d'en avoir entendu  parler si je décide d'utiliser des frameworks ou bibliothèques les utilisant.
 
 2.) Récupérer une annotation
 
 Pour récupérer une annotation, il va d'abord falloir récupérer la classe via la bibliothèque en créant une instance de 
 ReflectionAnnotatedClass, comme je l'ai fait en début de chapitre avec ReflectionClass :
 
 <?php 
 // On commence par inclure les fichiers nécessaires
 require 'annotations/addendum/annotations.php';
 require 'annotations/MyAnnotations.php';
 require 'annotations/Personnage.class.php';
 
 $reflectedClass = new ReflectionAnnotatedClass('Personnage');
 
 echo 'La valeur de l\'annotation <strong>Table</strong> est <strong>' , $reflectedClass->getAnnotation('Table')->value , '</strong>';
 echo '<br />';
 ?>
 
 Il est aussi possible, pour une annotation, d'avoir un tableau pour valeur. Pour réaliser ceci, il faut mettre la valeur de 
 l'annotation entre accolades et séparer les valeurs du tableau par des virgules :
 
 
 /**
  * @Type({'brute', 'guerrier', 'magicien'})
  */
  class Personnage
  {
  
  }
  
 Si je récupère l'annotation, j'obtiendrai un tableau classique : 
 
 <?php 
 print_r($reflectedClass->getAnnotation('Type')->value);	// Affiche le détail du tableau
 ?>
 
 Je peux aussi spécifier des clés pour les valeurs comme ceci : 
 
 /**
  * @Type({meilleur = 'magicien', 'moins bon' = 'brute', neutre = 'guerrier'})
  */
  class Personnage 
  {
  
  }
  
 Note : notons la mise entre quotes de <strong>moins bon</strong> : elles sont utiles ici car un espace est présent. Cependant,
 pour <strong>meilleur</strong> et <strong>neutre</strong> elles ne sont pas obligatoires.
 
 Enfin, pour finir avec les tableaux, on peut en emboîter autant que l'on veut. Pour placer un tableau dans un autre, il suffit
 d'ouvrir une nouvelle paire d'accolades :
 
 /**
  * @UneAnnotation({uneCle = 1348, {uneCle2 = true, uneCle3 = 'une valeur'}})
  */
  
 3.) Savoir si telle classe possède telle annotation 
 
 Il est possible de savoir si une classe possède telle annotation grâce à la méthode hasAnnotation() : 
 
 <?php 
 $reflectedClass = new ReflectionAnnotatedClass('Personnage');
 
 $ann = 'Table';
 if($reflectedClass->hasAnnotation($ann))
 	echo 'La classe possède une annotaton <strong>', $ann ,'</strong> dont la valeur est <strong>'
		,$reflectedClass->getAnnotation($ann)->value, '</strong><br />';
 ?>
 
 4.) Une annotation à multiples valeurs
 
 Il est possible pour une annotation de posséder plusieurs valeurs. Chacune de ces valeurs est stockée dans un attribut de la 
 classe représentant l'annotation. Par défaut, une annotation ne contient qu'un attribut ($value) qui est la valeur de
 l'annotation.
 
 Pour pouvoir assigner plusieurs valeurs à une annotation, il va donc falloir ajouter des attributs à ma classe. Commençons par
 ça :
 
 &lt;?php
 class ClassInfos extends Annotation {
 	public $author;
 	public $version;
 }
 ?&gt;
 
 Attention : il est important que les attributs soient publics pour que le code extérieur à la classe puisse modifier leur
 valeur.
 
 Maintenant, tout se joue lors de la création de l'annotation. Pour assigner les valeurs souhaitées aux attributs, il suffit 
 d'écrire ces valeurs précédées du nom de l'attribut. Exemple :
 
 &lt;?php
 /**
  * @ClassInfos(author = "vyk12", version = "1.0")
  */
  class Personnage
  {
  
  }
  
 ?&gt;
 
 Pour accéder aux valeurs des attributs, il faut récupérer l'annotation, comme je l'ai fait précédemment, et récupérer l'attribut.
 
 &lt;?php
 $classInfos = $reflectedClass->getAnnotation('ClassInfos');
 
 echo $classInfos->author;
 echo $classInfos->version;
 
 ?&gt;
 
 Le fait que les attributs soient publics peut poser quelques problèmes. En effet, de la sorte, je ne peux pas être sûr que
 les valeurs assignées soient correctes. Heureusement, la bibliothèque me permet de pallier ce problème en réécrivant la
 méthode :
 
 checkConstraints($target) : (déclarée dans sa classe mère Annotation) 
 
 ...dans ma classe représentant l'annotation, appelée à chaque assignation de valeur, dans l'ordre dans lequel sont assignées 
 les valeurs. Je peux ainsi vérifier l'intégrité des données, et lancer une erreur si il y a un problème. Cette méthode
 prend un argument : la cible d'où provient l'annotation. Dans mon cas, l'annotation vient de ma classe Personnage, donc
 le paramètre sera une instance de ReflectionAnnotatedClass représentant Personnage. Je verrai ensuite que cela peut être une
 méthode ou un attribut : 
 
 &lt;?php
 class ClassInfos extends Annotation {
 	public $author;
 	public $version;
 	
 	public function checkConstraints($target) {
 		if(!is_string($this->author) 
 			throw new Exception('L\'auteur doit être une chaîne de caractères');
 		if(!is_numeric($this->version))
 			throw new Exception('Le numéro de version doit être un nombre valide');
 	}
 }
 ?&gt;
 
 5.) Des annotations pour les attributs et méthodes
 
 Jusqu'ici, j'ai ajouté des annotations à une classe. Il est cependant possible d'en ajouter à des méthodes et attributs comme
 je l'ai fait pour la classe :
 
 &lt;?php
 /**
  * @Table("Personnages")
  * @ClasseInfos(author = "vik23", version = "1.0")
  */
  class Personnage {
  
  		/**
  		 * @AttrInfos(description = 'Contient la force du personnage, de 0 à 100', type = 'int')
  		 */
  		protected $force;
  		 
  		/**
  		 * @ParamInfo(name = 'destination', description = 'La destination du personnage')
  		 * @ParamInfo(name = 'vitesse', description = 'La vitesse à laquelle se déplace le personnage')
  		 * @MethodInfos(description = 'Deplace le personnage à un autre endroit', return = true, returnDescription = 'Retourne true si le personnage peut se deplacer')
  		 */
  		public function deplacer($destination, $vitesse)
  		{
  			//...
  		}
  }
  
?&gt;

Pour récupérer une de ces annotations, il faut d'abord récupérer l'attribut ou la méthode. Je vais pour cela me tourner vers
RelfectionAnnotatedProperty et ReflectionAnnotatedMethod. Le constructeur de ces classes attend en premier paramètre le nom
de la classe contenant l'élément et, en second, le nom de l'attribut ou de la méthode. Exemple :

&lt;?php
$reflectedAttr = new ReflectionAnnotatedProperty('Personnage', 'force');
$reflectedMethod = new ReflectionAnnotatedMethod('Personnage', 'deplacer');

echo 'Infos concernant l\'attribut : ';
var_dump($reflectedAttr->getAnnotation('AttrInfos')):

echo 'Infos concernant les paramètres de la méthode : ';
var_dump($reflectedMethod->getAllAnnotations('ParamInfo'));

echo 'Infos concernant la méthode : ';
var_dump($reflectedMethod->getAnnotation('MethodInfos'));

?&gt;

Je note ici l'utilisation de ReflectionAnnotatedMethod::getAllAnnotations(). Cette méthode permet de récupérer toutes les annotations
d'une entité correspondant au nom donné en argument. Si aucun nom n'est donné, alors toutes les annotations de l'entité sont retournées.

6.) Contraindre une annotation à une cible précise

Grâce à une annotation un peu spéciale, j'ai la possibilité d'imposer un type de cible pour une annotation. En effet, jusqu'à 
maintenant, mes annotations pouvaient être utilisées aussi bien par des classes que par des attributs ou des méthodes. Dans le
cas des annotations ClassInfos, AttrInfos, MethodInfos et ParamInfos, cela présenterait un non-sens qu'elles puissent être
utilisées par n'importe quel type d'élément. 

Pour pallier ce problème, retournons à la classe ClassInfos. Pour dire à cette annotation qu'elle ne peut être utilisée que sur 
des classes, il faut utiliser l'annotation @Target : 

&lt;?php
/** @Target("class") */
class ClassInfos extends Annotation {
	public $author;
	public $version;
}
?&gt;

A présent, si j'essaye d'utiliser l'annotation @classInfos sur un attribut ou une méthode, je verrai qu'une erreur sera levée.
Cette annotation peut aussi prendre pour valeur property, method ou nesty. Ce dernier type est un peu particulier (pour
le connaître ==>https://github.com/jsuchal/addendum). 

Il y a une autre classe qui n'a pas été vue dans ce chapitre : la classe ReflectionParameter qui me permet d'obtenir des informations
sur les paramètres de mes méthodes. Me documenter.

EN RESUMé : 

Il est possible d'obtenir des informations sur ses classes, attributs et méthodes, respectivement grâce à ReflectionClass, 
ReflectionProperty et ReflectionMethod.
Utiliser des annotations sur ses classes permet, grâce à une bibliothèque telle qu'addendum, de récupérer dynamiquement leurs
contenus.
L'utilisation d'annotations dans un but de configuration dynamique est utilisée par certains frameworks ou ORM tel que 
Doctrine par exemple, ce qui permet d'économiser un fichier de configuration en plaçant la description des tables et des colonnes
directement dans des annotations.
</pre>
</body>
</html>