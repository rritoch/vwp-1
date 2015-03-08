<?php

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

<form action="" method="post" id="categories">
<table class="itable">
<thead>
 <tr class="title">
  <th colspan="4">Content Categories</th>
 </tr>
 <tr class="toolbar">
  <th colspan="4">
   <table class="itable_toolbar">
    <thead>
     <tr>
      <th><input type="button" onclick="javascript:tbClick('categories','edit_category');" value="Edit" /></th>
      <th><input type="button" onclick="javascript:tbClick('categories','new_category');" value="New" /></th>
      <th><input type="button" onclick="javascript:tbClick('categories','delete_categories');" value="Delete" /></th>
     </tr>
     </thead>
    </table>    
 </tr>
 <tr>
  <th><input type="checkbox" name="ckall" onClick="javascript:tbCheckAll('categories','id[');" /></th>
  <th>ID</th>
  <th>Name</th>
  <th>Parent</th>  
 </tr>
</thead>
<tbody>
<?php if (count($this->category_list) > 0) { 
 foreach($this->category_list as $cat) {

?>
 <tr>
  <td><input type="checkbox" name="id[<?php echo htmlentities($cat["id"]); ?>]" /></td>
  <td><?php echo htmlentities($cat["id"]); ?></td>
  <td><?php echo htmlentities($cat["name"]); ?></td>
  <td><?php echo empty($cat["parent"]) ? "(none)" : htmlentities($cat["parent"] . " : " . $cat["_parent_name"]); ?></td>
 </tr>
<?php 
 }

} else { ?>
<tr>
 <td colspan="4">No Categories</td>
</tr>
<?php } ?>
</tbody>
</table>
<input type="hidden" name="app" value="content" />
<input type="hidden" name="widget" value="catmgr" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="screen" value="<?php echo htmlentities($this->screen); ?>" />
</form>