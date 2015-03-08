<?php

/**
 * Search Content
 *  
 * This file provides the content search event         
 * 
 * @package VWP.Content
 * @subpackage Events.Search  
 * @author Ralph Ritoch 
 * @copyright Ralph Ritoch <rritoch@gmail.com>
 * @link http://www.vnetpublishing.com    
 */

VWP::RequireLibrary('content.search');

/**
 * Search Content
 *  
 * This class provides the content search event         
 * 
 * @package VWP.Content
 * @subpackage Events.Search  
 * @author Ralph Ritoch 
 * @copyright Ralph Ritoch <rritoch@gmail.com>
 * @link http://www.vnetpublishing.com    
 */

class ContentEventSearch extends VEvent
{
	
	function onScan($args) 
	{		
        return ContentSearch::scan($args);		
	}
	
	// end class ContentEventSearch
}

