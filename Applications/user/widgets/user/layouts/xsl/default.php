<?php

?>

<xsl:template match="user_login" mode="<?php echo XSLDocument::screen2Mode($this->screen); ?>">
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
 <td colspan="2"><a><xsl:attribute name="href"><xsl:value-of select="new_user/@href" /></xsl:attribute>New User</a></td>
</tr>
<tr> 
 <td colspan="2"><a><xsl:attribute name="href"><xsl:value-of select="remind/@href" /></xsl:attribute>Lost Username or Password</a></td>
</tr> 
</table>
</form>
</xsl:template>