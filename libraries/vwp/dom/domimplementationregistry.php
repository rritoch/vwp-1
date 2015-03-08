<?php

/**
 * Virtual Web Platform - DOM Implementation Registry 
 *
 * This file provides the DOM Implementation Registry
 * 
 * @package VWP
 * @subpackage Libraries.DOM  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License   
 */

// No direct access
class_exists('VWP') || die();

/**
 * Virtual Web Platform - DOM Implementation Registry 
 *
 * This class provides the DOM Implementation Registry
 * 
 * @package VWP
 * @subpackage Libraries.DOM  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License   
 */

class VDOMImplementationRegistry extends VObject
{

	/**
	 * DOM Implementation Registry Instance
	 * 
	 * @var VDomImplementationRegistry $_instance DOM Implementation Registry
	 * @access private
	 */
	
	static $_instance;
	
	/**
	 * Sources 
	 */
	
	static $_sources;
	
	/**
	 * Get DOM Implementation
	 * 
	 * @todo Implement VDomImplementationRegistry::getDOMImplementation()
	 * @param string $feature Feature
	 * @return VDomImplementation DOM Implementation
	 * @access public
	 */
	
    function &getDOMImplementation($feature) 
    {
    	if (!isset(self::$_sources)) {
    		$this->loadSources();
    	}
    	
    	$impl = null;
    	
    	foreach(self::$_sources as $id=>$ob) 
    	{
    	   $impl = $ob->getDOMImplementation($feature);
    	   if ($impl !== null) {
    	       return $impl;
    	   }
    	       	
    	}
    	
        return $impl;
    }
    
	/**
	 * Get DOM Implementation List
	 * 
	 * @todo Implement VDomImplementationRegistry::getDOMImplementationList()
	 * @param string $feature Feature
	 * @return VDomDOMImplementationList DOM Implementations
	 * @access public
	 */
    
    function &getDOMImplementationList($feature) 
    {
        $domil = new VDOMImplementationList;
        return $domil;
    }
    
    /**
     * Get DOM Implementation Registry
     * 
     * Note: This method is DOM compliant but not specifically
     *       established by the DOM Specification.  
     *       The DOM specification specifically allows
     *       the structure of the DOM Implementation Registry
     *       to be defined by the implementation. Conforming
     *       applications should test for the existance of
     *       this class beforehand and only access it via 
     *       the static context of VDomImplementationRegistry::getInstance().
     *       
     * @return VDomImplementationRegistry;
     * @access public
     */
    
    public static function &getInstance() 
    {
    	if (!isset(self::$_instance)) {
    		self::$_instance = new VDOMImplementationRegistry;
    	}
        return self::$_instance;	
    }
    
    /**
     * Register Implementation Source
     * 
     * @param string $id Implementation ID
     * @param string $library Library providing DOMImplementationSource 
     * @param string $className DOMImplementationSource Class Name
     * @return boolean|object True on success, error or warning otherwise
     * @access public
     */
    
    public function registerImplementationSource($id,$library,$className) 
    {
    	
        $id = preg_replace( '/[^A-Z0-9_\\.]/i', '', (string)$id );
    
    	if (empty($id)) {
    		return VWP::raiseWarning('Invalid ID',__CLASS__,null,false);
    	}
    	
    	$settings = array(
    	  "library"=>(string)$library,
    	  "source_class_name" => (string)$className,
    	);
    	
    	$regkey = 'SOFTWARE\\VNetPublishing\\VWP\\ImplementationSources\\'.$id;
    	
        $localMachine = & Registry::LocalMachine();  
  
        $result = Registry::RegCreateKeyEx($localMachine,
                              $regkey,
                              0,
                              '',
                              0,
                              0,
                              0,
                              $registryKey,
                              $result); 
                              
        if (!VWP::isWarning($result)) {
            $result = true;
            foreach($settings as $key=>$val) {
                $sresult= Registry::RegSetValueEx($registryKey,
                           $key,
                           0, // reserved 
                           REG_SZ, // string
                           $val,
                           strlen($val)); 
                if (VWP::isWarning($sresult)) {
                    $result = $sresult;                            
                }  
            }
   
            Registry::RegCloseKey($registryKey);
            Registry::RegCloseKey($localMachine);
            if ($result === true) {
                self::$_sources = null;
            }
            return $result;
        }
  
        Registry::RegCloseKey($localMachine);
        return $result;     	    	    	
    }
    
    /**
     * Unregister Implementation Source
     * 
     * @todo Implement VDOMImplementationRegistry::unregisterImplementation()
     * @param string $id Unique implementation identifier
     * @access public
     */
    
    public function unregisterImplementationSource($id)
    {
    	return VWP::raiseWarning('Feature Not Implemented!');
    }
    
    /**
     * Load Implementation Sources
     * 
     */
    
    public function loadSources() 
    {
    	self::$_sources = array();
    	
    	$baseKey = "SOFTWARE\\VNetPublishing\\VWP\\ImplementationSources";
    	
    	$localMachine = & Registry::LocalMachine();
    	$hKey = null;
    	
    	$err = Registry::RegOpenKeyEx($localMachine,
                        $baseKey,
                        0,
                        null,
                        $hKey);
        if ($err != ERROR_SUCCESS) {
        	return;
        }

        $index = 0;
        
        $lpReserved = 0;                
        $lpName = '';
        $lpClass = null;
        $lpftLastWriteTime = '';
        
        $lpcClass = 255;
        $lpcName = 255;
        
        $err = Registry::RegEnumKeyEx($hKey,$index++,$lpName,$lpcName,$lpReserved,$lpClass,$lpcClass,$lpftLastWriteTime);
        
        while($err === ERROR_SUCCESS) {
            
        	$implKey = $baseKey . "\\" . $lpName;
                    	
            $result = Registry::RegOpenKeyEx($localMachine,
                        $implKey,
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
                
                if (isset($data['library']) && isset($data['source_class_name'])) {
                    if (!class_exists($data['source_class_name'])) {
                        VWP::requireLibrary($data['library']);	
                    }
                    if (class_exists($data['source_class_name'])) {
                        self::$_sources[$lpName] = new 	$data['source_class_name'];
                    }                     	
                }
            }         	
        	Registry::RegCloseKey($registryKey);
        	
            $lpcClass = 255;
            $lpcName = 255;        	
            $err = Registry::RegEnumKeyEx($hKey,$index++,$lpName,$lpcName,$lpReserved,$lpClass,$lpcClass,$lpftLastWriteTime);
        }
        
        Registry::RegCloseKey($localMachine);
    }
    
    // end class VDomImplementationRegistry
}
