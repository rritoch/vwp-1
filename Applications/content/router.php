<?php

/**
 * Content Manager Application Router 
 *    
 * @package    VWP.Content
 * @subpackage Base
 * @author Ralph Ritoch <rritoch@gmail.com> 
 * @copyright (c) Ralph Ritoch 2011 - All rights reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License
 */

/**
 * Content Manager Application Router 
 *    
 * @package    VWP.Content
 * @subpackage Base
 * @author Ralph Ritoch <rritoch@gmail.com> 
 * @copyright (c) Ralph Ritoch 2011 - All rights reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License 
 */

class ContentVRoute extends VRoute 
{

	/**
	 * Encode URL Variables
	 * 
	 * @param array $vars URL Variables
	 * @return array URL Segments
	 * @access public
	 */
		
    function encode(&$vars) 
    {
    	$result = array();
                
        if (isset($vars['widget'])) {
        	$result[] = $vars['widget'];
        	unset($vars['widget']);
        }
        
        return $result;
        	
    }	
    
    /**
     * Decode URL Segments
     * 
     * @param array Segments
     * @return array URL Variables
     * @access public
     */
    
    function decode(&$segments) 
    {    	
    	$vars = array();
    
    	if (count($segments) > 0) {
    		$vars['widget'] = array_shift($segments);
    	}
    
    	return $vars;
    }
    
    // end class ContentVRoute
}
