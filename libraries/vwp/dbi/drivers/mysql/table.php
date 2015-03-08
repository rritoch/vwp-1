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

class VMysqlTable extends VMysqlDatabase 
{
 
    /**
     * @var object Paging
     * @access public
     */
       
    public $paging = null;
 
    /**
     * @var string $tableName
     * @access public
     */
       
    public $tableName = null;
 
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
     * @var string $primary_key Primary key column
     * @access public
     */
       
    public $primary_key = null;

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
     * Create a filter
     * 
     * @return VDatabaseFilter Filter object
     * @access public
     */
  
    function createFilter() 
    {
        return new VDatabaseFilter;
    }

    /**
     * Get Matches
     * 
     * @param VDatabaseFilter $filter Filter
     * @return array|object Matched column identifiers on success, error or warning on failure
     * @access public        
     */
 
    function getMatches($filter) 
    {
 
        // Init Settings
  
        $values = $filter->m_values;
        $match_types = $filter->m_operators;
        $connector = $filter->m_connector;
        $ordering = $filter->s_keys;
        $offset = $filter->m_offset + 0;
        $maxresults = $filter->m_maxresults;
  
        // Validate Request
  
        if (empty($this->primary_key)) 
        {
            $this->loadColumns();
            if (empty($this->primary_key)) {    
                return VWP::raiseWarning('No Primary Key!',get_class($this),null,false);
            }
        }
  
        // Short Circuit
  
        if ($maxresults !== null) {
            $maxresults = $maxresults + 0;
            if ($maxresults < 1) {
                return array();
            }
        }
  
        // Set Pointer
  
        $sql_key = $this->nameQuote($this->primary_key);
        $sql_table = $this->nameQuote($this->tableName);
  
        // Set Filter
  
        $mlist = array();  
        foreach($values as $key=>$val) {
            $t = isset($match_types[$key]) ? $match_types[$key] : '=';
   
            if (($t == '=') && ($val === null)) {
                $sql = $this->nameQuote($key) . " IS NULL";
            } else {
                $sql = $this->nameQuote($key) . " " . $t . " " . $this->quote($val);
            }
            array_push($mlist,$sql);
        }
  
        $sql_where = '';
        if (count($mlist) > 0) {
            $sql_where = " WHERE " . implode(" ".$connector." ",$mlist);  
        }
    
        // Set Ordering
  
        $order_parts = array();
        foreach($ordering as $field=>$dir) {
               if ($dir < 0) {
                   array_push($order_parts, $this->nameQuote($field) . ' DESC');
               } else {
                   array_push($order_parts, $this->nameQuote($field) . ' ASC');
               }
        }
  
        $sql_order = '';
        if (count($order_parts) > 0) {
            $sql_order = ' ORDER BY ' . implode(',',$order_parts);
        }  
  
        // Set Limits
  
        $sql_limit = '';
        if ($maxresults !== null) {      
            $sql_limit = ' LIMIT ' . $offset . ',' . $maxresults;
            $offset = 0;
        }
  
        // Send Query
     
        $query = "SELECT "
                 .$sql_key
                 ." FROM "
                 .$sql_table
                 ." "
                 .$sql_where
                 .$sql_order
                 .$sql_limit;  
          
        $this->setQuery($query);
  
        $r = $this->query();
        if (VWP::isWarning($r)) {
            return $r;
        }
  
        $result = array();
        $r = $this->loadAssocList();
        foreach($r as $data) {
            if ($offset > 0) {
                $offset--;
            } else {
                array_push($result,$data[$this->primary_key]);
            }
        }
        return $result;    
    }
 
    /**
     * Get table properties
     * 
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
     * Set table properties
     * 
     * @param array $properties Properties
     * @return true on success  
     * @access public
     */
 
    function setProperties( $properties ) 
    {
        if (isset($properties["paging"])) {
            if (is_array($properties["paging"])) {
                $old = $properties["paging"];
                $properties["paging"] = new VDatabasePaging;
                $properties["paging"]->setProperties($old);
            }
        }
        return parent::setProperties($properties);
    } 
 
    /**
     * Reserved for future use
     *   
     * types:
     * boolean   : (allownull)
     * int       : (size,signed,allownull)
     * string    : (size,allownull)
     * timestamp : (auto,allownull)
     * datetime  : (date,time)
     * float     : (allownull,size)
     * decimal   : (allownull,size)
     * native    : Not createable!
     *
     * @todo Database insert Column support
     * @param string $columnName Column Name
     * @param string $type Data type
     * @param array $options column options
     * @param string $before Column name to insert before 
     * @access private
     */
     

    function insertColumn($columnName,$type,$options,$before) 
    {
 
    }
 
    /**
     * Update column settings
     *
     * @todo Database update column support
     * @param string $columnName
     * @param string $type Data type
     * @param unknown_type $options
     * @access private
     */
    
    function updateColumn($columnName,$type,$options) 
    {
 
    }
 
