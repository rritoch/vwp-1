<?php

/**
 * VWP Content Manager - Default Configuration Layout
 * 
 * @package VWP.Content
 * @subpackage Layouts.Admin.HTML
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @link http://www.vnetpublishing.com VNetPublishing.Com 
 * @copyright (c) Ralph Ritoch 2011 - All rights reserved
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

?>


<script type="text/javascript"><!--

 function tbClick(formName,task) {
  var frm;  
  frm = document.getElementById(formName);
  frm.task.value = task;
  frm.submit();  
 }

 function tbCheckAll(formName,prefix) {
  var frm;
  var i;
  var v;
  var idx;
  var s;
  
  frm = document.getElementById(formName);
  v = frm.ckall.checked;  
  i = frm.getElementsByTagName('input');
  
  for(idx = 0; idx <i.length; idx++) {
   s = i[idx].getAttribute('name');    
   if (s.substr(0,prefix.length) == prefix) {
    i[idx].checked = v;
   }
  }    
  return false;
 }
 
// --></script>

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
     <li><a href="#edit_config_go1"><span><input type="radio" name="task" value="" /> Reset</span></a></li>     
     <li><a href="#edit_config_go1"><span><input type="radio" name="task" value="save_config" /> Save</span></a></li>
    </ul>    
   </td>
  </tr>  
  <tr class="title">
   <td class="vbar"><div class="clr_b"></div></td>
   <td colspan="3" class="title"><h2>Configure Content Manager</h2></td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>

  <?php echo $hbar; ?>       
 </thead>
 <tbody> 
  <tr class="data">
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="label">Table Prefix:</td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="data"><input type="text" size="10" name="table_prefix" value="<?php echo htmlentities($this->config["table_prefix"]); ?>" /></td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>
  <tr class="data">
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="label">Default Editor:</td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="data"><select name="default_editor"><?php foreach($this->editors as $editor_id=>$editor_name) { 
    if ($this->config["default_editor"] == $editor_id) {?>
    <option value="<?php echo htmlentities($editor_id); ?>" selected="selected"><?php echo htmlentities($editor_name); ?></option>
<?php } else { ?>   
    <option value="<?php echo htmlentities($editor_id); ?>"><?php echo htmlentities($editor_name); ?></option>    
<?php }}    ?></select></td>
  <td class="vbar"><div class="clr_b"></div></td>
  </tr>
  
  <tr class="data">
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="label">Editor Mode:</td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="data"><?php 
      $selected = array( '','');
      $selected[0] = isset($this->config['edit_mode_theme_type']) && ($this->config['edit_mode_theme_type'] == 'admin') ? '' : ' checked="checked"';
      $selected[1] = isset($this->config['edit_mode_theme_type']) && ($this->config['edit_mode_theme_type'] == 'admin') ? ' checked="checked"' : '';
      
    ?>
    <input type="radio" name="edit_mode_theme_type" value="site"<?php echo $selected[0]; ?> /> Site
    <input type="radio" name="edit_mode_theme_type" value="admin"<?php echo $selected[1]; ?> /> Admin      
    </td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>   
  
  <tr class="data">
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="label">Index Page Title:</td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="data"><input type="text" size="40" name="home_title" value="<?php echo htmlentities($this->config["home_title"]); ?>" /></td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>  
  <tr class="data">
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="label">&nbsp;</td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="data"><input type="radio" name="home_page_type" value="article" <?php if ($this->config["home_page_type"] != "category") { echo 'checked="checked" '; } ?>/> Index Page as Article</td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>
  <tr class="data">
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="label">Index Article:</td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td><select name="home_article"><?php foreach ($this->articles as $article) { 
    if ($this->config["home_article"] == $article["id"]) {
   ?>
     <option value="<?php echo htmlentities($article["id"]); ?>" selected="selected">(<?php echo htmlentities($article["id"]); ?>) <?php echo htmlentities($article["title"]); ?></option>
<?php } else { ?>   
   <option value="<?php echo htmlentities($article["id"]); ?>">(<?php echo htmlentities($article["id"]); ?>) <?php echo htmlentities($article["title"]); ?></option>
<?php  }} ?></select></td>
  <td class="vbar"><div class="clr_b"></div></td>
  </tr>          
  <tr class="data">
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="label">&nbsp;</td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="data"><input type="radio" name="home_page_type" value="category" <?php if ($this->config["home_page_type"] == "category") { echo 'checked="checked" '; } ?>/> Index Page as Category</td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>
  <tr class="data">
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="label">Index Category:</td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="data"><select name="home_category"><?php foreach ($this->categories as $cat) { 
    if ($this->config["home_category"] == $cat["id"]) { ?>
     <option value="<?php echo htmlentities($cat["id"]); ?>" selected="selected">(<?php echo htmlentities($cat["id"]); ?>) <?php echo htmlentities($cat["name"]); ?></option>
<?php } else { ?>   
   <option value="<?php echo htmlentities($cat["id"]); ?>">(<?php echo htmlentities($cat["id"]); ?>) <?php echo htmlentities($cat["name"]); ?></option>
<?php   }} ?></select></td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>
  <tr class="data">
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="label">Index Layout:</td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="data"><select name="home_category_layout"><?php if ($this->config["home_category_layout"] != "blog") { ?><option value="default" selected="selected">Default</option><option value="blog">Blog</option><?php } else { ?><option value="default">Default</option><option value="blog" selected="selected">Blog</option><?php } ?></select></td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>
  <tr class="data">
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="label">Images Path:</td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="data"><input type="text" size="40" name="images_path" value="<?php echo htmlentities($this->config["images_path"]); ?>" /></td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>   
  <tr class="data">
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="label">Images URL:</td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="data"><input type="text" size="40" name="images_url" value="<?php echo htmlentities($this->config["images_url"]); ?>" /></td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>  
 </tbody>
 <tfoot>
  <tr class="controls">
   <td colspan="5">
    <ul>
     <li class="submit"><div class="btn"><span><input type="submit" value="Go" id="edit_config_go2" /></span></div></li>
     <li><a href="#edit_config_go2"><span><input type="radio" name="task" value="" /> Reset</span></a></li>     
     <li><a href="#edit_config_go2"><span><input type="radio" name="task" value="save_config" /> Save</span></a></li>
    </ul>    
   </td>
  </tr>
  </tfoot>   
</table>
<input type="hidden" name="app" value="content" />
<input type="hidden" name="widget" value="admin" />
<input type="hidden" name="screen" value="<?php echo htmlentities($this->screen); ?>" />
</form>