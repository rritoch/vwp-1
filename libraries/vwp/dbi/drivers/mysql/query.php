<?php

/**
 * Virtual Web Platform - MySQL Table
 *  
 * This file provides the driver for
 * MySQL Table Access.   
 * 
 * @package VWP
 * @subpackage Libraries.DBI.Drivers.MySQL  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License   
 */

/**
 * Require MySQL database support
 */

VWP::RequireLibrary('vwp.dbi.drivers.mysql');

/**
 * Require paging support
 */

VWP::RequireLibrary('vwp.dbi.paging');

/**
 * Require filter support
 */

VWP::RequireLibrary('vwp.dbi.filter');

/**
 * Require iofilter support
 */

VWP::RequireLibrary('vwp.dbi.drivers.mysql.queryfilter');


/**
 * Virtual Web Platform - MySQL Table
 *  
 * This class provides the driver for
 * MySQL Table Access.   
 * 
 * @package VWP
 * @subpackage Libraries.DBI.Drivers.MySQL 
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License 
 */

class VMysqlQuery extends VMysqlDatabase 
{
 
    /**
     * @var object Filter
     * @access public
     */
       
    public $paging = null;
 
    /**
     * @var VMysqlQueryFilter $infilter
     * @access public
     */
    
    public $infilter = null;

    /**
     * @var VMysqlQueryFilter $outfilter
     * @access public
     */    
    
    public $outfilter = null;
    
    /**
     * @var array $tables
     * @access public
     */
       
    public $tables = array();
 
    public $tableMap = array();

    public $ordering = null;
    
    /**     
     * Table Relationships
     * 
     * @var VDatabaseTableRelationships $tableRelationships Table Relationships
     */
    
    public $tableRelationships = null;
    
    /**
     * Join Type     
     * 
     * @var string $joinType Join Type
     * @access public
     */
    
    public $joinType = null;
    
    /**
     * @var array Columns
     * @access public
     */
       
    public $columns = null;
 
    /**
     * @var array Rows
     * @access public
     */
       
    var $rows = array();
 
    /**
     * Primary Key
     * 
     * @var string $primary_key Primary key column
     * @access public
     */
       
    public $primary_key = null;

    /**
     * Group List
     * 
     * @var array $groupList Group List
     * @access public
     */
    
    public $groupList = null;
    
    /**
     * Fields
     * 
     * @var array $fields Fields
     * @access public
     */
    
    public $fields = null;
    
    /**
     * Class destructor
     * 
     * @access public
     */
         
    function __destruct() 
    {
        // do nothing!
    } 

    /**
     * Get Input Filter
     * 
     * @return VMysqlQueryFilter Input Filter
     * @access public
     */
    
    function &getInFilter() 
    {
    	return $this->infilter;
    }

    /**
     * Get Output Filter
     * 
     * @return VMysqlQueryFilter Output Filter
     * @access public
     */
    
    
    function &getOutFilter() 
    {
    	return $this->outfilter;    	
    }
    
    /**
     * Test if valid join type
     * 
     * @param string $type Type
     * @param boolean $debug Debug Mode
     * @return boolean|object True if valid, error or warning otherwise
     * @access public
     */
    
    function validJoinType($type,$debug = false) {
    	switch($type) {
    		case "left":
    			break;    		
    		default:
    			if ($debug) {
    			    VWP::raiseWarning('Invalid Join Type',__CLASS__);	
    			}
    			return false;
    	}
    	return true;
    }

    /**
     * Valid table list
     * 
     * @param array $tables Tables
     * @param boolean $debug Debug Mode
     * @return boolean True if valid, error or warning otherwise
     */
    
    function validTableList($tables,$debug = false) {

    	
        if (!is_array($tables) || (count(array_keys($tables)) < 1)) {
        	if ($debug) {
        		VWP::raiseWarning('Empty table list!',__CLASS__);
        	}
        	return false;
        }
        
        foreach($tables as $tableId=>$tableName) {
        	if ((!is_string($tableName)) || empty($tableName)) {
        		if ($debug) {
        		    VWP::raiseWarning('Invalid Table ID!',__CLASS__);
        		}
        		return false;
        	}
        }
        
        foreach($tables as $tableId=>$tableName) {
        	$tbl =& $this->getTable($tableName);
        	if (VWP::isWarning($tbl)) {
        		if ($debug) {
        			$tbl->ethrow();
        		}
        		return false;
        	}
        }
        
    	return true;
    }    
    
