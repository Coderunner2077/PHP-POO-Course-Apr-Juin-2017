<h3>Modification d'un commentaire</h3>

<form action="" method="post">
	<p>
	<?= isset($errors) && in_array(\Entity\Comment::AUTEUR_INVAlIDE) ? 'Le pseudo est incorrect<br />' : '' ?>
	<label>Pseudo</label>
	<input type="text" name="auteur" value="<?= isset($comment) ? htmlspecialchars($comment['auteur']) : '' ?>" /><br />
	
	<?= isset($errors) && in_array(\Entity\Comment::CONTENU_INVALIDE) ? 'Le contenu est invalide' : '' ?>
	<label>Contenu</label>
	<textarea name="contenu" rows="7" cols="50"><?= isset($comment) ? htmlspecialchars($comment->contenu()) : '' ?></textarea><br />
	
	<input type="submit" value="Commenter" />
	<input type="hidden" name="news" value="<?= $news['news'] ?>" />
	</p>
</form>