<?php echo $this->tabs; ?>

<?php
 
 $hbar = '<tr class="hbar">
   <td class="vbar"></td>
   <td colspan="3" class="hbar"></td>
   <td class="vbar"></td>
  </tr>';

?>

<form action="" method="post" id="frames_control">
<table id="frames" class="control_panel">
 <thead>  
  <tr class="controls">
   <td colspan="5">
    <ul>
     <li class="submit"><div class="btn"><span><input type="submit" value="Go" id="frames_go1" /></span></div></li>
     <li><a href="#frames_go1"><span><input type="radio" name="task" value="close_new_frame" /> Close</span></a></li>
     <li><a href="#frames_go1"><span><input type="radio" name="task" value="refresh_new_frame" /> Refresh</span></a></li>
     <li><a href="#frames_go1"><span><input type="radio" name="task" value="create_frame" /> Save</span></a></li>
    </ul>
    
   </td>
  </tr>
  <tr class="title">
   <td class="vbar"><div class="clr_b"></div></td>
   <td colspan="3" class="title"><h2>New Frame</h2></td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>
  <?php
  
   echo $hbar;
    
  ?>    
 </thead>
 <tbody>
  
<?php  echo $hbar; ?>

 <tr class="data">
  <td class="vbar"><div class="clr_b"></div></td>
  <td class="label">Frame ID</td>
  <td class="vbar"><div class="clr_b"></div></td>
  <td class="data"><input type="text" name="new_frame_info[id]" value="<?php echo $this->escape($this->new_frame_info["id"]); ?>" /></td> 
  <td class="vbar"><div class="clr_b"></div></td>
 </tr>
<?php echo $hbar; ?> 
 </tbody>
 <tfoot>
  <tr class="controls">
   <td colspan="5">
    <ul>
     <li class="submit"><div class="btn"><span><input type="submit" id="frames_go2" value="Go" /></span></div></li>
     <li><a href="#frames_go1"><span><input type="radio" name="task" value="close_new_frame" /> Close</span></a></li>
     <li><a href="#frames_go1"><span><input type="radio" name="task" value="refresh_new_frame" /> Refresh</span></a></li>
     <li><a href="#frames_go1"><span><input type="radio" name="task" value="create_frame" /> Save</span></a></li>

    </ul>
    
   </td>   
  </tr>
 </tfoot>
</table>
</form>

<?php echo $this->tabs_foot; ?> 