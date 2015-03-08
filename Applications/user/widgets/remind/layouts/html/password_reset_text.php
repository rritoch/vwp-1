<?php

    $this->email->subject = $this->site_name . " - Account Information";

?>

<?php echo $this->site_name; ?> account information 

Name: <?php echo $this->name; ?>

Username: <?php echo $this->username; ?>

If you have lost your password you may change your password by going to the following page.

Change Password link: <?php echo $this->change_password_url; ?>

Feel free to reply to this email if you have any additional concerns regarding your account or this email.

-- Management