<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Description de l'application</title>
</head>
<body>
<pre>
                                                       Description de l'application
                                                       
Je vais réaliser dans cette dernière partie un site web comparable à une application. Pour y arriver, je vais créer une <strong>
bibliothèque</strong>, un <strong>module</strong> de news avec commentaires ainsi qu'un <strong>espace d'administration</strong>
complet. Le plus difficile consiste à développer ma bibliothèque. Au lieu de me lancer tête baissée dans sa création, je vais
réflechir sur ce que c'est, à quoi elle doit me servir, etc. Je ne développerai cette bibliothèque que dans le prochain chapitre.

<h3>
I./ Une application : qu'est-ce que c'est ?
</h3>
1.) Le déroulement d'une application

Avant de commencer à créer une application, encore faudrait-il savoir ce que c'est et en quoi cela consiste. En fait, il faut
décomposer le déroulement des actions effectuées du début à la fin (le début étant la requête envoyée par le client et la fin
étant la réponse envoyée à ce client). 

Voici en détails les quatre grandes étapes de déroulement d'une application : 

	i./ <strong>Lancement de l'application</strong> : lorsque l'internaute accédera à mon site, un fichier PHP est exécuté sur
		serveur. Dans mon cas, ce fichier sera exécuté <strong>à chaque fois que le visiteur voudra accéder à une page</strong>,
		quelque soit cette dernière. Que le visiteur veuille afficher une news, ouvrir un forum ou poster un commentaire, c'est
		ce fichier  qui sera exécuté (je verrais comment plus tard). Ce fichier sera composé de deux parties : la première aura
		pour rôle de gérer les différents autoloaders, et la seconde se chargera de <strong>lancer l'application</strong>. Ce genre
		de fichier est appelé <strong>bootstrap</strong>.
	ii./ <strong>Chargement de la requête du client</strong> : Cette étape consiste à analyser la requête envoyée par le client. C'est
		lors de cette étape que l'application ira chercher les variables transmises par les formulaires ou par l'URL.
	iii./ <strong>Exécution de la page désirée</strong> : c'est ici le coeur de l'exécution de l'application. Mais comment 
		l'application connaît-elle la page à exécuter ? Quelle action le visiteur veut-il exécuter ? Veut-il afficher une news,
		visiter un forum,  poster un commentaire ? Cette action est déterminée par ce qu'on appelle un <strong>routeur</strong>.
		En analysant l'URL, le routeur est capable de savoir ce que le visiteur veut. Par exemple, si l'URL entrée par le visiteur
		est http://www.monsupersite.com/news-12.html, alors le routeur saura que le visiteur veut afficher la news ayant pour 
		identifiant 12 dans la base de données. Le routeur va donc retourner cette action à l'application qui l'exécutera 
	iv./ <strong>Envoi de la réponse au client</strong> : après avoir exécuté l'action désirée (par exemple, si le visiteur
		veut afficher une news, l'action correspondante est de récupérer cette news), l'application va afficher le tout, et 
		l'exécution du script sera terminée. C'est à ce moment là que la page sera envoyée au visiteur.
		
Pour bien cerner le principe, prenons un exemple. Si le visiteur veut voir la news N°12, alors il demandera la page news-12.html.

Donc une application s'exécute et elle a pour rôle  d'orchestrer l'exécution du script afin  de donner  la page du visiteur. 
Mon application sera en fait... un objet ! Qui dit objet, dit classe, et qui dit classe dit fonctionnalités.

Quelles fonctionnalités devra posséder ma classe Application : 

Pour l'instant, elle ne possède qu'une fonctionnalité : celle de s'exécuter (y en a d'autres, à voir plus tard...). Cette
fonctionnalité est obligatoire quelle que soit l'application : quel serait l'intérêt d'une application si elle ne pouvait pas
s'exécuter ?