    /**
     * Valid Join
     *
     * @param array $tableList Table List
     * @param VDatabaseTableRelationships $tableRelationships Table Relationships
     * @param string $joinType Join Type
     * @param boolean $debug Debug Mode
     * @return boolean|object True if valid, error or warning otherwise
     */
    
    function validJoin($tableList,$tableRelationships,$joinType,$debug = false) 
    {
    	    	
    	$valid = true;
    	
    	// Validate Join Type
    	    	
    	if ($valid) {
            $valid = $this->validJoinType($joinType,$debug);
    	} 


    	// Validate Table List

        if ($valid) {
            $valid = $this->validTableList($tableList,$debug);	
        }
                
        // Check Table Relationships
        
        if ($valid) {
        	if (count(array_keys($tableList)) > 1) {
        	    $valid = is_object($tableRelationships);
        	    if ($debug && !$valid) {
        		    VWP::raiseWarning('Invalid table relationships!',__CLASS__,null,true);
        	    }
        	    
                if ($valid) {
        	        $valid = method_exists($tableRelationships,'getFieldLinks');
                    if ($debug && !$valid) {
        		        VWP::raiseWarning('Invalid table relationships!',__CLASS__,null,true);
        	        }        	                	        
                }        	            	    
        	}
        }
        
                        
        if (!$valid) {
        	return $valid;
        }
        
        // Validate Linkage
        
        switch($joinType) {
        	case "left":
        		
        		
        		$sz = count(array_keys($tableList));
        		
        		$ctables = array_keys($tableList);
        		
        		for($ridx = 1; $ridx < $sz; $ridx++) {
        			$lidx = $ridx - 1;
        			$links = $tableRelationships->getFieldLinks($ctables[$lidx],$ctables[$ridx]);
        			if (count($links) < 1) {
        				if ($debug) {
        					VWP::raiseWarning('Missing Links!',__CLASS__);
        				}
        				return false;
        			}
        		}
        		break;
        	default:
        		if ($debug) {
        			VWP::raiseWarning('Invalid Join Type!',__CLASS__);
        		}
        		return false;
        }
        
        
        return true;
    }
    
    /**
     * Set Query Tables
     * 
     * @param array $tableList Table list
     * @param VDatabaseTableRelationships $tableRelationships
     * @param string $joinType Join Type
     * @param boolean $debug Debug Mode
     * @return boolean True on success, error or warning otherwise
     * @access public
     */
    
    function setTables($tableList,$tableRelationships = null,$joinType = 'left',$debug = false) 
    {
    	    	
    	$this->tableMap = $tableList;
    	$this->tables = array();
    	$this->joinType = null;
    	    	
    	if (count(array_keys($tableList)) > 1) {
    	    $valid = $this->validJoin($tableList,$tableRelationships,$joinType,$debug);    	
    	    if (!$valid) {
    		    return VWP::raiseWarning('Invalid Query Settings!',__CLASS__,null,false);
    	    }
    	
    	    	    	
    	    $this->joinType = $joinType;

    	    $this->tableRelationships =& $tableRelationships;

    	    foreach($tableList as $tableId=>$table) {
    		    $this->tables[] =& $this->getTable($table);
    	    }
    	}
    	    	    	
    	return true;
    }

    /**
     * Get query properties
     * 
     * @param boolean $public Unused
     * @return array Table properties
     * @access public
     */
         
    function getProperties($public = true) 
    {
        $result = parent::getProperties($public);
    
        if (is_object($this->paging)) {
            $result["paging"] = $this->paging->getProperties();
        }
    
        if (is_array($this->rows)) {
            unset($result["rows"]);
            $result["rows"] = array();   
            foreach($this->rows as $key=>$val) {
                if (is_object($val)) {          
                    $result["rows"][$key] = get_object_vars($val);           
                } else {
                    $result["rows"][$key] = $val;
                }     
            }
        }  
        return $result; 
    }

    /**
     * Unused
     * 
     * @access private
     */    
    
    function getMatches() 
    {
    	
    	$this->setQuery($query);
    	parent::query();
    	
    }
    
    /**
     * Field to SQL
     */
    
