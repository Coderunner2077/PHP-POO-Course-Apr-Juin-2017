<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Gérer les formulaires</title>
</head>
<body>
<pre>
                                                       Gérer les formulaires
                                                       
Dans le frontend, j'ai créé un formulaire pour ajouter un commentaire. Dans le backend, j'ai recréé quasimenet le même : j'ai
fait de la <strong>duplication de code</strong>. 
Pour pallier ce problème courant de duplication de formulaires, je vais <strong>externaliser</strong> mes formulaires à l'aide
d'une API, c'est-à-dire que le code créant le formulaire sera accessible à un autre endroit, par n'importe quel module de n'importe
quelle application. Cette technique fera d'une pierre deux coups : non seulement mes formulaires seront décentralisés (donc 
réutilisables une infinité de fois), mais la création se fera de manière bcp plus aisée ! Bien sûr, comme pour la conception de
l'application, cela deviendra rapide une fois l'API développée ! 

<p>Pour s'y retrouver dans les dossiers du projet de la création du site : 
<a href="http://www.victorthuillier.com/oc/poo/tp_app/?f=Partie+III%2FChapitre+5">cliquer ici</a></p>

<h3>
I./ Le formulaire
</h3>
1.) Conception du formulaire

Commençons dans ce chapitre par créer un nouveau formulaire. Un formulaire, n'est qu'un ensemble de champs permettant 
d'interagir avec le contenu du site. Par exemple, voici mon formulaire d'ajout de commentaire : 

<form action="" method="post">
  <p>
    <?= isset($erreurs) && in_array(\Entity\Comment::AUTEUR_INVALIDE, $erreurs) ? 'L\'auteur est invalide.<br />' : '' ?>
    <label>Pseudo</label>
    <input type="text" name="pseudo" value="<?= isset($comment) ? htmlspecialchars($comment['auteur']) : '' ?>" /><br />
    
    <?= isset($erreurs) && in_array(\Entity\Comment::CONTENU_INVALIDE, $erreurs) ? 'Le contenu est invalide.<br />' : '' ?>
    <label>Contenu</label>
    <textarea name="contenu" rows="7" cols="50"><?= isset($comment) ? htmlspecialchars($comment['contenu']) : '' ?></textarea><br />
    
    <input type="submit" value="Commenter" />
  </p>
</form>

Cependant, il est évident que ce formulaire est long et fastidueux à créer. De plus, si je veux éditer un commentaire, il va 
falloir le dupliquer dans l'application <em>backend</em>. Dans un premier temps, je vais m'occuper de l'aspect long et 
fastidueux : laissons un objet générer tous ces champs à notre place !

2.) L'objet Form

Comme je viens de le voir, <strong>un formulaire n'est autre qu'une liste de champs</strong>. Le rôle de cet objet sera 
donc de représenter le formulaire en dressant une liste de champs.

Commençons alors la liste des fonctionnalités de mon formulaire. Mon formulaire contient divers champs. Je dois donc pouvoir 
ajouter des champs à mon formulaire. Ensuite, que serait un formulaire si on ne pouvait pas l'afficher ? Dans mon cas, le 
formulaire ne doit pas être capable de s'afficher mais de <strong>générer</strong> tous les champs qui lui sont attachés
afin que le contrôleur puisse récupérer le corps du form pour passer à la vue. Enfin, mon form doit posséder une dernière
fonctionnalité : la capacité de déclarer si le formulaire est valide ou non en vérifiant que chaque champ l'est. 

Pour résumer, j'ai trois fonctionnalités. Un objet Form doit être capable de :

	-	Ajouter des champs à sa liste de champs
	-	Générer le corps du form
	-	Vérifier si tous les champs sont valides.
	
Ainsi, au niveau des caractéristiques de l'objet, j'en ai un qui saute aux yeux : <strong>la liste des champs !</strong>

