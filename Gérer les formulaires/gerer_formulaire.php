<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>G�rer les formulaires</title>
</head>
<body>
<pre>
                                                       G�rer les formulaires
                                                       
Dans le frontend, j'ai cr�� un formulaire pour ajouter un commentaire. Dans le backend, j'ai recr�� quasimenet le m�me : j'ai
fait de la <strong>duplication de code</strong>. 
Pour pallier ce probl�me courant de duplication de formulaires, je vais <strong>externaliser</strong> mes formulaires � l'aide
d'une API, c'est-�-dire que le code cr�ant le formulaire sera accessible � un autre endroit, par n'importe quel module de n'importe
quelle application. Cette technique fera d'une pierre deux coups : non seulement mes formulaires seront d�centralis�s (donc 
r�utilisables une infinit� de fois), mais la cr�ation se fera de mani�re bcp plus ais�e ! Bien s�r, comme pour la conception de
l'application, cela deviendra rapide une fois l'API d�velopp�e ! 

<p>Pour s'y retrouver dans les dossiers du projet de la cr�ation du site : 
<a href="http://www.victorthuillier.com/oc/poo/tp_app/?f=Partie+III%2FChapitre+5">cliquer ici</a></p>

<h3>
I./ Le formulaire
</h3>
1.) Conception du formulaire

Commen�ons dans ce chapitre par cr�er un nouveau formulaire. Un formulaire, n'est qu'un ensemble de champs permettant 
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

Cependant, il est �vident que ce formulaire est long et fastidueux � cr�er. De plus, si je veux �diter un commentaire, il va 
falloir le dupliquer dans l'application <em>backend</em>. Dans un premier temps, je vais m'occuper de l'aspect long et 
fastidueux : laissons un objet g�n�rer tous ces champs � notre place !

2.) L'objet Form

Comme je viens de le voir, <strong>un formulaire n'est autre qu'une liste de champs</strong>. Le r�le de cet objet sera 
donc de repr�senter le formulaire en dressant une liste de champs.

Commen�ons alors la liste des fonctionnalit�s de mon formulaire. Mon formulaire contient divers champs. Je dois donc pouvoir 
ajouter des champs � mon formulaire. Ensuite, que serait un formulaire si on ne pouvait pas l'afficher ? Dans mon cas, le 
formulaire ne doit pas �tre capable de s'afficher mais de <strong>g�n�rer</strong> tous les champs qui lui sont attach�s
afin que le contr�leur puisse r�cup�rer le corps du form pour passer � la vue. Enfin, mon form doit poss�der une derni�re
fonctionnalit� : la capacit� de d�clarer si le formulaire est valide ou non en v�rifiant que chaque champ l'est. 

Pour r�sumer, j'ai trois fonctionnalit�s. Un objet Form doit �tre capable de :

	-	Ajouter des champs � sa liste de champs
	-	G�n�rer le corps du form
	-	V�rifier si tous les champs sont valides.
	
Ainsi, au niveau des caract�ristiques de l'objet, j'en ai un qui saute aux yeux : <strong>la liste des champs !</strong>

Cependant, un form est �galement caract�ris� par autre chose. En effet, un tel form n'a de sens que s'il est capable d'assigner 
des valeurs � ces champs. Or, afin qu'il puisse �tre r�utilisable, le mieux que l'on puisse faire, c'est de le coupler avec
un objet poss�dant ou pouvant poss�der ces valeurs � assigner. Cet objet sera dans mon cas une des classes filles de l'objet
Entity. Et tout le r�le de mon objet repr�sentant le formulaire sera alors d'assigner, lors de l'ajout d'un champ, la valeur 
correspondante dans l'objet similaire � Entity qu'il poss�de en attribut. Par exemple, si je veux modifier un commentaire, je 
vais cr�er un objet Comment que je vais hydrater, puis je cr�erai un objet Form en passant l'objet Comment au constructeur.

