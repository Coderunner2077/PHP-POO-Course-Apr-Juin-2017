<h3>Hello the world !</h3>
<?php
foreach($listeNews as $news) { ?>
<h2><a href="news-<?= $news['id'] ?>.html"><em><?= htmlspecialchars($news['titre']) ?></em></a></h2>
<p><?= nl2br(htmlspecialchars($news['contenu'])) ?></p>
<p style="text-align: right;"><strong><?= htmlspecialchars($news['auteur']) ?></strong></p><hr />
<?php } ?>