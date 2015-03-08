<?php


?>
<h4>Recover username or password</h4>

<fieldset>
<legend>Lost Username</legend>
<form action="" method="post">
<p>Email Address: <input type="text" name="email" value="<?php echo htmlentities($this->email); ?>" /></p>
<p><br /></p>
<p><input type="submit" value="Retrieve account information" /></p>
<input type="hidden" name="app" value="user" />
<input type="hidden" name="widget" value="remind" />
<input type="hidden" name="task" value="recover_username" />
<input type="hidden" name="screen" value="<?php echo htmlentities($this->screen); ?>" />
</form>
</fieldset>

<p><br /></p>

<fieldset>
<legend>Reset Password</legend>
<form action="" method="post">
<p>Username: <input type="text" name="username" value="<?php echo htmlentities($this->username); ?>" /></p>
<p><br /></p>
<p><input type="submit" value="Reset password" /></p>
<input type="hidden" name="app" value="user" />
<input type="hidden" name="widget" value="remind" />
<input type="hidden" name="task" value="recover_password" />
<input type="hidden" name="screen" value="<?php echo htmlentities($this->screen); ?>" />
</form>
</fieldset>
