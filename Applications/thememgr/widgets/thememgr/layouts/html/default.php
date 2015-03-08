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
     <li><a href="#themes_go1"><span><input type="radio" name="task" value="save_defaults" /> Save</span></a></li>
     <li><a href="#themes_go1"><span><input type="radio" name="task" value="manage" /> Manage</span></a></li>
     <li><a href="#themes_go1"><span><input type="radio" name="task" value="uninstall" /> Uninstall</span></a></li>
     <li><a href="#themes_go1"><span><input type="radio" name="task" value="install" /> Install</span></a></li>     
    </ul>
    
   </td>
  </tr>
  <tr class="title">
   <td class="vbar"><div class="clr_b"></div></td>
   <td colspan="15" class="title"><h2>Themes</h2></td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>
  <?php echo $hbar; ?>
  <tr class="col_titles">
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="header" id="eselect"><input type="checkbox" onClick="javascript:columnToggleCheckAll('eselect');" /></td>  
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="header">Class</td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="header">Theme</td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="header">Default</td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="header">Author</td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="header">Version</td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="header">Release Date</td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="header">Author Site</td>
   <td class="vbar"><div class="clr_b"></div></td>               
  </tr>
  <?php echo $hbar; ?> 
 </thead>
 <tbody>
 
 <?php if (count($this->theme_list) > 0) {
  
  $ctr = 0; foreach($this->theme_list as $theme_info) { $ctr++; ?>
  <tr class="data">
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="ctr data"><input type="checkbox" name="ck[<?php echo $ctr; ?>]" /></td>  
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="label"><?php echo $this->escape($theme_info["themeType"]); ?><input type="hidden" name="theme_info[<?php echo $ctr; ?>][type]" value="<?php echo $this->escape($theme_info["themeType"]); ?>" /></td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="data"><?php echo isset($theme_info["name"]) ? $this->escape($theme_info["name"]) : $this->escape($theme_info["themeId"]); ?><input type="hidden" name="theme_info[<?php echo $ctr; ?>][id]" value="<?php echo $this->escape($theme_info["themeId"]); ?>" /></td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="data ctr"><input type="radio" name="default_theme[<?php echo $this->escape($theme_info["themeType"]); ?>]" value="<?php echo $this->escape($theme_info["themeId"]); ?>"<?php if ($theme_info["is_default"]) { echo ' checked="checked"'; } ?> /></td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="data"><?php if (isset($theme_info['author'])) { echo $this->escape($theme_info['author']); } ?></td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="data"><?php if (isset($theme_info['version'])) { echo $this->escape($theme_info['version']); } ?></td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="data"><?php if (isset($theme_info['version_release_date'])) { echo $this->escape($theme_info['version_release_date']); } ?></td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="data"><?php if (isset($theme_info['author_link'])) { echo $this->escape($theme_info['author_link']); } ?></td>
   <td class="vbar"><div class="clr_b"></div></td>               
  </tr>  
  
<?php  
  echo $hbar;
 } 

 } else { ?>
<tr>
 <td class="vbar"><div class="clr_b"></div></td>
 <td  class="data" colspan="15">No themes found</td>
 <td class="vbar"><div class="clr_b"></div></td>
</tr>     
<?php } ?>
 </tbody>
 <tfoot>
  <tr class="controls">
   <td colspan="17">
    <ul>
     <li class="submit"><div class="btn"><span><input type="submit" id="themes_go2" value="Go" /></span></div></li>
     <li><a href="#themes_go2"><span><input type="radio" name="task" value="install" /> Install</span></a></li>
     <li><a href="#themes_go2"><span><input type="radio" name="task" value="uninstall" /> Uninstall</span></a></li>
     <li><a href="#themes_go2"><span><input type="radio" name="task" value="manage" /> Manage</span></a></li>
     <li><a href="#themes_go2"><span><input type="radio" name="task" value="save_defaults" /> Save</span></a></li>
    </ul>
    
   </td>   
  </tr>
 </tfoot>
</table>
</form> 
<?php echo $this->tabs_foot; ?>