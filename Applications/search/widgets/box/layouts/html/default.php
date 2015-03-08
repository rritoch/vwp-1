<?php

?>
<div class="searchbox">
<form action="<?php echo $this->escape($this->search_url); ?>" method="get">
 <input type="text" name="q" value="" />
<?php foreach($this->extra as $key=>$val) { ?> 
 <input type="hidden" name="<?php echo $this->escape($key); ?>" value="<?php echo $this->escape($val); ?>" />
<?php } ?>  
 <input type="submit" value="Search" />
</form>
</div>