Dans un site web, on peut difféncier deux parties : le frontend et le backend. La première est la partie accessible par tout le 
monde : c'est à travers cette dernière  qu'un utilisateur pourra afficher une news, lier un sujet  d'un forum, poster un commentaire,
etc. La seconde partie est l'espace d'administration : l'accès est bloquée aux visiteurs. Pour y accéder, une paire identifiant -
mot de passe est requise. Dans ce TP, je vais séparer ces deux parties en deux applications distinctes. Et ces deux applications
 seront représentées par deux classes héritant de ma classe Application.
 
2.) Un peu d'organisation

La classe Application fait partie de ma <strong>bibliothèque</strong> (ou <strong>library</strong> en anglais), comme de 
nombreuses autres classes dont je parlerai plus tard. Ces classes-là constituent le <strong>coeur</strong> de mon framework. Comme
tout  framework qui se respecte, il doit avoir un nom. Dans le cadre de ce cours, ce framework sera nommé <strong>OCFram</strong>. 
Les fichiers contenant les classes  de mon framework seront donc dans un dossier lui étant dédié, lui-même dans le dossier qui
contiendra toutes les bibliothèques (je verrai par la suite que j'en aurai besoin d'en créer d'autres). Ces classes seront
donc placés dans le dossier <strong>/lib/OCFram</strong>. Créons ces dossiers dès maintenant.

Pour les deux applications, c'est un peu plus compliqué. En effet, une application ne tient pas dans un seul fichier : elle est
divisée en plusieurs parties. Parmi ces parties, je trouverai la plus grosse: la partie concernant les modules. 

Qu'est-ce qu'un module ?

Un module est un ensemble d'actions et de données concernant une partie du site. Par exemple, les actions "afficher une news" 
et "commenter une news" font partie du même module de news, tandis que les actions "afficher un sujet" et "poster dans ce sujet"
font partie du module forum. 

Ainsi, voici quelle sera mon architecture : 

	-	Le dossier <strong>/App</strong> contiendra mes applications (frontend et backend)
	-	Le sous-dossier <strong>/App/Nomdelapplication/Modules</strong> contiendra les modules de l'application (par exemple, si
			l'application frontend possède un module de news, alors il y aura un dossier <strong>/App/Frontend/Modules/News</strong>)
			
J'y reviendrai plus en détail sur ces modules. J'en parle ici pour introduire la notion d'architecture (et rebondir sur autre
chose). En effet, je devrai me poser une question : quand l'utilisateur veut afficher une page, quel fichier PHP sera exécuté
en premier ? Où pourrai-je placer mes feuilles de style et mes images ? 

Tous les fichiers accessibles au public devront être placés dans un dossier <strong>/Web</strong>. Pour être plus précis, mon
serveur HTTP ne pointera pas vers la racine du projet, mais vers le dossier /Web.

Explication s'impose : sur mon serveur local, si je tape localhost dans la barre d'adresse, alors mon serveur renverra
le contenu du dossier C:\MAMP\htdocs (ou C:\WAMP\www sous WampServer ou encore /var/www sous LAMP). Mais comment est-ce que
cela se fait-il ? Qui a décrété cela ? En fait, c'est écrit quelque part, dans un fichier de configuration. Il y en a un qui 
dit que si on tape localhost dans la barre d'adresse, alors on se connectera sur l'ordinateur. Ce fichier de configuration
est le fichier <strong>hosts</strong>. Le chemin menant vers ce fichier est <strong>C:\Windows\System32\drivers\etc\hosts</strong>
sous Windows (et /etc/hosts sous Linux et Mac OS). Je peux l'éditer (à condition d'avoir les droits). A la fin de ce fichier,
je peux par exemple taper : 

	127.0.0.1	monsupersite
	
Après avoir sauvegardé ce fichier, j'ouvre mon navigateur, je tape monsupersite et... j'atteris sur la même page que lorsque
je tapais localhost ! 

