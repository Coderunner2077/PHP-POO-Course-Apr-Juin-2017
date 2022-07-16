<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>
		<?= isset($title) ? $title : 'Mon wonderful site' ?>
	</title>
	<link rel="stylesheet" href="/css/Envision.css" type="text/css" />
</head>
<body>
<div id="wrap">
	<header>
		<h1><a href="/">Mon super site</a></h1>
		<p>Keep it up !</p>
	</header>
	
	<nav>
		<ul>
			<li><a href="/">Accueil</a></li>
			<?php if($user->isAutenticated()) {?>
			<li><a href="/admin/">Admin</a></li>
			<li><a href="/admin/news-insert.html">Insérer une news</a></li>
			<?php } ?>
		</ul>
	</nav>
	
	<div id="content-wrap">
		<section id="main">
			<?php if($user->hasFlash()) echo '<p style="text-align: center;">' , $user->getFlash() , '</p>'; ?>
			<?= $content ?>
		</section>
	</div>
	
	<footer></footer>
</div>
</body>
</html>