<?php

/**
 * VWP - Default tabs layout
 * 
 * @package    VWP
 * @subpackage Layouts.Tabs
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @link http://www.vnetpublishing.com VNetPublishing.Com 
 * @copyright (c) Ralph Ritoch - All rights reserved
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License   
 */

?>
<h2>Virtual Web Platform Manager</h2>

<div class="tabsettabs">
<ul class="tabset">
 <li<?php if ($this->current_widget == 'configure') { echo ' class="selected"'; } ?>><a href="<?php echo htmlentities($this->tab_urls['configure']); ?>"><span>Configure</span></a></li>
 <li<?php if ($this->current_widget == 'eventmgr') { echo ' class="selected"'; } ?>><a href="<?php echo htmlentities($this->tab_urls['eventmgr']); ?>"><span>Events</span></a></li> 
 <li<?php if ($this->current_widget == 'dbiconfig') { echo ' class="selected"'; } ?>><a href="<?php echo htmlentities($this->tab_urls['dbiconfig']); ?>"><span>Databases</span></a></li>
 <li<?php if ($this->current_widget == 'appmgr') { echo ' class="selected"'; } ?>><a href="<?php echo htmlentities($this->tab_urls['appmgr']); ?>"><span>Applications</span></a></li>
 <li<?php if ($this->current_widget == 'install') { echo ' class="selected"'; } ?>><a href="<?php echo htmlentities($this->tab_urls['install']); ?>"><span>Installer</span></a></li>
</ul>
<div class="clr_b"></div>
</div>
<div class="tab_top">
 <div class="tab_bottom">
  <div class="tab_left">
   <div class="tab_right">