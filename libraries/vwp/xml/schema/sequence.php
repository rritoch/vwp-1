<?php

/**
 * Virtual Web Platform - Schema Sequence
 *  
 * This file provides XML Schema Sequence support
 *        
 * @package VWP
 * @subpackage Libraries.XML.Schema  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

/**
 * Virtual Web Platform - Schema Sequence
 *  
 * This class provides XML Schema Sequence support
 *        
 * @package VWP
 * @subpackage Libraries.XML.Schema  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

class VSchema_Sequence extends VObject
{
	/**
	 * Sequence Node
	 * 
	 * @var DOMNode Sequence node
	 * @access public
	 */
	
	protected $_node = null;

	/**
	 * Get Sequence Length
	 * 
	 * @return integer Length
	 * @access public
	 */
	
	public function getLength()
	{
		$len = 0;
		
		$l = $this->_node->childNodes->length;
		for($idx=0;$idx<$l;$idx++) {
			$c = $this->_node->childNodes->item($idx);
			if ($c->nodeType == XML_ELEMENT_NODE && $c->localName == 'element') {
				$len++;
			}
		}		
		return $len;
	}
	
	/**
	 * Get Sequence Item
	 * 
	 * @param integer $index
	 * @return DOMElement Sequence Item
	 * @access public
	 */
	
	public function getItem($index)
	{
	    $item = null;

		$l = $this->_node->childNodes->length;
		$ptr = 0;
		for($idx=0;$idx<$l;$idx++) {
			$n = $this->_node->childNodes->item($idx);
			if ($n->nodeType == XML_ELEMENT_NODE && $n->localName == 'element') {
				if ($ptr == $index) {
					return  $n;
				}
				$ptr++;
			}
		}	    	    
	}
	
	/**
	 * Class Constructor
	 * 
	 * @param object $sequenceNode Sequence node
	 * @access public
	 */
	
	function __construct($sequenceNode) 
	{
	    parent::__construct();
	    $this->_node = $sequenceNode;	
	}
	
	// end class VSchema_Sequence
}
