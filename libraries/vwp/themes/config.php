<?php 

/**
 * Virtual Web Platform - Theme Configuration
 *  
 * This file provides theme selection support.   
 * 
 * @package VWP
 * @subpackage Libraries.Themes  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

/**
 * Virtual Web Platform - Theme Configuration
 *  
 * This class provides theme selection support.   
 * 
 * @package VWP
 * @subpackage Libraries.Themes  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

class VThemeConfig extends VObject 
{
	
	/**	 
	 * Get Default Theme
	 * 
	 * @static
	 * @param string $themeType Theme Type
	 * @access public
	 */
	
	public static function getDefaultTheme($themeType) 
	{
		$defaults = self::getDefaults();
		if (!VWP::isWarning($defaults)) { 
		    if (isset($defaults[$themeType])) {
			    return $defaults[$themeType];
		    }
		}
	    return 'default';	
	}
	

	/**	 
	 * Get Default Theme
	 * 
	 * @static
	 * @param string $themeType Theme Type
	 * @param string $themeId Theme Id
	 * @access public
	 */
	
    public static function setDefaultTheme($themeType,$themeId) 
    {
    	$defaults = array($themeType=>$themeId);
    	return self::setDefaults($defaults);    	
    }

    /**
     * Get Defaults Registry Key Name
     * 
     * @return string Defaults registry key name
     * @access public     
     */

    static function getDefaultsKey() {
        return "SOFTWARE\\VNetPublishing\\VWP\\Themes\\Defaults";
    }
  
    /**
     * Get default themes
     * 
     * @return array|object Configuration settings on success, error or warning otherwise
     * @access public
     */       
 
    static function getDefaults() 
    {
        $localMachine = & Registry::LocalMachine();
  
        $result = Registry::RegOpenKeyEx($localMachine,
                        self::getDefaultsKey(),
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

    /**
     * Save default themes
     * 
     * @param array $defaults Default themes indexed by type
     * @return true|object True on success, error or warning otherwise
     * @access public  
     */    
  
    static function setDefaults($defaults) 
    {
        $localMachine = & Registry::LocalMachine();  
  
        $result = Registry::RegCreateKeyEx($localMachine,
                              self::getDefaultsKey(),
                              0,
                              '',
                              0,
                              0,
                              0,
                              $registryKey,
                              $result); 
                              
        if (!VWP::isWarning($result)) {
            $result = true;
            foreach($defaults as $key=>$val) {
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

            return $result;
        }
  
        Registry::RegCloseKey($localMachine);
        return $result; 
    }
    
    // End VThemeConfig class
}