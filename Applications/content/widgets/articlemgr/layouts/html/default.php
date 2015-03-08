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

<form action="" method="post" id="articles">
<table class="itable">
<thead>
 <tr class="title">
  <th colspan="4">Content Articles</th>
 </tr>
 <tr class="toolbar">
  <th colspan="4">
   <table class="itable_toolbar">
    <thead>
     <tr>
      <th><input type="button" onclick="javascript:tbClick('articles','edit_article');" value="Edit" /></th>
      <th><input type="button" onclick="javascript:tbClick('articles','new_article');" value="New" /></th>
      <th><input type="button" onclick="javascript:tbClick('articles','delete_articles');" value="Delete" /></th>
     </tr>
     </thead>
    </table>    
 </tr>
 <tr>
  <th><input type="checkbox" name="ckall" onClick="javascript:tbCheckAll('articles','id[');" /></th>
  <th>ID</th>
  <th>Title</th>
  <th>Category</th>  
 </tr>
</thead>
<tbody>
<?php if (count($this->article_list) > 0) { 
 foreach($this->article_list as $article) {

?>
 <tr>
  <td><input type="checkbox" name="id[<?php echo htmlentities($article["id"]); ?>]" /></td>
  <td><?php echo htmlentities($article["id"]); ?></td>
  <td><?php echo htmlentities($article["title"]); ?></td>
  <td><?php if (empty($article["category"])) { echo "(none)"; } else echo isset($this->categories[$article["category"]]) ? htmlentities($this->categories[$article["category"]]) : "(Unknown)"; ?></td>
 </tr>
<?php 
 }

} else { ?>
<tr>
 <td colspan="4">No Articles</td>
</tr>
<?php } ?>
</tbody>
</table>
<input type="hidden" name="app" value="content" />
<input type="hidden" name="widget" value="articlemgr" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="screen" value="<?php echo htmlentities($this->screen); ?>" />
</form>