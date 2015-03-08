<?php

/**
 * Virtual Web Platform - MySQL Query Filter
 *  
 * This file provides the MySQL Query Filter  
 * 
 * @package VWP
 * @subpackage Libraries.DBI.Drivers.MySQL  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License 
 */


/**
 * Virtual Web Platform - MySQL Query Filter
 *  
 * This class provides the MySQL Query Filter  
 * 
 * @package VWP
 * @subpackage Libraries.DBI.Drivers.MySQL 
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License 
 */


class VMysqlQueryFilter extends VObject 
{

	/**
	 * Groups
	 * 
	 * @var array $_groups Filter Groups
	 * @access private
	 */
		
	protected $_groups = array();
	
	/**
	 * Base Group
	 * 
	 * @var string $_basegroup
	 * @access private
	 */
	
	protected $_basegroup = null;
	
	/**
	 * Recursion Check
	 * 
	 * @var integer $_depth
	 * @access private
	 */
	
	static $_depth = 0;
	
	/**
	 * Add Filter Group
	 * 
	 * @param string $id Group ID
	 * @param string $connector Logic Connector
	 * @param boolean $base Base group
	 * @return boolean|object True on success, error or warning otherwise
	 * @access public
	 */
	
	public function addGroup($id,$connector,$base) 
	{
		if (isset($this->_groups[$id])) {
			return VWP::RaiseWarning('Duplicate Filter group!',__CLASS__,null,false);
		}		
		$connector = strtoupper($connector);
		
		switch($connector) {
			case "OR":
				break;
			default:
				$connector = "AND";			
		}
		
		
		$group = new stdClass;
		
		if ($base) {
			if (isset($this->_basegroup) && isset($this->groups[$this->_basegroup])) {
			    $this->groups[$this->_basegroup]->base = false;	
			} 
			$this->_basegroup = $id;
		}
		
		$group->base = $base ? true : false;
		$group->id = $id;
		$group->connector = $connector;
		$group->conditions = array();
		
		$this->_groups[$id] = $group;
		return true;
	}
	
	/**
	 * Add Condition to Filter Group
	 * 
	 * @param string $group Group ID
	 * @param string $operator Operator
	 * @param array $left Left Condition value as array(value,conditionvaluetype)
	 * @param array $right Right Condition value as array(value,conditionvaluetype)
	 * @param unknown_type $right
	 * @return boolean|object True on success, error or warning otherwise	 
	 */
	
	public function addCondition($group,$operator,$left,$right) 
	{
		if (!isset($this->_groups[$group])) {
			return VWP::RaiseWarning('Filter group not found!',__CLASS__,null,false);
		}
		$cond = new stdClass();
		
		$cond->operator = $operator;
		$cond->left_value = $left[0];
		$cond->left_type = $left[1];
		$cond->right_value = $right[0];
		$cond->right_type = $right[1];
		$this->_groups[$group]->conditions[] = $cond;
		return true;		
	}
	
	/**
	 * Decode Condition
	 * 
	 * @param object $cond Condition
	 * @param object $driver SQL Database Driver
	 * @access private
	 */
	
	function _decodeCond(&$cond,&$driver) 
	{								
		switch($cond->right_type) {
			case "field":				
				$right = $driver->nameQuote($cond->right_value);
				if (is_array($cond->right_value)) {
					$right = $driver->nameQuote($cond->right_value[0]) . '.' . $driver->nameQuote($cond->right_value[1]);
				} else {
				    $right = $driver->nameQuote($cond->right_value);
				}								
				break;
			case "group":				
				if (isset($this->_groups[$cond->right_value])) {					
					self::$depth++;
					if (self::$depth > 20) {											
						return VWP::raiseWarning('Recursion error!',__CLASS__,null,false);
					}
					
					$txt = $this->_decode($this->_groups[$cond->right_value],$driver);
					if (VWP::isWarning($txt)) {						
						return $txt;
					}
					$result =  '(' .  $txt . ')';
					self::$depth--;
					return $result;					
				} else {
					return 'FALSE';
				}
				break;
			default:				
			    $right = $driver->quote($cond->right_value);			
		}		
				
		switch($cond->left_type) {
			case "field":
				if (is_array($cond->left_value)) {
					$left = $driver->nameQuote($cond->left_value[0]) . '.' . $driver->nameQuote($cond->left_value[1]);
				} else {
				    $left = $driver->nameQuote($cond->left_value);
				}
				break;
			case "group":
				if (isset($this->_groups[$cond->left_value])) {					
					self::$depth++;
					if (self::$depth > 20) {						
						return VWP::raiseWarning('Recursion error!',__CLASS__,null,false);
					}
					
					$txt = $this->_decode($this->_groups[$cond->left_value],$driver);
					if (VWP::isWarning($txt)) {						
						return $txt;
					}
					$result =  '(' .  $txt . ')';
					self::$depth--;
					return $result;					
				} else {
					return 'FALSE';
				}
				break;
			default:
			    $left = $driver->quote($cond->left_value);			
		}

		$ret = 'FALSE';
		switch ($cond->operator) {
			case "!":
				$ret = 'NOT ' . $right;
				break;				
			case "NULL":
			    $ret = $left . ' IS NULL';
				break;
			case "NOT NULL":
				$ret =  $left . ' IS NOT NULL';
				break;
			case "<>":
			case "!=":
				$ret = $left . ' != ' . $right;
				break;				
		    case "<":
		    case ">":
		    case "<=":
		    case ">=":
		    	$ret = $left . ' ' . $cond->operator . ' ' . $right;			
		    	break;
			default:
				$ret = $left . ' = ' . $right; 			
		}		
		return $ret;		
	}
	
	/**
	 * Decode Condition Group
	 * 
	 * @param object $group Filter Group
	 * @param object $driver SQL Database Driver
	 * @return string SQL encoded filter group
	 * @access private
	 */
	
	function _decode(&$group,&$driver) 
	{
			
		$c = array();
		foreach($group->conditions as $cond) {			 
			 $txt = $this->_decodeCond($cond,$driver);
			 if (VWP::isWarning($txt)) {
			 	return $txt;
			 }			 
			 
			 $c[] = $txt;
		}
		if (count($c) < 1) {
			return 'TRUE';
		}
		return implode(' ' . $group->connector. ' ',$c);
	}
	
	/**
	 * Get filter as SQL
	 * 
	 * @param object $driver SQL Database Driver
	 * @access public
	 */
	
	function toSQL(&$driver) 
	{
		
		
		 self::$_depth = 0;
		 $sql = '';
		 
	     if (!isset($this->_basegroup)) {	     	
	     	return $sql;
	     }
	     
	     if (!isset($this->_groups[$this->_basegroup])) {	     	
	     	return $sql;
	     }
	     	     	     
	     $sql = $this->_decode($this->_groups[$this->_basegroup],$driver);
	     	     
	     return $sql;
	}
	
	/**
	 * Clear Filter Settings
	 * 
	 * @access public
	 */
	
	public function clear() {
		$this->_groups = array();
		$this->_basegroup = '';	
	}
	
	// end class VMysqlQueryFilter
}
