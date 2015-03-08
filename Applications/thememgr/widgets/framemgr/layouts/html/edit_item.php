<?php echo $this->tabs; ?>


<?php

 $hbar = '<tr class="hbar">
   <td class="vbar"></td>
   <td colspan="3" class="hbar"></td>
   <td class="vbar"></td>
  </tr>';

?>

<form action="" method="post" id="edititem">
<table id="edititem_control" class="control_panel">
 <thead>
  <tr class="controls">
   <td colspan="5">
    <ul>
     <li class="submit"><div class="btn"><span><input type="submit" value="Go" id="edititem_go1" /></span></div></li>
     <li><a href="#edititem_go1"><span><input type="radio" name="task" value="edit_frame" /> Close</span></a></li>     
     <li><a href="#edititem_go1"><span><input type="radio" name="task" value="edit_item" /> Refresh</span></a></li>
     <li><a href="#edititem_go1"><span><input type="radio" name="task" value="save_item" /> Save</span></a></li>
    </ul>    
   </td>
  </tr> 
  <tr class="title">
   <td class="vbar"><div class="clr_b"></div></td>
   <td colspan="3" class="title"><h2>Edit Frame Item</h2></td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>
  <?php echo $hbar; ?>     
 </thead>
<tbody>
<tr class="data">
 <td class="vbar"><div class="clr_b"></div></td>
 <td class="label">Alias</td>
 <td class="vbar"><div class="clr_b"></div></td>
 <td class="data"><input type="text" name="item[ref]" size="50" value="<?php echo htmlentities($this->item->ref); ?>" /></td>
 <td class="vbar"><div class="clr_b"></div></td>
</tr>
<tr class="data">
 <td class="vbar"><div class="clr_b"></div></td>
 <td class="label">Widget</td>
 <td class="vbar"><div class="clr_b"></div></td>
 <td class="data"><select name="item[widget]"><?php
  foreach($this->widget_list as $widget_id=>$widget_name) {
   if ($this->item->widget == $widget_id) { ?>
<option value="<?php echo htmlentities($widget_id); ?>" selected="selected"><?php echo htmlentities($widget_name); ?></option>   
<?php
   } else { ?>
<option value="<?php echo htmlentities($widget_id); ?>"><?php echo htmlentities($widget_name); ?></option>   
<?php
   }
  }
 ?></select></td>
 <td class="vbar"><div class="clr_b"></div></td>
</tr>
 <tr class="data">
 <td class="vbar"><div class="clr_b"></div></td>
  <td class="label">Disabled</td>
  <td class="vbar"><div class="clr_b"></div></td>
  <td class="data"><?php if ($this->item->disabled != "1") {
   ?>No <input type="radio" name="item[disabled]" checked="checked" value="0" /> Yes <input type="radio" name="item[disabled]" value="1" /><?php  
  } else {
   ?>No <input type="radio" name="item[disabled]" value="0" /> Yes <input type="radio" checked="checked" name="item[disabled]" value="1" /><?php
  } ?></td>
  <td class="vbar"><div class="clr_b"></div></td>
 </tr>
 <tr class="data">
 <td class="vbar"><div class="clr_b"></div></td>
  <td class="label">Visibile</td>
  <td class="vbar"><div class="clr_b"></div></td>
  <td class="data"><?php if ($this->item->visible != "1") {
    ?>No <input type="radio" name="item[visible]" checked="checked" value="0" /> Yes <input type="radio" name="item[visible]" value="1" /><?php  
   } else {
    ?>No <input type="radio" name="item[visible]" value="0" /> Yes <input type="radio" checked="checked" name="item[visible]" value="1" /><?php
   } ?></td>
   <td class="vbar"><div class="clr_b"></div></td>
 </tr>
 <tr class="data">
 <td class="vbar"><div class="clr_b"></div></td>
  <td class="label">Default Security Policy</td>
  <td class="vbar"><div class="clr_b"></div></td>
  <td class="data"><select name="item[default_security_policy]"><?php 
   if ($this->item->default_security_policy == 'allow') { 
    ?><option value="allow" selected="selected">Allow</option><?php
    ?><option value="deny">Deny</option><?php
   } else {
    ?><option value="allow">Allow</option><?php
    ?><option value="deny" selected="selected">Deny</option><?php   
   } ?></select></td>
  <td class="vbar"><div class="clr_b"></div></td>
 </tr>  
 
<?php if (count(array_keys($this->params)) > 0) { ?>
<?php echo $hbar; ?>
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
<?php } ?>

</tbody>
 <tfoot>
  <tr class="controls">
   <td colspan="5">
    <ul>
     <li class="submit"><div class="btn"><span><input type="submit" value="Go" id="edititem_go2" /></span></div></li>
     <li><a href="#edititem_go2"><span><input type="radio" name="task" value="save_item" /> Save</span></a></li>
     <li><a href="#edititem_go2"><span><input type="radio" name="task" value="edit_item" /> Refresh</span></a></li>
     <li><a href="#edititem_go2"><span><input type="radio" name="task" value="edit_frame" /> Close</span></a></li>     
    </ul>    
   </td>
  </tr> 
  </tfoot>

</table>

<input type="hidden" name="id[<?php echo htmlentities($this->item_id); ?>]" value="on" />
<input type="hidden" name="ck[1]" value="ON" />
<input type="hidden" name="frame_list[1][id]" value="<?php echo $this->frameId; ?>" />
</form>

<?php echo $this->tabs_foot; ?>