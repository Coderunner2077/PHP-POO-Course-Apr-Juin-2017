<form action="" method="form">
	<p>
	<?= isset($errors) && in_array(\Entity\News::AUTEUR_INVALIDE, $errors) ? 'L\'auteur est invalide<br />' : '' ?>
	<label>Auteur</label>
	<input type="text" name="auteur" value="<?= isset($news) ? htmlspecialchars($news['auteur']) : ''?>" /><br />
	
	<?= isset($errors) && in_array(\Entity\News::TITRE_INVALIDE, $errors) ? 'Le titre est invalide<br />' : ''?>
	<label>Titre</label>
	<input type="text" name="titre" value="<?= isset($news) ? htmlspecialchars($news['titre']) : '' ?>" /><br />
	
	<?= isset($errors) && in_array(\Entity\News::CONTENU_INVALIDE, $errors) ? 'Le contenu est invalide<br />' : '' ?>
	<label>Contenu</label>
	<textarea name="contenu" rows="8" cols="60"><?= isset($news) ? htmlspecialchars($news['contenu']) : ''?></textarea><br />
	
	<?php 
	if(isset($news) && !$news->isNew()) { ?>
	<input type="hidden" name="id" value="<?= $news['id'] ?>" />
	<input type="submit" value="Modifier" name="modifier" />
	<?php 
	} 
	else { ?>
	<input type="submit" value="Ajouter" />
	<?php } ?>
	</p>
</form>