<?php

?>

<script type="text/javascript"><!--

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


<?php

 $hbar = '<tr class="hbar">
   <td class="vbar"></td>
   <td colspan="7" class="hbar"></td>
   <td class="vbar"></td>
  </tr>';

?>

<form action="" method="post" id="menus">

<table id="menus_control" class="control_panel">
 <thead>
  <tr class="controls">
   <td colspan="9">
    <ul>
     <li class="submit"><div class="btn"><span><input type="submit" value="Go" id="menus_go1" /></span></div></li>
     <li><a href="#menus_go1"><span><input type="radio" name="task" value="edit_menu" /> Edit</span></a></li>
     <li><a href="#menus_go1"><span><input type="radio" name="task" value="new_menu" /> New</span></a></li>
     <li><a href="#menus_go1"><span><input type="radio" name="task" value="delete_menus" /> Delete</span></a></li>     
    </ul>    
   </td>
  </tr> 
  <tr class="title">
   <td class="vbar"><div class="clr_b"></div></td>
   <td colspan="7" class="title"><h2>Menu's</h2></td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>
  <?php echo $hbar; ?>
  <tr class="col_titles">
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="header"><input type="checkbox" name="ckall" onClick="javascript:tbCheckAll('menus','id[');" /></td>  
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="header">Id</td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="header">Title</td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="header">Disabled</td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>
  <?php echo $hbar; ?>       
 </thead>
 <tbody>
<?php if (count($this->menu_list) > 0) { 
 foreach($this->menu_list as $cat) {

?>
 <tr class="data">
  <td class="vbar"><div class="clr_b"></div></td>
  <td class="data ctr"><input type="checkbox" name="id[<?php echo htmlentities($cat["id"]); ?>]" /></td>
  <td class="vbar"><div class="clr_b"></div></td>
  <td class="data"><?php echo htmlentities($cat["id"]); ?></td>
  <td class="vbar"><div class="clr_b"></div></td>
  <td class="data"><?php echo htmlentities($cat["title"]); ?></td>
  <td class="vbar"><div class="clr_b"></div></td>
  <td class="data"><?php echo empty($cat["disabled"]) ? "No" : "Yes"; ?></td>
  <td class="vbar"><div class="clr_b"></div></td>
 </tr>
 <?php echo $hbar; ?>
<?php 
 }

 } else { ?>
<tr class="data">
 <td class="vbar"><div class="clr_b"></div></td>
 <td class="data" colspan="7">No Menu's</td>
 <td class="vbar"><div class="clr_b"></div></td>
</tr>
<?php echo $hbar; ?>
<?php } ?>
</tbody>
<tfoot>
  <tr class="controls">
   <td colspan="9">
    <ul>
     <li class="submit"><div class="btn"><span><input type="submit" value="Go" id="menus_go2" /></span></div></li>
     <li><a href="#menus_go2"><span><input type="radio" name="task" value="edit_menu" /> Edit</span></a></li>
     <li><a href="#menus_go2"><span><input type="radio" name="task" value="new_menu" /> New</span></a></li>
     <li><a href="#menus_go2"><span><input type="radio" name="task" value="delete_menus" /> Delete</span></a></li>     
    </ul>    
   </td>
  </tr> 
</tfoot>  
</table>
<input type="hidden" name="app" value="menumgr" />
<input type="hidden" name="widget" value="menumgr" />
<input type="hidden" name="screen" value="<?php echo htmlentities($this->screen); ?>" />
</form>