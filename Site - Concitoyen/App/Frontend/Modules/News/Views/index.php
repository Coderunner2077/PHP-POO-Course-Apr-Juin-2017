<h2>Lste des <?php echo $nombreNews . (($pageCourante == 1) ? ' dernières news' : (' news de '
				.$listeNews[0]->dateModif()->format('d/m/Y'). ' à ' 
				.$listeNews[$nombreNews - 1]->dateModif()->format('d/m/Y'))) ?></h2>
<?php foreach($listeNews as $news) { ?>
<h3><a href="/news/<?= $news['id'] ?>"><em><?= htmlspecialchars($news['titre']) ?></em></a></h3>
<p><?= nl2br(htmlspecialchars($news['contenu'])) ?></p>
<p style="text-align: right;"><?= htmlspecialchars($news['auteur']) ?></p>
<p id="note"><a href="/liker-news-<?= $news['id'] ?>.html"><img src="liker.png" alt="Liker la news" /><strong><?= $news['note'] ?>
</strong></a></p>
<?php } ?>
<br /><br />
<?php 
if(isset($pages)) { 
	echo '<p style="text-align: center;">';
	foreach($pages as $page) 
		echo ($page == $pageCourante ? $pageCourante. ' ' : ('<a href="/'.$page.'"><strong>'.$page.'</strong></a>'));
	echo '</p>';
}
?>