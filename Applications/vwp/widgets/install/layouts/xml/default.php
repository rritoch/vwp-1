<?php 

/**
 * VWP - Default XML install response layout 
 *  
 * @package    VWP
 * @subpackage Layouts.Install.XML
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @link http://www.vnetpublishing.com VNetPublishing.Com 
 * @copyright (c) Ralph Ritoch - All rights reserved
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

if ($this->install_processed) { ?>
<install_result>
 <status><?php 
  if ($this->success && $this->complete) {
   ?>Success<?php  
  } elseif ($this->complete) {
   ?>Failed<?php
  } else {
   ?>Continue<?php }
  ?></status>
</install_result>
<?php } ?>
<frame>
<frame_title>Module Installer</frame_title>
<form action="" method="post">
 <form_title>Install from local package</form_title>
 <field>
   <label>Package Filename</label>
   <input type="text" size="60" name="package" />
 </field>
 <field>
  <input type="hidden" name="app" value="vwp" />
 </field>
 <field>
 <input type="hidden" name="widget" value="install" />
 </field>
 <field>
 <input type="hidden" name="task" value="install_from_package" />
 </field>
</form>

<form action="" method="post">
 <form_title>Install from local folder</form_title>
 <field>
   <label>Folder</label>
   <input type="text" size="60" name="folder" />
 </field> 
 <field>
  <input type="hidden" name="app" value="vwp" />
 </field>
 <field>
 <input type="hidden" name="widget" value="install" />
 </field>
 <field>
 <input type="hidden" name="task" value="install_from_folder" />
 </field>
</form>
</frame>
