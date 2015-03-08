<?php

 if ($this->mode == 'raw') {
     echo '<' . '?xml version="1.0" ?' . '>
';
 }
 
?>
<image_list
 xmlns="http://standards.vnetpublishing.com/schemas/vwp/2011/03/ImageList"
 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
 xsi:schemaLocation="http://standards.vnetpublishing.com/schemas/vwp/2011/03/ImageList http://standards.vnetpublishing.com/schemas/vwp/2011/03/ImageList/">
<?php foreach($this->image_list as $name=>$url) { 
?>    <image name="<?php echo $this->escape($name); ?>"><?php echo $this->escape($url); ?></image>    	
<?php } ?>
</image_list>