Maintenant, je vais utiliser un autre fichier. La manipulation consiste à dire à l'oridnateur que lorsqu'on tape <strong>
monsupersite</strong>, on ne voudra pas le contenu de C:\MAMP\htdocs, mais de :

	C:\MAMP\htdocs\monsupersite\Web
	
Tout d'abord, je dois dire que je vais utiliser le module <strong>mod_vhost_alias</strong> d'Apache. Il faut s'assurer que
ce dernier soit bien activé. Le fichier que je vais manipuler est le fichier de configuration d'Apache, à savoir "httpd.conf"
sous WampServer. Je dois rajouter ceci à la fin de ce fichier : 

&lt;VirtualHost *:80>
  ServerAdmin webmaster@localhost
  # Je mets ici le nom de domaine que j'ai utilisé dans le fichier hosts.
  ServerName monsupersite
  
  # Je mets ici le chemin vers lequel doit pointer le domaine.
  # Je suis sous Windows. Si je suis sous Linux, le chemin sera de la forme /home/victor/www/monsupersite/Web
  DocumentRoot "C:\Mamp\htdocs\monsupersite\Web"
  &lt;Directory "C:\Mamp\htdocs\monsupersite\Web">
    Options Indexes FollowSymLinks MultiViews
    
    # Cette directive permet d'activer les .htaccess.
    AllowOverride All
    
    # Si le serveur est accessible via l'Internet mais que je n'en fais qu'une utilisation personnelle
    # il faut penser à interdire l'accès à tout le monde
    # sauf au localhost, sinon je ne pourrai pas y accéder !
    # deny from all (ce truc là fait que le message "Forbidden..." s'affiche !)
    allow from localhost
  &lt;/Directory>
&lt;/VirtualHost>

Si j'ai un serveur Lamp, il ne faut pas chercher à trouver le fichier httpd.conf, il n'existe pas !

En fait, la création d'hôtes virtuels s'effectue en créant un nouveau fichier contenant la configuration de ce dernier. Le fichier
à créer est /etc/apache2/sites-available/monsupersite (je remplace "monsupersite" par le domaine choisi). A l'intérieur, je 
place le contenu précédent. Ensuite, il n'y a plus qu'à activer cette nouvelle configuration grâce à la commande suivante :

sudo a2ensite monsupersite (je remplace <strong>monsupersite</strong> par le nom du fichier contenant la configuration de l'hôte
							virtuel)
	
Dans tous les cas, que je sois sous Windows, Linuw, Mac OS ou quoi que ce soit d'autre, il faut <strong>redémarrer Apache</strong>
pour que la nouvelle configuration soit prise en compte. 

Et je n'ai plus qu'à entrer <strong>monsupersite</strong> dans la barre d'adresse, et je verrai que le navigateur m'affiche 
le dossier spécifié dans la configuration d'Apache !

<h3>
II./ Les entrailles de l'application
</h3>
1.) Retour sur les modules

Un module englobe un ensemble d'actions agissant sur une même partie du site. C'est donc à l'intérieur de ce module que je vais
créer le contrôleur, les vues et les modèles. 

a./ Le contrôleur

Je vais donc créer pour chaque module un contrôleur qui contiendra au moins autant de méthodes que d'actions. Par exemple, si dans
le module de news je veux avoir la possibilité d'afficher l'index du module (qui dévoilera la liste des cinq dernières
news par exemple) et afficher une news, j'aurais alors deux méthodes dans mon contrôleur : executeIndex() et executeShow(). Ce
fichier aura pour nom <strong>NomDuModuleControleur.php</strong>, ce qui donne, pour le module de news, un fichier du nom de
<strong>NewsControleur.php</strong>. Celui-ci est directement situé dans le dossier du module. 

b./ Les vues

Chacune de ces actions correspond à une vue. J'aurai donc pour chaque action une vue du même nom. Par exemple, pour l'action show(),
j'aurai un fichier show.php. Toutes les vues sont à placer dans le dossier Views du module. 

