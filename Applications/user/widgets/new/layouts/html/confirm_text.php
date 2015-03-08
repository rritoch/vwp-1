<?php
 
// Text Email

$this->email->subject = "Please confirm your email address";

?>
Welcome to <?php echo $this->site_name; ?>!

To begin accessing your account with us you will need to confirm your email address.

Your username is: <?php echo htmlentities($this->username); ?>

Your confirmation code is: <?php echo $this->confirmation_code; ?>

Confirmation page: <?php echo $this->confirmation_link; ?>

Please visit the confirmation page above to confirm your email address. 

If you believe you have received this in error, please reply to this email with your concerns.

-- Management
