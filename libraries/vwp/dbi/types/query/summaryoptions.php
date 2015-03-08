<?php

/**
 * VWP - DBI Query Summary Options Type
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

class VDBIQueryType_SummaryOptions extends VObject
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
	 * Get DOM Report Summary List Node
	 * 
	 * @return DOMElement Summary List Node on success, null if not found, error or warning otherwise
	 * @access private
	 */		
	
	protected function _getSummaryListNode() 
	{
	    $groupNode = $this->_helper->_getCoreNode('summary_options');
	    if (VWP::isWarning($groupNode)) {
	    	return $groupNode;
	    }

        if ($groupNode === null) {
        	return null; // short circuit
        }
	    	    
        $nodeList = $groupNode->getElementsByTagNameNS(VDBI_Query::NS_QUERY_1_0,'summary_list');
	    if ($nodeList->length > 1) {
            return VWP::raiseWarning('Ambiguous configuration!',get_class($this),null,false);
        }
        
        if ($nodeList->length > 0) {
        	return $nodeList->item(0);
        }
        return null;		
	}
	
	/**
	 * Get DOM Report Ratio List Node
	 * 
	 * @return DOMElement Ratio List Node on success, null if not found, error or warning otherwise
	 * @access private
	 */	
	
	protected function _getRatioListNode() 
	{
	    $groupNode = $this->_helper->_getCoreNode('summary_options');
	    if (VWP::isWarning($groupNode)) {
	    	return $groupNode;
	    }

        if ($groupNode === null) {
        	return null; // short circuit
        }
	    	    
        $nodeList = $groupNode->getElementsByTagNameNS(VDBI_Query::NS_QUERY_1_0,'ratio_list');
	    if ($nodeList->length > 1) {
            return VWP::raiseWarning('Ambiguous configuration!',get_class($this),null,false);
        }
        
        if ($nodeList->length > 0) {
        	return $nodeList->item(0);
        }
        return null;		
	}
	
	/**
	 * Make DOM Report Summary List Node
	 * 
	 * @return DOMElement Summary List Node on success, null if not found, error or warning otherwise
	 * @access private
	 */	
		
    protected function _makeSummaryListNode() 
	{

		$newNode = $this->_getSummaryListNode();
		if ($newNode !== null) {
			return $newNode;
		}		

		$summaryOptionsNode = $this->_makeCoreNode('summary_options');
		
		if (VWP::isWarning($summaryOptionsNode)) {
			return $summaryOptionsNode;
		}
		
		$newNode = $this->_doc->createElementNS(VDBI_Query::NS_QUERY_1_0,'summary_list');
		
        $nextSibling = $this->_getRatioListNode();
		        
        if ($nextSibling === null) {
        	$summaryOptionsNode->appendChild($newNode);
        } else {
        	$summaryOptionsNode->insertBefore($newNode,$nextSibling);
        }

        $this->summary_options['summary_list'] = array();

        return $newNode;
	}	
	
	/**
	 * Make DOM Report Ratio List Node
	 * 
	 * @return DOMElement Ratio List Node on success, null if not found, error or warning otherwise
	 * @access private
	 */	
		
	protected function _makeRatioListNode() 
	{
		$newNode = $this->_getRatioListNode();
		if ($newNode !== null) {
			return $newNode;
		}
		
		$summaryOptionsNode = $this->_makeCoreNode('summary_options');
		if (VWP::isWarning($summaryOptionsNode)) {
			return $summaryOptionsNode;
		}		
		
		$newNode = $this->_doc->createElementNS(VDBI_Query::NS_QUERY_1_0,'ratio_list');
		
		$summaryOptionsNode->appendChild($newNode);
		
		$this->summary_options['ratio_list'] = array();
		return $newNode;
	}
	
	/**
	 * List Summary Types
	 * 
	 * @return array Summary Types
	 * @access public
	 */

	function listSummaryTypes() 
	{
		return array(
            "plain", 
            "count", 
            "sum", 
            "avg", 
            "max", 
            "min", 
            "date", 
            "datetimetounix", 
            "unixtodatetime", 
            "secondstotime", 
            "timetoseconds", 
            "secondsofminute", 
            "minutesofhour", 
            "hourofday", 
            "dayofweek", 
            "dayofmonth", 
            "dayofyear", 
            "weekofyear", 
            "monthofyear", 
            "year", 
            "secondsofminutefromunix", 
            "minutesofhourfromunix", 
            "hourofdayfromunix", 
            "dayofweekfromunix", 
            "dayofmonthfromunix", 
            "dayofyearfromunix", 
            "weekofyearfromunix", 
            "monthofyearfromunix", 
            "yearfromunix" 						
		);
	}
	
	/**
	 * List Summary Fields
	 * 
	 * @return array|object Summary Fields on success, error or warning otherwise
	 * @access public
	 */
	
	function listSummaryFields() 
	{
		
		if (!isset($this->summary_options)) {
			$this->summary_options = array();
		}
		
		if (!isset($this->summary_options['summary_list'])) {
		    $groupNode = $this->_getSummaryListNode();
		    if (VWP::isWarning($groupNode)) {
			    return $groupNode;
		    }
		    if ($groupNode === null) {
			    return array();
		    }
			$this->summary_options['summary_list'] = array();
				    		
		    $nodeList = $groupNode->getElementsByTagNameNS(VDBI_Query::NS_QUERY_1_0,'summary_field');
		    for($idx=0;$idx < $nodeList->length; $idx++) {		        
		        $fieldNode = $nodeList->item($idx);
		        		        
		        $this->summary_options['summary_list'][(string)$fieldNode->getAttribute('alias')] = array(
		              'field'=>$this->_helper->_resolveValue($fieldNode),
		              'sumtype'=>(string)$fieldNode->getAttribute('sumtype'),
		              'value'=>true
		             );		        
		    }			
		}
				
		return array_keys($this->summary_options['summary_list']);
	}
	
	/**
	 * Get Summary Info
	 * 
	 * @param string $alias Summary Id
	 * @access public
	 */
	
	function getSummaryInfo($alias) {
	    $fieldList = $this->listSummaryFields();
	    if (!in_array($alias,$fieldList)) {
	    	return VWP::raiseWarning('Summary item not found',__CLASS__,null,false);	    	
	    }
	    $ret = $this->summary_options['summary_list'][$alias];	    
	    return $ret;
	    	
	}
	
	/**
	 * Get Summary Field Enabled Flag
	 * 	 
	 * @param string $alias Summary Id
	 * @return boolean|object True if Summary Field Enabled, false if not enabled, error or warning on failure  
	 * @access public 
	 */
	
	function getSummaryFieldEnabled($alias) 
	{
	    $fieldList = $this->listSummaryFields();
	    
	    if (VWP::isWarning($fieldList)) {
	    	return $fieldList;
	    }
	    	    
	    if (!in_array($alias,$fieldList)) {
	    	return false;
	    }	
        
		
		return $this->summary_options['summary_list'][$alias]['value'] ? true : false;
	}
	
	/**
	 * Set Summary Field Enabled Flag
	 * 
	 * @todo Delete node if all fields disabled
	 * @param string $field Field 
	 * @param string $type Summary Type
	 * @param boolean $value True to enable, false to disable
	 * @return boolean|object True on success, error or warning otherwise
	 */
	
	function setSummaryFieldEnabled($alias,$field,$sumtype,$value = true) 
	{

		// Validate Type
        $sumFieldTypes = $this->listSummaryTypes();
        if (!in_array($sumtype,$sumFieldTypes)) {
        	return VWP::raiseWarning('Unknown summary field type!',__CLASS__,null,false);
        }

        // Normalize Value
        $value = $value ? true : false;
        
        // Get Current Value
        $curValue = $this->getSummaryFieldEnabled($alias);
        if (VWP::isWarning($curValue)) {
        	return $curValue;
        }        
        $curValue = $curValue ? true : false;
        
        if ($curValue == $value) {
        	return true; // short circuit
        }
        
        // Get Summary List Node
        
		$groupNode = $this->_makeSummaryListNode();     
	
		if (VWP::isWarning($groupNode)) {
		    return $groupNode;
		}

		// Get Summary Field Node
		$summaryNode = null;		
		$nodeList = $groupNode->getElementsByTagNameNS(VDBI_Query::NS_QUERY_1_0,'summary_field');		
		for($idx = 0; $idx < $nodeList->length; $idx++) {
			if ($alias == $nodeList->item($idx)->getAttribute('alias')) {
				$summaryNode = $nodeList->item($idx);
			}
		}

		// Set New Value
		
		
		if ($summaryNode === null) {
			if ($value) {
			    $summaryNode = $this->_doc->createElementNS(VDBI_Query::NS_QUERY_1_0,'summary_field',XMLDocument::xmlentities($field));
                $summaryNode->setAttribute('sumtype',XMLDocument::xmlentities($sumtype));
			    $summaryNode->setAttribute('alias',XMLDocument::xmlentities($alias));
			    $groupNode->appendChild($summaryNode);
			}									
		} else {
			if (!$value) {
				$groupNode->removeChild($summaryNode);
			} else {
				$summaryNode->setAttribute('sumtype',$sumtype);
				$this->_helper->_setResolveValue($summaryNode,$field);				
			}
		}
		
		
		$this->summary_options['summary_list'][$alias] = array(
			 'sumtype'=> $sumtype,
			 'field'=> $field,
			 'value'=> $value ? true : false,
		);		
		
		// Return success
		return true;
	}
	
	/**
	 * Get Ratio List
	 * 
	 * @return array|object Ratio List on success, error or warning otherwise
	 * @access public
	 */
	
	function listRatios() 
	{
	    if (!isset($this->summary_options)) {
			$this->summary_options = array();
		}
		
		if (!isset($this->summary_options['ratio_list'])) {
		    $groupNode = $this->_getRatioListNode();
		    if (VWP::isWarning($groupNode)) {
		    	return $groupNode;
		    }
		    $this->summary_options['ratio_list'] = array();
            if ($groupNode !== null) {
                $nodeList = $groupNode->getElementsByTagNameNS(VDBI_Query::NS_QUERY_1_0,'ratio');

                
                for($idx=0;$idx < $nodeList->length; $idx++) {                	
                    $ratioNode = $nodeList->item($idx);
                	$segmentNodes = $ratioNode->getElementsByTagNameNS(VDBI_Query::NS_QUERY_1_0,'segment');
                    $domainNodes = $ratioNode->getElementsByTagNameNS(VDBI_Query::NS_QUERY_1_0,'domain');
                    $operator = $ratioNode->getAttribute('operator');
                    
                    if (($segmentNodes->length == 1) && ($domainNodes->length == 1) && (!empty($operator))) {
                       
                        $alias = $ratioNode->getAttribute('alias');                    	
                    	$segment = $segmentNodes->item(0)->nodeValue;
                    	$segment_type = $segmentNodes->item(0)->getAttribute('sumtype');
                    	$domain = $domainNodes->item(0)->nodeValue;
                    	$domain_type = $domainNodes->item(0)->getAttribute('sumtype');
                    	$this->summary_options['ratio_list'][$alias] = new VWPReport_Ratio($segment,$segment_type,$operator,$domain,$domain_type);
                    }
                }
            }		    
		}

		return array_keys($this->summary_options['ratio_list']);
	}	
	
	/**
	 * Get Ratio
	 * 
	 * @param string $alias Ratio Id
	 * @access public
	 */
	
	function &getRatio($alias) 
	{
	    $ratioList = $this->listRatios();
	    if (VWP::isWarning($ratioList)) {
	    	return $ratioList;
	    }	
	    
	    if (!in_array($alias,$ratioList)) {
	    	$err = VWP::raiseWarning('Ratio not found',__CLASS__,null,false);
	    	return $err;
	    }
	    
	    $ratio =& $this->summary_options['ratio_list'][$alias];
	    return $ratio; 
	}
	
	/**
	 * Create Ratio
	 * 
	 * @param string $segement Segment
	 * @param string $segment_type Segment type
	 * @param string $operator Operator
	 * @param string $domain Domain
	 * @param string $domain_type Domain type
	 * @return VDBIQueryType_Ratio Ratio
	 * @access public
	 */
	
	function createRatio($segement = null,$segment_type = null,$operator = null,$domain = null,$domain_type = null) 
	{
		return new VDBIQueryType_Ratio($segement,$segment_type,$operator,$domain,$domain_type);
	}
	
	/**
	 * Set Ratio
	 * 
	 * Note: To remove a ratio set a ratio with it's operator set to a value of null.
	 * 
	 * @param string $alias
	 * @param VDBIQueryType_Ratio $ratio
	 * @return boolean|object True on success, error or warning otherwise
	 */
	
    function setRatio($alias,&$ratio) 
    {
    	$alias = (string)$alias;
    	
    	$ratioList = $this->listRatios();
        if (VWP::isWarning($ratioList)) {
    		return $ratioList;
    	}
    	
    	$ratioListNode = $this->_makeRatioListNode();
    	if (VWP::isWarning($ratioListNode)) {
    		return $ratioListNode;
    	}
    	
    	$ratioNode = null;
    	
    	if (in_array($alias,$ratioList)) {
    		unset($this->summary_options['ratio_list'][$alias]);
    		
    	    $nodeList = $ratioListNode->getElementsByTagNameNS(VDBI_Query::NS_QUERY_1_0,'ratio');
    	    for($idx=0;$idx < $nodeList->length;$idx++) {
    	    	if ($nodeList->item($idx)->getAttribute('alias') == $alias) {
    	            $ratioNode = $nodeList->item($idx);    		
    	    	}
    	    }	
    	} 
    	
    	if ($ratioNode === null) {
    		$ratioNode = $this->_doc->createElementNS(VDBI_Query::NS_QUERY_1_0,'ratio');
    		$ratioListNode->appendChild($ratioNode); 
    	}
    	    	    	
    	if (isset($ratio)) {

    	    // Set Ratio
    	
        	$newNode = $this->_doc->createElementNS(VDBI_Query::NS_QUERY_1_0,'ratio');
        	$newNode->setAttribute('alias',XMLDocument::xmlentities($alias));
     	    $newNode->setAttribute('operator',XMLDocument::xmlentities($ratio->operator));
    	    $sNode = $this->_doc->createElementNS(VDBI_Query::NS_QUERY_1_0,'segment',XMLDocument::xmlentities($ratio->segment));
    	    $sNode->setAttribute('sumtype',XMLDocument::xmlentities($ratio->segment_type));
    	    $dNode = $this->_doc->createElementNS(VDBI_Query::NS_QUERY_1_0,'domain',XMLDocument::xmlentities($ratio->domain));
    	    $dNode->setAttribute('sumtype',XMLDocument::xmlentities($ratio->domain_type));
    	    $newNode->appendChild($sNode);
    	    $newNode->appendChild($dNode);     		
    		
    		$ratioListNode->replaceChild($newNode,$ratioNode);
    	    $this->summary_options['ratio_list'][$alias] =& $ratio;    	    
    	} else {
    		$ratioListNode->removeChild($ratioNode);
    		unset($this->summary_options['ratio_list'][$alias]);    		    	    	        	
    	}
    	
    	return true;
    }
	
    /**
     * Get Ratio Table Id
     * 
     * @return string Ratio Table Id
     * @access public
     */
    
	function getRatiosTableId() 
	{
		
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
	
	// end class VDBIQueryType_SummaryOptions		
}
