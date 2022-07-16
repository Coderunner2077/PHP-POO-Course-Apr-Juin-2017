<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Le backend</title>
</head>
<body>
<pre>
                                                      Le backend
                                                    
Mon application est composée de news avec commentaires. Or, je ne peux actuellement pas ajouter de news, ni modérer les commentaires.
Pour ce faire, je vais créer un espace d'administration, qui n'est autre que le <strong>backend</strong>. Cet espace 
d'administration sera ainsi composé d'un système de gestion de news (ajout, modification et suppression) ainsi que d'un 
système de gestion de commentaires (modification et suppression). 

Ayant déjà créé le frontend, il me sera plus facile de créer le backend. Néanmoins, une nouveauté fait son apparition : celle
d'interdire le contenu de l'application aux visiteurs. 

C'est parti !

<h3>
I./ L'application
</h3>
Comme pour l'application Frontend, j'aurai besoin de créer les fichiers de base : la classe représentant l'application, le
layout, les deux fichiers de configuration et le fichier qui instanciera ma classe. 

1.) La classe BackendApplication

Cette classe ne sera pas strictement identique à la classe FrontendApplication. En effet, je dois <strong>sécuriser</strong>
 l'application afin que seuls les utilisateurs authentifiés y aient accès. 
 
Pour rappel, voici le fonctionnement de la méthode run() de la classe FrontendApplication : 

	-	Obtention du contrôleur grâce à la méthode parente getController()
	-	Exécution du contrôleur
	-	Assignation de la page créée par le contrôleur à la réponse
	-	Envoi de la réponse
	
La classe BackendApplication fonctionnera de la même façon, à la différence près que la 1re instruction ne sera exécutée que si
l'utilisateur est authentifié. Sinon, je vais récupérer le contrôleur du module de connexion que je vais créer dans ce chapitre. 
Voici le fonctionnement de la méthode run()  de la classe BA (abrégé pour BackendApplication) :

	-	Si l'utilisateur est authentifié :
			==> obtention du contrôleur grâce à la méthode parente getController()
	-	Sinon 
			==> instanciation du contrôleur du module de connexion
	-	Exécution du contrôleur
	-	Assignation de la page créée par le contrôleur à la réponse
	-	Envoi de la réponse
	
Aide : j'ai un attribut $user dans ma classe qui représente l'utilisateur. 

Réf BackendApplication.php

2.) Le layout

Le layout est le même que celui du frontend. Cependant, en pratique, cela est rare car j'aurai généralement deux layouts différents 
(chaque application a ses spécificités). Cependant, ici il n'est pas nécessaire de faire deux fichiers différents. Je peux donc 
copier/coller le layout du frontend  dans le dossier <strong>/App/Backend/Templates</strong>, soit créer le layout et inclure celui
du frontend :

&lt;?php require __DIR__.'/../../Frontend/Templates/layout.php'; ?>

3.) Les deux fichiers de configuration

Là aussi, il faut créer deux fichiers de configuration. Pour l'instant, je mets juste les structures de base :

Réf routes.xml
Réf app.xml

4.) Réécrire les URL

Je vais maintenant modifier le fichier .htaccess. Actuellement, toutes les URL sont redirigées vers :

<strong>bootstrap.php?app=Frontend</strong>.

Je garderai toujours cette règle, mais je vais d'abord en rajouter une autre : Je vais rediriger toutes lse URL commençant par
<strong>admin/</strong> vers : 

<strong>bootstrap.php?app=Backend</strong>

...Je suis bien sûr, libre de choisir un autre préfixe. Le .htaccess ressemblera donc à ceci :

RewriteEngine On

RewriteRule ^admin/ bootstrap.php?app=Backend [QSA,L]

RewriteCond %{REQUEST_FILENAME} !-f

RewriteRule ^(.*)$ bootstrap.php?app=Frontend [QSA,L]

<h3>
II./ Le module de connexion
</h3>
1.) Réfléchissons

Ce module est un peu particulier. En effet, aucune route ne sera définie pour pointer vers ce module. De plus, ce module 
ne nécessite aucun stockage de données, je n'aurai donc pas de modèle. La seule fonctionnalité attendue du module est 
d'afficher son index. Cet index aura pour rôle d'afficher le formulaire de connexion et de traiter les données de ce formulaire. 

Avant de commencer à construire le module, je vais préparer le terrain. Où stocker l'identifiant et le mot de passe permettant
d'accéder à l'application ? Je vais tout simplement ajouter deux définitions dans le fichier de configuration de l'application.

Réf app.xml

Je peux maintenant, créer le dossier :

<strong>/App/Backend/Modules/Connexion</strong>

2.) La vue

Je vais débuter par créer la vue correspondant à l'index du module. Ce sera un simple formulaire demandant le nom d'utilisateur
et le mot de passe de l'internaute : 

Réf index.php

3.) Le contrôleur

Procédons maintenant à l'élaboration du contrôleur. Ce contrôleur implémentera une seule méthode :

<strong>executeIndex(HTTPRequest $r)</strong>. 

