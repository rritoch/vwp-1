<?php

/**
 * VWP - Default Configuration Layout
 * 
 * @package    VWP
 * @subpackage Layouts.Configure
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @link http://www.vnetpublishing.com VNetPublishing.Com 
 * @copyright (c) Ralph Ritoch - All rights reserved
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

?>
<?php echo $this->menu; ?>
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
     <li><a href="#edit_config_go1"><span><input type="radio" name="task" value="save_config" /> Save</span></a></li>
    </ul>    
   </td>
  </tr>  
  <tr class="title">
   <td class="vbar"><div class="clr_b"></div></td>
   <td colspan="3" class="title"><h2>VWP Configuration</h2></td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>

  <?php echo $hbar; ?>       
 </thead>
 <tbody> 
  
  <tr class="data">
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="label">Site Name</td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="data"><input type="text" size="60" name="cfg[site_name]" value="<?php echo htmlentities(isset($this->cfg["site_name"]) ? $this->cfg["site_name"] : ''); ?>" /></td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr> 
  <tr class="data">
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="label">Default Database</td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="data"><input type="text" size="60" name="cfg[default_database]" value="<?php echo htmlentities(isset($this->cfg["default_database"]) ? $this->cfg["default_database"] : ''); ?>" /></td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>
  <tr class="data">
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="label">Path Separator</td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="data"><input type="text" size="60" name="cfg[path_separator]" value="<?php echo htmlentities(isset($this->cfg["path_separator"]) ? $this->cfg["path_separator"] : ''); ?>" /></td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>     
  <tr class="data">
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="label">Default application</td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="data"><input type="text" size="60" name="cfg[default_application]" value="<?php echo htmlentities(isset($this->cfg["default_application"]) ? $this->cfg["default_application"] : ''); ?>" /></td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>
  <tr class="data">
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="label">Default Document Type</td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="data"><input type="text" size="60" name="cfg[default_document_type]" value="<?php echo htmlentities(isset($this->cfg["default_document_type"]) ? $this->cfg["default_document_type"] : ''); ?>" /></td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>     
  <tr class="data">
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="label">Default language</td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="data"><input type="text" size="60" name="cfg[default_language]" value="<?php echo htmlentities(isset($this->cfg["default_language"]) ? $this->cfg["default_language"] : ''); ?>" /></td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>
  <tr class="data">
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="label">Temporary Directory</td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="data"><input type="text" size="60" name="cfg[temp_dir]" value="<?php echo htmlentities(isset($this->cfg["temp_dir"]) ? $this->cfg["temp_dir"] : ''); ?>" /></td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>  
  <tr class="data">
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="label">Shared Path</td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="data"><input type="text" size="60" name="cfg[shared_path]" value="<?php echo htmlentities(isset($this->cfg["shared_path"]) ? $this->cfg["shared_path"] : ''); ?>" /></td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>  
  <tr class="data">
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="label">Timezone</td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="data"><input type="text" size="60" name="cfg[timezone]" value="<?php echo htmlentities(isset($this->cfg["timezone"]) ? $this->cfg["timezone"] : ''); ?>" /></td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>
  <tr class="data">
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="label">SEF URLS</td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="data"><select name="cfg[sef_mode]">
        <option value="none"<?php if (($this->cfg["sef_mode"] != "rw_sef") && ($this->cfg["sef_mode"] != "sef"))echo ' selected="selected"'; ?>>Non-Search Engine Friendly</option>
        <option value="sef"<?php if ($this->cfg["sef_mode"] == "sef") echo ' selected="selected"'; ?>>Search Engine Friendly</option>
        <option value="rw_sef"<?php if ($this->cfg["sef_mode"] == "rw_sef") echo ' selected="selected"'; ?>>Search Engine Friendly w/Rewrite</option>
    </select></td>
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
     <li><a href="#edit_config_go2"><span><input type="radio" name="task" value="save_config" /> Save</span></a></li>
    </ul>    
   </td>
  </tr>
  </tfoot>  
</table>
<input type="hidden" name="app" value="vwp" />
<input type="hidden" name="widget" value="configure" />
<input type="hidden" name="screen" value="<?php echo htmlentities($this->screen); ?>" />
</form>

<blockquote>
 <p>See Instructions located in the htaccess.txt file located in the root folder of your installation before attempting to enable SEF Rewrite mode.</p> 
</blockquote>
<br />
<?php echo $this->menu_foot ?>