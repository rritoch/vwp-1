<?php

/**
 * VWP Content Manager - Default Category Editor Layout
 * 
 * @package VWP.Content
 * @subpackage Layouts.CatMgr.HTML
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

 function tbMove(formName,task,id,dir) {
  var frm;  
  frm = document.getElementById(formName);
  frm.task.value = task;
  frm.eid.value =id;
  frm.arg1.value = dir;
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
 
//--></script>

<form action="" method="post" id="editcat">
<table class="itable">
<thead>
 <tr class="title">
  <th colspan="4"><?php if ($this->is_new) { ?>New Category<?php } else { ?>Edit Category<?php } ?></th>
 </tr>
 <tr class="toolbar">
  <th colspan="4">
   <table class="itable_toolbar">
    <thead>
     <tr>
      <th><input type="button" onclick="javascript:tbClick('editcat','save_category');" value="Save" /></th>
      <th><input type="button" onclick="javascript:tbClick('editcat','refresh');" value="Refresh" /></th>
      <th><input type="button" onclick="javascript:tbClick('editcat','');" value="Close" /></th>
     </tr>
     </thead>
    </table>    
 </tr>
</thead>
<tbody>
<?php if (!$this->is_new) { ?> 
 <tr>
  <td class="label">ID</td>
  <td><?php echo htmlentities($this->category["id"]); ?><input type="hidden" size="50" name="cat[id]" value="<?php echo htmlentities($this->category["id"]); ?>"/></td> 
 </tr>

<?php } ?>
 <tr>
  <td class="label">Name</td>
  <td><input type="text" size="50" name="cat[name]" value="<?php echo htmlentities($this->category["name"]); ?>"/></td>
 </tr>
 <tr>
  <td class="label">File Alias</td>
  <td><input type="text" size="50" name="cat[filename]" value="<?php echo htmlentities($this->category["filename"]); ?>"/></td>
 </tr>
 <tr>
  <td class="label">Description</td>
  <td><input type="text" size="50" name="cat[description]" value="<?php echo htmlentities($this->category["keywords"]); ?>"/></td>
 </tr>
 <tr>
  <td class="label">Keywords</td>
  <td><input type="text" size="50" name="cat[keywords]" value="<?php echo htmlentities($this->category["keywords"]); ?>"/></td>
 </tr>  
 <tr>
  <td class="label">Parent</td>
  <td><select name="cat[parent]"><?php if (empty($this->category["parent"])) { ?><option value="" selected="selected"><?php } else { ?><option value="" ><?php } ?>(none)</option><?php
   foreach($this->category_list as $cat) { ?>
     <?php if ($this->category["parent"] == $cat["id"]) { ?><option value="<?php echo htmlentities($cat["id"]); ?>" selected="selected"><?php } else { ?><option value="<?php echo htmlentities($cat["id"]); ?>"><?php } ?><?php echo htmlentities($cat["name"]); ?></option>
<?php   }
  ?></select></td>
 </tr> 
</tbody>
</table>
<input type="hidden" name="app" value="content" />
<input type="hidden" name="widget" value="catmgr" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="is_new" value="<?php echo $this->is_new ? "1" : "0"; ?>" />
<input type="hidden" name="screen" value="<?php echo htmlentities($this->screen); ?>" />
</form>