<?php

/**
 * VWP - DBI Query Type Tables
 *  
 * This file provides the tables type for DBI queries        
 * 
 * @package VWP
 * @subpackage Libraries.DBI.Types.Query  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

/**
 * VWP - DBI Query Type Tables
 *  
 * This class provides the tables type for DBI queries        
 * 
 * @package VWP
 * @subpackage Libraries.DBI.Types.Query  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

class VDBIQueryType_Tables extends VObject 
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
	 * Helper
	 * 
	 * @var VDBI_QueryHelper $_helper Query Helper
	 * @access private
	 */
	
	protected $_helper;	
	
    /**
     * Get Table Node
     * 
     * @param string $tableId
     * @return object Table DOM Node on success, null if not found, error or warning otherwise
     * @access private
     */
	
	function _getTableNode($tableId) 
	{
		$queryTablesNode = $this->_helper->_getCoreNode('tables');
		if (VWP::isWarning($queryTablesNode)) {
			return $queryTablesNode;
		}
		
		$nodeList = $queryTablesNode->getElementsByTagNameNS(VDBI_Query::NS_QUERY_1_0,'table');
		$tableNode = null;
		
		for($idx=0;$idx<$nodeList->length;$idx++) {
			if ($tableId == (string)$nodeList->item($idx)->getAttribute('alias')) {
				$tableNode = $nodeList->item($idx);
			}
		}
		
		return $tableNode;
	}	
	
	/**
	 * Get Table Order
	 * 
	 * @return array|object Table ID list on success, error or warning otherwise
	 * @access public
	 */	

	function getOrder() 
	{
		if (!isset($this->_data)) {
			
	        $queryTablesNode = $this->_helper->_getCoreNode('tables');
	        if (VWP::isWarning($queryTablesNode)) {
	            return $queryTablesNode;	
	        }
	        $this->_data = array();
	        
	        $tables = array();
	        
	        $tableList = $queryTablesNode->getElementsByTagNameNS(VDBI_Query::NS_QUERY_1_0,'table');
	        for($tidx = 0; $tidx < $tableList->length; $tidx++) {
	        	$tableNode = $tableList->item($tidx);
	        	
	        	$nameNodes = $tableNode->getElementsByTagNameNS(VDBI_Query::NS_QUERY_1_0,'name');
	        	
	        	if ($nameNodes->length > 0) {
	            	$alias = (string)$tableNode->getAttribute('alias');
	            	$tables[$alias] = array();
	            	$tables[$alias]['datasource'] = (string)$tableNode->getAttribute('datasource');	         	
    	        	$tables[$alias]['fields'] = array();    	        	
	        	    $tables[$alias]['name'] = $this->_helper->_resolveValue($nameNodes->item(0));	        		        	
	        	    $fieldList = $tableNode->getElementsByTagNameNS(VDBI_Query::NS_QUERY_1_0,'field');
	        	    $len = $fieldList->length;	        	    
	        	    for($fidx = 0; $fidx < $len; $fidx++) {
	        	        $fieldNode = $fieldList->item($fidx);
	        	        $tables[$alias]['fields'][(string)$fieldNode->getAttribute('alias')] = $this->_helper->_resolveValue($fieldNode,false);	
	        	    }
	        	} 
	        }
	        $this->_data = $tables;	        	        
		}
		
		return array_keys($this->_data);		
	}	
	
	/**
	 * Get Table Info
	 * 
	 * @param string $tableId Table Id
	 * @access public
	 */
	
	function getInfo($tableId) 
	{
	    $tableList = $this->getOrder();
	    if (VWP::isWarning($tableList)) {
	    	return $tableList;
	    }
	    
	    if (!in_array($tableId,$tableList)) {
	    	return VWP::raiseWarning('Table not found!',__CLASS__,null,false);
	    }
	    
	    $table_info = $this->_data[$tableId];
	    unset($table_info['fields']);
	    return $table_info;	    
	}
	
	/**
	 * set Table Order
	 * 
	 * @param array $table_list Table list
	 * @return boolean|object True on success, error or warning otherwise
	 * @access public
	 */		
	
	function setOrder($table_list) 
	{
						
        $cfgNode = $this->_helper->_getCfgNode();
	    if (VWP::isWarning($cfgNode)) {
        	return $cfgNode;
        }
        
		$oldGroup = $this->_helper->_getCoreNode('tables');
        if (VWP::isWarning($oldGroup)) {
        	return $oldGroup;
        }
        
		$result = $this->getOrder(); // cache		
		if (VWP::isWarning($result)) {
			return $result;
		}		
		
		// Move listed fields in requested order
		$newGroup = $this->_helper->getDomDocument()->createElementNS(VDBI_Query::NS_QUERY_1_0,'tables');
        		
		foreach($table_list as $tableID) {
			$tableNode = $this->_getTableNode($tableID);
			if (!($tableNode === null || VWP::isWarning($tableNode))) {
            	$newGroup->appendChild($oldGroup->removeChild($tableNode));			
			}
		}

		// Move unlisted fields in original order
		$rest = $oldGroup->getElementsByTagNameNS(VDBI_Query::NS_QUERY_1_0,'table');
		
		$tmp = array();
		for($idx=0;$idx < $rest->length; $idx++) {
		    $tmp[] = $oldGroup->item($idx);	
		}
		foreach($tmp as $node) {
			$newGroup->appendChild($oldGroup->removeChild($node));
		}
				
        // Register new group
        
		$cfgNode->replaceChild($newGroup,$oldGroup);
		
		$this->_data = null;
		
		$result = $this->getOrder(); // cache		
		if (VWP::isWarning($result)) {
			return $result;
		}
						    
	    // success!
	    return true; 
		
	}	

	/**
	 * Add Table
	 * 
	 * @param string $alias Table ID
	 * @param string $name Table Name
	 * @return boolean|object True on success, error or warning otherwise
	 * @access public
	 */		
	
	function addTable($alias,$name) 
	{
		$tableList = $this->getOrder();
		if (VWP::isWarning($tableList)) {
			return $tableList;
		}
		
		if (in_array($alias,$tableList)) {
			return VWP::raiseWarning('Duplicate table alias',__CLASS__,null,false);
		}
		
		$tableListNode = $this->_helper->_getCoreNode('tables');
		if (VWP::isWarning($tableListNode)) {
			return $tableListNode;
		}		
				
		$source = $this->_helper->datasources->getDatabaseAliasByIndex(0);

	    $source = $this->_helper->datasources->getDatabaseAliasByIndex(0);
	    $tables = $this->_data;
	    
	    $newTable = $this->_helper->getDOMDocument()->createElementNS(VDBI_Query::NS_QUERY_1_0,'table');
	    $newTable->setAttribute('alias',$tableId);	    	   
	    $newTable->setAttribute('datasource',$source);
	    	    	    
   	    $nameNode = $this->_helper->getDOMDocument()->createElementNS(VDBI_Query::NS_QUERY_1_0,'name'); 
	    $text = $this->_helper->getDOMDocument()->createTextNode($tables[$tableId]['name']);
	    $nameNode->appendChild($text);
	    $newTable->appendChild($nameNode);		
		
		
		$tables[$alias] = array(
		    'name'=>$name,
		    'source'=>$source,
		    'fields'=>array()
		);
		$this->_data = $tables;
		return true;		
	}
	
	/**
	 * Remove Table
	 * 
	 * @param string $tableId Table ID	 
	 * @return boolean|object True on success, error or warning otherwise
	 * @access public
	 */		
	
	function removeTable($tableId) 
	{
		
		$tableList = $this->getOrder();
		
		if (!in_array($tableId,$tableList)) {
			return true;
		}
		
		$tableNode = $this->_getTableNode($tableId);
		
		if (VWP::isWarning($tableNode)) {
			return $tableNode;
		}
		
        $tablesNode = $this->_helper->_getCoreNode('tables');
		if (VWP::isWarning($tablesNode)) {
			return $tableNode;
		}
        
        $tablesNode->removeChild($tableNode);
        $tables = $this->_data;                
        unset($tables[$alias]);
        $this->_data = $tables;       
        
        return true;		
	}
	
	/**
	 * List Report Fields
	 * 
	 * Note: If table ID is not provided this function
	 *       returns a list of field ID's
	 *       
	 * @param string $tableId Table ID
	 * @return array|object Report Fields on success, error or warning otherwise
	 * @access public
	 */		
	
	function getFields($tableId = null) 
	{
	    $tableList = $this->getOrder();
	    
	    if (VWP::isWarning($tableList)) {
	        return VWP::raiseWarning($tableList);
	    }

	    $fieldList = array();
	    if ($tableId === null) {
	    	$fields = $this->_data;	    	
	    	foreach($fields as $table_id=>$table_info) {
	    		$fieldList = array_merge($fieldList,array_keys($table_info['fields']));
	    	}                        
	    } else {
	    	if (in_array($tableId,$tableList)) {
	    		$fields = $this->_data;	    		
	    		return $fields[$tableId]['fields'];
	    	}
	    }
	    return $fieldList;	
	}	
	
	/**
	 * Set Report Fields
	 * 
	 * note: Report fields list must be indexed by field ID
	 * 
	 * @param array $fields Report Fields
	 * @param string $tableId Table Id
	 * @return boolean|object True on success, error or warning otherwise
	 */	
	
	function setFields($fields,$tableId) 
	{

		$tableList = $this->getOrder();
		
		if (!in_array($tableId,$tableList)) {
			return VWP::raiseWarning('Table alias not found',__CLASS__,null,false);
		}
		
		$tableNode = $this->_getTableNode($tableId);
		if (VWP::isWarning($tableNode)) {
			return $tableNode;
		}
		
		$groupNode = $this->_helper->_getCoreNode('tables');
	    if (VWP::isWarning($groupNode)) {
	    	return $groupNode;
	    }

	    $source = $this->_helper->datasources->getDatabaseAliasByIndex(0);
	    $tables = $this->_data;
	    
	    $newTable = $this->_helper->getDOMDocument()->createElementNS(VDBI_Query::NS_QUERY_1_0,'table');
	    $newTable->setAttribute('alias',$tableId);	    	   
	    $newTable->setAttribute('datasource',$source);
	    	    	    
   	    $nameNode = $this->_helper->getDOMDocument()->createElementNS(VDBI_Query::NS_QUERY_1_0,'name'); 
	    $text = $this->_helper->getDOMDocument()->createTextNode($tables[$tableId]['name']);
	    $nameNode->appendChild($text);
	    $newTable->appendChild($nameNode);
	    	    	    
	    foreach($fields as $fieldAlias=>$fieldName) {
	    	$fields[$fieldAlias] = (string)$fieldName;
	    	$node = $this->_helper->getDOMDocument()->createElementNS(VDBI_Query::NS_QUERY_1_0,'field',$fields[$fieldAlias]);
	    	$node->setAttribute('alias',$fieldAlias);
	    	$newTable->appendChild($node);	    	
	    }
	    
	    $groupNode->replaceChild($newTable,$tableNode);
	    	    
	    
	    $tables[$tableId]['fields'] = $fields;
	    $this->_data = $tables;
	    
	    return true;
	}	
	
	/**
	 * Class Constructor
	 * 
	 * @param VDBI_Query $query
	 * @access public
	 */
	
	function __construct($query) 
	{
		parent::__construct();
		$this->query =& $query;
		$this->_helper =& $query->getHelper();
	}
	
	// end class VDBIQueryType_Tables
}
