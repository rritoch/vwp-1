<?xml version="1.0" encoding="utf-8" ?>
<vwp_response>
 <title><?php echo htmlentities($this->page_title); ?></title>
 <site><?php echo htmlentities($this->site_name); ?></site>
 <promo>Virtual Web Platform.</promo>
<?php if (count($this->errors) > 0) { ?>
 <error_messages> 
 <?php foreach($this->errors as $errmsg) { ?>
  <error><?php echo htmlentities($errmsg); ?></error>   
 <?php } ?>
  <error_instruction>We are sorry for the inconvienience. We will repair the problem shortly.</error_instruction>
 </error_messages>
<?php } ?> <app_response><vdoc:include alias="content" /></app_response>
 <copyright>Copyright &#169; Ralph Ritoch 2010</copyright>
</vwp_response>