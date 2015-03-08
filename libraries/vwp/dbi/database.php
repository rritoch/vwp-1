<?php

/**
 * Virtual Web Platform - DBI Database support
 *  
 * This file provides the default API for
 * Database Access.   
 * 
 * @package VWP
 * @subpackage Libraries.DBI  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License
 * @todo Implement create/modify table   
 */

// Restrict access
class_exists("VWP") or die();

/**
 * Require filesystem support
 */

VWP::RequireLibrary('vwp.filesystem.file');

/**
 * Virtual Web Platform - DBI Database support
 *  
 * This Class provides the base class for all databases  
 * 
 * @package VWP
 * @subpackage Libraries.DBI  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License 
 */

class VDatabase extends VDBI 
{
 
    /**
     * @var mixed $_link Database connection
     *    
     * @access private   
     */
        
    protected $_link;

    /**
     * @var array $_tables Database table cache
     *    
     * @access private   
     */
  
    protected $_tables = array();
 
    /**
     * @var boolean $_connected Connection status
     *    
     * @access private
     */
       
    protected $_connected = false;

    /**
     * @var boolean $_tables_loaded Tables in cache flag
     *    
     * @access private
     */
         
    protected $_tables_loaded = false;
 
    /**
     * @var string $_protected_prefix Protected variable name prefix
     *    
     * @access private
     */
       
    protected $_protected_prefix = "db";
 
    /**
     * @var string $_db_class Database Class
     *    
     * @access private
     */
       
    protected $_db_class;

    /**
     * @var string $_db_class Database Class
     * @access private
     */
  
    protected $_db_classfile;

    /**  
     * Stored database Query
     * 
     * @var string $_query Database query         
     * @access private
     */

    protected $_query;
 
    /**
     * Get database type
     * 
     * @return string Database type
     * @access public
     */
         
    function getDBType() 
    { 
        if (preg_match('#^V(.*?)Database$#i',get_parent_class($this),$match)) {
            return $match[1];
        }
        return null;
    }
 
    /**
     * Set database query string
     * 
     * @param string $query Database query
     * @return true|object True on success, error or warning on failure
     */
           
    function setQuery($query) 
    {
        $this->_query = $query;
        return true;
    }
 
    /**
     * Drop database
     * 
     * @return true|object True on success, error or warning on failure     
     */
     
    function drop() 
    {
        return $this->raiseError(VText::_("Access Denied!"));  
    }
 
    /**
     * Connect to database
     * 
     * @return boolean True on success
     * @access public    
     */
     
    function connect() 
    {
        $loaded = $this->loadTables();
        if (!$loaded) {
            $this->disconnect();    
            return false;
        }
        $this->_connected = true;
        return true;
    }
 
    /**
     * Disconnect from database
     * 
     * @return boolean True on success
     * @access public
     */
         
    function disconnect() 
    {
        $this->unloadTables();
        $this->_connected = false;
        return true;
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
        return $this->raiseError(VText::_("Table Not Found!"));
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
        $table = & $this->getTable($tableName);
        if ($this->isError($table)) {
            return $table;
        }
        return $table->getRow($key);
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
        $table = & $this->getTable($tableName);
        if ($this->isError($table)) {
            return $table;
        }
        return $table->getField($key,$fieldName);
    }
 
    /**
     * Load tables
     * 
     * @return true|object True on success, error or warning otherwise
     * @access public
     */
       
    function loadTables() 
    {
        $this->_tables_loaded = true;
        return true;
    }

    /**
     * Create Query
     * 
     * @return object Query Object on success, error or warning otherwise
     * @access public
     */
    
    function &createQuery() {
    	$err =  VWP::raiseWarning('Database does not support complex queries!',__CLASS__,null,false);
    	return $err;
    }
        
    /**
     * UnLoad tables
     * 
     * Clears table cache
     *     
     * @return true|object True on success, error or warning otherwise
     * @access public
     */ 
 
    function unloadTables() 
    {
        $this->_tables = null;
        $this->_tables_loaded = false;
        return true;
    }
 
    /**
     * Class constructor
     * 
     * @access public
     */
         
    function __construct() 
    {
        $this->_db_class = get_class($this);
        $this->loadTables(); 
    }
    
    // end class VDatabase
} 
