<?php

/**
 * Default User settings Layout 
 *  
 * @package    VWP.User
 * @subpackage Widgets
 * @author Ralph Ritoch <rritoch@gmail.com> 
 * @copyright (c) Ralph Ritoch - All rights reserved
 * @link http://www.vnetpublishing.com 
 */

?>

<form action="" method="post">
<h4>User account settings</h4>
<table class="loginform">
<tr>
 <td class="label"><label for="name">Name:</label></td>
 <td><input type="text" size="40" id="name" name="acctinfo[name]" value="<?php echo htmlentities($this->acctinfo["name"]); ?>" /></td>
</tr>
<tr>
 <td class="label"><label for="email">Email:</label></td>
 <td><input type="text" size="40" id="email" name="acctinfo[email]" value="<?php echo htmlentities($this->acctinfo["email"]); ?>" /></td>
</tr>
<tr>
 <td class="label"><label for="password">Password:</label></td> 
 <td><input type="password" size="40" id="password" name="acctinfo[password]" value="<?php echo htmlentities($this->acctinfo["password"]); ?>" /></td>
</tr>
<tr>
 <td class="label"><label for="confirm_password">Confirm Password:</label></td> 
 <td><input type="password" size="40" id="confirm_password" name="acctinfo[confirm_password]" value="<?php echo htmlentities($this->acctinfo["confirm_password"]); ?>" /></td>
</tr>

<tr>
 <td class="label"><label for="save"></label></td> 
 <td><input type="submit" class="button" value="Save" id="save" /></td>
</tr>
</table>
<input type="hidden" name="app" value="user" />
<input type="hidden" name="widget" value="settings" />
<input type="hidden" name="task" value="save_settings" />
<input type="hidden" name="screen" value="<?php echo htmlentities($this->screen); ?>" />
</form>