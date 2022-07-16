<form action="" method="post">
	<p>
	<?= isset($errors) && in_array(\Entity\Comment::AUTEUR_INVALIDE, $errors) ? 'Le pseudo est invalide<br />' : '' ?>
	<label>Pseudo</label>
	<input type="text" name="pseudo" value="<?= htmlspecialchars($comment['pseudo']) ?>" /><br />
	
	<?= isset($errors) && in_array(\Entity\Comment::CONTENU_INVALIDE, $errors) ? 'Le contenu est invalide<br />' : '' ?>
	<label>Contenu</label>
	<textarea name="contenu" rows="7" cols="50"> htmlspecialchars($comment['contenu'])?></textarea><br />
	
	<input type="hidden" name="news" value="<?= $comment['news'] ?>" />
	<input type="submit" value="Modifier" />
	</p>
</form>