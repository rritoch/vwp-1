<?php

/**
 * VWP - DBI Query
 *  
 * This file provides a database independent query interface        
 * 
 * @package VWP
 * @subpackage Libraries.DBI  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

/**
 * Require Query Helper
 */

VWP::RequireLibrary('vwp.dbi.queryhelper');

/**
 * Require DOM Helper
 */

VWP::requireLibrary('vwp.dom.domhelper');

/**
 * Require Table Relationships Support
 */

VWP::requireLibrary('vwp.dbi.relationships');

/**
 * VWP - DBI Query
 *  
 * This file provides a database independent query interface        
 * 
 * @package VWP
 * @subpackage Libraries.DBI  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

class VDBI_Query extends VObject
{
	
	/**
	 * Query Id
	 * 
	 * @var string $id Query ID
	 */
	
	public $id = null;

	/**
	 * Query Type (tns:queryType)
	 * 
	 * @var string $type Query Type
	 */
	
	protected $type = null;
		
	/**
	 * Query Title (xsd:string)
	 * 
	 * @var string $title Query Title
	 */	
	
	protected $title = null;
		
	/**
	 * Datasources (tns:dataSourcesType)
	 * 
	 * @var VDBIQueryType_Datasources $datasources Datasources
	 */
	
	protected $datasources = null;	

	/**
	 * Database Tables (tns:tablesType)
	 * 
	 * @var VDBIQueryType_Tables $tables Tables
	 */
	
	protected $tables;	
	
	/**
	 * Database Table Relationships (tns:relationshipGroupListType)
	 * 
	 * @var VDBIQueryType_RelationshipGroupList $table_relationships Database Table Relationships
	 */	
	
	protected $table_relationships;	
	
	/**
	 * Input Filters (tns:filterListType)
	 * 
	 * @var  VDBIQueryType_FilterList $input_filters Input filters
	 */		
	
	protected $input_filters;	
	
	/**
	 * Summary Options (tns:summaryOptionsType)
	 * 
	 * @var VDBIQueryType_SummaryOptions $summary_options Summary options
	 */
	
	protected $summary_options;
		
	/**
	 * Report Groupings (tns:fieldListType)
	 * 
	 * @var array $groupings Report Grouping Fields
	 */
	
	protected $groupings;
	
	/**
	 * Output Filters (tns:filterListType)
	 * 
	 * @var  VDBIQueryType_FilterList $output_filters Output filters
	 */		
	
	protected $output_filters;

	/**
	 * Labels (tns:labelListType)
	 * 
	 * @var array $labels Labels
	 */

	protected $labels;
	
	/**
	 * Values (tns:valueListType)
	 * 
	 * @var array $values;
	 */
	
	protected $values;
	
	/**
	 * Helper
	 * 
	 * @var VDBI_QueryHelper $_helper
	 * @access private
	 */
	
	protected $_helper;
		
	/**	 
	 * QUERY v1.0 Namespace	 
	 */
	
	const NS_QUERY_1_0 = "http://standards.vnetpublishing.com/schemas/vwp/2011/02/DBI/Query";
                            
	/**	 
	 * QUERY v1.0 Namespace Location	 
	 */
		
	const NSLOC_QUERY_1_0 = "http://standards.vnetpublishing.com/schemas/vwp/2011/02/DBI/Query/";	
	
	/**
	 * Get Query Title
	 * 
	 * @return string|object Query Title on success, error or warning otherwise
	 * @access public
	 */		
	
	function getTitle() 
	{
	    if (!isset($this->title)) {
	    	
	    	$titleNode = $this->_helper->_getCoreNode('title');
	        if (VWP::isWarning($titleNode)) {
         	    return $titleNode;
            }

            // $this->title = $titleNode->nodeValue; W3C Anyone?
            $title = '';
            $cnodes = $titleNode->childNodes;
            $len = $cnodes->length;
            for($i=0; $i<$len; $i++) {
            	$item = $cnodes->item($i);
            	if ($item->nodeType == XML_TEXT_NODE) {
            		$title .= (string)$item->data;
            	}
            }
            $this->title = $this->_helper->_resolveValue($titleNode);
	    }
	    	    
	    return $this->title;
	}	
	
	/**
	 * Set Query Title
	 * 
	 * @param string $title Query Title
	 * @return boolean|object True on success, error or warning otherwise
	 */		
	
	function setTitle($title) 
	{
		$titleNode = $this->_helper->_getCoreNode('title');
	    if (VWP::isWarning($titleNode)) {
            return $titleNode;
        }
        
        $this->title = (string)$title;
        $this->_helper->_setResolveValue($titleNode,$title);                
        return true;
	}		
	
	/**
	 * Get Query Type
	 * 
	 * @return string|object Report Title on success, error or warning otherwise
	 * @access public
	 */		
	
	function getType() 
	{
	    if (!isset($this->type)) {	    	
	    	$typeNode = $this->_helper->_getCfgNode();
	        if (VWP::isWarning($typeNode)) {
         	    return $typeNode;
            }
            $type = (string)$typeNode->getAttribute('type');            
            $this->type = $type;
	    }
	    	    
	    return $this->type;
	}	
	
	/**
	 * Set Query Type
	 * 
	 * @param string $type Query type
	 * @return boolean|object True on success, error or warning otherwise
	 */		
	
	function setType($type) 
	{
		$typeNode = $this->_helper->_getCfgNode();
	    if (VWP::isWarning($typeNode)) {
            return $typeNode;
        }        
        $this->type = (string)$type;
        $typeNode->setAttribute('type',$type);                
        return true;
	}		

	/**
	 * Set Var
	 * 
	 * @param string $name Variable Name
	 * @param mixed $value Value
	 * @param string $class Namespace
	 * @access public
	 */
	
	function setVar($name,$value,$class = null) 
	{
        return $this->_helper->setVar($name,$value,$class);
	}
	
	/**
	 * Get Var
	 * 
	 * @param string $name Variable Name
	 * @param mixed $default Default Value
	 * @param string $class Namespace
	 * @access public
	 */	
		
	function getVar($name,$default,$class = null) 
	{
	    return $this->_helper->getVar($name,$default,$class);
	}
	
	/**
	 * Get Property
	 * 
	 * @param string $property Property
	 * @param mixed $default Default value
	 * @return mixed Value
	 * @access public
	 */
	
	function &get($property,$default = null) 
	{
	    switch($property) {
	    	
	    	case "title":
	    		$ret = $this->getTitle();
	    		break;
	    	default:
	    		$ret = parent::get($property,$default);
	    		break;
	    }

	    return $ret;
	}

	/**
	 * Run Query
	 * 
	 * @return array|object Results on success, error or warning otherwise
	 * @access public
	 */
	
	function runQuery() 
	{

		$databaseId = $this->datasources->getDatabaseAliasByIndex(0);
				
		if ($databaseId === null) {
			return VWP::raiseWarning('No Databases Defined For Query',__CLASS__,null,false);
		}
		
		$databaseType = $this->datasources->getDatabaseTypeByIndex(0);

		if ($databaseType != 'vwp') {
			return VWP::raiseWarning("Unsupported database type '$databaseType'",__CLASS__,null,false);
		}
		
		$dbConfig = $this->datasources->getDatabaseSettings($databaseId);
		
		if (VWP::isWarning($dbConfig)) {
			return $dbConfig;
		}

		if (!isset($dbConfig['dbid'])) {
			return VWP::raiseWarning("Query is missing database Id",__CLASS__,null,false);
		}
		
		$dbid = $dbConfig['dbid'];
							
		$db =& v()->dbi()->getDatabase($dbid);
		if (VWP::isWarning($db)) {
			return $db;
		}
		
		// Get Database Query Object		
        $query = $db->createQuery();        
        if (VWP::isWarning($query)) {
        	$query = null;
        }
        
	    // Build Summary List - SELECT [FIELDS]
                        
        $tableOrder = $this->tables->getOrder();
        
        $summaryMap = array();        
        $fieldMap = array();                
        $summaryList = array();
        
        $sfields = $this->summary_options->listSummaryFields();        
        
                        
        foreach($sfields as $fieldName) {        	
        	$info = $this->summary_options->getSummaryInfo($fieldName);        	        	
        	if ($info['value']) {
        		unset($info['value']);
        		$summaryMap[$fieldName] = $info;
        	}                      	
        } 
                
        foreach($tableOrder as $tbl) {            
            $tfields = $this->tables->getFields($tbl);
                        
            foreach($tfields as $fieldId=>$fieldName) {
                $fieldMap[$fieldId] = array(
                     'table'=>$tbl,
                     'name'=>$fieldName,
                 );	
            }            
        }
                        
        $result = $query->setFields($summaryList);        
        if (VWP::isWarning($result)) {
        	return $result;
        }          
        
        foreach($summaryMap as $sFieldId=>$sInfo) {
        	$field = new stdClass;
        	$field->op = $sInfo['sumtype'] == 'plain' ? '' : $sInfo['sumtype'];
        	$field->alias = $sFieldId;
        	$field->table_id = $fieldMap[$sInfo['field']]['table'];
        	$field->field =  $fieldMap[$sInfo['field']]['name'];
            
            $summaryList[] = $field;
        }
        
        $query->setFields($summaryList);
        
	    // Assign Relationships - FROM [TABLES]        
        
        $tableRelationships = new VDatabaseTableRelationships;        
        $rgroups = $this->table_relationships->listGroups();

        foreach($rgroups as $grp_id) {
        	
        	$grp = $this->table_relationships->getGropu($grp_id);
      		$left_table = $grp['left_table'];        		
       		$right_table = $grp['right_table'];
       		$logic = $grp['logic'];
       		       		       		
        	$gtbls = array();        	
        	$gsz = count($grp['fields']);
        	for ($condidx = 0; $condidx < $gsz; $condidx++) {                    		
                $left_field = array($fieldMap[$grp['fields'][$condidx][0]]['table'],$fieldMap[$grp['fields'][$condidx][0]]['name']);
                $right_field = array($fieldMap[$grp['fields'][$condidx][1]]['table'],$fieldMap[$grp['fields'][$condidx][1]]['name']);        		
                $tableRelationships->addFieldLink($left_table,$right_table,$left_field[0],$left_field[1],$right_field[0],$right_field[1]);                            
        	}        	
        	$tableRelationships->setLogic($left_table,$right_table,$logic);       	
        }        

        $tableOrder = $this->tables->getOrder();
                
        $tableList = array();
        
        foreach($tableOrder as $tbl) {        	
        	$info = $this->tables->getInfo($tbl);        	
        	$tableList[$tbl] = $info['name'];
        }
                
	    $result = $query->setTables($tableList,$tableRelationships,'left',true);
        if (VWP::isWarning($result)) {
        	return $result;
        }        
                        
        // Get Input Filters - WHERE [INFILTER]
        
        $f1 =& $query->getInFilter();
        $ifilters = $this->input_filters->listFilters();
        foreach($ifilters as $ruleId) {
        	$info = $this->input_filters->getRuleInfo($ruleId);
        	        	        	        
        	$f1->addGroup($ruleId,$info['logic'],$info['base']);
        	foreach($info['conditions'] as $cond) {
        		        	
        		if ($cond[0][1] == 'runtimefield') {
        			$f = $cond[0][0];
        			$cond[0][1] = 'constant';
        			$cond[0][0] = $runtimeForm[$f]; 
        		}

        	    if ($cond[2][1] == 'runtimefield') {
        			$f = $cond[2][0];
        			$cond[2][1] = 'constant';
        			$cond[2][0] = $runtimeForm[$f]; 
        		}      		        		
        	        		        		
        		if ($cond[0][1] == 'field') {
        			$cond[0][0] = array($fieldMap[$cond[0][0]]['table'],$fieldMap[$cond[0][0]]['name']);
        		}
        		if ($cond[2][1] == 'field') {
        			$cond[2][0] = array($fieldMap[$cond[2][0]]['table'],$fieldMap[$cond[2][0]]['name']);
        		}        		
        		$f1->addCondition($ruleId,$cond[1],$cond[0],$cond[2]);
        	}
        }

        // Get Group List - GROUP BY [GROUPINGS]
                
        $groupList = $this->groupings->getList();
        if (VWP::isWarning($groupList)) {
        	return $groupList;
        }
        
        $encGroupList = array();
                
        foreach($groupList as $grp) {
        	if (isset($summaryMap[$grp])) {
        	    $sFieldId= $grp;
        	    $sInfo = $summaryMap[$grp];        	            	            	    
           	    $field = new stdClass;
        	    $field->op = $sInfo['sumtype'] == 'plain' ? '' : $sInfo['sumtype'];
        	    $field->alias = $sFieldId;
        	    $field->table_id = $fieldMap[$sInfo['field']]['table'];
        	    $field->field =  $fieldMap[$sInfo['field']]['name'];
                $found = false;        	            	            	    
        	    foreach($summaryMap as $key =>$val) {
        	    	if (($val['sumtype'] == 'plain') && ($val['field'] == $sInfo['field'])) {
        	            $field->field_alias = $key;
        	            $found = true;		
        	    	} 
        	    }
        	            	    
        	    if (!$found) {
        	    	return VWP::RaiseWarning("Group '$grp' missing field source!",__CLASS__,null,false);
        	    }
        	            	            	    
        	    $encGroupList[] = $field;
        	} else {
        		return VWP::RaiseWarning("Group '$grp' not found!",__CLASS__,null,false);
        	}
        }
        
        $query->setGroups($encGroupList);            
                                
        // Get output Filters - HAVING [OUTFILTER]

	    $f2 =& $query->getOutFilter();
        $ifilters = $this->output_filters->listFilters();
        foreach($ifilters as $ruleId) {
        	$info = $this->output_filters->getRuleInfo($ruleId);        	        
        	$f2->addGroup($ruleId,$info['logic'],$info['base']);
        	foreach($info['conditions'] as $cond) {
        		$f2->addCondition($ruleId,$cond[1],$cond[0],$cond[2]);
        	}
        }        
        
        // Extra
        
	    $ratio_id_list = $this->summary_options->listRatios();
        
        if (VWP::isWarning($ratio_id_list)) {
        	return $ratio_id_list;
        }

        $ratio_list = array();
        foreach($ratio_id_list as $rid) {
        	$ratio_list[] =& $this->_settings->getRatio($rid);
        }
        
        // Perform Database Query
                        
        $result = $query->query();
        if (VWP::isWarning($result)) {
        	$result->ethrow();
        }
                
        $data = $query->loadAssocList();
        
        if (VWP::isWarning($data)) {
        	return $data;
        }
                       
        $ratio_count = count($ratio_list);
        if ($ratio_count < 1) {
        	return $data;
        }

        $row_count = count($data);
            
        for($idx = 0; $idx < $row_count; $idx++) { // every row
        	
        	for($ratio_id = 0; $ratio_id < $ratio_count; $ratio_id++) { // every ratio
        		        		
                $ratio_list[$ratio_id]->setData($this->_data[$idx]);
                
                //$parts = explode('.',$this->_settings->getRatioName($ratio_id));
                $table_id = $this->summary_options->getRatiosTableId();                                
                $field_id = $ratio_id_list[$ratio_id];                
                $op_id = $ratio_list[$ratio_id]->operator;
                
                if (!isset($data[$idx][$table_id])) {
                	$data[$idx][$table_id] = array();
                }
        	    if (!isset($data[$idx][$table_id][$field_id])) {
                	$data[$idx][$table_id][$field_id] = array();
                }
                
                $data[$idx][$table_id][$field_id][$op_id] = $ratio_list[$ratio_id]->getValue();                
        	}        	
        }
                
        return $data;
	}

	/**
	 * Set DOM Document
	 * 
	 * Note: Document must be W3C Dom Compliant
	 * 
	 * @param object $doc DOM Document
	 * @access public
	 */
	
	function setDOMDocument(&$doc) 
	{	
        $this->_helper->setDOMDocument($doc);
		$this->type = null;
		$this->title = null;		
		$this->datasources = new VDBIQueryType_Datasources($this);
		$this->tables = new VDBIQueryType_Tables($this);
		$this->table_relationships = new VDBIQueryType_RelationshipGroupList($this);		
		$this->input_filters = new VDBIQueryType_FilterList($this,'input_filters');
		$this->summary_options = new VDBIQueryType_SummaryOptions($this);
		$this->groupings = new VDBIQueryType_FieldList($this,'groupings');				
		$this->output_filters = new VDBIQueryType_FilterList($this,'output_filters');
		$this->labels = new VDBIQueryType_LabelList($this);
		$this->values = new VDBIQueryType_ValueList($this);			
	}
	
	/**
	 * Get DOM Document
	 * 
	 * @return object DOM Document
	 * @access public 
	 */	
	
	function &getDOMDocument() 
	{
		return $this->_helper->getDOMDocument();			
	}
	
	/**
	 * Get Helper
	 * 
	 * @return VDBI_QueryHelper Query Helper
	 * @access public
	 */
	
	function &getHelper() 
	{
		return $this->_helper;
	}
	
	/**
	 * Class Constructor
	 * 
	 * @access public
	 */
	
	function __construct() 
	{
		parent::__construct();
		$this->_helper = new VDBI_QueryHelper($this);
	}	
	
	// end class VDBI_Query	
}
