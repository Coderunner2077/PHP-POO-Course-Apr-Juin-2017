<?php include 'inc/header.php'; ?>

<?php 
    
    $articles = $db->query('SELECT * FROM blog');
    
    while($article = $articles->fetch()) {

	    ?>
        <div class="article">

            <div class="title"><?php echo $article['title']; ?></div> 
            <div class="content">
                <?php echo $article['content']; ?>
            </div>

        </div>
<?php 
    }
    

include 'inc/footer.php'; ?>