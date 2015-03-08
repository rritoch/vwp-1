<?php echo $this->tabs; ?>
<?php

 $hbar = '<tr class="hbar">
   <td class="vbar"></td>
   <td colspan="3" class="hbar"></td>
   <td class="vbar"></td>
  </tr>';

?>
<form action="" method="post" id="edit_frame_control">

<table id="edit_frame" class="control_panel">
 <thead>
  <tr class="controls">
   <td colspan="5">
    <ul>
     <li class="submit"><div class="btn"><span><input type="submit" value="Go" id="edit_frame_go1" /></span></div></li>
     <li><a href="#edit_frame_go1"><span><input type="radio" name="task" value="save_frame" /> Save</span></a></li>
     <li><a href="#edit_frame_go1"><span><input type="radio" name="task" value="refresh_frame" /> Refresh</span></a></li>
     <li><a href="#edit_frame_go1"><span><input type="radio" name="task" value="" /> Close</span></a></li>     
    </ul>    
   </td>
  </tr> 
  <tr class="title">
   <td class="vbar"><div class="clr_b"></div></td>
   <td colspan="3" class="title"><h2>Edit Frame</h2></td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>
  <?php echo $hbar; ?>     
 </thead>
<tbody>
 <tr class="data">
  <td class="vbar"><div class="clr_b"></div></td>
  <td class="label">ID</td>
  <td class="vbar"><div class="clr_b"></div></td>
  <td class="data"><?php echo htmlentities($this->frameId); ?></td>
  <td class="vbar"><div class="clr_b"></div></td> 
 </tr>

 <tr class="data">
 <td class="vbar"><div class="clr_b"></div></td>
  <td class="label">Disabled</td>
  <td class="vbar"><div class="clr_b"></div></td>
  <td class="data"><?php if ($this->frame_info["disabled"] != "1") {
   ?>No <input type="radio" name="frame_info[disabled]" checked="checked" value="0" /> Yes <input type="radio" name="frame_info[disabled]" value="1" /><?php  
  } else {
   ?>No <input type="radio" name="frame_info[disabled]" value="0" /> Yes <input type="radio" checked="checked" name="frame_info[disabled]" value="1" /><?php
  } ?></td>
  <td class="vbar"><div class="clr_b"></div></td>
 </tr>
 <tr class="data">
 <td class="vbar"><div class="clr_b"></div></td>
  <td class="label">Visible</td>
  <td class="vbar"><div class="clr_b"></div></td>
  <td class="data"><?php if ($this->frame_info["visible"] != "1") {
    ?>No <input type="radio" name="frame_info[visible]" checked="checked" value="0" /> Yes <input type="radio" name="frame_info[visible]" value="1" /><?php  
   } else {
    ?>No <input type="radio" name="frame_info[visible]" value="0" /> Yes <input type="radio" checked="checked" name="frame_info[visible]" value="1" /><?php
   } ?></td>
   <td class="vbar"><div class="clr_b"></div></td>
 </tr>
 <?php echo $hbar; ?>
</tbody>
 <tfoot>
  <tr class="controls">
   <td colspan="5">
    <ul>
     <li class="submit"><div class="btn"><span><input type="submit" value="Go" id="edit_frame_go2" /></span></div></li>
     <li><a href="#edit_frame_go2"><span><input type="radio" name="task" value="save_frame" /> Save</span></a></li>
     <li><a href="#edit_frame_go2"><span><input type="radio" name="task" value="refresh_frame" /> Refresh</span></a></li>
     <li><a href="#edit_frame_go2"><span><input type="radio" name="task" value="" /> Close</span></a></li>     
    </ul>    
   </td>
  </tr>
  </tfoot> 
</table>
<input type="hidden" name="ck[1]" value="ON" />
<input type="hidden" name="frame_list[1][id]" value="<?php echo $this->escape($this->frameId); ?>" />
</form>

<br />

<?php

 $hbar = '<tr class="hbar">
   <td class="vbar"></td>
   <td colspan="7" class="hbar"></td>
   <td class="vbar"></td>
  </tr>';

