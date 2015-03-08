<?php

/**
 * DOM 3 Helper
 * 
 * @package VWP
 * @subpackage Libraries.DOM
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License 
 */

/**
 * DOM 3 Helper
 * 
 * @package VWP
 * @subpackage Libraries.DOM
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License 
 */


class VDomHelper extends VObject 
{
	/**
	 * getNextElementSibling
	 * 
	 * @param object $node Reference node
	 * @return VDomHelper Helper
	 */

	function getNextElementSibling($node) {
				
		$sibling = $node->nextSibling;
		while($sibling !== null) {
			if ($sibling->nodeType == XML_ELEMENT_NODE) {
				return $sibling;
			}
			$sibling = $sibling->nextSibling;
		}
		return null;		
	}
	
	/**
	 * Get Helper
	 * 
	 * @return VDomHelper Helper
	 * @access public
	 */
	
	public static function &getInstance() 
	{
	    static $domHelper;
	    if (!isset($domHelper)) {
	    	$domHelper = new VDomHelper;
	    }	    
	    return $domHelper;
	}
	
	// end class VDomHelper
}
