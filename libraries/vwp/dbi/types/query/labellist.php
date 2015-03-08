<?php

/**
 * VWP - DBI Query Label List Type
 *  
 * This file provides the Summary Options Type        
 * 
 * @package VWP
 * @subpackage Libraries.DBI.Types.Query  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

/**
 * VWP - DBI Query Summary Options Type
 *  
 * This class provides the Summary Options Type        
 * 
 * @package VWP
 * @subpackage Libraries.DBI.Types.Query  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

class VDBIQueryType_LabelList extends VObject
{
	
	/**
	 * Query
	 * 
	 * @var VDBI_Query Query
	 * @access private
	 */
	
	protected $query;	

	/**
	 * Helper
	 * 
	 * @var VDBI_QueryHelper $_helper Query Helper
	 * @access private
	 */
	
	protected $_helper;	
	
	/**
	 * List Labels
	 * 
	 * @return array Label list
	 * @access public
	 */
	
	function listLabels() 
	{
	    if (!isset($this->labels)) {
	    	$this->labels = array();
	    	$labelsNode = $this->_getCoreNode('labels');
	    	if (VWP::isWarning($labelsNode)) {
	    		return $labelsNode;
	    	}
	    	if ($labelsNode !== null) {
	    		$nodeList = $labelsNode->getElementsByTagNameNS(self::NS_QUERY_1_0,'label');
	    		for($idx = 0; $idx < $nodeList->length; $idx++) {
	    			$aliasNode = $nodeList->item($idx);
	    			$field = $aliasNode->getAttribute('ref');
	    			$field = $field ? $field : '';	    				    			
	    			$this->labels[$field] = $aliasNode->nodeValue;
	    		}
	    	} 	    	
	    }
        return array_keys($this->labels);
	}
	
	
	
	/**
	 * Get Label Value
	 * 
	 * @param string $label_id
	 * @param string $default Default Value
	 * @return string Label Value
	 * @access public
	 */
	
	function getLabel($label_id,$default = null) 
	{
        $labelList = $this->listLabels();
        if (VWP::isWarning($labelList)) {
        	return $labelList;
        }
	    if (in_array($label_id,$labelList)) {
	    	return $this->labels[$label_id];
	    }
	    return $default;
	}
	
	/**
	 * Set Label Value
	 * 
	 * @param string $label_id Label ID
	 * @param string $value Value to set
	 * @return boolean|object True on success, error or warning otherwise
	 * @access public
	 */
	
	function setLabel($label_id,$value) 
	{
				
		$labelsNode = $this->_makeCoreNode('labels');
		if (VWP::isWarning($labelsNode)) {
			return $labelsNode;
		}
		
		$aliasNode = null;
		$nodeList = $labelsNode->getElementsByTagNameNS(self::NS_QUERY_1_0,'alias');
    	for($idx = 0; $idx < $nodeList->length; $idx++) {
	    		
     		$field = $nodeList->item($idx)->getAttribute('ref');
	    	if ($field == $label_id) {
	    		$aliasNode = $nodeList->item($idx);
	    	}
	    }		
		
		if ($aliasNode === null) {
			if (empty($value)) {
				return true;
			}
			$newLabel = $this->_doc->createElementNS(self::NS_QUERY_1_0,'label',XMLDocument::xmlentities($value));
			$newLabel->setAttribute('ref',XMLDocument::xmlentities($label_id));
			$labelsNode->appendChild($newLabel);			
		} else {
	    	if (empty($value)) {
	    		$labelsNode->removeChild($aliasNode);
	    	} else {
	    	    $aliasNode->nodeValue = XMLDocument::xmlentities($value);
	    	}			
		}
		if (empty($value)) {
			unset($this->labels[$label_id]);
		} else {
		    $this->labels[$label_id] = $value;
		}
		return true;
	}
	
    /**
     * Class Constructor
     * 
     * @param unknown_type $query
     * @access public
     */
    
    function __construct($query) 
    {
    	$this->query = $query;
    	$this->_helper =& $query->getHelper();
    }	
	
    // end class VDBIQueryType_LabelList
}
