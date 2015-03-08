<?php

 $vfile =& v()->filesystem()->file();
 $data = $vfile->read(dirname(__FILE__).DS.'base'.DS.'csv'.DS.'index.php');
 echo $data;