Cependant, un form est également caractérisé par autre chose. En effet, un tel form n'a de sens que s'il est capable d'assigner 
des valeurs à ces champs. Or, afin qu'il puisse être réutilisable, le mieux que l'on puisse faire, c'est de le coupler avec
un objet possédant ou pouvant posséder ces valeurs à assigner. Cet objet sera dans mon cas une des classes filles de l'objet
Entity. Et tout le rôle de mon objet représentant le formulaire sera alors d'assigner, lors de l'ajout d'un champ, la valeur 
correspondante dans l'objet similaire à Entity qu'il possède en attribut. Par exemple, si je veux modifier un commentaire, je 
vais créer un objet Comment que je vais hydrater, puis je créerai un objet Form en passant l'objet Comment au constructeur.

Ainsi, voici ma classe Form schématisée : 

Réf projet_form.dia

3.) L'objet Field

Puisque l'objet Form est intimement lié à ses champs, je vais m'intéresser à la conception de ces champs (ou <em>fields</em> en
anglais). Tous mes champs seront des objets, chacun représentant un champ différent (une classe représentera un champ de texte,
une autre représentera une zone de saisie, etc.). Par conséquent, ils doivent tous hériter d'une même classe représentant 
leur nature en commun, à savoir une classe Field ! 

Commençons par cette classe Field. Quelles seront des ses fonctionnalités ? Un objet Field doit être capable de : 

	-	Renvoyer du code HTML représentat un champ
	-	Vérifier si la valeur du champ est valide
	
Cependant, il y a une autre fonctionnalité que je dois implémenter : celle d'assigner automatiquement les valeurs aux attributs.
Et c'est justement la méthode hydrate() qui permet de le faire. 

Mais, la classe Entity possède déjà une telle méthode, comment éviter la duplication de code ? 

Eh bien, grâce aux traits. Je vais donc créer un trait Hydrator qui implémentera cette méthode hydrate() que mes classes
Entity et Field utiliseront ! 

Réf Hydrator.php

Je peux dès à présent modifier la classe Entity de mon framwork afin d'utiliser ce trait (il faudra donc penser à supprimer
la méthode hydrate() qui y est présente).

&lt;?php
namespace OCFram;

abstract class Entity implements \ArrayAccess {

	//use Hydrator;
	
	// la méthode hydrate() n'est ainsi plus implémentée dans ma classe
	// ...
	
}

Voici le schéma UML représentant ma classe Field liée à la classe Form, avec deux classes filles en exemple (StringField 
représentant un champ de texte sur une ligne et la classe TextField représentant un textarea).

Réf projet_form.dia

4.) Développement de l'API
a./ La classe Form

Pour rappel, voici de quoi est composée la classe Form :

	-	D'un attribut stockant <strong>la liste des champs</strong>
	-	D'un attribut stockant <strong>l'entité</strong> correspondant au formulaire
	-	D'une constructeur récupérant l'entité et invoquant le setter correspondant
	-	D'une méthode permettant d'ajouter un champ à la liste des champs
	-	D'une méthode permettant de générer le formulaire
	-	D'une méthode permettant de vérifier si le formulaire est valide
	
Réf Form.php

b./ La classe Field et ses filles

Voici un petit rappel sur la composition de la classe Field. Cette classe doit être composée de :

	-	Un attribut stockant le <strong>message d'erreur</strong> associé au champ
	-	Un attribut stockant le <strong>label</strong> du champ
	-	Un attribut stockant le <strong>nom</strong> du champ
	-	Un attribut stockant la <strong>valeur</strong> du champ
	-	Un constructeur demandant la liste des attributs avec leur valeur afin d'hydrater l'objet
	-	Une méthode (abstraite) chargée de renvoyer le code HTML du champ
	- 	Et d'une méthode permettant de savoir si le champ est valide ou non.
	
Les classes filles, quand à elles, n'implémenteront que la méthode abstraite. Si elles possèdent des atributs spécifiques
(comme l'attribut maxLength pour StringField), alors elles devront implémenter les mutateurs correspondant (comme je 
le verrai plus tard, ce n'est pas nécessaire d'implémenter les accesseurs). 

