<?php
 
// HTML Email

$this->email->subject = "Please confirm your email address";

?>
<HTML>
 <HEAD>
  <TITLE><?php echo htmlentities($this->email->subject); ?></TITLE>
 </HEAD>
 <BODY>
  <H1>Welcome to <?php echo htmlentities($this->site_name); ?>!</H1> 
  <P>To begin accessing your account with us you will need to confirm your email address.</P>
  <P>Your username is: <?php echo htmlentities($this->username); ?></P>
  <P>Your confirmation code is: <?php echo htmlentities($this->confirmation_code); ?></P>
  <P>Confirmation page: <A href="<?php echo htmlentities($this->confirmation_link); ?>"><?php echo htmlentities($this->confirmation_link); ?></A></P>
  <P>Please visit the confirmation page above to confirm your email address.</P> 
  <P>If you believe you have received this in error, please reply to this email with your concerns.</P>
  <P>-- Management</P>
 </BODY>
</HTML>