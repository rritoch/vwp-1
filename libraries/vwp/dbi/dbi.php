<?php

/**
 * Virtual Web Platform - DBI Database API
 *  
 * This file provides the primary
 * accesspoint for the default database API.
 * 
 * @package VWP
 * @subpackage Libraries.DBI  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License
 * @todo Implement create/modify table
 * @todo Implement database drivers via plugin support    
 */

class_exists("VWP") or die();

/**
 * Require folder support
 */

VWP::RequireLibrary('vwp.filesystem.folder');

/**
 * Virtual Web Platform - DBI Database API
 *  
 * This class provides the primary
 * access point for the default database API. This
 * class is extended by the VDatabase class to
 * provide core database access features for all
 * database types.  
 * 
 * @package VWP
 * @subpackage Libraries.DBI  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @todo Implement table,row,query,queryrow,queryfilter interface classes for documentation purposes
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License   
 */

  
class VDBI extends VObject 
{

    /**
     * Default database
     * 
     * @var string $_default_database Default database
     * @access private
     */
           
    static $_default_database;
 
    /**
     * Database cache
     * 
     * @var array $_databases Databases
     * @access private
     */
           
    static $_databases = array();
  
    /**
     * Quote a database identifier
     * 
     * @param string $name Identifier
     * @return string Quoted string
     * @access public
     */
                 
    function nameQuote($name) 
    {
        return $name;
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
        return $val;
    }
 
    /**
     * Test if a value is an error
     * 
     * @return boolean True if error, false otherwise
     * @access public  
     */
          
    function isError($val) 
    {
        return VWP::isError($val);
    }

    /**
     * Test if a value is a warning
     * 
     * @return boolean True if error, false otherwise
     * @access public  
     */

    function isWarning($val) 
    {
        return VWP::isWarning($val);
    }

    /**
     * Generate an error
     * 
     * @param string $errmsg Error message
     * @param integer $errno Error code
     * @param boolean $throw Send error message immediatly
     * @return VError Error
     * @access public            
     */
         
    function raiseError($errmsg,$errno = null,$throw = true) 
    {
        $this->setError($errmsg);
        return VWP::raiseError($errmsg,get_class($this),$errno,$throw);
    }

    /**
     * Generate a warning
     * 
     * @param string $errmsg Warning message
     * @param integer $errno Error code
     * @param boolean $throw Send warning message immediatly
     * @return VWarning Warning
     * @access public            
     */
  
    function raiseWarning($errmsg,$errno,$throw = true) 
    {
        $this->setError($errmsg);
        return VWP::raiseWarning($errmsg,get_class($this),$errno,$throw);
    }
  
  
    /**
     * Get instance of DBI object
     * 
     * @static
     * @return DBI Object
     * @access public      
     */
         
    public static function &getInstance() 
    {
        static $dbi;
        if (!isset($dbi)) {
            $dbi = new VDBI;
        }
        return $dbi;
    }
  
    /**
     * Get default database name
     * 
     * @static
     * @return string|false Database name or false if none provided
     * @access public
     */
         
    public static function getDefaultDatabaseName() 
    {
        if (!isset(self::$_default_database)) {
            return false;
        }
        return self::$_default_database;  
    }
 
    /**
     * Get a database object
     * 
     * @param string $databaseName
     * @return VDatabase Database on success, error or warning on failure  
     * @access public
     */
            
    function &getDatabase($databaseName = null) 
    {
    	    	
        $dbName = $databaseName;
  
        if (empty($dbName)) {
            $dbName = self::getDefaultDatabaseName();
        }
      
        // autoload database
  
        if (!isset(self::$_databases[$dbName])) {
        	        	
            $className = $dbName . "Database";   
            if (!class_exists($className)) {            	
                $dbFilename = VPATH_BASE.DS.'databases'.DS.$dbName.'.php';
                if (file_exists($dbFilename)) {                	
                    include_once($dbFilename);
                    if (class_exists($className)) {       
                        self::$_databases[$dbName] = new $className();       
                    }
                }               
            }    
        }
  
        // if not found return an error
     
        if (!isset(self::$_databases[$dbName])) {        	
            $e = VWP::raiseError(get_class($this),"Database \"$dbName\" not found!",false);
            return $e;
        }   
        // return database        
           
        return self::$_databases[$dbName];
    }
 