c./ Les modèles

En fait, les modèles, je les connais déjà, il s'agit des <strong>managers</strong>. Ce sont eux qui feront office de modèles. Les
modèles ne sont rien d'autre que des fichiers permettant l'interaction avec les données. Pour chaque module, j'aurai donc au
moins deux fichiers constituant le modèle : le manager abstrait de base (<strong>NewsManager.php</strong>) et au moins une
classe exploitant ce manager (par exemple NewsManagerPDO.php). Tous les modèles devront être placés dans le dossier suivant :

<strong>/lib/vendors/Model</strong> 

...afin qu'ils puissent être utilisés facilement par les deux applications différentes (ce qui est souvent le cas avec les 
applications backend et frontend). Cependant, ces modèles ont besoin des classes représentant les <strong>entités</strong>
qu'ils gèrent. La classe représenant un enregistrement (comme News dans mon cas) sera, elle, placée dans le dossier suivant :

<strong>/lib/vendors/Entity</strong>

Note : en règle générale, le dossier vendors d'un projet contient les bibliothèques tierces, ie les bibliothèques sur lesquelles
mon application s'appuye mais qui ne sont en soi pas obligatoires pour n'importe quel projet (contrairement aux classes 
constituant le coeur de mon framework). 

2.) Le back controller de base

Tous ces contrôleurs sont chacun des back controller. Et que met-on en place quand on peut dire qu'une entité B est une entité
A ? Un lien de parenté, bien évidemment ! Ainsi, j'aurai au sein de ma bibliothèque une classe abstraite BackController dont
héritera chaque back controller. L'éternelle question que l'on peut se poser : mais que permet de faire cette classe ? Pour
l'instant, il n'y en a qu'une : celle d'exécuter une action (donc une méthode). 

Faisons un petit retour sur le fonctionnement du routeur. On a bien dit que le routeur savait à quoi correspondait l'URL et
retournait en conséquence l'action à exécuter à l'application, qui sera donc apte à exécuter. Cela prendra plus de sens
maintenant que j'ai vu la notion de back controller. Le routeur aura donc pour rôle de <strong>récupérer la route
correspondant à l'URL</strong>. L'application, qui exploitera le routeur, instanciera donc le contrôleur correspondant à la route
que le routeur lui aura renvoyée. Par exemple, si l'utilisateur veut afficher une news, le routeur retournera la route
correspondante, et l'application créera une instance de NewsController en lui ayant spécifié qu'il devra effectuer l'action show().
L'application n'aura donc plus qu'à exécuter le back controller. 

Réf deroulement_application.png => pour voir le schéma de déroulement de l'application.
Réf relation_routeur_application.png 

En fait, le routeur analyse toute l'URL et décrypte ce qui s'y cache (je verrai plus tard comment). Quand on dit que "tout le 
monde pourra y accéder à travers la requête du client", cela veut dire que l'on pourra accéder à cette valeur à travers la classe
qui représentera la requête du client (que je construirai d'ailleurs dans le prochain chapitre).

3.) La page

Comment est générée la page que le visiteur aura devant les yeux ? Cette page sera représentée par une classe. Comme d'habitude,
qui dit classe dit fonctionnalité. En effet, une page en possède plusieurs : 

	-	Celle d'ajouter une variable à la page (le contrôleur aura besoin de passer des données à la vue)
	-	Celle d'assigner une vue à la page (il faut qu'on puisse dire à la page quelle vue elle doit utiliser)
	-	De générer la page avec le layout de l'application
	
Des explications s'imposent, notamment sur ce que sont précisément ces vues et ce fameux layout. Le layout est le fichier contenant
"l'enveloppe" du site (déclaration du doctype, inclusion des feuilles de style, déclaration des balises meta, etc.). Voici un
exemple très simple :

