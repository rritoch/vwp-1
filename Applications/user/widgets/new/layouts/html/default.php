<?php

/**
 * Default User Registration Layout 
 *  
 * @package    VWP.User
 * @subpackage Widgets
 * @author Ralph Ritoch <rritoch@gmail.com> 
 * @copyright (c) Ralph Ritoch - All rights reserved
 * @link http://www.vnetpublishing.com 
 */

?>

<form action="" method="post">
<h4>New User Registration</h4>
<table class="loginform">
<tr>
 <td class="label"><label for="name">Name:</label></td>
 <td><input type="text" size="40" id="name" name="register[name]" value="<?php echo htmlentities($this->register["name"]); ?>" /></td>
</tr>
<tr>
 <td class="label"><label for="username">Username:</label></td>
 <td><input type="text" size="40" id="username" name="register[username]" value="<?php echo htmlentities($this->register["username"]); ?>" /></td>
</tr>
<tr>
 <td class="label"><label for="email">Email:</label></td>
 <td><input type="text" size="40" id="email" name="register[email]" value="<?php echo htmlentities($this->register["email"]); ?>" /></td>
</tr>
<tr>
 <td class="label"><label for="password">Password:</label></td> 
 <td><input type="password" size="40" id="password" name="register[password]" value="<?php echo htmlentities($this->register["password"]); ?>" /></td>
</tr>
<tr>
 <td class="label"><label for="confirm_password">Confirm Password:</label></td> 
 <td><input type="password" size="40" id="confirm_password" name="register[confirm_password]" value="<?php echo htmlentities($this->register["confirm_password"]); ?>" /></td>
</tr>

<tr>
 <td class="label"><label for="login">Register:</label></td> 
 <td><input type="submit" class="button" value="Register" /></td>
</tr>
</table>
<input type="hidden" name="app" value="user" />
<input type="hidden" name="widget" value="new" />
<input type="hidden" name="task" value="register" />
<input type="hidden" name="screen" value="<?php echo htmlentities($this->screen); ?>" />
</form>