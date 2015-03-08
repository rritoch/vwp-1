<?php

    $this->email->subject = $this->site_name . " - Account Information";

?>

<?php echo $this->site_name; ?> account information 

<?php foreach($this->user_accounts as $acct) { ?>

Name: <?php echo $acct['name']; ?>

Username: <?php echo $acct['username']; ?>

If you have lost your password for this account you may change your password by going to the following page.

Change Password link: <?php echo $acct['change_password_url']; ?>

----

<?php } ?>

Feel free to reply to this email if you have any additional concerns regarding your account or this email.

-- Management