Réf Field.php
Réf StringField.php
Réf TextField.php

5.) Test de mes nouvelles classes

Dans mon contrôleur de news du frontend, je vais modifier l'action chargée d'ajouter un commentaire. Je crée mon formulaire 
avec mes nouvelles classes, en commençant par modifier le fichier <strong>NewsController.php</strong> du <em>frontend</em> : 

Réf NewsController.php

La vue correspondante, <strong>insertComment.php</strong>, ressemble maintenat à ceci : 

Réf insertComment.php

Tout bien considéré, ce n'est pas pratique d'avoir ceci en plein milieu de mon contrôleur. De plus, si j'ai besoin de créer ce 
formulaire à un autre endroit, je devrai copier/coller tous ces appels à la méthode add() et recréer tous les champs. Niveau
duplication de code, je suis servi ! Je résoudrai ce problème dans la suite du chapitre. Mais avant cela, je vais me pencher
sur la validation du form. En effet, le contenu de la méthode isValid() est resté vide : faisons appel aux 
<strong>validateurs</strong> !

<h3>
II./ Validateurs
</h3>
Un validateur, comme son nom l'indique, est chargé de valider une donnée. Mais attention : un validateur ne peut valider 
<strong>qu'une seule contrainte</strong>. Par exemple, si je veux vérifier que ma valeur n'est pas nulle <strong>et</strong>
qu'elle ne dépasse pas les cinquante caractères, alors j'aurai besoin de deux validateurs : le premier vérifiera que la valeur
n'est pas nulle, et le second vérifiera que la chaîne de caractères ne dépassera pas les 50 caractères.

J'aurai donc une classe de base (Validator) et une infinité de classes filles (dans le cas précédent, on peut imaginer 
les classes NotNullValidator et MaxLengthValidator. En avant !

1.) Conception des classes
a./ La classe Validator

Ma classe de base sera donc chargée de <strong>valider</strong> une donnée. Et c'est tout : un validateur ne sert à rien
d'autre que de valider une donnée. Au niveau des caractéristiques : il n'y en a qu'une seule, le message d'erreur que le
validateur doit pouvoir renvoyer si la valeur passée n'est pas vide. 

Réf projet_form.dia

b./ Les classe filles

Les classes filles sont elles aussi très simples. Commençons par la plus facile : NotNullValidator. Celle-ci, comme toute classe
fille, sera chargée d'implémenter la méthode isValid($value). et c'est tout ! La seconde classe MaxLengthValidator,
implémente elle aussi cette méthode.
Cependant, il faut qu'elle connaisse le nombre de caractères maximal que la chaîne doit avoir ! Pour cela, cette classe 
implémentera un constructeur demandant ce nombre en paramètre, et assignera cette valeur à l'attribut correspondant.

Réf projet_form.dia ==> NotNullValidator, MaxLengthValidator

2.) Développement des classes
a./ La classe Validator

Réf Validator.php

b./ Les classes filles

Réf NotNullValidator.php 
Réf MaxLengthValidator.php

c./ Modification de la classe Field

Comme je l'ai vu, pour savoir si un champ est vide, il lui faut des <strong>validateurs</strong>. Il va donc falloir
passer, dans le constructeur de l'objet Field créé, la liste des validateurs que l'on veut imposer au champ. Dans le cas 
du champ <strong>auteur</strong> par exemple, je lui passerai les deux validateurs : je veux à la fois que le champ ne soit
pas vide et que la valeur ne dépasse pas les cinquante caractères. La création du form ressemblerait donc à ceci : 

&lt;?php

// $form représente le formulaire que l'on souhaite créer
// Ici, on souhaite lui ajouter le champ "auteur"
$form->add(new StringField([
	'label' => 'Auteur',
	'name' => 'auteur',
	'maxLength' => 50,
	'validators' => [
		new \OCFram\MaxLengthValidator('L\'Auteur spécifié est trop long (50 caractères maximum)', 50),
		new \OCFram\NotNullValidator('Merci de spécifier l\'auteur du commentaire')
		]
]));

