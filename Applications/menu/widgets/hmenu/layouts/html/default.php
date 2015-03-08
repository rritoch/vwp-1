<?php

 $idx = 0;
 $links = array();
 
?>
<ul class="hmenu <?php echo htmlentities('hmenu_'.$this->menu_name); ?>">
<?php foreach($this->cur_menu["_items"] as $item) {  
 if (($item["type"] == "link") || ($item["type"] == "applink")) {
  if ($idx < 1) {
    $class = ' class="first"';
  } else {
   $class = '';   
  }
  $idx++;
 
?>
<li<?php echo $class;?>><a href="<?php echo htmlentities($item["url"]); ?>"><?php echo htmlentities($item["text"]) ?></a></li>
<?php }} ?>
</ul>