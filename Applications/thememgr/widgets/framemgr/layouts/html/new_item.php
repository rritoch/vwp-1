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
     <li><a href="#edititem_go1"><span><input type="radio" name="task" value="refresh_frame" /> Close</span></a></li>     
     <li><a href="#edititem_go1"><span><input type="radio" name="task" value="refresh_new_item" /> Refresh</span></a></li>
     <li><a href="#edititem_go1"><span><input type="radio" name="task" value="create_item" /> Save</span></a></li>
    </ul>    
   </td>
  </tr> 
  <tr class="title">
   <td class="vbar"><div class="clr_b"></div></td>
   <td colspan="3" class="title"><h2>New Item</h2></td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>
  <?php echo $hbar; ?>     
 </thead>
<tbody>
<tr class="data">
 <td class="vbar"><div class="clr_b"></div></td>
 <td class="label">Alias</td>
 <td class="vbar"><div class="clr_b"></div></td>
 <td class="data"><input type="text" name="item[ref]" size="50" value="<?php echo htmlentities($this->item["ref"]); ?>" /></td>
 <td class="vbar"><div class="clr_b"></div></td>
</tr>

<tr class="data">
 <td class="vbar"><div class="clr_b"></div></td>
 <td class="label">Widget</td>
 <td class="vbar"><div class="clr_b"></div></td>
 <td class="data"><select name="item[widget]"><?php
  foreach($this->widget_list as $app_id=>$app_name) {
   if ($this->item["widget"] == $app_id) { ?>
<option value="<?php echo htmlentities($app_id); ?>" selected="selected"><?php echo htmlentities($app_name); ?></option>   
<?php
   } else { ?>
<option value="<?php echo htmlentities($app_id); ?>"><?php echo htmlentities($app_name); ?></option>   
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
  <td class="data"><?php if ($this->item["disabled"] != "1") {
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
  <td class="data"><?php if ($this->item["visible"] != "1") {
    ?>No <input type="radio" name="menu[visible]" checked="checked" value="0" /> Yes <input type="radio" name="item[visible]" value="1" /><?php  
   } else {
    ?>No <input type="radio" name="menu[visible]" value="0" /> Yes <input type="radio" checked="checked" name="item[visible]" value="1" /><?php
   } ?></td>
   <td class="vbar"><div class="clr_b"></div></td>
 </tr>

</tbody>
 <tfoot>
  <tr class="controls">
   <td colspan="5">
    <ul>
     <li class="submit"><div class="btn"><span><input type="submit" value="Go" id="edititem_go2" /></span></div></li>
     <li><a href="#edititem_go2"><span><input type="radio" name="task" value="create_item" /> Save</span></a></li>
     <li><a href="#edititem_go2"><span><input type="radio" name="task" value="refresh_new_item" /> Refresh</span></a></li>
     <li><a href="#edititem_go2"><span><input type="radio" name="task" value="refresh_frame" /> Close</span></a></li>     
    </ul>    
   </td>
  </tr> 
  </tfoot>

</table>
<input type="hidden" name="ck[1]" value="ON" />
<input type="hidden" name="frame_list[1][id]" value="<?php echo $this->frameId; ?>" />
</form>

<?php echo $this->tabs_foot; ?>