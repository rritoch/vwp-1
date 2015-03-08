<?php echo $this->tabs; ?>

<?php

 $hbar = '<tr class="hbar">
   <td class="vbar"></td>
   <td colspan="15" class="hbar"></td>
   <td class="vbar"></td>
  </tr>';

?>

<form action="" method="post" id="themes_control">
<table id="themes" class="control_panel">
 <thead>  
  <tr class="controls">
   <td colspan="17">
    <ul>
     <li class="submit"><div class="btn"><span><input type="submit" value="Go" id="themes_go1" /></span></div></li>
     <li><a href="#themes_go1"><span><input type="radio" name="task" value="close_theme" /> Close</span></a></li>
     <li><a href="#themes_go1"><span><input type="radio" name="task" value="refresh_theme" /> Refresh</span></a></li>
    </ul>    
   </td>
  </tr>
  <tr class="title">
   <td class="vbar"><div class="clr_b"></div></td>
   <td colspan="15" class="title">
     <p>Theme ID: <?php echo $this->escape($this->themeId); ?></p>
     <p>Theme Type: <?php echo $this->escape($this->themeType); ?></p>
   </td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>
  <?php echo $hbar; ?>

 <tbody>

 </tbody>
 <tfoot>
  <tr class="controls">
   <td colspan="17">
    <ul>
     <li class="submit"><div class="btn"><span><input type="submit" id="themes_go2" value="Go" /></span></div></li>
     <li><a href="#themes_go2"><span><input type="radio" name="task" value="refresh_theme" /> Refresh</span></a></li>
     <li><a href="#themes_go2"><span><input type="radio" name="task" value="close_theme" /> Close</span></a></li>
    </ul>
    
   </td>   
  </tr>
 </tfoot>
</table>
<input type="hidden" name="ck[1]" value="ON" />
<input type="hidden" name="theme_info[1][id]" value="<?php echo $this->escape($this->themeId); ?>" />
<input type="hidden" name="theme_info[1][type]" value="<?php echo $this->escape($this->themeType); ?>" />
</form> 

<br />

<?php

 $hbar = '<tr class="hbar">
   <td class="vbar"></td>
   <td colspan="3" class="hbar"></td>
   <td class="vbar"></td>
  </tr>';

?>

<form action="" method="post" id="theme_settings_control">
<table id="theme_settings" class="control_panel">
 <thead>  
  <tr class="controls">
   <td colspan="5">
    <ul>
     <li class="submit"><div class="btn"><span><input type="submit" value="Go" id="themes_go1" /></span></div></li>
     <li><a href="#themes_go1"><span><input type="radio" name="task" value="update_theme_settings" /> Save</span></a></li>
     <li><a href="#themes_go1"><span><input type="radio" name="task" value="refresh_theme" /> Refresh</span></a></li>
    </ul>
    
   </td>
  </tr>
  <tr class="title">
   <td class="vbar"><div class="clr_b"></div></td>
   <td colspan="3" class="title">
     <h2>Theme Settings</h2>
   </td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>
  <?php echo $hbar; ?>

 <tbody>

<?php if (count(array_keys($this->params)) > 0) { ?>
<tr class="data">
<td class="vbar"><div class="clr_b"></div></td>
 <td class="label" colspan="3" align="center">Parameters</td>
 <td class="vbar"><div class="clr_b"></div></td>
</tr>
<?php echo $hbar; ?>
<?php foreach($this->params as $param_id=>$p) {
 $t = '';
 if (isset($p["type"])) {
  $t = $p["type"]; 
 }
 
 
 switch($t) {
 
  case "text": ?>
<tr class="data">
<td class="vbar"><div class="clr_b"></div></td>
 <td class="label"><?php echo htmlentities($p["label"]); ?></td>
 <td class="vbar"><div class="clr_b"></div></td>
 <td class="data"><input type="text" name="params[<?php echo htmlentities($param_id); ?>]" value="<?php echo htmlentities($p["data"]); ?>" /></td>
 <td class="vbar"><div class="clr_b"></div></td>
</tr>  
<?php   break;
  case "select": ?>
<tr class="data">
<td class="vbar"><div class="clr_b"></div></td>
 <td class="label"><?php echo htmlentities($p["label"]); ?></td>
 <td class="vbar"><div class="clr_b"></div></td>
 <td class="data"><select name="params[<?php echo htmlentities($param_id); ?>]"><?php  
  
  foreach($p["values"] as $key=>$val) {
   
   if ($p["data"] == $key) { ?>
   <option value="<?php echo htmlentities($key); ?>" selected="selected"><?php echo htmlentities($val); ?></option>
 <?php } else { ?>
 <option value="<?php echo htmlentities($key); ?>"><?php echo htmlentities($val); ?></option>
 <?php }} ?></select></td>
 <td class="vbar"><div class="clr_b"></div></td>
</tr>   
<?php   break;
  default:
   break;
 }
 } ?>
<?php } else { ?>

 <tr class="data">
  <td class="vbar"><div class="clr_b"></div></td>
  <td class="data" colspan="3">There are no custom settings for this theme.</td>
  <td class="vbar"><div class="clr_b"></div></td>
 </tr>
 <?php echo $hbar; ?>
<?php } ?>
 </tbody>
 <tfoot>
  <tr class="controls">
   <td colspan="5">
    <ul>
     <li class="submit"><div class="btn"><span><input type="submit" id="theme_settings_go2" value="Go" /></span></div></li>
     <li><a href="#theme_settings_go2"><span><input type="radio" name="task" value="update_theme_settings" /> Save</span></a></li>
     <li><a href="#theme_settings_go2"><span><input type="radio" name="task" value="refresh_theme" /> Refresh</span></a></li>
    </ul>
    
   </td>   
  </tr>
 </tfoot>