Cette méthode devra, si le formulaire a été envoyé, vérifier si le pseudo et le mot de passe entrés sont corrects. Si c'est le 
cas, l'utilisateur est authentifié, sinon un message d'erreur s'affiche. 

Réf ConnexionController.php

Ainsi, je viens de sécuriser en un rien de temps l'application toute entière. De plus, ce module est réutilisable dans
d'autres projets ! En effet, rien ne le lie à cette application. A partir du moment où l'application aura un fichier de 
configuration adapté (ie avec les variables login et pass), alors elle pourra s'en servir !

<h3>
III./ Le module de news
</h3>
1.) Fonctionnalités

Ce module doit me permettre  de gérer le contenu de la base de données. Par conséquent, je dois avoir quatre actions : 

	-	L'action <strong>index</strong> qui m'affiche la liste de news avec des liens pour les modifier ou supprimer.
	-	L'action <strong>insert</strong> pour ajouter une news
	-	L'action <strong>update</strong> pour modifier une news
	-	L'action <strong>delete</strong> pour supprimer une news
	
2.) L'action <em>index</em>
a./ La route

Tout d'abord, définissons l'URL  qui pointera vers cette action. En l'occurence, ce sera l'accueil de l'espace d'administration :

Réf routes.xml ==> url="/admin/"

b./ Le contrôleur

Le contrôleur se chargera uniquement de passer la liste des news à la vue ainsi que le nombre de news présent. Le contenu
de la méthode est donc assez simple : 

Réf NewsController.php

Ainsi, je me resers de la méthode getList() que j'avais implémentée au cours du précedent chapitre. Cependant, il me reste
à implémenter une méthode dans mon manager : count(). 

c./ Le modèle

La méthode count() est très simple : elle ne fait qu'exécuter une requête pour renvoyer le résultat. 

Réf NewsManager.php
Réf NewsManagerPDO.php

d./ La vue

La vue se contente de parcourir le tableau  de news pour en afficher les données.

Réf indexBis.php (pas de "Bis" dans le projet...)

3.) L'action <em>insert</em>
a./ La route

Réf routes.xml ==> url="/admin/news-insert\.html module="News" action="insert" 

b./ Le contrôleur

Le contrôleur vérifie si le formulaire a été envoyé. Si c'est le cas, alors il procédera à la vérification des données et
insérera la news en BDD si tout est valide. Cependant, il y a un petit problème : lorsqu'on implémente l'action <em>update</em>,
on va dévoir réécrire la partie "traitement du formulaire"  car la validation des données sui la même logique. Je vais donc
créer une autre méthode au sein du contrôleur , nommée processForm() qui se chargera de traiter le formulaire et d'enregistrer 
la news en BDD. 

Rappel : le manager contient une méthode save() qui se chargera soit d'ajouter la news si elle est nouvelle , soit de la mettre
à jour si elle est déjà enregistrée. C'est cette méthode que je dois invoquer. 

Réf NewsController.php ==> processForm() & executeInsert

c./ Le modèle

Je vais implémenter les méthodes save() et add() dans mon manager afin que mon contrôleur puisse être fonctionnel.

Rappel : la méthode save() s'implémente directement dans NewsManager puisqu'elle ne dépend pas du DAO.

Réf NewsManager.php ==> add() & save()
Réf NewsManagerPDO.php ==> add()

d./ La vue

Là aussi, j'utiliserai de la duplication de code pour afficher le formulaire. En effet, la vue correspondant à l'action 
<em>update</em> devra également afficher ce formulaire. Je vais donc créer un fichier qui contiendra ce formulaire et qui sera
inclus au sein des vues. Je vais l'appeler ainsi :

<strong>_form.php</strong> (le "_" est utilisé pour bien indiquer qu'il ne s'agit pas d'une vue mais d'un élément à inclure)

Ma vue <strong>insert.php</strong> contiendra ainsi : 

Réf insert.php

Et le contenu du fichier <strong>_form.php</strong> sera celui-ci : 

Réf _form.php

4.) L'action <em>update</em>
a./ La route

Je vais choisir une URL basique pour la route : 

<strong>/admin/news-update-id.html</strong>

Réf routesxml 

b./ Le contrôleur

La méthode executeUpdate() est quasiment identique à executeInsert(). La seule différence est qu'il faut passer la news à la vue
si le formulaire n'a pas été envoyé. 

Réf NewsController.php

c./ Le modèle

Ce code fait appel à deux méthodes : getUnique() et modify(). La 1re a déjà été implémentée au cours du précédent chapitre  et la
seconde avait volontairement été laissée de côté. Il est maintenant temps de l'implémenter : 

Réf NewsManager.php

d./ La vue

De la même façon que pour l'action <em>insert</em>, ce procédé tient en deux lignes : il s'agit seulement d'inclure le 
formulaire, c'est tout !

Réf update.php

5.) L'action <em>delete</em>
a./ La route

Pour continuer dans l'originalité, l'URL qui pointera vers cette action sera du type :

