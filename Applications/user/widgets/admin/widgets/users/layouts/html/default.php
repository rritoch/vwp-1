<?php

 $hbar = '<tr class="hbar">
   <td class="vbar"></td>
   <td colspan="11" class="hbar"></td>
   <td class="vbar"></td>
  </tr>';

?>


<?php echo $this->menu; ?>


<form action="" method="post" id="ulist_control">
 
<table id="ulist" class="control_panel">
 <thead>
  <tr class="controls">
   <td colspan="13">
    <ul>
     <li class="submit"><div class="btn"><span><input type="submit" value="Go" id="ulist_go1" /></span></div></li>
     <li><a href="#ulist_go1"><span><input type="radio" name="task" value="delete_users" /> Delete</span></a></li>
     <li><a href="#ulist_go1"><span><input type="radio" name="task" value="edit_user" /> Edit</span></a></li>
     <li><a href="#ulist_go1"><span><input type="radio" name="task" value="new_user" /> New</span></a></li>     
    </ul>    
   </td>
  </tr> 
  <tr class="title">
   <td class="vbar"><div class="clr_b"></div></td>
   <td colspan="11" class="title"><h2>Users</h2></td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>
  <?php echo $hbar; ?> 
    
  <tr class="col_titles">
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="header" id="eselect"><input type="checkbox" name="ckall" onClick="javascript:columnToggleCheckAll('eselect');" /></td>  
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="header">Source</td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="header">Name</td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="header">Username</td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="header">Email</td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="header">Admin</td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>
  <?php echo $hbar; ?>      
 </thead> 
 
<?php if (count($this->users) > 0) { 
  foreach($this->users as $userinfo) { ?>
  <tr class="data">
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="ctr data"><input type="checkbox" name="ck[<?php echo htmlentities($userinfo["username"]); ?>]" /></td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="data"><?php echo htmlentities($userinfo["_source"]); ?></td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="data"><?php echo htmlentities($userinfo["name"]); ?></td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="data"><?php echo htmlentities($userinfo["username"]); ?></td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="data"><?php echo htmlentities($userinfo["email"]); ?></td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="data"><?php echo $userinfo["_admin"] ? "Yes" : "No" ; ?></td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>
  <?php echo $hbar; ?> 
<?php  }
 } else { ?> 
<tr class="data">
 <td class="vbar"><div class="clr_b"></div></td>
 <td class="data" colspan="11">No users</td>
 <td class="vbar"><div class="clr_b"></div></td>
</tr>
  <?php echo $hbar; ?>
<?php } ?>
 </tbody>
  <tfoot>
  <tr class="controls">
   <td colspan="13">
    <ul>
     <li class="submit"><div class="btn"><span><input type="submit" value="Go" id="ulist_go2" /></span></div></li>
     <li><a href="#ulist_go2"><span><input type="radio" name="task" value="edit_user" /> Edit</span></a></li>
     <li><a href="#ulist_go2"><span><input type="radio" name="task" value="new_user" /> New</span></a></li>
     <li><a href="#ulist_go2"><span><input type="radio" name="task" value="delete_users" /> Delete</span></a></li>     
    </ul>    
   </td>
  </tr>
  </tfoot> 
</table>
<input type="hidden" name="app" value="user" />
<input type="hidden" name="widget" value="admin.users" />
<input type="hidden" name="screen" value="<?php echo htmlentities($this->screen); ?>" />
</form>

<?php echo $this->menu_foot; ?>