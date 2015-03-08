<?php

/**
 * VWP - Default edit database configuration layout
 * 
 * @package    VWP
 * @subpackage Layouts.DBIConfig
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @link http://www.vnetpublishing.com VNetPublishing.Com 
 * @copyright (c) Ralph Ritoch - All rights reserved
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License   
 */

?>
<?php echo $this->menu ?>

<?php

 $hbar = '<tr class="hbar">
   <td class="vbar"></td>
   <td colspan="3" class="hbar"></td>
   <td class="vbar"></td>
  </tr>';

?>

<form action="" method="post" id="edit_config">

<table class="control_panel" id="edit_config_control">
 <thead>
  <tr class="controls">
   <td colspan="5">
    <ul>
     <li class="submit"><div class="btn"><span><input type="submit" value="Go" id="edit_config_go1" /></span></div></li>
     <li><a href="#edit_config_go1"><span><input type="radio" name="task" value="reset" /> Reset</span></a></li>     
     <li><a href="#edit_config_go1"><span><input type="radio" name="task" value="save_db" /> Save</span></a></li>
     <li><a href="#edit_config_go1"><span><input type="radio" name="task" value="" /> Close</span></a></li>
    </ul>    
   </td>
  </tr>  
  <tr class="title">
   <td class="vbar"><div class="clr_b"></div></td>
   <td colspan="3" class="title"><h2>Database Configuration</h2></td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>

  <?php echo $hbar; ?>       
 </thead>
 <tbody> 

<?php if (isset($this->dbinfo["_id"])) { ?> 
  <tr class="data">
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="label">Name</td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="data"><?php echo htmlentities(isset($this->dbinfo["_id"]) ? $this->dbinfo["_id"] : ''); ?></td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>
<?php } else { ?>
  <tr class="data">
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="label">Name</td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="data"><input type="text" size="60" name="dbicfg[_id]" value="<?php echo htmlentities(isset($this->dbinfo["_id"]) ? $this->dbinfo["_id"] : ''); ?>" /></td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>
<?php } ?>   
  <tr class="data">
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="label">Type</td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="data"><select name="dbicfg[_type]"><?php
   
 foreach($this->dbtypes as $key=>$val) { 
  if (isset($this->dbinfo["_type"]) && ($this->dbinfo["_type"] == $key)) {
 ?><option value="<?php echo htmlentities($key); ?>" selected="selected"><?php echo htmlentities($val); ?></option><?php
  } else {
 ?><option value="<?php echo htmlentities($key); ?>"><?php echo htmlentities($val); ?></option><?php 
  }
 } 
 ?></select></td>
 <td class="vbar"><div class="clr_b"></div></td>
  </tr>
  <tr class="data">
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="label">Server</td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="data"><input type="text" size="60" name="dbicfg[server]" value="<?php echo htmlentities(isset($this->dbinfo["server"]) ? $this->dbinfo["server"] : ''); ?>" /></td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>  
  <tr class="data">
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="label">Username</td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="data"><input type="text" size="60" name="dbicfg[username]" value="<?php echo htmlentities(isset($this->dbinfo["username"]) ? $this->dbinfo["username"] : ''); ?>" /></td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>  
  <tr class="data">
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="label">Password</td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="data"><input type="password" size="60" name="dbicfg[password]" value="<?php echo htmlentities(isset($this->dbinfo["password"]) ? $this->dbinfo["password"] : ''); ?>" /></td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>  
  <tr class="data">
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="label">Database</td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="data"><input type="text" size="60" name="dbicfg[database]" value="<?php echo htmlentities(isset($this->dbinfo["database"]) ? $this->dbinfo["database"] : ''); ?>" /></td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr> 
  <?php echo $hbar; ?>
 </tbody>
 <tfoot>
  <tr class="controls">
   <td colspan="5">
    <ul>
     <li class="submit"><div class="btn"><span><input type="submit" value="Go" id="edit_config_go2" /></span></div></li>
     <li><a href="#edit_config_go2"><span><input type="radio" name="task" value="reset" /> Reset</span></a></li>     
     <li><a href="#edit_config_go2"><span><input type="radio" name="task" value="save_db" /> Save</span></a></li>
     <li><a href="#edit_config_go2"><span><input type="radio" name="task" value="" /> Close</span></a></li>
    </ul>    
   </td>
  </tr>
 </tfoot>
</table>
<input type="hidden" name="ck[<?php echo htmlentities($this->dbid); ?>]" value="ON" />
<input type="hidden" name="app" value="vwp" />
<input type="hidden" name="widget" value="dbiconfig" />
<input type="hidden" name="reset_task" value="<?php echo $this->task; ?>" />
<input type="hidden" name="screen" value="<?php echo htmlentities($this->screen); ?>" />
</form>
<?php echo $this->menu_foot ?>