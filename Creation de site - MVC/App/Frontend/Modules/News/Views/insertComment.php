<h2>Ajouter un commentaire</h2>
<form action="" method="post">
	<p>
		<?= isset($errors) && in_array(\Entity\Comment::AUTEUR_INVALIDE, $errors) ? 'L\'auteur est invalide<br />' : '' ?>
		<label>Pseudo</label> : 
		<input type="text" name="auteur" value="<?= isset($comment) ? htmlspecialchars($comment['auteur']) : '' ?>" /><br />
		<?= isset($errors) && in_array(\Entity\Comment::CONTENU_INVALIDE, $errors) ? 'Le contenu est invalide.<br />' : '' ?>
		<label>Contenu</label> : 
		<textarea name="contenu" rows="7" cols="50"><?= isset($comment) ? htmlspecialchars($comment['contenu']) : '' ?></textarea><br />
		
		<input type="submit" value="Commenter" />
	</p>
</form>