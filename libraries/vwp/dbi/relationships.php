<?php

/**
 * Virtual Web Platform - Database Table Relationships
 *  
 * This file provides database table relationship management
 * 
 * @package VWP
 * @subpackage Libraries.DBI  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License   
 */

/**
 * Virtual Web Platform - Database Table Relationships
 *  
 * This class provides database table relationship management
 * 
 * @package VWP
 * @subpackage Libraries.DBI  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License   
 */

class VDatabaseTableRelationships extends VType
{
	/**
	 * Table Links
	 * 
	 * @var array $_table_links
	 * @access private
	 */

	protected $_table_links = array();

	/**
	 * Get Relationship Logic 
	 * 
	 * @param string $left_table Left Table ID
	 * @param string $right_table Right Table ID
	 * @return string Logic Function
	 */
	
	function getLogic($left_table,$right_table) {		
		return isset($this->_table_links[$left_table][$right_table]['logic']) ? $this->_table_links[$left_table][$right_table]['logic'] : 'or';
	}
	
	/**
	 * Set Logic Connection
	 * 
	 * @param string $left_table Left table ID
	 * @param string $right_table Right table ID
	 * @param string $logic Logic Function	 
	 */
	
	function setLogic($left_table,$right_table, $logic) 
	{

	    if (!isset($this->_table_links[$left_table])) {
           $this->_table_links[$left_table] = array($right_table=>array('links'=>array()));
        }
      
        if (!isset($this->_table_links[$left_table][$right_table])) {
          $this->_table_links[$left_table][$right_table] = array('links'=>array());
        }
      
        if (!isset($this->_table_links[$left_table][$right_table]['links'])) {
         $this->_table_links[$left_table][$right_table]['links'] = array();
        }
								
	    $this->_table_links[$left_table][$right_table]['logic'] = $logic == 'and' ? 'and' : 'or';
    }
	
    /**
     * Get Field Links
     * 
     * @param string $left_table Left table ID
     * @param string $right_table Right table ID
     * @return array Field links
     * @access public
     */
    
	function getFieldLinks($left_table,$right_table) 
	{
	    $links = array();

	    if (isset($this->_table_links[$left_table][$right_table]['links'])) {
            for($idx = 0; $idx < count($this->_table_links[$left_table][$right_table]['links']);$idx++) { 	   
                $link = array(
  		           array('table'=>$this->_table_links[$left_table][$right_table]['links'][$idx][0][0],'field'=>$this->_table_links[$left_table][$right_table]['links'][$idx][0][1]),
  		           array('table'=>$this->_table_links[$left_table][$right_table]['links'][$idx][1][0],'field'=>$this->_table_links[$left_table][$right_table]['links'][$idx][1][1])       
                );
           
                $links[] = $link;
            }
	    }
	    return $links;
	}
	
	/**
     * Add Field Link
     * 
     * @param string $left_table Left table ID
     * @param string $right_table Right table ID
     * @param string $table1 Table for field 1
     * @param string $field1 Field 1
     * @param string $table2 Table for field 2
     * @param string $field2 Field 2
     * @return boolean|object True on success, error or warning otherwise     
     * @access public
     */
	
	
	function addFieldLink($left_table,$right_table,$table1,$field1,$table2,$field2) 
	{
		
		// _table_links[left_table][right_table][#][#]
		
		if (!isset($this->_table_links[$left_table])) {
           $this->_table_links[$left_table] = array($right_table=>array('links'=>array()));
      }
      
      if (!isset($this->_table_links[$left_table][$right_table])) {
          $this->_table_links[$left_table][$right_table] = array('links'=>array());
      }
      
      if (!isset($this->_table_links[$left_table][$right_table]['links'])) {
         $this->_table_links[$left_table][$right_table]['links'] = array();
      }
      
      $this->_table_links[$left_table][$right_table]['links'][] = array( array($table1,$field1),array($table2,$field2));
      		
		return true;
	}

	/**
	 * Remove field links
	 * 
	 * @param string $left_table Left table ID
	 * @param string $right_table Right table ID
	 * @return boolean|object True on success, error or warning otherwise
	 */
	
	function removeFieldLinks($left_table,$right_table) 
	{
		
		if (isset($this->_table_links[$left_table][$right_table])) {
          unset($this->_table_links[$left_table][$right_table]);    
          if (count(array_keys($this->table_links[$left_table])) < 1) {
              unset($this->table_links[$left_table]);
          }
      
      }	    
		return true;
	}
	
	// End class VDatabaseTableRelationships
}