De cette façons, quelques modifications au niveau de ma classe Field s'imposent. En effet, il va falloir créer un attribut
validators, ainsi que l'accesseur et le mutateur correspondant. De la sorte, ma méthode hydrate() assignera automatiquement
les validateurs passés au constructeur à l'attribut $validators. 

Réf projet_form.dia

Vient maintenant l'implémentation de la méthode isValid(). Cette méthode doit parcourir tous les validateurs et invoquer la 
méthode isValid($value) sur ces validateurs afin de voir si la valeur passe au travers du filet de <strong>tous</strong>
les validateurs. De cette façon, je suis sûr que toutes les contraintes ont été respectées. Si un validateur renvoie une 
réponse négatve lorsqu'on lui demande si la valeur est vide , alors on devra lui demander le message d'erreur 
qui lui a été assigné et l'assigner à mon tout à l'attribut correspondant. Ainsi, la nouvelle classe Field :

Réf Field.php

<h3>
III./ Le constructeur de formulaires
</h3>
Comme on l'a vu, créer un formulaire au sein du contrôleur présente deux inconvénients. Premièrement, cela encombre
le contrôleur. Le contrôleur doit être clair, et la création du formulaire devrait donc se faire autre part. Deuxièmement, 
il y a le problème de duplication de code : si je veux utiliser ce formulaire dans un autre contrôleur, je devrai copier/coller
tout le code responsable de la création du formulaire. Pas très flexible tout cela ! C'est pourquoi, je vais créer des
<strong>constructeurs de formulaire</strong>. Il y aura par conséquent autant de constructeurs que de formulaires différents. 

1.) Conception des classes
a./ La classe FomrBuilder

La classe FormBuilder a un rôle bien précis : elle est chargée de <strong>construire un formulaire</strong>. Ainsi, il 
n'y a qu'une fonctionnalité à implémenter... celle de construire le formulaire ! Mais, pour ce faire, encore faudrait-il
avoir un objet Form. Je le créerai donc dans le constructeur et je l'assignerai à l'attribut correspondant. 

J'ai donc : 

	-	Une méthode abstraite chargée de construire le formulaire
	-	Un attribut stockant le formulaire
	-	L'accesseur et le mutateur correspondant
	
Réf projet_form.dia

b./ Les classes filles

Un constructeur de base c'est bien beau, mais sans classes filles, difficile de construire grand-chose. Je vais donc créer
deux constructeurs de formulaire : un constructeur de formulaire de commentaires, et un constructeur de formulaire de news. J'aurai
donc ma classe FormBuilder dont hériteront deux classes : CommentFormBuilder et NewsFormBuilder : 

Réf projet_form.dia

2.) Développement des classes
a./ La classe FormBuilder

Réf FormBuilder.php

Les classes filles sont simples à créer. Il n'y a que la méthode build() à implémenter, en ayant pour simple contenu 
d'appeler successivement

b./ Les classes filles

Réf CommentFormBuilder.php
Réf NewsFormBuilder.php

3.) Ajout de l'autoload

Je viens à l'instant de créer un nouveau vendor. Afin de pouvoir charger automatiquement les classes qui le composent, je dois
modifier mon bootstrap (situé dans /Web/bootstrap.php)

Réf bootsrap.php

4.) Modification des contrôleurs
a./ L'ajout de commentaire

Effectuons les premières modifications, en commençant par le form d'ajout de commentaire dans le <em>frontend</em>. En 
utilisant mes classes, voici les instructions que je dois exécuter :

	-	Si la requête est de type POST (formulaire soumis), il faut créer un nouveau commentaire en le remplissant avec 
				données envoyées, sinon on crée un nouveau commentaire
	-	J'instancie mon constructeur de formulaire en lui passant le commentaire en argument
	-	On invoque la méthode de construction de formulaire
	-	Si le formulaire est valide, on enregistre le commentaire en BDD
	-	On passe le formulaire généré à la vue
	
Voilà ce que ça donne : 

Réf NewsControllerBis.php

