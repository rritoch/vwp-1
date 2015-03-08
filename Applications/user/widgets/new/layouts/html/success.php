<?php

?>
<h4>Account Created</h4>
<?php 
    if (
         isset($this->settings['require_email_verification']) && 
         ($this->settings['require_email_verification'] > 0)
        ) { ?>
<p>Please check your email, you will need to confirm your email address to access your account.</p>
<p><a href="<?php echo htmlentities($this->confirmation_link_short); ?>">Go to confirmation page</a></p>    	
<?php } else {  ?>
<p>Your account has been created, you may now login.</p>    	    	
<?php } ?>