    /**
     * Lock a section of code
     * 
     * @param integer $lock Lock variable
     * @return integer Original lock value
     * @access public  
     */
           
    function lock(&$lock) 
    {
        $oldlock = $lock;
        $lock++;
        return $oldlock; 
    }

    /**
     * UnLock a section of code
     * 
     * @param integer $lock Lock variable
     * @return integer Original lock value  
     */
 
    function unlock(&$lock) 
    {
        $oldlock = $lock;
        $lock--;
        return $oldlock; 
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
   
        static $tlock;

        if (!isset($tlock)) {
            $tlock = 0;
        }
        
        $db = & $this->getDatabase($databaseName);
     
        if (VWP::isError($db)) {
            return $db;
        }
  
        if ($this->lock($tlock)) {
            return self::raiseError("Database does not support the listTables method!",null,false);	
        }      
        $ret = $db->listTables();
        $this->unlock($tlock);
  
        return $ret;
    }

    /**
     * Get database table
     * 
     * @param string $tableName Table Name
     * @param string $databaseName Database name
     * @return object Table object on success, error or warning on failure
     * @access public
     */ 
 
    function &getTable($tableName,$databaseName = null) 
    {
        $db = & $this->getDatabase($databaseName);
        if (VWP::isError($db)) {
            return $db;
        }
        return $db->getTable($tableName);
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
     * @access public
     */

    function &createTable($tableId,$tableName,$schema,$schemaSource,$databaseName = null) 
    {
    	$db =& $this->getDatabase($databaseName);
        if (VWP::isError($db)) {
            return $db;
        }
        return $db->createTable($tableId,$tableName,$schema,$schemaSource);
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
        $db = & $this->getDatabase($databaseName);
        if (VWP::isError($db)) {
            return $db;
        }
        return $db->getRow($key,$tableName,$databaseName);
    }
  
    /**
     * Get a tables field
     * 
     * NOT IMPLEMENTED
     * 
     * @access private    
     */
                  
    function getField($fieldName,$key = null,$tableName = null,$databaseName = null) 
    {
        $db = & $this->getDatabase($databaseName);
        if (VWP::isError($db)) {
            return $db;
        }
        return $db->getField($fieldName,$key,$tableName);
    }
 
    /**
     * Load databases
     * 
     * @access public
     */
        
    function loadDatabases() 
    {
        self::$_databases = array();
   
        $default_db = "default";
        $cfg = VWP::getConfig();
   
        if ((isset($cfg->default_database)) && ($cfg->default_database !== null)) {
            $default_db = $cfg->default_database;       
        }
        self::$_default_database = $default_db;     
    }
 
    /**
     * Get list of supported database types
     *   
     * @return array|object Database types on success, error or warning on failure  
     * @access public
     */
     
    function listDBTypes() 
    {
        $vfolder = v()->filesystem()->folder();
        $dbpath = VPATH_BASE.DS.'libraries'.DS.'vwp'.DS.'dbi'.DS.'drivers';   
        $files = $vfolder->files($dbpath);
        if (VWP::isWarning($files)) {
            return $files;
        }
        $dbtype_list = array();
        foreach($files as $file) {
            if (substr($file,strlen($file) - 4) == ".php") {
                $type = substr(ucfirst(strtolower($file)),0,strlen($file) - 4);
                array_push($dbtype_list,$type);
            }
        }
        return $dbtype_list;  
    }
 
    /**
     * List databases
     * 
     * @return array|object Database names on success, error or warning on failure  
     * @access public
     */
         
    function listDatabases() 
    {
    	    	
        $vfolder =& v()->filesystem()->folder();
        $dbpath = VPATH_BASE.DS.'databases';   
        $files = $vfolder->files($dbpath);
        
        if (VWP::isWarning($files)) {
            return $files;
        }
        
        $db_list = array();
        foreach($files as $file) {
            if (substr($file,strlen($file) - 4) == ".php") {
                array_push($db_list,substr($file,0,strlen($file) - 4));
            }
        }
        return $db_list; 
    }
 
    /**
     * Class constructor
     * 
     * @access public    
     */
     
    function __construct() 
    {
        if (!isset(self::$_default_database)) {
            $this->loadDatabases();
        }
    }
    
    // end class VDBI
}
 