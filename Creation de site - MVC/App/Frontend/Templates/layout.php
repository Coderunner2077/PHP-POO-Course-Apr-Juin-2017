<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<link rel="stylesheet" href="/css/Envision.css" type="text/css" />
	<title><?= isset($title) ? $title : "Mon wonder sit" ?></title>
</head>
<body>
	<div id="wrap">
		<header>
			<h2><a href="/">Mon super site</a></h2>
			<p>Keep it up !</p>
		</header>
		
		<nav>
			<ul>
				<li><a href="/">Accueil</a></li>
				<?php if(($user->isAuthenticated())) { ?>
				<li><a href="/admin/">Admin</a></li>
				<li><a href="/admin/news-insert.html">Insérer une news</a></li>
				<li><a href="/admin/deconnecter.html">Se déconnecter</a>
				<?php } else { ?>
				<li><a href="/admin/">Restrict Area</a></li>
				<?php } ?>
			</ul>
		</nav>
		
		<div id="content-wrap">
			<section id="main">
				<?php if($user->hasFlash()) echo '<p style="text-align: center;">' ,$user->getFlash(), '</p>'; ?>
				<?= isset($_GET['time']) ? 'Temps écoulé : ' . $_GET['time'] : '' ?>
				<?= $content ?>
			</section>
		</div>
		
		<footer>
		</footer>
	</div>
</body>
</html>