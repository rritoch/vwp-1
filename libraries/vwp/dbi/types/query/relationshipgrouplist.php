<?php

/**
 * VWP - DBI Query Relationship Group List Type
 *  
 * This file provides the Relationship Group List Type        
 * 
 * @package VWP
 * @subpackage Libraries.DBI.Types.Query  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

/**
 * VWP - DBI Query Relationship Group List Type
 *  
 * This class provides the Relationship Group List Type        
 * 
 * @package VWP
 * @subpackage Libraries.DBI.Types.Query  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

class VDBIQueryType_RelationshipGroupList extends VObject 
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
	 * Get DOM Report Table Relationship Node
	 * 
	 * @param string $group_id Relationship Group
	 * @return object Node on success, null if not found, error or warning otherwise
	 * @access private
	 */
	
	protected function _getRelationshipNode($group_id) 
	{

		$groupNode = $this->_helper->_getCoreNode('table_relationships');
	    if (VWP::isWarning($groupNode)) {
	    	return $groupNode;
	    }

        if ($groupNode === null) {
        	return null; // short circuit
        }
	    	    
        $nodeList = $groupNode->getElementsByTagNameNS(VDBI_Query::NS_QUERY_1_0,'relationship');
	    $node = null;
	            
        if ($nodeList->length > 0) {
        	for($idx = 0; $idx < $nodeList->length; $idx++) {
        		if ($group_id == (string)$nodeList->item($idx)->getAttribute('name')) {
        		    $node = $nodeList->item($idx);
        		}
        	}          	
        }
        return $node;	
	}	
	
	/**
	 * Make DOM Report Relationship Group Node
	 * 
	 * @param string $group_id Group ID
	 * @access private
	 */	
	
	protected function _makeRelationshipNode($group_id) 
	{
		
		$newNode = $this->_getRelationshipNode((string)$group_id);
		if ($newNode !== null) {
			return $newNode;
		}		

		$tableRelationshipsNode = $this->_helper->_makeCoreNode('table_relationships');
		
		if (VWP::isWarning($tableRelationshipsNode)) {
			return $tableRelationshipsNode;
		}
		
		$newNode = $this->_helper->getDOMDocument()->createElementNS(VDBI_Query::NS_QUERY_1_0,'relationship');
		
		$newNode->setAttribute('name',(string)$group_id);
		$newNode->setAttribute('left_table','');
		$newNode->setAttribute('right_table','');
		
		$tableRelationshipsNode->appendChild($newNode);

        $table_relationships = $this->_data;
        
        $table_relationships[(string)$group_id] = array(
         'left_table'=>'',
         'right_table'=>'',
         'fields'=>array(),
        );

        $this->_data = $table_relationships;        
        return $newNode;		
		
	}

	/**
	 * List Relationship Groups
	 * 
	 * @return array|object Relationship Groups on success, error or warning otherwise
	 * @access public
	 */		
	
	function listGroups() 
	{

		if (!isset($this->_data)) {
			
			$relationshipsNode = $this->_helper->_getCoreNode('table_relationships');
            
            if (VWP::isWarning($relationshipsNode)) {
            	return $relationshipsNode;
            }
            
            $table_relationships = array();
            
            $nodeList = $relationshipsNode->getElementsByTagNameNS(VDBI_Query::NS_QUERY_1_0,'relationship');
            for($r = 0; $r < $nodeList->length; $r++) {
            	$relationshipNode = $nodeList->item($r);
            	$rname = (string)$relationshipNode->getAttribute('name');
            	$table_relationships[$rname] = array();
            	$table_relationships[$rname]['left_table'] = (string)$relationshipNode->getAttribute('left_table');
            	$table_relationships[$rname]['right_table'] = (string)$relationshipNode->getAttribute('right_table');            	
                $table_relationships[$rname]['fields'] = array();
                
                $groupList = $relationshipNode->getElementsByTagNameNS(VDBI_Query::NS_QUERY_1_0,'condition_group');
                for($cg = 0; $cg < $groupList->length; $cg++) {
                	
            	    $cgdata = $this->_helper->_unSerializeConditionGroup($groupList->item($cg));
            	                	                	    
            	    if (isset($cgdata[1]['base']) && $cgdata[1]['base']) {
            	    	$table_relationships[$rname]['logic'] = $cgdata[1]['logic'];
            	    	$fields = array();
            	    	foreach($cgdata[1]['conditions'] as $cond) {
            	    		
            	    		if (
            	    		   ($cond['op'] == '=') &&
            	    		   ($cond['value'][0][0] == 'field') &&
            	    		   ($cond['value'][1][0] == 'field')
            	    		) {
            	    			$table_relationships[$rname]['fields'][] = array($cond['value'][0][1],$cond['value'][1][1]); 
            	    		}
            	    	}            	    	     	
            	    }            	                	       
                }  
            }
            $this->_data = $table_relationships;
		}
		
		return array_keys($this->_data);
	}
	
	/**
	 * Get Relationship Group
	 * 
	 * @param string $group_id Group ID
	 * @return array|object Related fields on success, error or warning otherwise
	 * @access public
	 */		
	
	function getGroup($group_id) 
	{
        $groups = $this->listGroups();
        if (VWP::isWarning($groups)) {
        	return $groups;
        }
        if (!in_array($group_id,$groups)) {
        	return VWP::raiseWarning("Relationship Group '$group_id' not found!",get_class($this),null,false);
        }
        $table_relationships = $this->_data;
        return $table_relationships[$group_id];           		
	}
	
	/**
	 * Set Relationship Group
	 * 
	 * @todo Delete node if fields is null
	 * @param string $group_id Group ID
	 * @param array $fields Left fields
	 * @param string $logic Logic connector
	 * @return boolean|object True on success, error or warning otherwise
	 * @access public
	 */		
	
	function setGroup($group_id,$left_table,$right_table,$fields,$logic) 
	{
		$logic = $logic == 'and' ? 'and' : 'or';
		 
		// init
		$group_id = (string)$group_id;

		$tableRelationshipsNode = $this->_helper->_makeCoreNode('table_relationships');
		if (VWP::isWarning($tableRelationshipsNode)) {
			return $tableRelationshipsNode; 
		}
		
		$oldGroup = $this->_makeRelationshipNode($group_id);
		if (VWP::isWarning($oldGroup)) {
			return $oldGroup;
		}
		
		// define
		
		$newGroup = $this->_helper->getDOMDocument()->createElementNS(VDBI_Query::NS_QUERY_1_0,'relationship');
		$newGroup->setAttribute('name',$group_id);
		$newGroup->setAttribute('left_table',(string)$left_table);
		$newGroup->setAttribute('right_table',(string)$right_table);

		$condGroupNode = $this->_helper->getDOMDocument()->createElementNS(VDBI_Query::NS_QUERY_1_0,'condition_group');
		$condGroupNode->setAttribute('logic',$logic);
		$condGroupNode->setAttribute('base','true');
		$condGroupNode->setAttribute('alias',$group_id);		
		$newGroup->appendChild($condGroupNode);
		
		for($fidx=0;$fidx < count($fields);$fidx++) {
			
			$fields[$fidx][0] = (string) $fields[$fidx][0];
			$fields[$fidx][1] = (string) $fields[$fidx][1];
									
			$condNode = $this->_helper->getDOMDocument()->createElementNS(VDBI_Query::NS_QUERY_1_0,'condition');
			$condNode->setAttribute('operator','=');
			$condNode->setAttribute('left_value',$fields[$fidx][0]);
			$condNode->setAttribute('left_type','field');
			$condNode->setAttribute('right_value',$fields[$fidx][1]);
			$condNode->setAttribute('right_type','field');
			$condGroupNode->appendChild($condNode);						
		}
		
		// assign
		
		$tableRelationshipsNode->replaceChild($newGroup,$oldGroup);

		$table_relationships = $this->_data;
		$table_relationships[$group_id] = array(
		  'fields'=>$fields,
		  'left_table'=>(string)$left_table,
		  'right_table'=>(string)$right_table,
		  'logic'=>$logic,		  
		);
		$this->_data = $table_relationships;
		
	    // success!
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
	
	// end class VDBIQueryType_RelationshipGroupList
}
