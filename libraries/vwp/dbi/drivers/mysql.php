<?php

/**
 * Virtual Web Platform - MySQL Database support
 *  
 * This file provides the driver for
 * MySQL Database Access.   
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
 * Require mysql PHP extension
 */

VWP::RequireExtension('mysql');

/**
 * Require database support
 */

VWP::RequireLibrary('vwp.dbi.database');

/**
 * Require Schema Support
 */

VWP::RequireLibrary('vwp.xml.schema');


/**
 * Virtual Web Platform - MySQL Database support
 *  
 * This class provides the driver for
 * MsSQL Database Access.   
 * 
 * @package VWP
 * @subpackage Libraries.DBI.Drivers  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

class VMysqlDatabase extends VDatabase 
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
     * @var integer $_cerrno Error code
     * @access public
     */
  
    public $_cerrno = null;

    const NS_SCHEMA = "http://www.w3.org/2001/XMLSchema";
    const NS_SQLTYPES = "http://standards.vnetpublishing.com/schemas/vwp/2010/12/DBI/sqltypes";

    /**
     * Quote a database identifier
     * 
     * @param string $name Identifier
     * @return string Quoted string
     * @access public
     */
   
    function nameQuote($name) 
    {
        return '`' . mysql_real_escape_string($name) . '`';
    }

    /**
     * Create Query
     * 
     * @return object Query Object on success, error or warning otherwise
     * @access public
     */
    
    function &createQuery() 
    {
    	$query = new VMysqlQuery;
    	$query->bind($this);  
    	return $query;
    }    
    
    /**
     * Quote a database value
     * 
     * @param string $name Identifier
     * @return string Quoted string
     */
    
    function quote($val) 
    {
        if ($val === null) {
            return "NULL";
        }
        if ($val === true) {
            $val = '1';
        } elseif ($val === false) {
            $val = '0';
        }
        
        if (is_object($val)) {
    		if (strtolower(get_class($val)) == 'vtime') {
    			$val = strftime("%Y-%m-%d %H:%M:%S",$val->getPHPTime());
    		} else {
    			$val = (string)$val;
    		} 
    		
    	}
        
        return "'" . mysql_real_escape_string($val) . "'";
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
        $rows = array();
        while($row = mysql_fetch_assoc($this->_result)) {
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
        if (!function_exists('mysql_result')) {
            return $this->raiseError("mysql_result() not supported!",null,false);  
        }
        return mysql_result($this->_result,0,0);
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
            return $this->raiseError($this->_cerrmsg,$this->_cerrno,false);
        }  
  
        $this->_result = mysql_query($this->_query,$this->_link);
        if ($this->_result === false) {
            $this->_cerrmsg = mysql_error();
            $this->_cerrno = mysql_errno();   
            return $this->raiseError($this->_cerrmsg,$this->_cerrno,false);
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
            $query = "SHOW TABLES";
            $this->setQuery($query);
            $err = self::query();   
            if ($this->isError($err)) {             
                return $err;
            }
            if (!function_exists('mysql_fetch_row')) {
                return $this->raiseError("mysql_fetch_row() not supported!",null,false);
            }
               
            $this->_tables = array();
            while($row = mysql_fetch_row($this->_result)) {
                $this->_tables[$row[0]] = false;
            }
        }
        $ret = array_keys($this->_tables);  
        return $ret;
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
            $this->_tables[$tableName] = new VMysqlTable($tableName);
            $this->_tables[$tableName]->bind($this);   
        }
 
        if (is_object($this->_tables[$tableName])) {
            return $this->_tables[$tableName];
        }
        $err = $this->raiseError("Unable to load table $tableName ",null,false);
        return $err; 
    }

    /**
     * Create Table
     * 
     * Create a table from a schema
     *
     * @param string $tableId Table ID
     * @param string $tableName Table name
     * @param string $schema Schema
     * @param string $schemaSource Source of schema
     * @param string $databaseName Database name
     * @return boolean|object True on success, error or warning otherwise 
     * @access public
     */
    
    function &createTable($tableId, $tableName,$schema,$schemaSource,$databaseName = null) 
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
    	 
    	$colDecls = $tableSchema->getElementDeclAttributes($colDeclPtr);
    	
    	if (VWP::isWarning($colDecls)) {
    		return $colDecls;
    	}
    	
    	$timeTypes = array('date','time','timestamp');
    	    	    
    	$len = count($colDecls);
    	
    	for($idx=0;$idx < $len; $idx++) {
    		$colNode = $colDecls[$idx];
    		$baseType = $tableSchema->getAttributeDeclBaseType($colNode);
    		if (VWP::isWarning($baseType)) {
    			return $baseType;
    		}
    		$colName = $colNode->getAttribute('name');
    		 
    		if ($baseType->namespace == self::NS_SQLTYPES) {

    		    $nullok = $colNode->getAttribute('use') == 'required' ? false : true;

    		    $sqlnull = $nullok ? ' NULL ' : ' NOT NULL ';
    		    $sqlautoinc = $colName == $autoinc ? ' AUTO_INCREMENT ' : '';
    			    			
    		    $default = $colNode->hasAttribute('default') ? $colNode->getAttribute('default') : null;
    		
    		    $sqldefault = '';
    		    
    		    if (
    		        ($default !== null) &&    		            		        
    		        (
    		          (!in_array($baseType->name,$timeTypes)) ||
    		          ($default !== 'NOW')
    		        )
    		       ) {    		        
    		        $sqldefault = ' DEFAULT ' . $this->quote($default) . ' ';
                }

                if ($default == 'NOW' && in_array($baseType->name,$timeTypes)) {
                    $sqldefault = ' DEFAULT CURRENT_TIMESTAMP ';	
                }
                
    			switch($baseType->name) {
    				case "integer":
    					$columns[] = $this->nameQuote($colName) . ' INT ' . $sqlnull . $sqldefault . $sqlautoinc; 
    					break;
    			    case "double_precision":    				
    					$columns[] = $this->nameQuote($colName) . ' DOUBLE ' . $sqlnull . $sqldefault; 
    					break;    			    	
    				case "character_varying":
    					$size = $tableSchema->getAttributeDeclMaxLength($colNode);
    					$size = $size === null ? 1 : $size + 0;
    					if ($size > 255) {
    						$columns[] = $this->nameQuote($colName) . ' TEXT ' . $sqlnull . $sqldefault;
    					} else {
                            $columns[] = $this->nameQuote($colName) . ' VARCHAR('.$size.') ' . $sqlnull . $sqldefault;
    					}
                        break;    					
    				case "smallint":    				
    				case "bit":
    				case "bit_varying":
    				case "date":
    				case "time":
    				case "timestamp":
    				case "decimal":
    				case "real":
    				
    				case "float":
    				case "character":    				
    				case "national_character":		
                    case "national_character_varying":
                    case "interval":    					
    				default:
    			        $e = VWP::raiseWarning("Column '$colName' has an unknown type '".$baseType->type."'.",__CLASS__,null,false);
    			        return $e;    					
    			}
    		} else {
    			$e = VWP::raiseWarning("Column '$colName' has an unknown type",__CLASS__,null,false);
    			return $e;
    		}
    	}
    	
    	if (!empty($prikey)) {
    		$columns[] = 'PRIMARY KEY ('.$this->nameQuote($prikey).')';
    	}
    	
    	// Build Table
    	
    	$query = 'CREATE TABLE ' . $this->nameQuote($tableName) . '(' . implode(',',$columns) . ')';
    	
    	$this->setQuery($query);
    	
    	$result = $this->query();
    	if (VWP::isWarning($result)) {
    		return $result;
    	}
    	$this->_tables = null;
    	$table =& $this->getTable($tableName);
    	return $table;
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
            @mysql_close($this->_link);
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
  
        if (!function_exists('mysql_connect')) {
            $this->cerrmsg = "mysql_connect() not supported!";    
            return; 
        }
  
        if (!function_exists('mysql_select_db')) {
            $this->cerrmsg = "mysql_select_db() not supported!";    
            return; 
        }

        if (!function_exists('mysql_error')) {
            $this->cerrmsg = "mysql_error() not supported!";    
            return; 
        }

        if (!function_exists('mysql_errno')) {
            $this->cerrmsg = "mysql_errno() not supported!";    
            return; 
        }
  
        VWP::noWarn();   
        $this->_link = mysql_connect($this->server, $this->username, $this->password);
        VWP::noWarn(false);
  
        if ($this->_link === false) {
            $this->_cerrmsg = mysql_error();
            $this->_ccerrno = mysql_errno();    
        }
  
        VWP::noWarn();
        $seldb =  @mysql_select_db($this->database,$this->_link);
        VWP::noWarn(false);
  
        if (!$seldb) {
            $this->_cerrmsg = mysql_error();
            $this->_ccerrno = mysql_errno();
            $this->_link = false;  
        }     
    }
    
    // end class VMysqlDatabase
}


/**
 * Require MySQL table support
 */

VWP::RequireLibrary('vwp.dbi.drivers.mysql.table');

/**
 * Require MySQL row support
 */

VWP::RequireLibrary('vwp.dbi.drivers.mysql.row');

/**
 * Require query support
 */

VWP::RequireLibrary('vwp.dbi.drivers.mysql.query');


