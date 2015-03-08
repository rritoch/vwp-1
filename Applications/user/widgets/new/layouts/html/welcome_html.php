<?php
 
// HTML Email

$this->email->subject = "Welcome to " . $this->site_name;

?>
<HTML>
 <HEAD>
  <TITLE><?php echo htmlentities($this->email->subject); ?></TITLE>
 </HEAD>
 <BODY>
  <H1>Welcome to <?php echo $this->site_name; ?>!</H1>
  <P>This email is being sent to you as a courtesy to notify you that someone signed up for an account with us at <A href="<?php echo $this->site_url; ?>"><?php echo $this->site_url; ?></A> using this email address. If you believe that you are receiving this email in error please reply to this email with your concerns.</P>
  <P>-- Management</P>
 </BODY>
</HTML>