<strong>/admin/news-delete-id.html</strong>

Réf routes.xml

b./ Le contrôleur

Le contrôleur se chargera d'invoquer la méthode du manager qui supprimera la news. Ensuite, il redirigera l'utilisateur à 
l'accueil de l'espace d'administration en ayant pris soin de spécifier un message qui s'affichera au prochain chargement de 
page. Ainsi, cette action ne possèdera aucune vue. 

Réf NewsController.php

c./ Le modèle

Ici, une simple requête DELETE suffit.

<h3>
IV./ Le module de commentaires
</h3>
Finissons de construire notre application en implémentant les dernières fonctionnalités permettant de gérer les commentaires. 

1.) Fonctionnalités

Je vais faire simple et implémenter deux fonctionnalités :

	-	La <strong>modification</strong> de commentaires
	-	La <strong>suppression</strong> de commentaires
	
2.) L'action <em>updateComment</em>
a./ La route

Commençons, comme d'habitude, par définir l'URL qui pointera vers ce module. 

==> '/admin/comment-update-id.html

Réf routes.xml

b./ Le contrôleur

La méthode que l'on implémentera aura pour rôle de contrôler les valeurs du formulaire et de modifier le commentaire en BDD si
tout est valide. Je devrai avoir quelque chose de semblable à ce que j'ai fait dans l'application Frontend. Il faudra ensuite 
rediriger l'utilisateur sur la news qu'il lisait. 

Aide : Pour rediriger l'utilisateur sur la news, il va falloir obtenir l'identifiant de cette dernière. Il faudra donc ajouter
un champ caché dans le formulaire pour transmettre ce paramètre. 

Réf NewsController.php

c./ Le modèle 

J'ai ici besoin d'implémenter deux méthodes, modify() et get(). La 1re se contente d'exécuter une requête  de type UPDATE et
la seconde une requête de type SELECT : 

Réf CommentsManager.php
Réf CommentsManagerPDO.php

d./ La vue

La vue ne fera que contenir le formulaire et afficher les erreurs qu'il y en a.

Réf updateComment.php

e./ Modification de la vue de l'affichage des commentaires

Pour des raisons pratiques, il serait préférable de modifier l'affichage des commentaires afin d'ajouter un lien à chacun 
menant vers la modification du commentaire. Pour cela, il faudra modifier la vue correspondant à l'action <strong>show</strong>
du module <strong>news</strong> de l'application <strong>Frontend</strong>.

Réf show.php

3.) L'action <em>deleteComment</em>
a./ La route

==> /admin/comments-delete-id.html

Réf routes.xml

b./ Le contrôleur

Il faut dans un premier temps invoquer la méthode du manager permettant de supprimer un commentaire. Je redirige ensuite
l'utilisateur sur l'espace d'administration.

Réf NewsController.php ==> executeDeleteComment

Aucune vue n'est donc nécessaire ici

c./ Le modèle

Il suffit ici d'implémenter la méthode delete() exécutant une simple requête DELETE : 

Réf CommentsManager.php
Réf CommentsManagerPDO.php

d./ Modification de l'affichage des commentaires

Je vais là aussi insérer le lien de suppression de chaque commentaire afin de me faciliter la tâche. Je modifie donc la vue de 
l'action <strong>show</strong> du module news de l'application <strong>Frontend</strong> : 

Réf show.php ==> ligne 22

e./ Suppression des commentaires associés à une news

Il reste un dernier détail à régler. Si l'on supprime une news, qu'advient-il des commentaires ? 
Actuellement, ces commentaires sont toujours présents en base de données. Ceci est un problème récurrent dans la gestion de
données. Pour pallier ce problème, il est possible de dire à MySQL lors de la création de la table <em>comments</em> que la
colonne <em>news_id</em> est en réalité une référence à la colonne id de la table news. Ainsi, si l'on supprime une news, on
peut dire très simplement à MySQL de supprimer toutes les entrées de la table <em>comments</em> liées à la news supprimée. 

Cela impose par conséquent une contrainte sur ma table <em>comments</em> : chaque <em>news_id</em> de chaque commentraire
<strong>doit</strong> contenir un identifiant <strong>valide</strong>, c'est-à-dire un identifiant pointant sur une news
existante. Ainsi, il devient <strong>impossible</strong> de laisser en BDD un commentaire faisant référence à une news inexistante.
Ce n'est peut-être pas le cas ici, mais dans d'autres situations on peut être amené à vouloir éviter une telle contrainte et 
opter ainsi pour plus de liberté. Dans ce cas-là, ce sera à moi de m'occuper des suppressions souhaitées. 

Cela se fera très simplement. Dans un premier temps, je vais créer une méthode deleteFromNews prenant un argument : l'identifiant
de la news à supprimer. Cette méthode sera chargée de supprimer tous les commentaires liés à la news concernée.

Réf CommentsManager.php
Réf CommentsManagerPDO.php

Il ne me reste plus qu'à modifier l'action delete de mon NewsController : 

Réf NewsController.php






</pre>
</body>
</html>