    protected function _column2sql($col,$antialias = false) 
    {

    	if ($antialias) {
    		$nf = $this->nameQuote($col->field_alias);
    	} else {
		    if (empty($col->table_id)) {
		        $nf = $this->nameQuote($col->field);  
	        } else {
	            $nf = $this->nameQuote($col->table_id).'.'.$this->nameQuote($col->field);
	        }
    	}

        if (isset($col->op)) {
            switch($col->op) {
            	case "count":
            		$nf = 'COUNT(' . $nf . ')';
            		break;
                case "sum":
                	$nf = 'SUM(' . $nf . ')';
                	break;
                case "avg":
                	$nf = 'AVG(' . $nf . ')';
                	break;
                case "min":
                	$nf = 'MIN(' . $nf . ')';
                	break;
                case "max":
            	    $nf = 'MAX(' . $nf . ')'; 
            	    break;
            	    
                case "date":
                	$nf = 'DATE('.$nf.')';
                	break;
                	 
                case "datetimetounix": 
                	$nf = 'UNIX_TIMESTAMP('.$nf.')';
                	break;
                	 
                case "unixtodatetime":
                	$nf = 'FROM_UNIXTIME('.$nf.')';
                	break;
                	                	 
                case "secondstotime":
                	$nf = 'SEC_TO_TIME('.$nf.')';
                	break;
                	                	 
                case "timetoseconds":
                	$nf = 'TIME_TO_SEC('.$nf.')';
                	break;
                	                	 
                case "secondsofminute":
                	$nf = 'EXTRACT(SECOND FROM '.$nf.')';
                	break;
                	                	 
                case "minutesofhour":
                	$nf = 'MINUTE('.$nf.')';
                	break;
                	                	 
                case "hourofday":
                	$nf = 'HOUR('.$nf.')';
                	break;
                	                	 
                case "dayofweek":
                	$nf = 'DAYOFWEEK('.$nf.')';
                	break;
                	                	 
                case "dayofmonth":
                	$nf = 'DAYOFMONTH('.$nf.')';
                	break;
                	                	 
                case "dayofyear":
                	$nf = 'DAYOFYEAR('.$nf.')';
                	break;
                	                	 
                case "weekofyear": 
                	$nf = 'WEEKOFYEAR('.$nf.')';
                	break;
                	                	
                case "monthofyear":
                	$nf = 'MONTH('.$nf.')';
                	break;
                	                	 
                case "year":
                	$nf = 'YEAR('.$nf.')';
                	break;
                	                	 
                case "secondsofminutefromunix":
                	$nf = 'EXTRACT(SECOND FROM FROM_UNIXTIME('.$nf.'))';
                	break;
                	                	                 	
                case "minutesofhourfromunix":
                	$nf = 'MINUTE(FROM_UNIXTIME('.$nf.'))';
                	break;
                	
                case "hourofdayfromunix":
                	$nf = 'HOUR(FROM_UNIXTIME('.$nf.'))';
                	break;

                case "dayofweekfromunix":
                	$nf = 'DAYOFWEEK(FROM_UNIXTIME('.$nf.'))';
                	break;
                	                	 
                case "dayofmonthfromunix":
                	$nf = 'DAYOFMONTH(FROM_UNIXTIME('.$nf.'))';
                	break;
                	                	 
                case "dayofyearfromunix":
                	$nf = 'DAYOFYEAR(FROM_UNIXTIME('.$nf.'))';
                	break;

                case "weekofyearfromunix": 
                	$nf = 'WEEKOFYEAR(FROM_UNIXTIME('.$nf.'))';
                	break;
                	                	
                case "monthofyearfromunix":
                	$nf = 'MONTH(FROM_UNIXTIME('.$nf.'))';
                	break;
                	                	 
                case "yearfromunix":
                	$nf = 'YEAR(FROM_UNIXTIME('.$nf.'))';
                	break;            	    
            	                	    
            	default:
            		break;
            }	
        }    	
        return $nf;	
    }
    
    /**
     * Get Fields as SQL
     * 
     * @return string SQL fields
     * @access public
     */
        
