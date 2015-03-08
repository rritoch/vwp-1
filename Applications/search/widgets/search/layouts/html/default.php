<?php


?>
<div class="sb" style="text-align: center;">
<form action="" method="get">
 <input type="text" name="q" value="<?php echo $this->escape($this->query); ?>" />
 <input type="hidden" name="app" value="search" />
 <input type="hidden" name="widget" value="search" />
 <input type="hidden" name="l" value="<?php echo $this->escape($this->results_per_page); ?>" />
 <input type="submit" value="Search" />
</form>
</div>
<?php if ($this->processed) { ?>
<h2 class="search">Found: <?php echo $this->escape($this->total) ?></h2>
<div class="sr">
<?php 

if (count($this->results) > 0) {

	// Results
	
    foreach($this->results as $result) { 
    
?>
<h3 class="r"><a href="<?php echo $this->escape($result['url']); ?>"><?php echo $this->escape($result['display_title']); ?></a></h3>
<div class="r">
<div class="summary">
<?php echo $this->escape($result['display_description']) ?>
</div>
<cite><?php echo $this->escape($result['display_url']); ?></cite>
</div>
<?php } ?>

<?php 

 // Paging
 
if (isset($this->previous)|| (isset($this->next))) { ?>
<div class="paging">
<ul>
<?php if (isset($this->previous)) { ?>
 <li style="display:inline-block; margin: 0 0.25em; list_style_type: none;"><a href="<?php echo htmlentities($this->previous['url']); ?>">Previous</a></li>
<?php } 

 foreach($this->paging as $p) {

 	if (isset($p['url'])) {
?>
     <li style="display:inline-block; margin: 0 0.25em; list_style_type: none;"><a href="<?php echo htmlentities($p['url']); ?>"><?php echo htmlentities($p['title']); ?></a></li>
<?php } else { ?>
     <li style="display:inline-block; margin: 0 0.25em; list_style_type: none;"><b><?php echo htmlentities($p['title']); ?></b></li> 
<?php 
      }
 }

if (isset($this->next)) { ?> 
 <li style="display:inline-block; margin: 0 0.25em; list_style_type: none;"><a href="<?php echo htmlentities($this->next['url']); ?>">Next</a></li>
<?php } ?> 
</ul>
</div>
<?php } ?>

<?php } else { ?>
 <h3 class="e">Your search did not match any documents</h3>
<div class="r">
<div class="summary">
 <p>Suggestions</p>
 <ul>
  <li>Make sure all words are spelled correctly.</li>  
  <li>Try fewer or more general keywords.</li>
 </ul>
</div>
</div> 
<?php } ?>

</div>
<?php } ?>
