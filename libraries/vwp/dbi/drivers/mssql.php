<?php

/**
 * Virtual Web Platform - MsSQL Database support
 *  
 * This file provides the driver for
 * MsSQL Database Access.   
 * 
 * @package VWP
 * @subpackage Libraries.DBI.Drivers
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License   
 */

// Restrict access

class_exists("VWP") or die();

/**
 * Require mssql PHP extension
 */

VWP::RequireExtension('mssql');


/**
 * Require database support
 */

VWP::RequireLibrary('vwp.dbi.database');

/**
 * Virtual Web Platform - MsSQL Database support
 *  
 * This class provides the driver for
 * MsSQL Database Access.   
 * 
 * @package VWP
 * @subpackage Libraries.DBI.Drivers.MsSQL
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License   
 */
  
class VMssqlDatabase extends VDatabase 
{

    /**
     * @var string $username Username
     * @access public
     */
     
    public $username = null;
 
    /**
     * @var string $password Password
     * @access public
     */
      
    public $password = null;

    /**
     * @var string $database Database
     * @access public
     */
  
    public $database = null;

    /**
     * @var string $server Server
     * @access public
     */
  
    public $server = 'localhost';
 
    /**
     * @var mixed $_result Request result
     * @access public
     */
      
    public $_result = null;
 
    /**
     * @var string $_cerrmsg Error message
     * @access public
     */
     
    public $_cerrmsg = null; 

    /**
     * Quote a database identifier
     * 
     * @param string $name Identifier
     * @return string Quoted string
     * @access public
     */
 
    function nameQuote($name) 
    {  
        return '[' . str_replace("\0",'',str_replace("]","]]",$name)) . ']';
    }

    /**
     * Quote a database value
     * 
     * @param string $name Identifier
     * @return string Quoted string
     * @access public
     */
    
    function quote($val) 
    {
        if ($val === null) {
            return "NULL";
        }
        if(is_numeric($val)) {
            return $val;
        }   
        return "'" . str_replace("\0","'+NULL+'",str_replace("'","' + CHAR(39) + '",$val)) . "'";  
    }    
 
    /**
     * Get database response as associative list
     * 
     * @return array|object Response data on success, error or warning on failure
     * @access public
     */       

    function loadAssocList() 
    {
        if (($this->_result === false) || ($this->_result === null)) {
            return $this->raiseError("Trying to Loading data without a response!",null,false);    
        }
        if (!function_exists('mssql_fetch_assoc')) {
        	
        	if (function_exists('sqlsrv_fetch_array')) {
                $rows = array();
                while($row = sqlsrv_fetch_array($this->_result,SQLSRV_FETCH_ASSOC)) {
                    array_push($rows,$row);
                }
                return $rows;        		        		        		
        	} else {
                return $this->raiseError("mssql_fetch_assoc() not supported!",null,false);
        	}  
        }
        $rows = array();
        while($row = mssql_fetch_assoc($this->_result)) {
            array_push($rows,$row);
        }
        return $rows;
    }

    /**
     * Get database response as single data field
     * 
     * @return mixed Response data on success, error or warning on failure
     * @access public
     */ 
 
    function loadResult() 
    {
        if (($this->_result === false) || ($this->_result === null)) {
            return $this->raiseError("Trying to Loading data without a response!",null,false);    
        }
        if (!function_exists('mssql_result')) {
        	if (function_exists('sqlsrv_fetch')) {
        		$f = sqlsrv_fetch($this->_result);
        		if ($f === true) {
        			return sqlsrv_get_field($this->_result,0);
        		} else {
        			return $f;
        		}
        		
        	} else {        	
                return $this->raiseError("mssql_result() not supported!",null,false);
        	}  
        }
        return mssql_result($this->_result,0,0);
    }
 
    /**
     * Send database query
     * 
     * @return true|object True on success, error or warning on failure
     * @access public
     */       
 
    function query() 
    {
        if ($this->_link === false) {
            return $this->raiseError($this->_cerrmsg,null,false);
        }
  
        if (!function_exists('mssql_query')) {
        	if (function_exists('sqlsrv_query')) {
                $this->_result = sqlsrv_query($this->_link,$this->_query);
                if ($this->_result === false) {
                	$sqlsrverrors = sqlsrv_errors();
                    $errmsg = '[state:' . $sqlsrverrors[0]['SQLSTATE'] . '] ' . $sqlsrverrors[0]['message'] . " IN QUERY: " . $this->_query;
                    $errcode = $sqlsrverrors[0]['code'];   
                    return $this->raiseError($errmsg,$errcode,false);
                }
                return true;        		
        	} else {
                return $this->raiseError("mssql_query() not supported!",null,false);
        	}
        }
  
        if (!function_exists('mssql_get_last_message')) {
            return $this->raiseError("mssql_get_last_message() not supported!",null,false);
        }
  
        $this->_result = mssql_query($this->_query,$this->_link);
        if ($this->_result === false) {
            $errmsg = mssql_get_last_message() . " IN QUERY: " . $this->_query;   
            return $this->raiseError($errmsg,null,false);
        }
        return true;
    }
 
    /**
     * List database tables
     * 
     * @param string $databaseName Database name
     * @return array|object Table names on success, error or warning on failure
     * @access public  
     */
   
