<?php

    $this->email->subject = $this->site_name . " - Account Information";

?>
<HTML>
 <HEAD>
  <TITLE><?php echo $this->email->subject; ?></TITLE>
 </HEAD>
 <BODY>
  <H1><?php echo $this->site_name; ?> account information</H1> 
  <P>Name: <?php echo $this->name; ?></P>
  <P>Username: <?php echo $this->username; ?></P>
  <P>If you have lost your password you may change your password by going to the following page.</P>
  <P>Change Password link: <?php echo $this->change_password_url; ?></P>
  <P>Feel free to reply to this email if you have any additional concerns regarding your account or this email.</P>
  <P>-- Management</P>
 </BODY>
</HTML>