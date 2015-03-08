<?php

/**
 * Virtual Web Platform - MsSQL Table
 *  
 * This file provides the driver for
 * MsSQL Table Access.   
 * 
 * @package VWP
 * @subpackage Libraries.DBI.Drivers.MsSQL
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License   
 */

/**
 * Require MsSQL Database support
 */

VWP::RequireLibrary('vwp.dbi.drivers.mssql');

/**
 * Require paging support
 */

VWP::RequireLibrary('vwp.dbi.paging');

/**
 * Require filter support
 */

VWP::RequireLibrary('vwp.dbi.filter');

/**
 * Virtual Web Platform - MsSQL Table
 *  
 * This class provides the driver for
 * MsSQL Table Access.   
 * 
 * @package VWP
 * @subpackage Libraries.DBI.Drivers.MsSQL  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License   
 */

class VMssqlTable extends VMssqlDatabase 
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
       
    public $rows = array();
 
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
     * @return object Filter object
     */
  
    function createFilter() 
    {
        return new VDatabaseFilter;
    }
        
    /**
     * Get Matches
     * 
     * @param obect $filter Filter
     * @return array|object Matched column identifiers on success, error or warning on failure
     * @access public        
     */
         
    function getMatches($filter) 
    {

        $values = $filter->m_values;
        $match_types = $filter->m_operators;
        $connector = $filter->m_connector;
 
        if (empty($this->primary_key)) {
            $this->loadColumns();
            if (empty($this->primary_key)) {    
                return array();
            }
        }
          
        $sql_key = $this->nameQuote($this->primary_key);
        $sql_table = $this->nameQuote($this->tableName);
  
        $mlist = array();
  
        foreach($values as $key=>$val) {
            $t = isset($match_types[$key]) ? $match_types[$key] : '=';
            $sql = $this->nameQuote($key) . " " . $t . " " . $this->quote($val);
            array_push($mlist,$sql);
        }
  
        $sql_where = '';
        if (count($mlist) > 0) {
            $sql_where = " WHERE " . implode(" ".$connector." ",$mlist);  
        }
     
        $query = "SELECT "
                 .$sql_key
                 ." FROM "
                 .$sql_table
                 ." "
                 .$sql_where;
        $this->setQuery($query);
        $r = $this->query();
        if (VWP::isWarning($r)) {
            return $r;
        }
  
        $result = array();
        $r = $this->loadAssocList();
        foreach($r as $data) {
            array_push($result,$data[$this->primary_key]);
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
     * Get paging object
     *   
     * @return object Table paging
     * @access public
     */
   
    function &getPaging() 
    {
        if ($this->_paging === null) {
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
        $this->paging =& $paging;
    }

    /**
     * Load table columns
     * 
     * @return true|object True on success, error or warning otherwise
     * @access public  
     */
         
    function loadColumns() 
    {
  
        $query = "SELECT * FROM "  
                . $this->nameQuote("INFORMATION_SCHEMA")
                . "."
                . $this->nameQuote("Columns")
                . " WHERE TABLE_NAME = "
                . $this->quote($this->tableName);
          

        $this->setQuery($query);
        $response = $this->query();
        if ($this->isError($response)) {
            return $response;
        }
  
        $response = $this->loadAssocList();
        $this->columns = array();
        foreach($response as $trow) {
            $row = array();
            foreach($trow as $key=>$val) {
                $row["_".$key] = $val;
            }
            $row["name"] = $row["_COLUMN_NAME"];
   
            if (strlen(trim($row["_COLUMN_DEFAULT"])) > 0) {
                $query = "SELECT " . $row["_COLUMN_DEFAULT"];
                $this->setQuery($query);
                $r = $this->query();
                if ($this->isError($r)) {
                    $row["default"] = null;
                } else {
                    $row["default"] = $this->loadResult();
                }
            } else {
                $row["default"] = null;
            }
            array_push($this->columns,$row);
        }
  
        //  get primary key
  
        $query = 'SELECT [name] '
                 . 'FROM syscolumns ' 
                 . 'WHERE [id] IN (SELECT [id] 
                        FROM sysobjects 
                        WHERE [name] = '
                . $this->quote($this->tableName)
                . ') AND colid IN (SELECT SIK.colid 
                      FROM sysindexkeys SIK 
                      JOIN sysobjects SO ON SIK.[id] = SO.[id]  
                      WHERE SIK.indid = 1
                      AND SO.[name] = '
                . $this->quote($this->tableName)
                . ')';

        $this->setQuery($query);
        $response = $this->query();
        if ($this->isError($response)) {
            return $response;
        }
  
        $response = $this->loadAssocList();
        if (count($response) > 0) {
            $this->primary_key = $response[0]["name"];  
        } else {
            $this->primary_key = null;
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
                            $limit = "$start,$length";       
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
                $row_ob = new MssqlRow;
                $row_ob->bind($this);
                $row_ob->setProperties($new_row);
                array_push($this->rows,$row_ob);    
            } else {
                $this->rows[$row[$this->primary_key]] = new VMssqlRow;
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
     * @access public
     */
  
    function &getRow($key,$tableName = null,$databaseName = null) 
    {

        if ($this->columns === null) {
            $result = $this->loadColumns();
            if ($this->isError($result)) {
                return $result;
            }
        }
        
        //  Handle new rows
  
        if ($key === null) {   
            $row = new VMssqlRow;
            $row->bind($this);
            $result = $row->load();
            if ($this->isError($result)) {
                return $result;
            }
            return $row;   
        }
  
        // Handle existing rows
  
        if (!isset($this->rows[$key])) {
            $this->rows[$key] = new VMssqlRow;
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
     * @access public
     */
 
    function __construct($tableName = null) 
    {
        $this->_link = false;
        parent::__construct();
        $this->tableName = $tableName;  
    }
    
    // end class VMssqlTable
}
 