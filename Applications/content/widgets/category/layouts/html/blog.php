<h4><?php echo $this->category["name"]; ?></h4>
<?php foreach ($this->articles as $article) { ?>
<div class="article">
<h5 class="title"><?php echo htmlentities($article["title"]); ?></h5>
<?php echo $article["content"]; ?>
</div>
<?php } 

foreach ($this->child_articles as $article) { ?>
<div class="article">
<h5 class="title"><?php echo htmlentities($article["title"]); ?></h5>
<?php echo $article["content"]; ?>
</div>
<?php }