Ainsi, voici ma classe Form sch�matis�e : 

R�f projet_form.dia

3.) L'objet Field

Puisque l'objet Form est intimement li� � ses champs, je vais m'int�resser � la conception de ces champs (ou <em>fields</em> en
anglais). Tous mes champs seront des objets, chacun repr�sentant un champ diff�rent (une classe repr�sentera un champ de texte,
une autre repr�sentera une zone de saisie, etc.). Par cons�quent, ils doivent tous h�riter d'une m�me classe repr�sentant 
leur nature en commun, � savoir une classe Field ! 

Commen�ons par cette classe Field. Quelles seront des ses fonctionnalit�s ? Un objet Field doit �tre capable de : 

	-	Renvoyer du code HTML repr�sentat un champ
	-	V�rifier si la valeur du champ est valide
	
Cependant, il y a une autre fonctionnalit� que je dois impl�menter : celle d'assigner automatiquement les valeurs aux attributs.
Et c'est justement la m�thode hydrate() qui permet de le faire. 

Mais, la classe Entity poss�de d�j� une telle m�thode, comment �viter la duplication de code ? 

Eh bien, gr�ce aux traits. Je vais donc cr�er un trait Hydrator qui impl�mentera cette m�thode hydrate() que mes classes
Entity et Field utiliseront ! 

R�f Hydrator.php

Je peux d�s � pr�sent modifier la classe Entity de mon framwork afin d'utiliser ce trait (il faudra donc penser � supprimer
la m�thode hydrate() qui y est pr�sente).

&lt;?php
namespace OCFram;

abstract class Entity implements \ArrayAccess {

	//use Hydrator;
	
	// la m�thode hydrate() n'est ainsi plus impl�ment�e dans ma classe
	// ...
	
}

Voici le sch�ma UML repr�sentant ma classe Field li�e � la classe Form, avec deux classes filles en exemple (StringField 
repr�sentant un champ de texte sur une ligne et la classe TextField repr�sentant un textarea).

R�f projet_form.dia

4.) D�veloppement de l'API
a./ La classe Form

Pour rappel, voici de quoi est compos�e la classe Form :

	-	D'un attribut stockant <strong>la liste des champs</strong>
	-	D'un attribut stockant <strong>l'entit�</strong> correspondant au formulaire
	-	D'une constructeur r�cup�rant l'entit� et invoquant le setter correspondant
	-	D'une m�thode permettant d'ajouter un champ � la liste des champs
	-	D'une m�thode permettant de g�n�rer le formulaire
	-	D'une m�thode permettant de v�rifier si le formulaire est valide
	
R�f Form.php

b./ La classe Field et ses filles

Voici un petit rappel sur la composition de la classe Field. Cette classe doit �tre compos�e de :

	-	Un attribut stockant le <strong>message d'erreur</strong> associ� au champ
	-	Un attribut stockant le <strong>label</strong> du champ
	-	Un attribut stockant le <strong>nom</strong> du champ
	-	Un attribut stockant la <strong>valeur</strong> du champ
	-	Un constructeur demandant la liste des attributs avec leur valeur afin d'hydrater l'objet
	-	Une m�thode (abstraite) charg�e de renvoyer le code HTML du champ
	- 	Et d'une m�thode permettant de savoir si le champ est valide ou non.
	
Les classes filles, quand � elles, n'impl�menteront que la m�thode abstraite. Si elles poss�dent des atributs sp�cifiques
(comme l'attribut maxLength pour StringField), alors elles devront impl�menter les mutateurs correspondant (comme je 
le verrai plus tard, ce n'est pas n�cessaire d'impl�menter les accesseurs). 

R�f Field.php
R�f StringField.php
R�f TextField.php

5.) Test de mes nouvelles classes

Dans mon contr�leur de news du frontend, je vais modifier l'action charg�e d'ajouter un commentaire. Je cr�e mon formulaire 
avec mes nouvelles classes, en commen�ant par modifier le fichier <strong>NewsController.php</strong> du <em>frontend</em> : 

