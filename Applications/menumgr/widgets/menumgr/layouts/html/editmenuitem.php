<?php
 
 
?>
<?php

 $hbar = '<tr class="hbar">
   <td class="vbar"></td>
   <td colspan="3" class="hbar"></td>
   <td class="vbar"></td>
  </tr>';

?>
<form action="" method="post" id="edmenu_control">

<table id="edmenu" class="control_panel">
 <thead>
  <tr class="controls">
   <td colspan="5">
    <ul>
     <li class="submit"><div class="btn"><span><input type="submit" value="Go" id="edmenu_go1" /></span></div></li>
     <li><a href="#edmenu_go1"><span><input type="radio" name="task" value="save_menu" /> Save</span></a></li>
     <li><a href="#edmenu_go1"><span><input type="radio" name="task" value="refresh" /> Refresh</span></a></li>
     <li><a href="#edmenu_go1"><span><input type="radio" name="task" value="" /> Close</span></a></li>     
    </ul>    
   </td>
  </tr> 
  <tr class="title">
   <td class="vbar"><div class="clr_b"></div></td>
   <td colspan="3" class="title"><h2><?php if ($this->is_new) { ?>New Menu<?php } else { ?>Edit Menu<?php } ?></h2></td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>
  <?php echo $hbar; ?>     
 </thead>
<tbody>
<?php if ($this->is_new) { ?>
 <tr class="data">
 <td class="vbar"><div class="clr_b"></div></td>
  <td class="label">ID</td>
  <td class="vbar"><div class="clr_b"></div></td>
  <td class="data"><input type="text" size="50" name="menu[id]" value="<?php echo htmlentities($this->menu["id"]); ?>"/></td>
  <td class="vbar"><div class="clr_b"></div></td> 
 </tr>
<?php } else { ?> 
 <tr class="data">
  <td class="vbar"><div class="clr_b"></div></td>
  <td class="label">ID</td>
  <td class="vbar"><div class="clr_b"></div></td>
  <td class="data"><?php echo htmlentities($this->menu["id"]); ?><input type="hidden" size="50" name="menu[id]" value="<?php echo htmlentities($this->menu["id"]); ?>"/></td>
  <td class="vbar"><div class="clr_b"></div></td> 
 </tr>
<?php } ?>
 <tr class="data">
 <td class="vbar"><div class="clr_b"></div></td>
  <td class="label">Title</td>
  <td class="vbar"><div class="clr_b"></div></td>
  <td class="data"><input type="text" size="50" name="menu[title]" value="<?php echo htmlentities($this->menu["title"]); ?>"/></td>
  <td class="vbar"><div class="clr_b"></div></td>
 </tr>
 <tr class="data">
 <td class="vbar"><div class="clr_b"></div></td>
  <td class="label">Disabled</td>
  <td class="vbar"><div class="clr_b"></div></td>
  <td class="data"><?php if ($this->menu["disabled"] != "1") {
   ?>No <input type="radio" name="menu[disabled]" checked="checked" value="0" /> Yes <input type="radio" name="menu[disabled]" value="1" /><?php  
  } else {
   ?>No <input type="radio" name="menu[disabled]" value="0" /> Yes <input type="radio" checked="checked" name="menu[disabled]" value="1" /><?php
  } ?></td>
  <td class="vbar"><div class="clr_b"></div></td>
 </tr>
 <tr class="data">
 <td class="vbar"><div class="clr_b"></div></td>
  <td class="label">Visible</td>
  <td class="vbar"><div class="clr_b"></div></td>
  <td class="data"><?php if ($this->menu["visible"] != "1") {
    ?>No <input type="radio" name="menu[visible]" checked="checked" value="0" /> Yes <input type="radio" name="menu[visible]" value="1" /><?php  
   } else {
    ?>No <input type="radio" name="menu[visible]" value="0" /> Yes <input type="radio" checked="checked" name="menu[visible]" value="1" /><?php
   } ?></td>
   <td class="vbar"><div class="clr_b"></div></td>
 </tr>
 <tr class="data">
 <td class="vbar"><div class="clr_b"></div></td>
  <td class="label">Default Security Policy</td>
  <td class="vbar"><div class="clr_b"></div></td>
  <td class="data"><select name="menu[default_security_policy]"><?php 
   if ($this->menu["default_security_policy"] == 'allow') { 
    ?><option value="allow" selected="selected">Allow</option><?php
    ?><option value="deny">Deny</option><?php
   } else {
    ?><option value="allow">Allow</option><?php
    ?><option value="deny" selected="selected">Deny</option><?php   
   } ?></select></td>
  <td class="vbar"><div class="clr_b"></div></td>
 </tr> 
 <?php echo $hbar; ?>
</tbody>
 <tfoot>
  <tr class="controls">
   <td colspan="5">
    <ul>
     <li class="submit"><div class="btn"><span><input type="submit" value="Go" id="edmenu_go2" /></span></div></li>
     <li><a href="#edmenu_go2"><span><input type="radio" name="task" value="save_menu" /> Save</span></a></li>
     <li><a href="#edmenu_go2"><span><input type="radio" name="task" value="refresh" /> Refresh</span></a></li>
     <li><a href="#edmenu_go2"><span><input type="radio" name="task" value="" /> Close</span></a></li>     
    </ul>    
   </td>
  </tr>
  </tfoot> 