    function listTables($databaseName = null) 
    {

        if ($this->_tables === null) {
            $query = "select * from SYSOBJECTS where TYPE = " 
                     . $this->quote('U') .  " order by NAME";
            $this->setQuery($query);
            if ($this->isError($err = $this->query())) {             
                return $err;
            }
            if (!function_exists('mssql_fetch_row')) {
            	if (function_exists('sqlsrv_fetch_array')) {
            	    $this->_tables = array();
                    while($row = sqlsrv_fetch_array($this->_result,SQLSRV_FETCH_NUMERIC)) {
                        $this->_tables[$row[0]] = false;
                    }            		
            	} else {
                    return $this->raiseError("mssql_fetch_row() not supported!",null,false);
            	}
            } else {
                $this->_tables = array();
                while($row = mssql_fetch_row($this->_result)) {
                    $this->_tables[$row[0]] = false;
                }
            }
        }
        return array_keys($this->_tables);
    }

    /**
     * Get database table
     * 
     * @param string $tableName Table Name
     * @param string $databaseName Database name
     * @return object Table object on success, error or warning on failure
     */ 
  
    function &getTable($tableName,$databaseName = null) 
    {
        if (count($this->_tables) < 1) {
            $result = self::listTables();
            if ($this->isError($result)) {
                return $result;
            }
        }

        if (!isset($this->_tables[$tableName])) {
            $err = $this->raiseError("Table $tableName not found",null,false);
            return $err; 
        }
  
        if (!is_object($this->_tables[$tableName])) {
            $this->_tables[$tableName] = new VMssqlTable($tableName);
            $this->_tables[$tableName]->bind($this);   
        }
 
        if (is_object($this->_tables[$tableName])) {
            return $this->_tables[$tableName];
        }
        $err = $this->raiseError("Unable to load table $tableName ",null,false);
        return $err; 
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
        $args = func_get_args();
        $table = & self::getTable($tableName,$databaseName);   
        if ($this->isError($table)) {
            return $table;
        }   
        return $table->getRow($key,$tableName,$databaseName);   
    }
 
    /**
     * Class destructor
     * 
     * @access public
     */
         
    function __destruct() 
    {  
        if (($this->_link !== false) && ($this->_link !== null)) {    
            VWP::noWarn();
            if (function_exists('mssql_close')) {
                @mssql_close($this->_link);
            }
            if (function_exists('sqlsrv_close')) {
                @sqlsrv_close($this->_link);
            }            
            VWP::noWarn(false);
        }
    }
 
    /**
     * Class constructor
     * 
     * @access public
     */
   
    function __construct() 
    {

        if ($this->_link === false) {
            parent::__construct();
            return;
        }
  
        $this->_tables = null;  
  
        if (function_exists('mssql_connect')) {
        
  
            if (!function_exists('mssql_select_db')) {
                $this->cerrmsg = "mssql_select_db() not supported!";    
                return; 
            }

            if (!function_exists('mssql_get_last_message')) {
                $this->cerrmsg = "mssql_get_last_message() not supported!";    
                return; 
            }
        
            VWP::noWarn();   
            $this->_link = mssql_connect($this->server, $this->username, $this->password);
            VWP::noWarn(false);
    
            if ($this->_link === false) {
   
                $emsg = mssql_get_last_message();
                if (empty($emsg)) {
                    $tmp = VWP::getLastError();
                    $emsg = $tmp[1];
                }

                $this->_cerrmsg = "Could not connect to database server: " . $emsg;
          
            } else {
 
                VWP::noWarn();  
                $seldb = @ mssql_select_db($this->database,$this->_link);
                VWP::noWarn(false);
    
                if (!$seldb) {
                    $this->_cerrmsg = mssql_get_last_message();
                    $this->_link = false;  
                }    
            }
        } else {
        	if (function_exists('sqlsrv_connect')) {

        
        		$connectionInfo = array("UID" => $this->username, "PWD" => $this->password, "Database"=>$this->database);
        		
                VWP::noWarn();                
                $this->_link = sqlsrv_connect($this->server,$connectionInfo);
                VWP::noWarn(false);
    
                if ($this->_link === false) {
   
                	$sqlsrverrors = sqlsrv_errors();
                	$errcode = $sqlsrverrors[0]['code'];                	
                    $emsg = 'Error #' . $errcode . ' [state:' . $sqlsrverrors[0]['SQLSTATE'] . '] ' . $sqlsrverrors[0]['message'] . " ON CONNECT";                                     	                                       
                    if (empty($emsg)) {
                        $tmp = VWP::getLastError();
                        $emsg = $tmp[1];
                    }
                    $this->_cerrmsg = "Could not connect to database server: " . $emsg;
          
                }
                return;
                      		
        	} else {
                $this->cerrmsg = "mssql_connect() not supported!";    
                return;
        	}
        } 
        
    }
    
    // end class VMssqlDatabase
} 

/**
 * Require MsSQL Row support
 */

VWP::RequireLibrary('vwp.dbi.drivers.mssql.row');

/**
 * Require MsSQL Table support
 */

VWP::RequireLibrary('vwp.dbi.drivers.mssql.table');