R�f NewsController.php

La vue correspondante, <strong>insertComment.php</strong>, ressemble maintenat � ceci : 

R�f insertComment.php

Tout bien consid�r�, ce n'est pas pratique d'avoir ceci en plein milieu de mon contr�leur. De plus, si j'ai besoin de cr�er ce 
formulaire � un autre endroit, je devrai copier/coller tous ces appels � la m�thode add() et recr�er tous les champs. Niveau
duplication de code, je suis servi ! Je r�soudrai ce probl�me dans la suite du chapitre. Mais avant cela, je vais me pencher
sur la validation du form. En effet, le contenu de la m�thode isValid() est rest� vide : faisons appel aux 
<strong>validateurs</strong> !

<h3>
II./ Validateurs
</h3>
Un validateur, comme son nom l'indique, est charg� de valider une donn�e. Mais attention : un validateur ne peut valider 
<strong>qu'une seule contrainte</strong>. Par exemple, si je veux v�rifier que ma valeur n'est pas nulle <strong>et</strong>
qu'elle ne d�passe pas les cinquante caract�res, alors j'aurai besoin de deux validateurs : le premier v�rifiera que la valeur
n'est pas nulle, et le second v�rifiera que la cha�ne de caract�res ne d�passera pas les 50 caract�res.

J'aurai donc une classe de base (Validator) et une infinit� de classes filles (dans le cas pr�c�dent, on peut imaginer 
les classes NotNullValidator et MaxLengthValidator. En avant !

1.) Conception des classes
a./ La classe Validator

Ma classe de base sera donc charg�e de <strong>valider</strong> une donn�e. Et c'est tout : un validateur ne sert � rien
d'autre que de valider une donn�e. Au niveau des caract�ristiques : il n'y en a qu'une seule, le message d'erreur que le
validateur doit pouvoir renvoyer si la valeur pass�e n'est pas vide. 

R�f projet_form.dia

b./ Les classe filles

Les classes filles sont elles aussi tr�s simples. Commen�ons par la plus facile : NotNullValidator. Celle-ci, comme toute classe
fille, sera charg�e d'impl�menter la m�thode isValid($value). et c'est tout ! La seconde classe MaxLengthValidator,
impl�mente elle aussi cette m�thode.
Cependant, il faut qu'elle connaisse le nombre de caract�res maximal que la cha�ne doit avoir ! Pour cela, cette classe 
impl�mentera un constructeur demandant ce nombre en param�tre, et assignera cette valeur � l'attribut correspondant.

R�f projet_form.dia ==> NotNullValidator, MaxLengthValidator

2.) D�veloppement des classes
a./ La classe Validator

R�f Validator.php

b./ Les classes filles

R�f NotNullValidator.php 
R�f MaxLengthValidator.php

c./ Modification de la classe Field

Comme je l'ai vu, pour savoir si un champ est vide, il lui faut des <strong>validateurs</strong>. Il va donc falloir
passer, dans le constructeur de l'objet Field cr��, la liste des validateurs que l'on veut imposer au champ. Dans le cas 
du champ <strong>auteur</strong> par exemple, je lui passerai les deux validateurs : je veux � la fois que le champ ne soit
pas vide et que la valeur ne d�passe pas les cinquante caract�res. La cr�ation du form ressemblerait donc � ceci : 

&lt;?php

// $form repr�sente le formulaire que l'on souhaite cr�er
// Ici, on souhaite lui ajouter le champ "auteur"
$form->add(new StringField([
	'label' => 'Auteur',
	'name' => 'auteur',
	'maxLength' => 50,
	'validators' => [
		new \OCFram\MaxLengthValidator('L\'Auteur sp�cifi� est trop long (50 caract�res maximum)', 50),
		new \OCFram\NotNullValidator('Merci de sp�cifier l\'auteur du commentaire')
		]
]));

