<?php

 $idx = 0;
 $links = array();
 
?>
<ul class="hmenu">
<?php foreach($this->cur_menu["_items"] as $item) {  
 if (($item["type"] == "link") || ($item["type"] == "applink")) {
  if ($idx < 1) {
    $class = ' class="first"';
  } else {
   $class = '';   
  }
  $idx++;
 
?>
<li<?php echo $class;?>><a href="<?php echo htmlentities($item["url"]); ?>"><span class="linktext"><?php echo htmlentities($item["text"]) ?></span></a></li>
<?php }} ?>
</ul>