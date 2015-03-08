<?php
 
/**
 * Content Category Widget Router
 *  
 * @package VWP.Content
 * @subpackage Widgets.Category
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License
 */ 

/**
 * Content Category Widget Router
 *  
 * @package VWP.Content
 * @subpackage Widgets.Category
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License
 */ 

class Content_VRoute_Category extends VRoute 
{
	
	/**
	 * Get Model
	 * 
	 * @param string $id Model Id
	 * @return object Model
	 * @access public
	 */
		
	function &getModel($id) 
	{
		static $_models = array();
		if (!isset($_models[$id])) {
			$lib = dirname(dirname(dirname(__FILE__))).DS.'models'.DS.$id.'.php';			
			if (v()->filesystem()->file()->exists($lib)) {
		        require_once($lib);
			}
			$className = 'Content_Model_'.ucfirst($id);
			if (class_exists($className)) {
				$_models[$id] = new $className;
			} else {
				$_models[$id] = VWP::raiseWarning('Model not found!');
			}	
		}
		return $_models[$id];		
	}

	/**
	 * Encode URL variables
	 * 
	 * @param array $vars URL Variables
	 * @return array URL Segments
	 * @access public
	 */
		
    function encode(&$vars) 
    {
    	    	
    	$result = array();
        if (isset($vars['category'])) {
        	$result[] = $vars['category'];
        	
        	$category =& $this->getModel('category');
        	if (!VWP::isWarning($category)) {
        		$load = $category->load($vars['category']);
        		if (!VWP::isWarning($load)) {
        			if (!empty($category->filename)) {
        				$result[] = $category->filename;
        			}
        		}
        	}
        	
        	unset($vars['category']);
        }                    	
    	        
        return $result;
        	
    }	
    
    /**
     * Decode URL Segments
     * 
     * @param array $segments URL Segments
     * @return array URL Variables
     * @access public
     */
        
    function decode(&$segments) 
    {    	    	    	    
    	$vars = array();
    	if (count($segments) > 0) {
    		$vars['category'] = $segments[0];
    		while(count($segments) > 0) {
    			array_pop($segments);
    		}    		
    	}    	
    	return $vars;
    }
    
    // end class Content_VRoute_Category
}
