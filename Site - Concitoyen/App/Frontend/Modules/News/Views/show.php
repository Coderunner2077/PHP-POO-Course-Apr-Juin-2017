<h3>Par <strong><?= htmlspecialchars($news['auteur']) ?></strong>, le <?= $news['dateAjout']->format('d/m/Y à Hhi') ?></h3>
<h2><?= htmlspecialchars($news['titre']) ?></h2>
<p><?= nl2br((htmlspecialchars($news['contenu'])))?></p>
<?php if($news->dateAjout() != $news->dateModif()) { ?>
<p style="text-align: right;"><small><em>Modifié le <?= $news['dateModif']->format('d/m/Y à Hhi') ?></em></small></p>
<?php } ?>

<!-- Commentaires -->
<p><a href="/commenter-<?= $news['id'] ?>.html">Ajouter un commentaire</a></p>
<?php 
if(empty($comments))
	echo '<p>Aucun commentaire n\'a été posté. Soyez le first !</p>';
$id = $news['id'];
foreach($comments as $comment) {?>
	<fieldset>
		<legend>Posté par <strong><?= htmlspecialchars($comment['auteur']) ?></strong> le <?= $comment['date']->format('d/m/Y à Hhi') ?></legend>
		<p><?= nl2br(htmlspecialchars($comment['contenu'])) ?></p>
		<p class="commentBar"><a href="espace-perso/repondre-<?= $id ?>-<?= $comment['id'] ?>.html">Répondre</a>
		<a href="espace-perso/liker-comment-<?= $comment['id'] ?>.html"><img src="liker.png" alt="Liker le commentaire" /><strong><?= $comment['note'] ?>
		</strong></a></p>
		<?php if($user->isAuthenticated() || $user->isMember($comment['id'])) { ?>
				<p style="text-align: right;">
				<a href="<?= $user->isMember($comment['id']) ? 'espace-perso-'.$comment->mem() : 'admin' ?>/comment-update-<?= $comment['id'] ?>.html">Modifier </a> |
				<a href="<?= $user->isMember($comment['id']) ? 'espace-perso-'.$comment->mem() : 'admin' ?>/comment-delete-<?= $comment['id'] ?>.html"> Supprimer</a>
				</p>
		<?php }  
		foreach($comment->comms() as $comm) { ?>
		<fieldset>
			<legend><strong><?= htmlspecialchars($comm['auteur']) ?></strong> le <?= $comm['date']->format('d/m/Y à Hhi') ?></legend>
			<p><?= nl2br(htmlspecialchars($comm['contenu'])) ?></p>
			<p class="commentBar">
			<a href="membre/liker-comment-<?= $comm['id'] ?>.html"><img src="liker.png" alt="Liker le commentaire" /><strong><?= $comm['note'] ?>
			</strong></a></p>
			<?php if($user->isAuthenticated() || $user->isMember($comm['id'])) { ?>
					<p style="text-align: right;">
					<a href="<?= $user->isMember($comm['id']) ? 'espace-perso-'.$comm->mem() : 'admin' ?>/comment-update-<?= $comm['id'] ?>.html">Modifier </a> |
					<a href="<?= $user->isMember($comm['id']) ? 'espace-perso-'.$comm->mem() : 'admin' ?>/comment-delete-<?= $comm['id'] ?>.html"> Supprimer</a>
					</p>
			<?php } ?>
		</fieldset>
		<?php } ?>
	</fieldset>

<?php 
}
?>