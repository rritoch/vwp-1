<?php

/**
 * VWP Database configuration model 
 *  
 * @package    VWP
 * @subpackage Models
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @link http://www.vnetpublishing.com VNetPublishing.Com 
 * @copyright (c) Ralph Ritoch - All rights reserved
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License
 */

/**
 * Require Model Support
 */

VWP::RequireLibrary('vwp.model');

/**
 * Require Folder Support
 */

VWP::RequireLibrary('vwp.filesystem');

/**
 * VWP Database configuration model 
 *  
 * @package    VWP
 * @subpackage Models
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @link http://www.vnetpublishing.com VNetPublishing.Com 
 * @copyright (c) Ralph Ritoch - All rights reserved
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License
 */

class VWP_Model_DBIConfig extends VModel 
{
	/**	 
	 * Database cache
	 * 
	 * @static
	 * @var array $databases Database cache
	 * @access public
	 */
	
    public static $databases = array();
 
    /**     
     * Get list of supported database types
     * 
     * @return array|object Database types on success, error or warning otherwise
     * @access public
     */
    
    function getDBTypes() 
    {
        $dbi = & VDBI::getInstance();
        $types = $dbi->listDBTypes();
        if (VWP::isWarning($types)) {
            return $types;
        }
        $dbtypelist = array();
        foreach($types as $t) {
            $typeName = str_replace("sql","SQL",ucfirst(strtolower($t)));
            $dbtypelist[$t] = $typeName;
        }
        return $dbtypelist;
    }
 
    /**     
     * Save database configuration
     * 
     * If Database ID is null, the _id configuration setting is used
     * 
     * @param array $dbicfg Configuration settings
     * @param string $dbid Database ID
     * @return boolean|object True on success, error or warning otherwise
     */
    
    function saveDatabase($dbicfg,$dbid) 
    {
  
        if (empty($dbid)) {
            $dbid = $dbicfg["_id"];
        }
    
        $ltype = strtolower($dbicfg["_type"]);
  
        $new_info = array('_id'=>$dbid,'_type'=>$dbicfg["_type"]);
  
        $dbfile = VPATH_BASE.DS.'databases'.DS.$dbid.'.php';
        $nl = "\r\n";
        $className = $dbid . "Database";
        $parentClassName = "V" . ucfirst($ltype) . "Database";
    
        $file = '<' . '?php' . $nl
               . $nl
               . "VWP::RequireLibrary('vwp.dbi.drivers." .$ltype . "');".$nl
               . $nl
               . "class " . $className . " extends " . $parentClassName . " {" . $nl;

        foreach($dbicfg as $key=>$val) {
            if (substr($key,0,1) != "_") {
                $file .= ' var $' . $key . " = '" . addslashes($val) . "';" . $nl;
                $new_info[$key] = $val;   
            }
        }         
  
        $file .= ' var $' . '_classfile = __FILE__;' . $nl
              . '}' . $nl;
        $vfile =& v()->filesystem()->file();
  
        $result = $vfile->write($dbfile,$file);
        if (VWP::isWarning($result)) {
            return $result;
        }
        self::$databases[$dbid] = $new_info;
        return $dbid;         
    }
 
    /**     
     * Get database configuration settings
     * 
     * @param string $dbid Database ID
     * @return array|object Configuration settings on success, error or warning otherwise
     * @access public
     */
    
    function getDatabaseInfo($dbid) 
    {
        	    	
        if (!isset(self::$databases[$dbid])) {
        	
        	
            $dbi = & VDBI::getInstance();
                                   
            $dbinfo = array();
            $db = $dbi->getDatabase($dbid);            
            if (VWP::isWarning($db)) {
                return $db;
            }
            
            $type = get_parent_class($db);
            $type = ltrim($type,"V");
            if (substr(strtolower($type),strlen($type) - 8) == "database") {
                $type = substr($type,0,strlen($type) - 8);
            }
            $dbinfo = array("_id"=>$dbid,"_type"=>$type);
            $dbvars = get_object_vars($db);
            foreach($dbvars as $key=>$val) {
                if (substr($key,0,1) != "_") {
                    if (is_string($val) || $val === null) {
                        $dbinfo[$key] = $val;
                    }
                }
            }
            self::$databases[$dbid] = $dbinfo;
        }
        return self::$databases[$dbid];
    }
 
    /**     
     * Get list of databases
     * 
     * @return array|object Database list on success, error or warning otherwise
     * @access public
     */
    
    function getDatabases() 
    {
    	
    	
        $dbi = & VDBI::getInstance();
        
        $dblist = $dbi->listDatabases();  
        if (VWP::isWarning($dblist)) {
            return $dblist;
        }
        
        $databases = array();
        foreach($dblist as $dbid) {        	
            $dbinfo = $this->getDatabaseInfo($dbid);            
            if (!VWP::isWarning($dbinfo)) {
                array_push($databases,$dbinfo);
            }
        }
        
        return $databases;
    }
 
    // End class VWP_Model_DBIConfig
}
