<?php

/**
 * VWP Registry Library
 * 
 * This file provides the LOCAL_MACHINE registry key.
 *   
 * @package VWP
 * @subpackage Libraries.System
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License
 */

/**
 * VWP Registry Library
 * 
 * This is the class for a LOCAL_MACHINE registry key.
 *   
 * @package VWP
 * @subpackage Libraries.System
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License
 */

 
 class HKEY_LOCAL_MACHINE extends HKEY 
 {
  
    /**
     * Data modified flag
     * 
     * @var true|false $dirty True if modified or false otherwise
     * @access private
     */
                 
    var $dirty;
  
    /**
     * Close registry key
     * 
     * This function should not be called directly!
     *       
     * @access private
     */           
  
    function Close() 
    {
        if (isset($this->dirty) && ($this->dirty)) {
            $source = $this->saveXML();
            $nl = "\n";
            $secure_prefix = '<' . '?php' . $nl
                   . '/**' . $nl
                   . ' * Secure Registry' . $nl
                   . ' *' . $nl
                   . ' * This file contains a secure registry file' . $nl
                   . ' *' . $nl
                   . ' * @package VWP' . $nl
                   . ' * @subpackage Registries' . $nl
                   . ' * @author Ralph Ritoch <rritoch@gmail.com> ' . $nl                   
                   . ' * @link http://www.vnetpublishing.com' . $nl
                   . ' */' . $nl
                   . ' die("Access Denied!");' . $nl
                   . '//@>@';
    
            @ file_put_contents(VREG_LOCAL_MACHINE,$secure_prefix . $source);
            unset($this->dirty);
        }   
    }
  
    /**
     * Class Constrcutor
     * 
     * @access private
     */
              
    function __construct() 
    {
        parent::__construct();
        $this->doc = new DomDocument();
        VWP::noWarn();   
        $source = @ file_get_contents(VREG_LOCAL_MACHINE);
        VWP::noWarn(false);
        if ($source !== false) {
            $prefix = '<' . '?php';
            $postfix = '//@>@';
    
            if (substr($source,0,5) == $prefix) {
                $tmpa = explode($postfix,$source);
                array_shift($tmpa);
                $source = implode($postfix,$tmpa);
            }        
        }
   
        if ($source === false) {
            $nl = "\n";
            $source = '<' . '?xml version="1.0" encoding="utf-8" ?' . '>'. $nl;
            $source .= '<vwp_registry></vwp_registry>';
            $this->dirty = true;   
        }
   
        parent::loadXML($source);
        $nodeList = $this->doc->getElementsByTagName("vwp_registry");
        $this->rootNode = $nodeList->item(0);     
    }
    
    // end class HKEY_LOCAL_MACHINE
} 
