<?php

/**
 * Default User Login Layout 
 *  
 * @package    VWP.User
 * @subpackage Layouts
 * @author Ralph Ritoch <rritoch@gmail.com> 
 * @copyright (c) Ralph Ritoch - All rights reserved
 * @link http://www.vnetpublishing.com 
 */

?>

<form action="" method="post">
<h4>Login</h4>
<table class="loginform">
<tr>
 <td class="label"><label for="username">Username:</label></td>
 <td><input type="text" size="20" id="username" name="auth[username]" /></td>
</tr>
<tr>
 <td class="label"><label for="password">Password:</label></td> 
 <td><input type="password" size="20" id="password" name="auth[password]" /></td>
</tr>
<tr>
 <td class="label"><label for="login">Login:</label></td> 
 <td><input type="submit" class="button" value="Login" /></td>
</tr>
<tr>
 <td colspan="2"><a href="<?php echo htmlentities($this->register_url); ?>">New User</a></td>
</tr>
<tr> 
 <td colspan="2"><a href="<?php echo htmlentities($this->remind_url); ?>">Lost Username or Password</a></td>
</tr>
<tr> 
 <td colspan="2"><a href="<?php echo htmlentities($this->confirm_url); ?>">Lost email confirmation code</a></td>
</tr> 
</table>
</form>