</table>
<input type="hidden" name="ck[1]" value="ON" />
<input type="hidden" name="theme_info[1][id]" value="<?php echo $this->escape($this->themeId); ?>" />
<input type="hidden" name="theme_info[1][type]" value="<?php echo $this->escape($this->themeType); ?>" />
</form> 

<br />

<!-- Theme Targets -->
<?php

 $hbar = '<tr class="hbar">
   <td class="vbar"></td>
   <td colspan="3" class="hbar"></td>
   <td class="vbar"></td>
  </tr>';

?>

<form action="" method="post" id="theme_targets_control">
<table id="theme_targets" class="control_panel">
 <thead>  
  <tr class="controls">
   <td colspan="5">
    <ul>
     <li class="submit"><div class="btn"><span><input type="submit" value="Go" id="theme_targets_go1" /></span></div></li>
     <li><a href="#theme_targets_go1"><span><input type="radio" name="task" value="save_theme_targets" /> Save</span></a></li>  
    </ul>
    
   </td>
  </tr>
  <tr class="title">
   <td class="vbar"><div class="clr_b"></div></td>
   <td colspan="3" class="title">
     <h2>Theme Targets</h2>
   </td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>
  <?php echo $hbar; ?>

  <tr class="col_titles">
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="header">Target ID</td>  
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="header">Assigned Frame</td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>
  <?php echo $hbar; ?>  

 <tbody>
 <?php foreach($this->target_list as $info) { ?>
 <tr class="data">
  <td class="vbar"><div class="clr_b"></div></td>
  <td class="label"><?php echo $this->escape($info["id"]); ?></td>
  <td class="vbar"><div class="clr_b"></div></td>
  <td class="data"><select name="targets[<?php echo $this->escape($info["id"]); ?>]"><option value="">(none)</option><?php
 foreach($this->frame_list as $frame) {
  if ((isset($info["frame"]))&&($info["frame"] == $frame["id"])) {
   ?><option value="<?php echo $this->escape($frame["id"]); ?>" selected="selected"><?php echo $this->escape($frame["id"]); ?></option><?php
  } else {
  ?><option value="<?php echo $this->escape($frame["id"]); ?>"><?php echo $this->escape($frame["id"]); ?></option><?php
  }
 }
?></select></td>
  <td class="vbar"><div class="clr_b"></div></td>
 </tr>
 <?php echo $hbar; ?>
 <?php } ?>
 </tbody>
 <tfoot>
  <tr class="controls">
   <td colspan="5">
    <ul>
     <li class="submit"><div class="btn"><span><input type="submit" id="theme_targets_go2" value="Go" /></span></div></li>
     <li><a href="#theme_targets_go2"><span><input type="radio" name="task" value="save_theme_targets" /> Save</span></a></li>  
    </ul>
    
   </td>   
  </tr>
 </tfoot>
</table>
<input type="hidden" name="ck[1]" value="ON" />
<input type="hidden" name="theme_info[1][id]" value="<?php echo $this->escape($this->themeId); ?>" />
<input type="hidden" name="theme_info[1][type]" value="<?php echo $this->escape($this->themeType); ?>" />
</form> 

<br />
<?php echo $this->tabs_foot; ?>