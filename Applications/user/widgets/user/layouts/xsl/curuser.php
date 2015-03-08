<?php

/**
 * Default User Login Layout 
 *  
 * @package    VWP.User
 * @subpackage Layouts.XSL
 * @author Ralph Ritoch <rritoch@gmail.com> 
 * @copyright (c) Ralph Ritoch - All rights reserved
 * @link http://www.vnetpublishing.com 
 */

?>
<xsl:template mode="<?php echo $this->screen; ?>">
<xsl:apply-template mode="<?php echo $this->screen; ?>" select="current_user" />
</xsl:template>

<xsl:template select="current_user" mode="<?phg echo $this->screen; ?>">
<form action="" method="post">
<h4>Logout</h4>

<table class="loginform">
<tr>
 <td class="label"><label for="username">Username:</label></td>
 <td><xsl:value-of select="domain" /> / <xsl:value-of select="username" /></td>
</tr>
<tr>
 <td class="label"><label for="login">Logout:</label></td> 
 <td><input type="submit" class="button" value="Logout" /></td>
</tr>
</table>
<input type="hidden" name="auth[logout]" value="1" />
</form>
</xsl:template>