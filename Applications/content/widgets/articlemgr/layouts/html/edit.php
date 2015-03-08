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

<form action="" method="post" id="editarticle">
<table class="itable">
<thead>
 <tr class="title">
  <th colspan="4"><?php if ($this->is_new) { ?>New Article<?php } else { ?>Edit Article<?php } ?></th>
 </tr>
 <tr class="toolbar">
  <th colspan="4">
   <table class="itable_toolbar">
    <thead>
     <tr>
      <th><input type="button" onclick="javascript:tbClick('editarticle','save_article');" value="Save" /></th>
      <th><input type="button" onclick="javascript:tbClick('editarticle','refresh');" value="Refresh" /></th>
      <th><input type="button" onclick="javascript:tbClick('editarticle','');" value="Close" /></th>
     </tr>
     </thead>
    </table>    
 </tr>
</thead>
<tbody>
<?php if (!$this->is_new) { ?> 
 <tr>
  <td class="label">ID</td>
  <td><?php echo htmlentities($this->article["id"]); ?><input type="hidden" size="50" name="article[id]" value="<?php echo htmlentities($this->article["id"]); ?>"/></td> 
 </tr>

<?php } ?>
 <tr>
  <td class="label">Title</td>
  <td><input type="text" size="50" name="article[title]" value="<?php echo htmlentities($this->article["title"]); ?>"/></td>
 </tr>

 <tr>
  <td class="label">Author</td>
  <td><input type="text" size="50" name="article[author_name]" value="<?php echo htmlentities($this->article["author_name"]); ?>"/></td>
 </tr>
 
 <tr>  
  <td class="editor" colspan="2"><?php echo $this->editor; ?></td>
 </tr>

 <tr>
  <td class="label">Description</td>
  <td><textarea rows="5" cols="40" name="article[description]"><?php echo htmlentities($this->article["description"]); ?></textarea></td>
 </tr>

 <tr>
  <td class="label">Keywords</td>
  <td><input type="text" size="50" name="article[keywords]" value="<?php echo htmlentities($this->article["keywords"]); ?>"/></td>
 </tr>

 <tr>
  <td class="label">Category</td>
  <td><select name="article[category]"><option value="">== Select Category ==</option><?php
   foreach($this->category_list as $cat) { ?>
     <?php if ($this->article["category"] == $cat["id"]) { ?><option value="<?php echo htmlentities($cat["id"]); ?>" selected="selected"><?php } else { ?><option value="<?php echo htmlentities($cat["id"]); ?>"><?php } ?><?php echo htmlentities($cat["name"]); ?></option>
<?php   }
  ?></select></td>
 </tr>
 <tr>
  <td class="label">Published</td>
  <td>No <input type="radio" size="50" name="article[published]" value="0" <?php if (!$this->article["published"]) { echo "checked=\"checked\" "; } ?>/> Yes <input type="radio" size="50" name="article[published]" value="1" <?php if ($this->article["published"]) { echo "checked=\"checked\" "; } ?>/></td>
 </tr>
  
</tbody>
</table>
<input type="hidden" name="app" value="content" />
<input type="hidden" name="widget" value="articlemgr" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="is_new" value="<?php echo $this->is_new ? "1" : "0"; ?>" />
<input type="hidden" name="screen" value="<?php echo htmlentities($this->screen); ?>" />
</form>