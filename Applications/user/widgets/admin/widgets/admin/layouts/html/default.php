<?php

 $hbar = '<tr class="hbar">
   <td class="vbar"></td>
   <td colspan="3" class="hbar"></td>
   <td class="vbar"></td>
  </tr>';

?>


<?php echo $this->menu; ?>
<script type="text/javascript"><!--

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
 
// --></script>

<form action="" method="post" id="uconfig_control">

<table id="uconfig" class="control_panel">
 <thead>
  <tr class="controls">
   <td colspan="5">
    <ul>
     <li class="submit"><div class="btn"><span><input type="submit" value="Go" id="uconfig_go1" /></span></div></li>
     <li><a href="#uconfig_go1"><span><input type="radio" name="task" value="save_config" /> Save</span></a></li>
     <li><a href="#uconfig_go1"><span><input type="radio" name="task" value="" /> Reset</span></a></li>     
    </ul>    
   </td>
  </tr> 
  <tr class="title">
   <td class="vbar"><div class="clr_b"></div></td>
   <td colspan="3" class="title"><h2>Configure User System</h2></td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>
  <?php echo $hbar; ?>     
 </thead>
 <tbody>
  <tr class="data">
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="label"><p><b>Database:</b></p></td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="data"><select name="user_database"><?php
    foreach($this->dblist as $val=>$name) {
     if ($this->config["user_database"] == $val) { ?>
      <option value="<?php echo htmlentities($val); ?>" selected="selected"><?php echo htmlentities($name); ?></option>     
<?php     } else { ?>
      <option value="<?php echo htmlentities($val); ?>"><?php echo htmlentities($name); ?></option>
<?php }    
    }   
   ?></select></td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>
  
  <tr class="data">
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="label"><p><b>Table Prefix:</b></p></td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="data"><input type="text" size="10" name="table_prefix" value="<?php echo htmlentities($this->config["table_prefix"]); ?>" /></td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>
  <?php echo $hbar; ?>
  <tr class="data"><?php 
    $selected = array(
      (isset($this->config["require_email_verification"]) && $this->config["require_email_verification"] == 1) ? 'checked = "checked"' : '',
      (isset($this->config["require_email_verification"]) && $this->config["require_email_verification"] == 1) ? '' : 'checked = "checked"', 
    );
  
  ?>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="label"><p><b>Require Email Verification:</b></p></td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="data"><input type="radio" name="require_email_verification" value="1"<?php echo $selected[0]; ?> /> Yes <input type="radio" name="require_email_verification" value="1"<?php echo $selected[1]; ?> /> No</td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>
  <tr class="data">
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="label"><p><b>Email From Name:</b></p></td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="data"><input type="text" size="30" name="email_from_name" value="<?php if (isset($this->config["email_from_name"])) {echo htmlentities($this->config["email_from_name"]); } ?>" /></td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>
  <tr class="data">
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="label"><p><b>Email Address:</b></p></td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="data"><input type="text" size="30" name="email_address" value="<?php if (isset($this->config["email_address"])) {echo htmlentities($this->config["email_address"]); } ?>" /></td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>
        
  <tr class="data">
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="label"><p><b>SMTP Host:</b></p></td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="data"><input type="text" size="30" name="smtp_host" value="<?php if (isset($this->config["smtp_host"])) {echo htmlentities($this->config["smtp_host"]); } ?>" /></td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>
  <tr class="data">
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="label"><p><b>SMTP Port:</b></p></td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="data"><input type="text" size="30" name="smtp_port" value="<?php if (isset($this->config["smtp_port"])) {echo htmlentities($this->config["smtp_port"]); } else { echo "25"; }?>" /></td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>    
  <tr class="data">
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="label"><p><b>SMTP Username:</b></p></td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="data"><input type="text" size="30" name="smtp_username" value="<?php if (isset($this->config["smtp_username"])) { echo htmlentities($this->config["smtp_username"]);} ?>" /></td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>
  <tr class="data">
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="label"><p><b>SMTP Password:</b></p></td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="data"><input type="password" size="30" name="smtp_password" value="<?php if (isset($this->config["smtp_password"])) { echo htmlentities($this->config["smtp_password"]); } ?>" /></td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>      
  <?php echo $hbar; ?>         
 </tbody>
 <tfoot>
  <tr class="controls">
   <td colspan="5">
    <ul>
     <li class="submit"><div class="btn"><span><input type="submit" value="Go" id="uconfig_go2" /></span></div></li>
     <li><a href="#uconfig_go2"><span><input type="radio" name="task" value="save_config" /> Save</span></a></li>
     <li><a href="#uconfig_go2"><span><input type="radio" name="task" value="" /> Reset</span></a></li>     
    </ul>    
   </td>
  </tr> 
 </tfoot> 
</table>
<input type="hidden" name="app" value="user" />
<input type="hidden" name="widget" value="admin.admin" />
<input type="hidden" name="screen" value="<?php echo htmlentities($this->screen); ?>" />
</form>

<?php echo $this->menu_foot; ?>