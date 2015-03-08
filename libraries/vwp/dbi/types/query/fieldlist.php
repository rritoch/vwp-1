<?php

/**
 * VWP - DBI Query Field List Type
 *  
 * This file provides the Field List Type        
 * 
 * @package VWP
 * @subpackage Libraries.DBI.Types.Query  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

/**
 * VWP - DBI Query Field List Type
 *  
 * This class provides the Field List Type        
 * 
 * @package VWP
 * @subpackage Libraries.DBI.Types.Query  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

class VDBIQueryType_FieldList extends VObject
{

	/**
	 * Query 
	 *
	 * @var VDBI_Query $query
	 * @access private
	 */
	
	protected $query;
	
	/**
	 * Data
	 * 
	 * @var array $_data
	 * @access private
	 */
	
	protected $_data;

	/**
	 * Root Tag Name
	 * 
	 * @var unknown_type
	 */
	
	protected $_rootTagName;

	/**
	 * Helper
	 * 
	 * @var VDBI_QueryHelper $_helper Query Helper
	 * @access private
	 */
	
	protected $_helper;	
	
	/**
	 * Get List
	 * 
	 * @return array|object Fields on success, error or warning otherwise
	 * @access public
	 */
	
	function getList() 
	{
	    if (!isset($this->_data)) {

            $listNode = $this->_helper->_getCoreNode($this->_rootTagName);
            if (VWP::isWarning($listNode)) {
            	return $listNode;
            }
            
            $data = array();
            if ($listNode !== null) {
                $nodeList = $listNode->getElementsByTagNameNS(VDBI_Query::NS_QUERY_1_0,'field');
                $len = $nodeList->length;
                for($idx=0;$idx < $len; $idx++) {
                	$data[] = $this->_helper->_resolveValue($nodeList->item($idx));
                }            	            
            }
            $this->_data = $data;                        
	    }
	    return $this->_data;	
	}
			
	/**
	 * Set Groupings
	 * 
	 * @param array $fields Grouping Fields
	 * @return boolean|object True on success, error or warning otherwise
	 * @access public
	 */
	
	function setList($fields) 
	{
		$cfgNode = $this->_getCfgNode();
	    if (VWP::isWarning($cfgNode)) {
	    	return $cfgNode;
	    }		
		
		$listNode = $this->_helper->_makeCoreNode($this->_rootTagName);
	    if (VWP::isWarning($listNode)) {
	    	return $listNode;
	    }
	    
	    $doc =& $this->_helper->getDOMDocument();
	    
	    $newGroup = $doc->createElementNS(VDBI_Query::NS_QUERY_1_0,$this->_rootTagName);
	    for($idx = 0; $idx < count($fields); $idx++) {
	    	$fields[$idx] = (string)$fields[$idx];	    	
	    	$node = $doc->createElementNS(VDBI_Query::NS_QUERY_1_0,'field');
	    	$this->_helper->_setResolveValue($node,$fields[$idx]);
	    	$newGroup->appendChild($node);
	    }    
	    
	    $cfgNode->replaceChild($newGroup,$groupNode);
	    $this->_data = $fields;
	    return true;
	}	

	/**
	 * Class Constructor
	 * 
	 * @param VDBI_Query $query
	 * @param string $rootTagName Root Tag Name
	 * @access public
	 */
	
	function __construct($query,$rootTagName) 
	{
		parent::__construct();
		$this->query =& $query;
		$this->_rootTagName = $rootTagName;
		$this->_helper =& $query->getHelper();
	}	

	// end class VDBIQueryType_FieldList
}
	