    /**
     * Set primary key
     * 
     * @todo Implement set primary key support     
     * @param string $columnName Column name
     * @param boolean $auto Auto increment
     * @access private
     */
    
    function setPrimaryKey($columnName,$auto) 
    {
 
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
     * Import data from XML
     *
     * @param string $tableId Table ID     
     * @param string $schema Schema
     * @param string $schemaSource Source of schema
     * @param string $databaseName Database name
     * @return boolean|object True on success, error or warning otherwise 
     * @access public
     */
    
    function importXMLData($tableId, &$xmlData,$schema,$schemaSource,$databaseName = null) 
    {
    	
    	// Load Schema
    	
    	$doc = new DOMDocument();    	
    	VWP::noWarn();
    	$r = $doc->loadXML($schema);
    	VWP::noWarn(false);    	
    	if (!$r) {
    		$e = VWP::raiseWarning('Invalid Table Schema!',__CLASS__,null,false);
    		return $e;
    	}

    	$tableSchema = new VSchema($doc,$schemaSource);

    	// Locate Table Declaration
    	
    	$tableElement = $tableSchema->getGlobalElementDeclByName($tableId);
    	
    	if (VWP::isWarning($tableElement)) {
    		$e = VWP::raiseWarning('Table declaration not found!',__CLASS__,null,false);
    		return $e;
    	}  
    	    	
    	// Get Table Attributes
    	
        $autoinc = null;
    	$prikey = null;
    	
    	$attrs = $tableSchema->getElementDeclAttributes($tableElement);
    	if (VWP::isWarning($attrs)) {
    		return $attrs;
    	}
    	
    	$len = count($attrs);
    	for($idx=0;$idx < $len;$idx++) {
    		$node = $attrs[$idx];
    		$name = $node->getAttribute('name');
    		
    		switch($name) {
    			case "prikey":
    				$prikey = $node->getAttribute('default');
    				break;
    			case "auto_increment":
    				$autoinc = $node->getAttribute('default');
    				break;
    			default:
    			    break;    		
    		}
    	}
    	
    	// Get Table Columns
    	
    	$columns = array();
    	
    	$sequence =& $tableSchema->getElementDeclSequence($tableElement);
    	if (VWP::isWarning($sequence)) {
    		return $sequence;
    	}
    	    	
    	if ($sequence->getLength() != 1) {
    		$e = VWP::raiseWarning('Invalid column declaration pointer!',__CLASS__,null,false);
    		return $e;    		
    	}

    	$colDeclPtr = $sequence->getItem(0);

    	$colDeclName = '';

        if ($colDeclPtr->hasAttribute('ref')) {
    		$colDeclName = $colDeclPtr->getAttribute('ref'); 
    	}
    	    	
    	if ($colDeclPtr->hasAttribute('name')) {
    		$colDeclName = $colDeclPtr->getAttribute('name'); 
    	}
    	
    	$parts = explode(':',$colDeclName);
    	$colDeclLocalName = array_pop($parts);
    	$colDeclPrefix = implode(':',$parts);
    	if (empty($colDeclPrefix)) {
    		$colDeclPrefix = null;
    	}    	
    	$colDeclNamespace = $colDeclPtr->lookupNamespaceUri($colDeclPrefix);
    	    	
    	$colDecls = $tableSchema->getElementDeclAttributes($colDeclPtr);
    	
    	if (VWP::isWarning($colDecls)) {
    		return $colDecls;
    	}
    	
    	$timeTypes = array('date','time','timestamp');
    	    	    
    	$len = count($colDecls);
    	
    	$colids = array();
    	
    	for($idx=0;$idx < $len; $idx++) {
    		$colNode = $colDecls[$idx];
    		$colName = $colNode->getAttribute('name');
    		$columns[] = $this->nameQuote($colName);
    		$colids[] = $colName;
    	}
    	
    	// Load Data
    	
    	VWP::setTimeLimit(0);
    	$data = new DOMDocument;
    	
    	VWP::noWarn();
    	$r = $data->loadXML($xmlData);
    	VWP::noWarn(false);
    	
    	if (VWP::isWarning($r)) {
    	    $xmldata = VWP::raiseWarning('Invalid source data',__CLASS__,null,false);
    	    VWP::resetTimeLimit(); 
    		return $xmldata;
    	}
    	
    	$xmlData = true;
    	
    	$datarows = $data->getELementsByTagNameNS($colDeclNamespace,$colDeclLocalName);
    	
    	$len = $datarows->length;
    	    	
    	for($p=0;$p < $len; $p++) {
    		$item = $datarows->item($p);
    		$coldata = array();
    		foreach($colids as $cid) {
    			if ($item->hasAttribute($cid)) {
    				$coldata[] = $this->quote($item->getAttribute($cid));
    			} else {
    				$coldata[] = 'NULL';
    			}
    		}
    		$vals = implode(',',$coldata);    	
    		$values = ' VALUES(' . $vals . ')';
    	    $query = 'INSERT INTO '. $this->nameQuote($this->tableName) . '(' . implode(',',$columns) . ')' . $values;
    	    	
    	    $this->setQuery($query);
    	
    	    $result = $this->query();
    	    if (VWP::isWarning($result)) {
    	    	VWP::resetTimeLimit();
    		    return $result;    		    
    	    }    		    		    		
    	}
    	    	       	
        VWP::resetTimeLimit();
    	return true;
    }
    
    
    /**
     * Load table columns
     * 
     * @return true|object True on success, error or warning otherwise
     * @access public  
     */

    function loadColumns() 
    {
        $query = "SHOW COLUMNS FROM " 
          . $this->nameQuote($this->tableName);
        $this->setQuery($query);
        $response = $this->query();
        if ($this->isError($response)) {
            return $response;
        }
  
        $response = $this->loadAssocList();
        $this->columns = array();
        $this->primary_key = null;
        foreach($response as $trow) {
            $row = array();
            foreach($trow as $key=>$val) {
                $row["_".$key] = $val;
            }
            $row["name"] = $row["_Field"];
            if ($row["_Default"] == "CURRENT_TIMESTAMP") {   
                $row["default"] = null;
            } else {
                $row["default"] = $row["_Default"];
            }
            if ($row["_Key"] == "PRI") {
                $this->primary_key = $row["name"];
            }
            array_push($this->columns,$row);
        }
  
        return true;
    }
 
    /**
     * Load table data
     * 
     * @return true|object True on success, error or warning otherwise
     * @access public  
     */
   
    function loadData() 
    {
        $limit = '';
        if (is_object($this->paging)) {
            switch($this->paging->getMode()) {
                case "page":
                    $pagesize = $this->paging->getPageSize();
                    $pagenum = $this->paging->getPageNum();
                    if ($pagesize !== null) {
                        if ($pagesize < 1) {
                            $limit = '';
                        } else {
                            if ($pagenum < 1) {
                                $pagenum = 1;
                            }
                            $length = (int) $pagesize;
                            $start = ($pagenum - 1) * $length;
                            $limit = " LIMIT $start,$length";       
                        }
                    }
                    break;
   
                default:
                    $this->raiseWarning("Unrecognized paging mode!",1,true);
                    break;
            }
        }
  
        $response = $this->loadColumns();
        if ($this->isError($response)) {
            return $response;
        }
  
        $query = "SELECT * FROM "
           . $this->nameQuote($this->tableName)
           . $limit;
  
        $this->setQuery($query);
        $response = $this->query();
        if ($this->isError($response)) {
            return $response; 
        }
  
        $response = $this->loadAssocList();
  
        $this->rows = array();
        foreach ($response as $row) {
            $new_row = array("fields"=>array());
            foreach($row as $name=>$value) {
                $columninfo = array(
                    "name"=>$name,
                    "value"=>$value
                   );    
                $new_row["fields"][$name] = $columninfo;
            }

            if ($this->primary_key === null) {    
                $row_ob = new MysqlRow;
                $row_ob->bind($this);
                $row_ob->setProperties($new_row);
                array_push($this->rows,$row_ob);    
            } else {
                $this->rows[$row[$this->primary_key]] = new VMysqlRow;
                $this->rows[$row[$this->primary_key]]->bind($this);
                $this->rows[$row[$this->primary_key]]->setProperties($new_row);        
            }         
        }
        return true;
    }

    /**
     * Get a row from a table
     * 
     * @param mixed $key Row identifier (primary key value)
     * @param string $tableName Table name
     * @param string $databaseName Database name
     * @return object Row object on success, error or warning on failure
     */

    function &getRow($key,$tableName = null,$databaseName = null) 
    {

        if ($this->columns === null) {
            $result = $this->loadColumns();
            if ($this->isError($result)) {
                return $result;
            }
        }
        
        // Handle new rows
  
        if ($key === null) {   
            $row = new VMysqlRow;
            $row->bind($this);
            $result = $row->load();
            if ($this->isError($result)) {
                return $result;
            }
            return $row;   
        }
        
        // Handle existing rows
  
        if (!isset($this->rows[$key])) {
            $this->rows[$key] = new VMysqlRow;
            $this->rows[$key]->bind($this);
            $result = $this->rows[$key]->load($key);
            if ($this->isError($result)) {
                $this->rows[$key] = $result;
            }   
        }
        return $this->rows[$key];  
    }
 
    /**
     * List Columns
     * 
     * @return array Column list
     */
    
    function listColumns() {
    	if (!is_array($this->columns)) {
    		$this->loadColumns();
    	}
    	
    	$cols = array();
    	foreach($this->columns as $row) {
    		$cols[] = $row['name'];
    	}
    	return $cols;    	
    }
    
    /**
     * Class constructor
     *
     * @param string $tableName Table name
     * @access public
     */
          
    function __construct($tableName = null) 
    {
        $this->_link = false;
        parent::__construct();
        $this->tableName = $tableName;  
    }
    
    // end class VMysqlTable
} 