De cette fa�ons, quelques modifications au niveau de ma classe Field s'imposent. En effet, il va falloir cr�er un attribut
validators, ainsi que l'accesseur et le mutateur correspondant. De la sorte, ma m�thode hydrate() assignera automatiquement
les validateurs pass�s au constructeur � l'attribut $validators. 

R�f projet_form.dia

Vient maintenant l'impl�mentation de la m�thode isValid(). Cette m�thode doit parcourir tous les validateurs et invoquer la 
m�thode isValid($value) sur ces validateurs afin de voir si la valeur passe au travers du filet de <strong>tous</strong>
les validateurs. De cette fa�on, je suis s�r que toutes les contraintes ont �t� respect�es. Si un validateur renvoie une 
r�ponse n�gatve lorsqu'on lui demande si la valeur est vide , alors on devra lui demander le message d'erreur 
qui lui a �t� assign� et l'assigner � mon tout � l'attribut correspondant. Ainsi, la nouvelle classe Field :

R�f Field.php

<h3>
III./ Le constructeur de formulaires
</h3>
Comme on l'a vu, cr�er un formulaire au sein du contr�leur pr�sente deux inconv�nients. Premi�rement, cela encombre
le contr�leur. Le contr�leur doit �tre clair, et la cr�ation du formulaire devrait donc se faire autre part. Deuxi�mement, 
il y a le probl�me de duplication de code : si je veux utiliser ce formulaire dans un autre contr�leur, je devrai copier/coller
tout le code responsable de la cr�ation du formulaire. Pas tr�s flexible tout cela ! C'est pourquoi, je vais cr�er des
<strong>constructeurs de formulaire</strong>. Il y aura par cons�quent autant de constructeurs que de formulaires diff�rents. 

1.) Conception des classes
a./ La classe FomrBuilder

La classe FormBuilder a un r�le bien pr�cis : elle est charg�e de <strong>construire un formulaire</strong>. Ainsi, il 
n'y a qu'une fonctionnalit� � impl�menter... celle de construire le formulaire ! Mais, pour ce faire, encore faudrait-il
avoir un objet Form. Je le cr�erai donc dans le constructeur et je l'assignerai � l'attribut correspondant. 

J'ai donc : 

	-	Une m�thode abstraite charg�e de construire le formulaire
	-	Un attribut stockant le formulaire
	-	L'accesseur et le mutateur correspondant
	
R�f projet_form.dia

b./ Les classes filles

Un constructeur de base c'est bien beau, mais sans classes filles, difficile de construire grand-chose. Je vais donc cr�er
deux constructeurs de formulaire : un constructeur de formulaire de commentaires, et un constructeur de formulaire de news. J'aurai
donc ma classe FormBuilder dont h�riteront deux classes : CommentFormBuilder et NewsFormBuilder : 

R�f projet_form.dia

2.) D�veloppement des classes
a./ La classe FormBuilder

R�f FormBuilder.php

Les classes filles sont simples � cr�er. Il n'y a que la m�thode build() � impl�menter, en ayant pour simple contenu 
d'appeler successivement

b./ Les classes filles

R�f CommentFormBuilder.php
R�f NewsFormBuilder.php

3.) Ajout de l'autoload

Je viens � l'instant de cr�er un nouveau vendor. Afin de pouvoir charger automatiquement les classes qui le composent, je dois
modifier mon bootstrap (situ� dans /Web/bootstrap.php)

R�f bootsrap.php

4.) Modification des contr�leurs
a./ L'ajout de commentaire

Effectuons les premi�res modifications, en commen�ant par le form d'ajout de commentaire dans le <em>frontend</em>. En 
utilisant mes classes, voici les instructions que je dois ex�cuter :

	-	Si la requ�te est de type POST (formulaire soumis), il faut cr�er un nouveau commentaire en le remplissant avec 
				donn�es envoy�es, sinon on cr�e un nouveau commentaire
	-	J'instancie mon constructeur de formulaire en lui passant le commentaire en argument
	-	On invoque la m�thode de construction de formulaire
	-	Si le formulaire est valide, on enregistre le commentaire en BDD
	-	On passe le formulaire g�n�r� � la vue
	