?>
<form action="" method="post" id="frame_items">
<table id="frame_items_control" class="control_panel">
<thead>
  <tr class="controls">
   <td colspan="9">
    <ul>
     <li class="submit"><div class="btn"><span><input type="submit" value="Go" id="frame_items_go1" /></span></div></li>
     <li><a href="#frame_items_go1"><span><input type="radio" name="task" value="save_item_order" /> Save Order</span></a></li>
     <li><a href="#frame_items_go1"><span><input type="radio" name="task" value="delete_items" /> Delete</span></a></li>          
     <li><a href="#frame_items_go1"><span><input type="radio" name="task" value="edit_item" /> Edit</span></a></li>
     <li><a href="#frame_items_go1"><span><input type="radio" name="task" value="new_item" /> New</span></a></li>          
    </ul>    
   </td>
  </tr> 
  <tr class="title">
   <td class="vbar"><div class="clr_b"></div></td>
   <td colspan="7" class="title"><h2>Frame Items</h2></td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>
  <?php echo $hbar; ?>   
  <tr class="col_titles">
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="header"><input type="checkbox" name="ckall" onClick="javascript:tbCheckAll('menu_items','id[');" /></td>  
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="header">Widget</td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="header">Order (#)</td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="header">Visible</td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>
  <?php echo $hbar; ?>  
</thead>
<tbody>
<?php if (count($this->frame_item_list) > 0) { 
 $ctr = 0; foreach($this->frame_item_list as $item) { $ctr++;

    
    
   $fldName = 'order[' .htmlentities($ctr) . ']';

   $num = $ctr * 2;
      
   $order = '<input class="order" type="text" onload="javascript:alert(\'here\')" size="3" name="'.$fldName.'" value="' . htmlentities($num).'" />'
           . '<script type="text/javascript"><!-- 
              addMoveButtons(\'frame_items\',\'item_move_event\',\'' . $ctr . '\',\'' . $fldName . '\',\'move_id\',\'move_dir\');
              // --></script>';   
 
   
   
?>
 <tr class="data">
  <td class="vbar"><div class="clr_b"></div></td>
  <td class="data ctr"><input type="checkbox" name="id[<?php echo htmlentities($ctr); ?>]" /></td>
  <td class="vbar"><div class="clr_b"></div></td>
  <td class="data"><?php echo htmlentities($item->widget); ?></td>
  <td class="vbar"><div class="clr_b"></div></td>
  <td class="data"><?php echo $order; ?></td>
  <td class="vbar"><div class="clr_b"></div></td>
  <td class="data"><?php echo empty($item->visible) ? "No" : "Yes"; ?></td>
  <td class="vbar"><div class="clr_b"></div></td>
 </tr>
 <?php echo $hbar; ?> 
<?php 
 }

} else { ?>
<tr class="data">
 <td class="vbar"><div class="clr_b"></div></td>
 <td class="data" colspan="7">No frame items</td>
 <td class="vbar"><div class="clr_b"></div></td>
</tr>
<?php echo $hbar; ?> 
<?php } ?>
</tbody>
<tfoot>
  <tr class="controls">
   <td colspan="9">
    <ul>
     <li class="submit"><div class="btn"><span><input type="submit" value="Go" id="frame_items_go2" /></span></div></li>
     <li><a href="#frame_items_go2"><span><input type="radio" name="task" value="delete_items" /> Delete</span></a></li>          
     <li><a href="#frame_items_go2"><span><input type="radio" name="task" value="edit_item" /> Edit</span></a></li>
     <li><a href="#frame_items_go2"><span><input type="radio" name="task" value="new_item" /> New</span></a></li>
     <li><a href="#frame_items_go2"><span><input type="radio" name="task" value="save_item_order" /> Save Order</span></a></li>          
    </ul>    
   </td>
  </tr>
</tfoot>
</table>
<input type="hidden" name="move_id" value="" />
<input type="hidden" name="move_dir" value="" />
<input type="hidden" name="ck[1]" value="ON" />
<input type="hidden" name="frame_list[1][id]" value="<?php echo $this->escape($this->frameId); ?>" />
</form>
<br />
<?php echo $this->tabs_foot; ?>