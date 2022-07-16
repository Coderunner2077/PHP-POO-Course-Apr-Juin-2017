<?php include 'inc/header.php'; ?>

<?php 
	$articleQuery = $db->query('SELECT * FROM blog');
	$articles = $articleQuery->fetchAll();
	
	$template = $twig->loadTemplate('blog.twig');
	echo $template->render(array('articles' => $articles));
?>

<?php include 'inc/footer.php'; ?>