    protected function _makeFields() 
    {
     
    	if (!is_array($this->groupList) || (count($this->groupList) < 1)) {
    		// No Groups!

    		
            if (is_array($this->fields) && count($this->fields) > 0) {
        	
    		    foreach($this->fields as $col) {
    		    	$nf = $this->_column2sql($col);    		    	    			        			    
    			    $fields[] = $nf . ' AS ' . $this->nameQuote($col->alias);    			        			
    		    }
    		    
    		    $fields = implode(',',$fields);
        	        	
    	    } else {    		    
                return VWP::raiseWarning('NO FIELDS SELECTED!',__CLASS__,null,false);
    	    }
    	} else {
    		// Groups
    	    	
    		$fields = array();
    		
    		/*
    		foreach($this->groupList as $g) {
    			$table_id = $g->table_id;
    			$field_id = $g->field;
    		    if (empty($g->table_id)) {
        		    $fields[] = $this->nameQuote($table_id) . ' AS ' . $this->nameQuote($g->alias);
    			} else {
    				$fields[] = $this->nameQuote($table_id).'.'.$this->nameQuote($field_id) . ' AS ' . $this->nameQuote($g->alias);
    			}    			
    		}
    		*/
    		
    		foreach($this->fields as $field) {    			    			    				    			
    			$nf = $this->_column2sql($field);    			
    			$fields[] = $nf . ' AS ' . $this->nameQuote($field->alias);
    		}
    		
    		$fields = implode(',',$fields);
    	}
            
    	return $fields;
    }
    
    /**
     * Run Query
     * 
     * @return boolean|object True on success, error or warning otherwise
     * @access public
     */
    
    function query() 
    {

    	$fields = '';
    	$from = '';
    	$where = '';
    	$groupby = '';
    	$orderby = '';
    	$limit = '';
    	    	    
    	// Setup Table List
    	
    	$table_id_list = array();
    	$table_name_list = array();
    	
    	$idx = 0;
    	foreach($this->tableMap as $key=>$val) {
    		$table_id_list[] = $key;
    		$table_name_list[] = $val;
    	}
    	    	
    	if (count($table_id_list) < 1) {
    	    return VWP::raiseWarning('No tables requested!',__CLASS__,null,false);	
    	}

    	
        // Setup Field List
    	
        $fields = $this->_makeFields();
    	if (VWP::isWarning($fields)) {
    		return $fields;
    	}
    	
    	// Setup From
    	
    	$from =' FROM ' . $this->nameQuote($table_name_list[0]) . ' AS ' . $this->nameQuote($table_id_list[0]);
    	
    	$sz = count($table_id_list);
    	
    	//print_r($this->tableRelationships);
    	
    	for($ridx = 1; $ridx < $sz; $ridx++) {
    		$lidx = $ridx - 1;
    		switch($this->joinType) {
    			case "left":
    				$raw_links = $this->tableRelationships->getFieldLinks($table_id_list[$lidx],$table_id_list[$ridx]);    				
    				if (count($raw_links) < 1) {
    					return VWP::raiseWarning('Missing link between tables!',__CLASS__,null,false);
    				}
                    
    				$logic = $this->tableRelationships->getLogic($table_id_list[$lidx],$table_id_list[$ridx]) == 'and' ? ' AND ' : ' OR ';
    				
    				$links = array();
    				
    				foreach($raw_links as $l) {
    					$links[] =   $this->nameQuote($l[0]['table']) 
    					           . '.' 
    					           . $this->nameQuote($l[0]['field'])
    					           . ' = '
    					           . $this->nameQuote($l[1]['table'])
    					           . '.'
    					           . $this->nameQuote($l[1]['field']);
    				}
    				
    				$on = ' ON ' . implode($logic,$links);
    				
    				$from .= ' LEFT JOIN ' . $this->nameQuote($table_name_list[$ridx]) . ' AS ' . $this->nameQuote($table_id_list[$ridx]) . $on;
    				break;    			
    			default:
    				return VWP::raiseWarning('Invalid Join Type',__CLASS__,null,false);
    		}
    	}    	

    	// Where infilters
    	    	    	    	    
        $where = $this->infilter->toSQL($this);
                                        
    	if (VWP::isWarning($where)) {
    		return $where;
    	}
    	
    	if (!empty($where)) {
    		$where = ' WHERE ' . $where; 
    	}    	
    	
    	// Setup Groups
    	    	
    	
    	if (is_array($this->groupList) && (count($this->groupList) > 0)) {
    		$groupby = array();
    	   	foreach($this->groupList as $g) {    		
   				$groupby[] = $this->_column2sql($g,true);    			    	
    		}    		
    		
   		    $groupby = ' GROUP BY ' . implode(',',$groupby);
    	}
    		
        // having outfilter
        
    	$having = $this->outfilter->toSQL($this);
    	if (VWP::isWarning($having)) {
    		return $having;
    	}
    	
    	if (!empty($having)) {
    		$having = ' HAVING ' . $having; 
    	}

    	if (isset($this->ordering) && is_array($this->ordering)) {
    		$slist = array();
    		foreach($this->ordering as $item) {
    			$dir = $item[1] < 0 ? ' DESC' : ' ASC';
    			$slist[] = $this->nameQuote($item[0]) . $dir;
    		}
    		
    		if (count($slist) > 0) {
    			$orderby = ' ORDER BY ' . implode(',',$slist) . ' ';
    		}
    	}
    	
    	$query = 'SELECT ' 
    	         . $fields 
    	         . $from
    	         . $where 
    	         . $groupby
    	         . $having 
    	         . $orderby 
    	         . $limit;  
      
        //VWP::raiseWarning($query,__CLASS__);              	    	
    	$this->setQuery($query);
    	
    	return parent::query();
    }
    
