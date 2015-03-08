<?php
?>

var tinyMCEImageList = new Array(
    // Name, URL
<?php 

  $clist = "\r\n\t\"";
  
  // ["Logo 1", "media/logo.jpg"],
  $enclist = array();
  foreach($this->image_list as $name=>$url) {
      $enclist[] = '    ["' . addcslashes($name,$clist). '","' . addcslashes($url,$clist) . '"]';
  }
  echo implode(',
',$enclist);
  
?>	
		
);
