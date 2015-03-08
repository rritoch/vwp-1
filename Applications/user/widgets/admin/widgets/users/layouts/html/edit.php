<?php
 
 $hbar = '<tr class="hbar">
   <td class="vbar"></td>
   <td colspan="3" class="hbar"></td>
   <td class="vbar"></td>
  </tr>';
  
?>


<form action="" method="post" id="userinfo_control">

<table id="userinfo" class="control_panel">
 <thead>
  <tr class="controls">
   <td colspan="5">
    <ul>
     <li class="submit"><div class="btn"><span><input type="submit" value="Go" id="userinfo_go1" /></span></div></li>
     <li><a href="#userinfo_go1"><span><input type="radio" name="task" value="save_user" /> Save</span></a></li>
     <li><a href="#userinfo_go1"><span><input type="radio" name="task" value="edit_user" /> Reset</span></a></li>
     <li><a href="#userinfo_go1"><span><input type="radio" name="task" value="close" /> Close</span></a></li>     
    </ul>    
   </td>   
  </tr>
  <tr class="title">
   <td class="vbar"><div class="clr_b"></div></td>
   <td colspan="3" class="title"><h2>Edit User</h2></td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>
  <?php echo $hbar; ?>    
 </thead>
 <tbody>
  <tr class="data">
   <td class="vbar"><div class="clr_b"></div></td>      
   <td class="label"><label>Username</label></td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="data"><?php echo htmlentities($this->userinfo["username"]); ?><input type="hidden" name="userinfo[username]" value="<?php echo htmlentities($this->userinfo["username"]); ?>" /></td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>
  <tr class="data">
   <td class="vbar"><div class="clr_b"></div></td>      
   <td class="label"><label>Name</label></td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="data"><input type="text" size="60" name="userinfo[name]" value="<?php echo htmlentities($this->userinfo["name"]); ?>" /></td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>
  <tr class="data">
  <td class="vbar"><div class="clr_b"></div></td>      
   <td class="label"><label>Email</label></td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="data"><input type="text" size="60" name="userinfo[email]" value="<?php echo htmlentities($this->userinfo["email"]); ?>" /></td>
   <td class="vbar"><div class="clr_b"></div></td>   
  </tr>
  <tr class="data">
   <td class="vbar"><div class="clr_b"></div></td>      
   <td class="label"><label>Administrator</label></td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="data">
    <?php if ($this->userinfo["_admin"]) { ?>
       Yes <input type="radio" size="60" name="userinfo[_admin]" value="1" checked="checked" />
       No <input type="radio" size="60" name="userinfo[_admin]" value="0" />
    <?php } else { ?>
       Yes <input type="radio" size="60" name="userinfo[_admin]" value="1" />
       No <input type="radio" size="60" name="userinfo[_admin]" value="0" checked="checked" />    
    <?php } ?>   
   </td>
   <td class="vbar"><div class="clr_b"></div></td>   
  </tr>  
  <tr class="data">
   <td class="vbar"><div class="clr_b"></div></td>      
   <td class="label"><label>Password</label></td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="data"><input type="password" size="60" name="userinfo[password]" value="<?php echo htmlentities($this->userinfo["password"]); ?>" /></td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>
  <tr class="data">
   <td class="vbar"><div class="clr_b"></div></td>      
   <td class="label"><label>Confirm Password</label></td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="data"><input type="password" size="60" name="userinfo[confirm_password]" value="<?php echo htmlentities($this->userinfo["confirm_password"]); ?>" /></td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>
 </tbody>
  <tfoot>
  <tr class="controls">
   <td colspan="5">
    <ul>
     <li class="submit"><div class="btn"><span><input type="submit" value="Go" id="userinfo_go2" /></span></div></li>
     <li><a href="#userinfo_go2"><span><input type="radio" name="task" value="save_user" /> Save</span></a></li>
     <li><a href="#userinfo_go2"><span><input type="radio" name="task" value="edit_reset" /> Reset</span></a></li>
     <li><a href="#userinfo_go2"><span><input type="radio" name="task" value="close" /> Close</span></a></li>     
    </ul>    
   </td>   
  </tr>
 </tfoot> 
</table>
<input type="hidden" name="app" value="user" />
<input type="hidden" name="widget" value="admin.users" />
<input type="hidden" name="screen" value="<?php echo htmlentities($this->screen); ?>" />
</form>