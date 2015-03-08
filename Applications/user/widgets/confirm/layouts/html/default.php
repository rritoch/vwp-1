<?php


?>

<h4>Confirm your email address</h4>

<form action="" method="post">

<table border="0">
<tr>
 <td style="text-align: right;">Username:</td>
 <td><input type="text" name="userid" size="40" value="<?php echo htmlentities($this->username); ?>" /> </td>
</tr>
<tr>
 <td style="text-align: right;">Confirmation Code:</td>
 <td><input type="text" name="code" size="40" value="<?php echo htmlentities($this->confirmation_code); ?>" /> </td>

</tr>
<tr>
 <td></td>
 <td><input type="submit" value="Confirm Email" /></td>
</tr>  
</table>

<input type="hidden" name="screen" value="<?php echo htmlentities($this->screen); ?>" />
<input type="hidden" name="app" value="user" />
<input type="hidden" name="widget" value="confirm" />
<input type="hidden" name="task" value="confirm" />

</form>

<hr />

<form action="" method="post">

<table border="0">
<tr>
 <td style="text-align: right;">Username:</td>
 <td><input type="text" name="userid" size="40" value="<?php echo htmlentities($this->username); ?>" /> </td>
</tr>
<tr>
 <td></td>
 <td><input type="submit" value="Resend Confirmation Email" /></td>
</tr>  
</table>

<input type="hidden" name="screen" value="<?php echo htmlentities($this->screen); ?>" />
<input type="hidden" name="app" value="user" />
<input type="hidden" name="widget" value="confirm" />
<input type="hidden" name="task" value="resend_confirmation" />

</form>