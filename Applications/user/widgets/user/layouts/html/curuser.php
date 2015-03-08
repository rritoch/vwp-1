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
<h4>Logout</h4>

<table class="loginform">
<tr>
 <td class="label"><label for="username">Username:</label></td>
 <td><?php if (empty($this->cur_user["_domain"])) { echo htmlentities($this->cur_user["username"]); } else {echo htmlentities($this->cur_user["username"] . '/' .  $this->cur_user["username"]);} ?></td>
</tr>
<tr>
 <td class="label"><label for="login">Logout:</label></td> 
 <td><input type="submit" class="button" value="Logout" /></td>
</tr>
</table>
<input type="hidden" name="auth[logout]" value="1" />
</form>