</table>
<input type="hidden" name="app" value="menumgr" />
<input type="hidden" name="widget" value="menumgr" />
<input type="hidden" name="is_new" value="<?php echo $this->is_new ? "1" : "0"; ?>" />
<input type="hidden" name="screen" value="<?php echo htmlentities($this->screen); ?>" />
</form>

<?php if (!$this->is_new) { ?> 

<br />

<?php

 $hbar = '<tr class="hbar">
   <td class="vbar"></td>
   <td colspan="13" class="hbar"></td>
   <td class="vbar"></td>
  </tr>';

?>
<form action="" method="post" id="menu_items">
<table id="menu_items_control" class="control_panel">
<thead>
  <tr class="controls">
   <td colspan="15">
    <ul>
     <li class="submit"><div class="btn"><span><input type="submit" value="Go" id="editems_go1" /></span></div></li>
     <li><a href="#editems_go1"><span><input type="radio" name="task" value="save_item_order" /> Save Order</span></a></li>
     <li><a href="#editems_go1"><span><input type="radio" name="task" value="delete_items" /> Delete</span></a></li>          
     <li><a href="#editems_go1"><span><input type="radio" name="task" value="edit_item" /> Edit</span></a></li>
     <li><a href="#editems_go1"><span><input type="radio" name="task" value="new_item" /> New</span></a></li>          
    </ul>    
   </td>
  </tr> 
  <tr class="title">
   <td class="vbar"><div class="clr_b"></div></td>
   <td colspan="13" class="title"><h2>Menu Items</h2></td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>
  <?php echo $hbar; ?>   
  <tr class="col_titles">
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="header" id="eselect"><input type="checkbox" name="ckall" onClick="javascript:columnToggleCheckAll('eselect');" /></td>  
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="header">ID</td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="header">Title</td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="header">Type</td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="header">Order (#)</td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="header">Visible</td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="header">Parent</td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>
  <?php echo $hbar; ?>  
</thead>
<tbody>
<?php if (count($this->menu_item_list) > 0) { 
 foreach($this->menu_item_list as $cat) {

   $fldName = 'order[' .htmlentities($cat["id"]) . ']';
   
   $order = '<input class="order" type="text" onload="javascript:alert(\'here\')" size="3" name="'.$fldName.'" value="' . htmlentities($cat["_order"]).'" />'
           . '<script type="text/javascript"><!-- 
              addMoveButtons(\'menu_items\',\'item_move_event\',\'' . $cat["id"] . '\',\'' . $fldName . '\',\'move_id\',\'move_dir\');
              // --></script>';   
 
   
   
?>
 <tr class="data">
  <td class="vbar"><div class="clr_b"></div></td>
  <td class="data ctr"><input type="checkbox" name="id[<?php echo htmlentities($cat["id"]); ?>]" /></td>
  <td class="vbar"><div class="clr_b"></div></td>
  <td class="data"><?php echo htmlentities($cat["id"]); ?></td>
  <td class="vbar"><div class="clr_b"></div></td>
  <td class="data"><?php echo htmlentities($cat["title"]); ?></td>
  <td class="vbar"><div class="clr_b"></div></td>
  <td class="data"><?php echo htmlentities($cat["type"]); ?></td>
  <td class="vbar"><div class="clr_b"></div></td>
  <td class="data"><?php echo $order; ?></td>
  <td class="vbar"><div class="clr_b"></div></td>
  <td class="data"><?php echo empty($cat["visible"]) ? "No" : "Yes"; ?></td>
  <td class="vbar"><div class="clr_b"></div></td>
  <td class="data"><?php echo htmlentities($cat["parent_name"]); ?></td>
  <td class="vbar"><div class="clr_b"></div></td>
 </tr>
 <?php echo $hbar; ?> 
<?php 
 }

} else { ?>
<tr class="data">
 <td class="vbar"><div class="clr_b"></div></td>
 <td class="data" colspan="13">No menu items</td>
 <td class="vbar"><div class="clr_b"></div></td>
</tr>
<?php echo $hbar; ?> 
<?php } ?>
</tbody>
<tfoot>
  <tr class="controls">
   <td colspan="15">
    <ul>
     <li class="submit"><div class="btn"><span><input type="submit" value="Go" id="editems_go2" /></span></div></li>
     <li><a href="#editems_go2"><span><input type="radio" name="task" value="delete_items" /> Delete</span></a></li>          
     <li><a href="#editems_go2"><span><input type="radio" name="task" value="edit_item" /> Edit</span></a></li>
     <li><a href="#editems_go2"><span><input type="radio" name="task" value="new_item" /> New</span></a></li>
     <li><a href="#editems_go2"><span><input type="radio" name="task" value="save_item_order" /> Save Order</span></a></li>          
    </ul>    
   </td>
  </tr>
</tfoot>
</table>
<input type="hidden" name="app" value="menumgr" />
<input type="hidden" name="widget" value="menumgr" />
<input type="hidden" name="move_id" value="" />
<input type="hidden" name="move_dir" value="" />
<input type="hidden" name="menu" value="<?php echo htmlentities($this->menu["id"]); ?>"/>
<input type="hidden" name="screen" value="<?php echo htmlentities($this->screen); ?>" />
</form>
<?php } ?>
