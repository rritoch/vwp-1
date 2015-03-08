<?php

/**
 * VWP - DBI Query Filter Type
 *  
 * This file provides the query filter type        
 * 
 * @package VWP
 * @subpackage Libraries.DBI.Types.Query  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

/**
 * VWP - DBI Query Filter Type
 *  
 * This class provides the query filter type        
 * 
 * @package VWP
 * @subpackage Libraries.DBI.Types.Query  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

class VDBIQueryType_Filter extends VObject
{
	/**
	 * Base Condition
	 * 
	 * @var boolean Base condition
	 */
	
	protected $baseCondition = null;
	
	/**
	 * Rules
	 * 
	 * @var array Rules
	 */
	
	protected $rules = array();
	
	/**
	 * Add Condition
	 * 
	 * @param string $ruleId
	 * @param string $leftValue
	 * @param string $op
	 * @param string $rightValue
	 * @return boolean|object True on success, error or warning otherwise
	 */
	
	function addCondition($ruleId, $leftValue, $op, $rightValue) 
	{
		if (!is_array($leftValue)) {
			$leftValue = array('constant',$leftValue);
		}
		
		if (!is_array($rightValue)) {
			$rightValue = array('constant',$rightValue);
		}
		
		if (!isset($this->rules[$ruleId])) {
			return VWP::raiseWarning('Unknown Rule',__CLASS__,null,false);
		}

		$rules = $this->rules;
		$rules[$ruleId]['conditions'][] = array($leftValue,$op,$rightValue);
		$this->rules = $rules;
		return true;
	}
	
	/**
	 * List Conditions
	 *
	 * @param string $ruleId Rule Id
	 * @return array Conditions
	 * @access public
	 */
	
	function listConditions($ruleId) 
	{
		if (!isset($this->rules[$ruleId])) {
			return VWP::raiseWarning('Rule not found',__CLASS__,null,false);
		}		
	    return $this->rules[$ruleId]['conditions'];	
	}
	
	/**
	 * Add Rule 
	 * 
	 * @param string $ruleId Rule Id
	 * @param boolean $base Is Base Rule
	 * @param string $logic Logic Function
	 * @return boolean|object True on success, error or warning otherwise
	 */
	
	function addRule($ruleId,$base = false, $logic = null) 
	{
		switch($logic) {
			case "or":
				break;
			default:
				$logic = "and";
		}
		
		if (isset($this->rules[$ruleId])) {
			return VWP::raiseWarning('Duplicat Rule ID',__CLASS__,null,false);
		}
		
		if ($base) {
			if (isset($this->baseCondition)) {
			    return VWP::raiseWarning('Ambiguous base selection!',__CLASS__,null,false);	
			}			
			$this->baseCondition = $ruleId;
		}
		$rules = $this->rules; 
		
	    $rules[$ruleId] = array('logic'=>$logic,	                                   
	                                   "conditions"=> array());
	    $this->rules = $rules;
	    return true;	
	}
	
	/**
	 * Remove Rule
	 *
	 * @param string $ruleId
	 * @return boolean True on success, error or warning otherwise
	 */
	
	function removeRule($ruleId) 
	{
		if (isset($this->rules[$ruleId])) {
			if ($this->baseCondition == $ruleId) {
				unset($this->baseCondition);
			}
			$rules = $this->rules;
			unset($rules[$ruleId]);
			$this->rules = $rules;						
		}
		return true;
	}
	
	/**
	 * Get Logic Operator
	 * 
	 * @param string $ruleId Rule Id
	 * @return string|object Operator on success, error or warning otherwise
	 * @access public
	 */
	
	function getLogicOperator($ruleId) 
	{
		if (!isset($this->rules[$ruleId])) {
			return VWP::raiseWarning('Rule not found',__CLASS__,null,false);
		}

		return $this->rules[$ruleId]['logic'];
	}
	
	/**
	 * Is Base Rule
	 * 
	 * @param string $ruleId Rule Id
	 * @return boolean True if provided rule is the base rule
	 */
	
	function isBaseRule($ruleId) 
	{
		return $ruleId == $this->baseCondition;
	}
	
	/**
	 * List Rules 
	 * 
	 * @return array Rule Id List
	 * @access public
	 */
	
	function listRules() 
	{
		return array_keys($this->rules);
	}
	
	// end class VWPReport_Filter
}
