<?php


?>
<h4>Password reset</h4>

<form action="" method="post">

<p>New Password: <input type="password" name="reset[new_password]" /></p>
<p>Confirm Password: <input type="password" name="reset[confirm_password]" /></p>
<p><br /></p>
<input type="submit" value="Change Password" />
<input type="hidden" name="app" value="user" />
<input type="hidden" name="task" value="save_password" />
<input type="hidden" name="widget" value="reset" />
<input type="hidden" name="screen" value="<?php echo htmlentities($this->screen); ?>" />
</form>