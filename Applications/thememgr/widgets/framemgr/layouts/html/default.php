<?php echo $this->tabs; ?>
<?php
 
 $hbar = '<tr class="hbar">
   <td class="vbar"></td>
   <td colspan="5" class="hbar"></td>
   <td class="vbar"></td>
  </tr>';

?>

<form action="" method="post" id="frames_control">
<table id="frames" class="control_panel">
 <thead>  
  <tr class="controls">
   <td colspan="7">
    <ul>
     <li class="submit"><div class="btn"><span><input type="submit" value="Go" id="frames_go1" /></span></div></li>
     <li><a href="#frames_go1"><span><input type="radio" name="task" value="delete_frames" /> Delete</span></a></li>
     <li><a href="#frames_go1"><span><input type="radio" name="task" value="edit_frame" /> Edit</span></a></li>
     <li><a href="#frames_go1"><span><input type="radio" name="task" value="new_frame" /> New</span></a></li>
    </ul>
    
   </td>
  </tr>
  <tr class="title">
   <td class="vbar"><div class="clr_b"></div></td>
   <td colspan="5" class="title"><h2>Frames</h2></td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>
  <?php echo $hbar; ?>
  <tr class="col_titles">
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="header"><input type="checkbox" /></td>  
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="header">ID</td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="header">Items</td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>
  <?php echo $hbar; ?> 
 </thead>
 <tbody>
 <?php if (count($this->frame_list) > 0) {
  
  $ctr = 0; foreach($this->frame_list as $frame_info) { $ctr++; ?>
  <tr class="data">
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="ctr data"><input type="checkbox" name="ck[<?php echo $ctr; ?>]" /></td>  
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="label"><?php echo $this->escape($frame_info["id"]); ?><input type="hidden" name="frame_list[<?php echo $ctr; ?>][id]" value="<?php echo $this->escape($frame_info["id"]); ?>" /></td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="data"><?php echo $this->escape(count($frame_info["_items"])); ?></td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>  
  
<?php  
  echo $hbar;
 } 

 } else { ?>
<tr class="data">
 <td class="vbar"><div class="clr_b"></div></td>
 <td class="data" colspan="5">No frames found</td>
 <td class="vbar"><div class="clr_b"></div></td>
</tr>     
<?php  echo $hbar; ?>
<?php } ?>
 </tbody>
 <tfoot>
  <tr class="controls">
   <td colspan="7">
    <ul>
     <li class="submit"><div class="btn"><span><input type="submit" id="frames_go2" value="Go" /></span></div></li>
     <li><a href="#frames_go2"><span><input type="radio" name="task" value="new_frame" /> New</span></a></li>
     <li><a href="#frames_go2"><span><input type="radio" name="task" value="edit_frame" /> Edit</span></a></li>
     <li><a href="#frames_go2"><span><input type="radio" name="task" value="delete_frames" /> Delete</span></a></li>
    </ul>
    
   </td>   
  </tr>
 </tfoot>
</table>
</form> 
<?php echo $this->tabs_foot; ?>