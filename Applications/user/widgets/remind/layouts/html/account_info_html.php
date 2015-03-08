<?php

    $this->email->subject = $this->site_name . " - Account Information";

?>
<HTML>
 <HEAD>
  <TITLE><?php echo $this->email->subject; ?></TITLE>
 </HEAD>
 <BODY>
  <H1><?php echo $this->site_name; ?> account information</H1> 

<?php foreach($this->user_accounts as $acct) { ?>

  <P>Name: <?php echo $acct['name']; ?></P>
  <P>Username: <?php echo $acct['username']; ?></P>
  <P>If you have lost your password for this account you may change your password by going to the following page.</P>
  <P>Change Password link: <A href="<?php echo $acct['change_password_url']; ?>"><?php echo $acct['change_password_url']; ?></A></P>
  <HR>

<?php } ?>

  <P>Feel free to reply to this email if you have any additional concerns regarding your account or this email.</P>
  <P>-- Management</P>
 </BODY>
</HTML>