&lt;!DOCTYPE html>
&lt;html>
  &lt;head>
    &lt;title>Mon super site&lt;/title>
    &lt;meta charset="utf-8" />
  &lt;/head>
  
  &lt;body>
    &lt;?= $content ?>
  &lt;/body>
&lt;/html>

Le layout, spécifique à chaque application, doit se placer dans le dossier suivant :

<strong>/App/Nomdelapplication/Templates</strong>

...sous le nom de <strong>layout.php</strong>. Je remarque qu'il y a une variable $content qui traîne. Cette variable contiendra
ma classe Page qui se chargera de générer la vue, de stocker ce contenu dans $content puis d'inclure le layout correspondant
à l'application. 
Schématiquement, voici ce que ça donne : réf schema_page.png

4.) L'autoload

Afin de pouvoir partager le plus facilement possible les différents codes que les développeurs écrivent, certains d'entre eux
ont proposé d'établir des règles à suivre afin de respecter une structure commune dans leurs projets. Parmi ces règles, il y
a <strong>PSR-0</strong>, qui est en fait un ensemble de règles dont le but est de proposer une structure à respecter de sorte
à ce que tous les développeurs suivant ces recommandations puissent utiliser le même autoload. Je peux consulter ces règles
sur le GitHub de PHP FIG : https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md. 

Voici cet ensemble de règles : 

	-	Les classes et les espaces de noms entièrement qualifiés doivent respecter la structure suivante : 
			=>	<strong>\&lt;Nom du vendor>\(&lt;Espace de noms>\)*&lt;Nom de la classe></strong>
	-	Chaque espace de noms doit avoir un espace de noms racine ("Nom du vendor")
	-	Chaque espace de noms peut avoir autant de sous-espaces qu'il le souhaite
	-	Chaque séparateur d'espace de noms est converti en <strong>DIRECTORY_SEPARATOR</strong> lors du chargement à partir
			du système de fichiers
	-	Chaque "_" dans le nom d'une CLASSE est converti en <strong>DIRECTORY_SEPARATOR</strong>. Le caractère "_" n'a pas de 
			signification particulière dans un espace de noms
	-	Les classes et espaces de noms complètement qualifiés sont suffixés avec ".php" lors du chargement à partir du système
			de fichiers
	-	Les caractères alphabétiques dans les noms de vendors, espaces de noms et nom de classes peuvent contenir 
			n'importe quelle combinaison de minuscules et majuscules.
			
Les noms de vendors correspondent aux noms des bibliothèques : <strong>OCFram, Model et Entity</strong> par exemple.

Certains développeurs ont ainsi développé un autoloader qui sera capable de charger mes classes si je suis ces recommandations. 
Pour ne pas réinventer la roue, je vais directement m'en servir : il s'agit de <strong>SplClassLoader</strong>

Réf SplClassLoader.php

Je peux dès à présent copier le contenu de cette classe dans le fichier SplClassLoader.php dans le dossier suivant :

<strong>/lib/OCFram</strong>

J'ai déjà le premier fichier de mon framework ! Pour l'utiliser, je devrai créer une instance de cette classe et spécifier le
nom du vendor ainsi que le dossier dans lequel est contenue la bibliothèque. Par exemple, pour charger automatiquement
les classes de mon framework, je ferai :

&lt;?php
$OCFramLoader = new SplClassLoader('OCFram', '/lib');
$OCFramLoader->register();
?>

On indique ici que le dossier de la bibliothèque OCFram (qui contiendra toutes les classes de ce vendor) se situe dans 
<strong>/lib</strong>.

<h3>
III./ Résumé du déroulement de l'application
</h3>
Je vais ici résumer  tout ce que j'ai vu à travers un gros schéma => réf schema_resumant_application.png

Il est <strong>indispensable</strong> de comprendre parfaitement ce schéma, voire l'apprendre par coeur.


</pre>
</body>
</html>