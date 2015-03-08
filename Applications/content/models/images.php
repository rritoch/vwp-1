<?php

class Content_Model_Images extends VModel 
{
	
    /**
     * Get Configuration Key
     * 
     * @return string Configuration key
     * @access public
     */
    
    function getConfigKey() 
    {
        return "SOFTWARE\\VNetPublishing\\Content\\Config";
    }	
	
    /**
     * Get configuration settings
     * 
     * @return array|object Configuration settings on success, error or warning otherwise
     * @access public
     */       
 
    function getConfig() 
    {
        $localMachine = & Registry::LocalMachine();
  
        $result = Registry::RegOpenKeyEx($localMachine,
                        self::getConfigKey(),
                        0,
                        0, //samDesired
                        $registryKey);
                         
        if (!VWP::isWarning($result)) {     
            $data = array();
            $idx = 0;
            $keylen = 255;
            $vallen = 255;
            $lptype = REG_SZ; 
            while (!VWP::isError($result = Registry::RegEnumValue(
                                     $registryKey,
                                     $idx++,
                                     $key,
                                     $keylen,
                                     0, // reserved
                                     $lpType,
                                     $val,
                                     $vallen)))  {
                if (!VWP::isWarning($result) || $result->errno = ERROR_MORE_DATA) {                                       
                    $data[$key] = $val;
                    $keylen = 255;
                    $vallen = 255;  
                }
            }  
            Registry::RegCloseKey($registryKey);
            Registry::RegCloseKey($localMachine);
            return $data;
        }
  
        Registry::RegCloseKey($localMachine);
        return $result;
 
    }
	
	function getList() 
	{
		$imageext = array('png','gif','jpeg','jpg');
		
		$cfg = $this->getConfig();
		if (VWP::isWarning($cfg)) {
			return $cfg;
		}
		
		$image_list = array();
		if (!isset($cfg['images_path'])) {
			return $image_list;
		}

		if (empty($cfg['images_path'])) {
			return $image_list;
		}
		
		$vpath =& v()->filesystem()->path();
		
		$basePath = $vpath->clean($cfg['images_path']);
		$files = $this->_vfolder->files($basePath,".",true,true);
		
		$baseLen = strlen($basePath);
		foreach($files as $file) {
		    $file = $vpath->clean($file);
		    if (substr($file,0,$baseLen) == $basePath) {
		    	$rpath = ltrim($vpath->clean(substr($file,$baseLen),'/'),'/');		    	
		    	
		    	$parts = explode('/',$rpath);
		    	$n = array_pop($parts);
		    	$e = explode('.',$n);
		    	if (count($e) > 1) {
		    	    $ext = array_pop($e);
		    	} else {
		    		$ext = '';
		    	}
		    	
		    	if (in_array($ext,$imageext)) {
		    	    $n = implode('.',$e);
		    	    array_push($parts,$n);		    	 
		    	    $name = implode(' > ',$parts) . " ($ext)";
		    	    $image_list[$name] = rtrim($cfg['images_url'],'/') .'/'. $rpath; 
		    	}		    	
		    }   	
		}
	    return $image_list;
	}
}