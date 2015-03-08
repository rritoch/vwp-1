<?php

/**
 * Content Article Widget Router
 *  
 * @package VWP.Content
 * @subpackage Widgets.Article
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License
 */ 

/**
 * Content Article Widget Router
 *  
 * @package VWP.Content
 * @subpackage Widgets.Article
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License
 */ 

class Content_VRoute_Article extends VRoute 
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
        if (isset($vars['article'])) {
        	$result[] = $vars['article'];
        	
        	$article =& $this->getModel('article');
        	if (!VWP::isWarning($article)) {
        		$load = $article->load($vars['article']);
        		if (!VWP::isWarning($load)) {
        			if (!empty($article->filename)) {
        				$result[] = $article->filename;
        			}
        		}
        	}
        	
        	unset($vars['article']);
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
    		$vars['article'] = $segments[0];
    		while(count($segments) > 0) {
    			array_pop($segments);
    		}    		
    	}    	
    	return $vars;
    }
    
    // end class Content_VRoute_Article
}