    /**
     * Get Result as a list of rows indexed by table and field
     * 
     * @return array|object Results on success, error or warning otherwise
     * @access public
     */
    
    function loadAssocList() {
        $raw_data = parent::loadAssocList();

        if (VWP::isWarning($raw_data)) {
        	return $raw_data;
        }

        $fieldMap = array();
        foreach($this->fields as $field) {
        	$fieldMap[$field->alias] = $field;
        }
        
        $data = array();
        
        foreach($raw_data as $raw_row) {
        	$row = array();
        	foreach($raw_row as $key=>$val) {
        	        		
        		$fld = $fieldMap[$key];
        		
        		$table_id = $fld->table_id;
        		$field_id = $key;
        		$op_id = isset($fld->op) ? $fld->op : '';         		
        		
        		if (!isset($row[$table_id])) {
        			$row[$table_id] = array();
        		}
        		
        		if (!isset($row[$table_id][$field_id])) {
        			$row[$table_id][$field_id] = array();
        		}
        		
        		$row[$table_id][$field_id][$op_id] = $val;
        	}
        	array_push($data,$row);        	        	
        }
        
        return $data;	
    }
    
  
    /**
     * Get paging object
     *   
     * @return object Table paging
     * @access public
     */
         
    function &getPaging() 
    {
        if (!isset($this->paging)) {
            $this->paging = new VDatabasePaging;
        }
        return $this->paging; 
    }

    /**
     * set paging object
     *   
     * @param object Table paging
     * @access public
     */
  
    function setPaging(&$paging) 
    {
        $this->paging = & $paging;
    }

 
    /**
     * Get a row from a query
     * 
     * @param mixed $key Row identifier (primary key value)
     * @param string $tableName Ignored
     * @param string $databaseName Ignored
     * @return object Row object on success, error or warning on failure
     */

    function &getRow($key,$tableName = null,$databaseName = null) 
    {
        
        // Handle new rows
  
        if ($key === null) {   
            $row = new VMysqlQueryRow;
            $row->bind($this);
            $result = $row->load();
            if ($this->isError($result)) {
                return $result;
            }
            return $row;   
        }
        
        // Handle existing rows
  
        if (!isset($this->rows[$key])) {
            return VWP::raiseWarning('Row not found!',__CLASS__,null,false);  
        }
        return $this->rows[$key];  
    }
 
    /**
     * Set Fields
     * 
     * @param array $fields
     * @access public
     */
   
    function setFields($fields) {
    	
    	$this->fields = $fields;
    }
    
    /**     
     * Set Groups
     * 
     * @param array $groupList List of grouping fields
     * @access public     
     */
    
    function setGroups($groupList) 
    {
    	$this->groupList = $groupList;    	
    }
    
    /**
     * Set Ordering
     *      
     * @param array $order Array of (string)column,(integer)direction arrays
     * @access public
     */
    
    function setOrdering($order) {
    	$this->ordering = $order;
    }
    /**
     * Class constructor
     *
     * @param string $tableName Table name
     * @access public
     */
          
    function __construct($databaseName = null) 
    {
        $this->_link = false;
        parent::__construct();
        $this->_link =& $this->getDatabase($databaseName);
        $this->infilter = new VMysqlQueryFilter;
        $this->outfilter = new VMysqlQueryFilter;  
    }
    
    // end class VMysqlQuery
} 
