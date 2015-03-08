<?php


?>


<?php

 $hbar = '<tr class="hbar">
   <td class="vbar"></td>
   <td colspan="3" class="hbar"></td>
   <td class="vbar"></td>
  </tr>';

?>
<form action="" method="post" id="newitem_control">

<table id="newitem" class="control_panel">
 <thead>
  <tr class="controls">
   <td colspan="5">
    <ul>
     <li class="submit"><div class="btn"><span><input type="submit" value="Go" id="newitem_go1" /></span></div></li>
     <li><a href="#newitem_go1"><span><input type="radio" name="task" value="create_item" /> Save</span></a></li>
     <li><a href="#newitem_go1"><span><input type="radio" name="task" value="edit_menu" /> Close</span></a></li>     
    </ul>    
   </td>
  </tr> 
  <tr class="title">
   <td class="vbar"><div class="clr_b"></div></td>
   <td colspan="3" class="title"><h2>Menu &gt; <?php echo htmlentities($this->menu); ?> &gt; New Item</h2></td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>
  <?php echo $hbar; ?>     
 </thead>
 <tr class="data">
  <td class="vbar"><div class="clr_b"></div></td>
  <td class="label">Type</td>
  <td class="vbar"><div class="clr_b"></div></td>
  <td class="data"><select name="item[type]"><?php
 foreach($this->item_types as $id=>$name) {
?><option value="<?php echo htmlentities($id); ?>"><?php echo htmlentities($name); ?></option><?php   
 }  
?></select></td>
 <td class="vbar"><div class="clr_b"></div></td>
 </tr>
</tbody>
<tfoot>
  <tr class="controls">
   <td colspan="5">
    <ul>
     <li class="submit"><div class="btn"><span><input type="submit" value="Go" id="newitem_go1" /></span></div></li>
     <li><a href="#newitem_go1"><span><input type="radio" name="task" value="create_item" /> Save</span></a></li>
     <li><a href="#newitem_go1"><span><input type="radio" name="task" value="edit_menu" /> Close</span></a></li>     
    </ul>    
   </td>
  </tr> 
</tfoot>
</table>
<input type="hidden" name="app" value="menumgr" />
<input type="hidden" name="widget" value="menumgr" />
<input type="hidden" name="id[<?php echo htmlentities($this->menu); ?>]" value="on" />
<input type="hidden" name="menu" value="<?php echo htmlentities($this->menu); ?>" />
<input type="hidden" name="screen" value="<?php echo htmlentities($this->screen); ?>" />
</form>
