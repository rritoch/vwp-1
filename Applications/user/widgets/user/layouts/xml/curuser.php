<?php

?>
<current_user>
 <username><?php echo XMLDocument::xmlentities($this->cur_user["username"]); ?></username>
 <domain><?php echo XMLDocument::xmlentities($this->cur_user["_domain"]); ?></domain>
</current_user>