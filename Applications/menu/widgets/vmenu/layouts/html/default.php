<?php
 
 $idx = 0;
 $links = array();
  
?>

<div class="vmenu">
<div class="vmenu_title">
<h4><?php echo $this->cur_menu["title"]; ?></h4>
</div>
<div><div><div>

<ul class="vmenu">
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
</div></div></div></div>