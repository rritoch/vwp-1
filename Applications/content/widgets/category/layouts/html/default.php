<h4><?php echo $this->category["name"]; ?></h4>
<?php foreach ($this->articles as $article) { ?>
<div class="article">
<h5 class="title"><?php echo htmlentities($article["title"]); ?></h5>
<?php echo $article["content"]; ?>
</div>
<?php } 

foreach($this->sub_categories as $cat) { ?>
<p><a href="index.php?app=content&widget=category&category=<?php echo htmlentities($cat["id"]); ?>"><?php echo htmlentities($cat["name"]); ?></a></p>
<?php 
 }
?>