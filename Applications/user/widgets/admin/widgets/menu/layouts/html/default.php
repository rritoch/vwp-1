<?php

?>
<h2>User Administration</h2>

<div class="tabsettabs">
<ul class="tabset">
 <li<?php if ($this->current_widget == 'admin.users') { echo ' class="selected"'; } ?>><a href="index.php?app=user&widget=admin.users"><span>Users</span></a></li>
 <li<?php if ($this->current_widget == 'admin.admin') { echo ' class="selected"'; } ?>><a href="index.php?app=user&widget=admin.admin"><span>Configure</span></a></li>
</ul>
<div class="clr_b"></div>
</div>
<div class="tab_top">
 <div class="tab_bottom">
  <div class="tab_left">
   <div class="tab_right">