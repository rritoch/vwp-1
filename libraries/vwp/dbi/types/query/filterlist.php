<?php

/**
 * VWP - DBI Query Filter List Type
 *  
 * This file provides the filter list        
 * 
 * @package VWP
 * @subpackage Libraries.DBI.Types.Query  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

/**
 * VWP - DBI Query Filter List Type
 *  
 * This class provides the filter list        
 * 
 * @package VWP
 * @subpackage Libraries.DBI.Types.Query  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

class VDBIQueryType_FilterList extends VObject
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
	 * Get Input Filter Rule Node
	 * 
	 * @param string $ruleId
	 * @return object Rule node on success, error or warning otherwise
	 * @access private
	 */
	
	protected function _getFilterRuleNode($ruleId) 
	{
		$gNode = $this->_getCoreNode($this->_rootTagName);
		if (VWP::isWarning($gNode)) {
			return $gNode;
		}
		
		$ruleNode = null;
		
		if ($gNode !== null) {
		    $nodeList = $gNode->getElementsByTagNameNS(VDBI_Query::NS_QUERY_1_0,'filter');
		    for($idx=0;$idx < $nodeList->length; $idx++) {
		    	if ($ruleId == (string)$nodeList->item($idx)->getAttribute('alias')) {
		    		$ruleNode = $nodeList->item($idx);
		    	}
		    }	
		}
				
		if ($ruleNode === null) {
			return VWP::raiseWarning("Rule '$ruleId' Not Found",__CLASS__.'::_getFilterRuleNode',null,false);
		}
		return $ruleNode;
	}	
	
    
	/**
	 * Unserialize Condition
	 * 
	 * @param object $conditionNode Condition Node
	 * @return array Condition
	 * @access private
	 */
	
	public function _unSerializeCondition($conditionNode) 
	{
		 
        $data = array(
          "op"=> $conditionNode->getAttribute('operator'),
          "value"=>array(
            /* left */ array($conditionNode->getAttribute('left_type'),$conditionNode->getAttribute('left_value')),
            /* right */ array($conditionNode->getAttribute('right_type'),$conditionNode->getAttribute('right_value')),                    
          )        
        );
                
		return $data;
	}	
	
	/**
	 * Unserialize Condition Group
	 * 
	 * @param object $groupNode Group Node
	 * @return array Condition Group
	 * @access private
	 */
		
	public function _unSerializeConditionGroup($groupNode) 
	{
		$data = array(array(),array());
		$data[0] = $groupNode->getAttribute('alias');
		$data[1]['logic'] = $groupNode->getAttribute('logic');
		
		$data[1]['base'] = $groupNode->getAttribute('base') == 'true' ? true : false;
		$data[1]['conditions'] = array();
		$conditionList = $groupNode->getElementsByTagNameNS(VDBI_Query::NS_QUERY_1_0,'condition');
		for($cidx=0;$cidx<$conditionList->length;$cidx++) {
			$data[1]['conditions'][] = $this->_unSerializeCondition($conditionList->item($cidx));
		}
				
		return $data;
	}	
	
	/**
	 * List Filters
	 * 
	 * @return array|object Relationship Groups on success, error or warning otherwise
	 * @access public
	 */		
	
	function listFilters() 
	{

		if (!isset($this->_data)) {
			$filtersNode = $this->_helper->_getCoreNode($this->_rootTagName);
            
            if (VWP::isWarning($filtersNode)) {
            	return $filtersNode;
            }
            
            if ($filtersNode === null) {
            	return array();
            }
            
            $this->_data = new VDBIQueryType_Filter;
                                    
            $nodeList = $filtersNode->getElementsByTagNameNS(VDBI_Query::NS_QUERY_1_0,'filter');
            
            for($r = 0; $r < $nodeList->length; $r++) {
            	$filterNode = $nodeList->item($r);
            	
            	$alias = (string)$filterNode->getAttribute('alias');
            	
            	$result = $this->_data->addRule(
            	                          $alias,
            	                          'true' == (string)$filterNode->getAttribute('base') ? true : false,
            	                          (string)$filterNode->getAttribute('logic')
            	                          );
            	if (VWP::isWarning($result)) {
            		return $result;
            	}
            	            
            	$condNodeList = $filterNode->getElementsByTagNameNS(VDBI_Query::NS_QUERY_1_0,'condition');
            	
            	for($cidx = 0; $cidx < $condNodeList->length; $cidx++) {
            		$condNode = $condNodeList->item($cidx);
            		
            	    $this->_data->addCondition($alias,
            	                                        array((string)$condNode->getAttribute('left_value'),(string)$condNode->getAttribute('left_type')),
            	                                        (string)$condNode->getAttribute('operator'),            	                                                    	                                        
            	                                        array((string)$condNode->getAttribute('right_value'),(string)$condNode->getAttribute('right_type'))
            	                                       );	
            	}            	            	  
            }
		}
		
		return $this->_data->listRules();
	}	
	
	/**
	 * Get Rule Info
	 * 
	 * @param string $ruleId
	 * @access public
	 */
	
	function getRuleInfo($ruleId) {
						
	    $ruleList = $this->listFilters();
	    if (!in_array($ruleId,$ruleList)) {
	    	return VWP::raiseWarning('Rule not found!',__CLASS__,null,false);
	    }	    	    
	    
	    $conditions = $this->_data->listConditions($ruleId);
	    $doc = $this->_helper->getDOMDocument();
	    for($idx=0;$idx<count($conditions);$idx++) {
	    	if ($conditions[$idx][0][1] == 'reference') {
	    		$conditions[$idx][0][1] = 'constant';
	    		$ref = $conditions[$idx][0][0];
	    		$refv = null;	    		
	    		if (substr($ref,0,1) == '#') {
	    		   $elem = $doc->getElementById($ref);
	    		   if ($elem !== null) {
	    		       $refv = $this->_helper->_resolveValue($elem);
	    		   }
	    		}
	    		$conditions[$idx][0][0] = $refv;
	    	}
	    	
	    	if ($conditions[$idx][2][1] == 'reference') {
	    		$conditions[$idx][2][1] = 'constant';	    		
	    	    $ref = $conditions[$idx][2][0];	    		
	    		$refv = null;	    		
	    		if (substr($ref,0,1) == '#') {
	    		   $elem = $doc->getElementById(substr($ref,1));
	    		   if ($elem !== null) {
	    		       $refv = $this->_helper->_resolveValue($elem);
	    		   }
	    		}
	    		$conditions[$idx][2][0] = $refv;	    	    	
	    	}
	    }
	    
	    $ruleInfo = array(
	        "logic"=>$this->_data->getLogicOperator($ruleId),
	        "base"=>$this->_data->isBaseRule($ruleId),
	        "conditions"=>$conditions,
	    );
	    
	    return $ruleInfo;	
	}
	
	/**
	 * Set Rule Info
	 * 
	 * @param string $ruleId Rule Id
	 * @param array $info Rule Info
	 * @access public
	 */
	
	function setRuleInfo($ruleId,$info) 
	{

		$ruleList = $this->listFilters();
	    if (!in_array($ruleId,$ruleList)) {
	    	return VWP::raiseWarning("Rule '$ruleId' not found!",__CLASS__,null,false);
	    }		

		$filtersNode = $this->_getCoreNode($this->_rootTagName);
		if (VWP::isWarning($filtersNode)) {
			return $filtersNode;
		}	    
				
	    $ruleNode = $this->_getFilterRuleNode($ruleId);
	    if (VWP::isWarning($ruleNode)) {
	    	return $ruleNode;
	    }
	    	    
	    $this->_data->removeRule($ruleId);
	    
	    $newRuleNode = $this->_helper->getDOMDocument()->createElementNS(VDBI_Query::NS_QUERY_1_0,'filter');
	    $newRuleNode->setAttribute('alias',$ruleId);
	    $newRuleNode->setAttribute('base',$info['base'] ? 'true' : 'false');
	    $newRuleNode->setAttribute('logic',$info['logic']);

	    $this->_data->addRule($ruleId,$info['base'], $info['logic']);
	    
	    foreach($info['conditions'] as $cond) {
	    		    	
	    	$condNode = $this->_helper->getDOMDocument()->createElementNS(VDBI_Query::NS_QUERY_1_0,'condition');	    		    	
	    	$condNode->setAttribute('left_value',$cond[0][0]);
	    	$condNode->setAttribute('left_type',$cond[0][1]);	    		    	
	    	$condNode->setAttribute('operator',$cond[1]);            	    	
	    	$condNode->setAttribute('right_value',$cond[2][0]);
	    	$condNode->setAttribute('right_type',$cond[2][1]);	    		    		    	
	    	$newRuleNode->appendChild($condNode);
	    	$this->_data->addCondition($ruleId,$cond[0],$cond[1],$cond[2]);
	    }

	    $filtersNode->replaceChild($newRuleNode,$ruleNode);
	    
	    return true;
	}
	
	/**
	 * Add Rule
	 * 
	 * @param string $ruleId Rule Id
	 * @param boolean $base Is Base Rule
	 * @param string $logic Logic Operator
	 * @return boolean|object True on success, error or warning otherwise
	 */
	
	function addRule($ruleId,$base = false, $logic = null) 
	{
		
		
		$ruleList = $this->listFilters();
		if (in_array($ruleId,$ruleList)) {			
			return VWP::raiseWarning('Duplicate Rule ID',__CLASS__,null,false);
		}


		$filtersNode = $this->_helper->_makeCoreNode($this->_rootTagName);
		if (VWP::isWarning($filtersNode)) {			
			return $filtersNode;
		}

		switch($logic) {
			case "or":
				break;
			default:
				$logic = "and";
		}
		
		$result = $this->_data->addRule($ruleId,$base,$logic);
		if (VWP::isWarning($result)) {			
			return $result;
		}		
		
		$newNode = $this->_helper->getDOMDocument()->createElementNS(VDBI_Query::NS_QUERY_1_0,'filter');
		$newNode->setAttribute('alias',$ruleId);
		$newNode->setAttribute('base',$base ? 'true' : 'false');
		$newNode->setAttribute('logic',$logic);
		$filtersNode->appendChild($newNode);
		
		return true;
	}
	
	/**
	 * Remove Rule
	 * 
	 * @param string $ruleId Rule Id
	 * @return boolean|object True on success, error or warning otherwise
	 */	
	
	function removeRule($ruleId) 
	{
		$ruleList = $this->listFilters();
		if (VWP::isWarning($ruleList)) {
			return $ruleList;
		}
		
		if (!in_array($ruleId,$ruleList)) {
			return true;
		}

		$filterNode = $this->_getCoreNode($this->_rootTagName);
		if (VWP::isWarning($filterNode)) {
			return $filterNode;
		}
		
		
		$result = $this->_data->removeRule($ruleId);
		if (VWP::isWarning($result)) {
			return $result;
		}

		$n = array();
		
		$nodeList = $filterNode->getElementsByTagNameNS(VDBI_Query::NS_QUERY_1_0,'filter');
		for($idx=0;$idx<$nodeList->length;$idx++) {
			if ($ruleId == (string)$nodeList->item($idx)->getAttribute('alias')) {
				$n[] = $nodeList->item($idx);
			}
		}
		
		foreach($n as $c) {
			$filterNode->removeChild($c);
		}

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
	
	// end class VDBIQueryType_FilterList	
}