La vue correspondante, <strong>insertComment.php</strong>, ne change pas par rapport à celle qu'on a créée au début du chapitre.

b./ La modification de commentaire, l'ajout et la modification de la news (backend)

Réf BackendNewsController.php

Bien sûr, il va falloir modifier les vues s'occupant d'afficher ces formulaires. Je peux aussi supprimer le fichier 
_form.php qui ne m'est plus d'aucune utilité.

Les vues <strong>insert.php, update.php</strong> et <strong>updateComment.php</strong> deviennent respectivement : 

Réf insert.php
Réf update.php
Réf updateComment.php

<h3>
IV./ Le gestionnaire de comentaires
</h3>
Je termine le chapitre en améliorant encore mon API permettant la création de formulaire. En effet, on peut noter qu'il y a
une dose non négligeable de duplication de code dans ce que je viens de faire, et notamment ceci :

&lt;?php
// Je suis ici au sein du contrôleur

if($request->method() == 'POST' && $form->isValid()) {
	$this->managers->getManagerOf('Manager')->save($comment); // pareil pour $news
	// ...
	
}

Bien que réduit, c'est toujours du code dupliqué. De plus, si l'on veut vraiment externaliser la gestion du formulaire, alors
il va falloir le sortir du contrôleur. Ainsi, il ne restera plus d'opération de traitement dans le contrôleur. On séparera donc
bien les rôles : le contrôleur n'aura plus à réfléchir sur le formulaire qu'il traite. En effet, il ne fera que demander au
constructeur de formulaire de construire le formulaire qu'il veut, puis demandera au gestionnaire de formulaire de s'occuper
de lui s'il a été envoyé. On ne se souciera donc plus de l'aspect interne du formulaire ! 

1.) Conception de gestionnaire de formulaire

Comme on vient de le voir, le gestionnaire de formulaire est chargéde traiter le formulaire une fois qu'il a été envoyé. J'ai
donc d'ores et déjà une fonctionnalité de ma classe : celle de traiter le formulaire. Concernant les caractéristiques, je vais
me pencher du côté des éléments dont mon gestionnaire a besoin pour fonctionner : Le premier élément est donc bien entendu le
formulaire dont il est question. Le deuxième élément, tout aussi évident : comment enregistrer l'entité correspondant au formulaire
si on n'a pas le <em>manager</em> correspondant ? Le deuxième élément est donc le <em>manager</em> correspondant à l'entité. 
Enfin, le troisième élément, plus subtil, est la requête du client dont le gestionnaire aura besoin pour savoir si le formulaire
a été envoyé et vérifier le type de la requête (POST ou GET). Ces trois éléments devront être passés au constructeur
de mon objet.

Réf projet_form.dia

2.) Développement du gestionnaire de formulaire

Réf FormHandler;

Modification dans le contrôleur (pour une méthode seulement ici) : 


&lt;?php
public function processForm(HTTPRequest $request) {
	if($request->method() == 'POST') {
		$news = new News([
			'auteur' => $request->postData('id'),
			etc.
		]);
		
		if($request->getExists('id')
			$news->setId($request->getData('id');
		
	}
	else {
		if($request->getExists('id')
			$news = $this->managers->getManagerOf('News')->getUnique($request->getData('id');
		else
			$news = new News();
	}
	
	$formBuilder = new NewsFormBuilder($news);
	$formBuilder->build();
	$form = $formBuilder->form();
	
	$formHandler = new FormHandler($form, $this->managers->getManagerOf('News'), $request);
	if($formHandler->process()) {
		$this->app->user()->setFlash($news->isNew() ? 'News ajoutée !' : 'News modifiée !');
		$this->app->httpResponse()->redirect('/admin/');
	}
	else
		$this->page->addVar('form', $form->createView());

}
	
Ici, la modification est très simple, j'ai juste décentralisé ce bout de code : 

&lt;?php
if($request->method() == 'POST' && $form->isValid() {
	$this->managers->getManagerOf('News')->save($comment);
	//...
}





</pre>
</body>
</html>