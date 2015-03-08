<?php if (
          (count($this->notices < 1)) &&
          (count($this->warnings < 1)) &&
          (count($this->errors < 1))
         ) { ?><vdoc:include alias="content" /><?php } else {

echo '<' . '?xml version="1.0" encoding="utf-8" ?' . '>';
?>
<error>
<?php if (count($this->notices) > 0) { ?>
<dt class="message">Message</dt>
<dd class="message message fade">
<ul>
 <?php foreach($this->notices as $errmsg) { ?>
<li><?php echo htmlentities($errmsg); ?></li>  
 <?php } ?>
</ul>
</dd>
<?php 

 } 

 if (count($this->warnings) > 0) { 
 
?>
<dt class="message">Warning</dt>
<dd class="message message fade">
<ul>
 <?php foreach($this->warnings as $errmsg) { ?>
<li><?php echo htmlentities($errmsg); ?></li>  
 <?php } ?>
</ul>
</dd>
<?php 

 }
 
 if (count($this->errors) > 0) { 
 
?>
<dt class="message">Error</dt>
<dd class="message message fade">
<ul>
 <?php foreach($this->errors as $errmsg) { ?>
<li><?php echo htmlentities($errmsg); ?></li>  
 <?php } ?>
</ul>
</dd>
<?php } ?>
</error>
<?php }
