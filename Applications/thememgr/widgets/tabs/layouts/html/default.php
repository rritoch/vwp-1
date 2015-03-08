<?php

?>
<h2>Theme Manager</h2>

<div class="tabsettabs">
<ul class="tabset">
 <li<?php if ($this->current_widget == 'thememgr') { echo ' class="selected"'; } ?>><a href="<?php echo htmlentities($this->tab_urls['thememgr']); ?>"><span>Themes</span></a></li>
 <li<?php if ($this->current_widget == 'framemgr') { echo ' class="selected"'; } ?>><a href="<?php echo htmlentities($this->tab_urls['framemgr']); ?>"><span>Frames</span></a></li>
</ul>
<div class="clr_b"></div>
</div>
<div class="tab_top">
 <div class="tab_bottom">
  <div class="tab_left">
   <div class="tab_right">