Voil� ce que �a donne : 

R�f NewsControllerBis.php

La vue correspondante, <strong>insertComment.php</strong>, ne change pas par rapport � celle qu'on a cr��e au d�but du chapitre.

b./ La modification de commentaire, l'ajout et la modification de la news (backend)

R�f BackendNewsController.php

Bien s�r, il va falloir modifier les vues s'occupant d'afficher ces formulaires. Je peux aussi supprimer le fichier 
_form.php qui ne m'est plus d'aucune utilit�.

Les vues <strong>insert.php, update.php</strong> et <strong>updateComment.php</strong> deviennent respectivement : 

R�f insert.php
R�f update.php
R�f updateComment.php

<h3>
IV./ Le gestionnaire de comentaires
</h3>
Je termine le chapitre en am�liorant encore mon API permettant la cr�ation de formulaire. En effet, on peut noter qu'il y a
une dose non n�gligeable de duplication de code dans ce que je viens de faire, et notamment ceci :

&lt;?php
// Je suis ici au sein du contr�leur

if($request->method() == 'POST' && $form->isValid()) {
	$this->managers->getManagerOf('Manager')->save($comment); // pareil pour $news
	// ...
	
}

Bien que r�duit, c'est toujours du code dupliqu�. De plus, si l'on veut vraiment externaliser la gestion du formulaire, alors
il va falloir le sortir du contr�leur. Ainsi, il ne restera plus d'op�ration de traitement dans le contr�leur. On s�parera donc
bien les r�les : le contr�leur n'aura plus � r�fl�chir sur le formulaire qu'il traite. En effet, il ne fera que demander au
constructeur de formulaire de construire le formulaire qu'il veut, puis demandera au gestionnaire de formulaire de s'occuper
de lui s'il a �t� envoy�. On ne se souciera donc plus de l'aspect interne du formulaire ! 

1.) Conception de gestionnaire de formulaire

Comme on vient de le voir, le gestionnaire de formulaire est charg�de traiter le formulaire une fois qu'il a �t� envoy�. J'ai
donc d'ores et d�j� une fonctionnalit� de ma classe : celle de traiter le formulaire. Concernant les caract�ristiques, je vais
me pencher du c�t� des �l�ments dont mon gestionnaire a besoin pour fonctionner : Le premier �l�ment est donc bien entendu le
formulaire dont il est question. Le deuxi�me �l�ment, tout aussi �vident : comment enregistrer l'entit� correspondant au formulaire
si on n'a pas le <em>manager</em> correspondant ? Le deuxi�me �l�ment est donc le <em>manager</em> correspondant � l'entit�. 
Enfin, le troisi�me �l�ment, plus subtil, est la requ�te du client dont le gestionnaire aura besoin pour savoir si le formulaire
a �t� envoy� et v�rifier le type de la requ�te (POST ou GET). Ces trois �l�ments devront �tre pass�s au constructeur
de mon objet.

R�f projet_form.dia

2.) D�veloppement du gestionnaire de formulaire

R�f FormHandler;

Modification dans le contr�leur (pour une m�thode seulement ici) : 


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
		$this->app->user()->setFlash($news->isNew() ? 'News ajout�e !' : 'News modifi�e !');
		$this->app->httpResponse()->redirect('/admin/');
	}
	else
		$this->page->addVar('form', $form->createView());

}
	
Ici, la modification est tr�s simple, j'ai juste d�centralis� ce bout de code : 

&lt;?php
if($request->method() == 'POST' && $form->isValid() {
	$this->managers->getManagerOf('News')->save($comment);
	//...
}





</pre>
</body>
</html>