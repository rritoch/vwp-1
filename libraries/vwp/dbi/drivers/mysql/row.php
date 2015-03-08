<?php

/**
 * Virtual Web Platform - MySQL Table Row
 *  
 * This file provides the driver for
 * MySQL Row Access.   
 * 
 * @package VWP
 * @subpackage Libraries.DBI.Drivers.MySQL
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License   
 */

/**
 * Require MySQL Table Support
 */

VWP::RequireLibrary('vwp.sys.time');

VWP::RequireLibrary('vwp.dbi.drivers.mysql.table');

/**
 * Virtual Web Platform - MySQL Table Row
 *  
 * This class provides the driver for
 * MySQL Row Access.   
 * 
 * @package VWP
 * @subpackage Libraries.DBI.Drivers.MySQL
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License
 */

class VMysqlRow extends VMysqlTable 
{

    /**
     * @var array $fields Fields
     * @access public
     */
       
    public $fields;
  
    /**
     * Row Id
     * 
     * @var object Id
     */
    
    public $id;
        
    /**
     * @var boolean $_new
     */
    
    public $_new = true;
    
    /**
     * Get Id
     * 
     * @return VDBI_MultiFieldIdentity|VDBI_SingleFieldIdentity
     * @access public
     */
    
    function &getId() 
    {
    	if (!isset($this->id)) {
    		if ($this->primary_key === null) {    			
        	    $this->id = new VDBI_MultiFieldIdentity($this);	
            } else {
        	    $this->id = new VDBI_SingleFieldIdentity($this);    			    			
    		}
    	}    	
    	return $this->id;
    }
    
    /**
     * Set the value of a field
     * 
     * @param string $field Field identifier
     * @param mixed $value Value
     * @access public        
     */   
 
    function &set($field,$value) 
    { 
    	$fields = $this->fields;
    	
        if (!isset($fields[$field])) {
            $fields[$field] = array(
                "name"=>$field,
                "value"=>$value,
            );
        }
        $fields[$field]["value"] = $value;
        $this->fields = $fields;
        return $value;    
    }
  
    /**
     * Get Field
     * 
     * @param string $field Field name
     * @param mixed $default Default value
     * @return mixed field value
     * @access public  
     */           

     public function &get($field, $default=null) 
     {
     	
        $myFields = $this->fields;        
        if (isset($myFields[$field]["value"])) {
            return $myFields[$field]["value"];
        }
        return $default;
     }


    /**
     * Get all fields
     *
     * @param boolean $public Return public properties
     * @return Object properties
     * @access public  
     */         

    function getAll( $public = true ) 
    {
        if (!isset($this->fields)) {
            return array();
        }
        $vars = $this->fields;
        if ($public) {
            foreach ($vars as $key => $value) {
                if ('_' == substr($key, 0, 1)) {
                    unset($vars[$key]);
                } else {
                    $vars[$key] = $vars[$key]['value'];
                }
            }
        } else {
            foreach ($vars as $key => $value) {
                $vars[$key] = $vars[$key]['value'];
            }        
        }
        return $vars;
    }
   
    /**
     * Save row
     * 
     * Note: If the table does not have a  primary key 
     *    this function will fail with an error. If the
     *    rows primary key has a null value a new row
     *    will be inserted. The primary key must be an
     *    identity column or else the primary key may be 
     *    filled in with a useless value.
     *    
     * @return true|object Returns true on success or an error on failure
     * @access public    
     */
                        
    function save() 
    {
  
        $pkey = $this->primary_key;
  

        $columns = array();
        
        $myColumns = $this->columns;
        
        foreach($myColumns as $col) {
            $columns[$col["name"]] = $col;  
        }
    
        $myFields = $this->fields;
        

        $this->id = null;
                                        
        if ($this->getId()->getKey() !== null) {
      
        	$rid = $this->id->getKey();
        	
        	if (is_array($rid)) {
        		$m = $rid;
        	} else {
        		$m = array($pkey => $this->get($pkey));
        	}
        	       	        	
        	if (!$this->_new) {
                $parts = array();

                foreach($myFields as $n=>$v) {
                    if ($n != $pkey) {     
                        array_push($parts, $this->nameQuote($n) . " = " . $this->quote($v["value"]));
                    }                
                }
   
                

                $conds = array();
                foreach($m as $key=>$val) {
                  $conds[] = $this->nameQuote($key). " = ". $this->quote($val);
                }
                
                $where = " WHERE " . implode(" AND ",$conds);
                
                $query = "UPDATE "
                  . $this->nameQuote($this->tableName)
                  . " SET "
                  . implode(",",$parts)
                  . $where
                  . " LIMIT 1";
                              
                $this->setQuery($query);
   
                $result = $this->query();
                if ($this->isWarning($result)) {
                    return $result;
                }
        	} else {
                $sql_cols = array();
                $sql_vals = array();
   
                foreach($myFields as $n=>$v) {                        
                    array_push($sql_cols,$this->nameQuote($n));
                    array_push($sql_vals,$this->quote($v["value"]));                            
                }
                $query = "INSERT INTO "
                     . $this->nameQuote($this->tableName)
                     . " ("
                     . implode(",",$sql_cols)
                     . ") VALUES ("
                     . implode(",",$sql_vals)
                     . ")";               
                $this->setQuery($query);
                $result = $this->query($query);
                if ($this->isError($result)) {
                    return $result;
                }
            	
            }
            
        } else {

            $sql_cols = array();
            $sql_vals = array();
   
            foreach($myFields as $n=>$v) {
                if ($n !== $pkey) {    
                    array_push($sql_cols,$this->nameQuote($n));
                    array_push($sql_vals,$this->quote($v["value"]));
                }        
            }
            $query = "INSERT INTO "
                     . $this->nameQuote($this->tableName)
                     . " ("
                     . implode(",",$sql_cols)
                     . ") VALUES ("
                     . implode(",",$sql_vals)
                     . ")";               
            $this->setQuery($query);
            $result = $this->query($query);
            if ($this->isError($result)) {
                return $result;
            }
   
            if ($pkey !== null) {
                $query = "SELECT last_insert_id()";
                $this->setQuery($query);
                $result = $this->query($query);
                if ($this->isError($result)) {
                    return $this->raiseWarning($result->errmsg,null,false);
                }
                $result = $this->loadResult();
   
                $myFields[$pkey] = array(
                    "name"=>$pkey,
                    "value"=>$result
                );
            
                $this->fields = $myFields;
            }                
        }
                
        $this->_new = false;
        
        $this->id = null;
        $this->getId();        
        return true;
    }

    /**
     * Delete row
     * 
     * @return true|object True on success, warning or error on failure
     * @access public
     */
   
    function delete() 
    {
    	
    	
    	$pkey = $this->getId()->getKey();
    	if ($pkey === null) {
    		$this->id = null;    		    	
    		return true;
    	}
    	
    	$cond = array();
        if (is_array($pkey)) {
        	foreach($pkey as $key=>$value) {
        	  $cond[] = $this->nameQuote($key)
                  . " = "
                  . $this->quote($value);        		
        	}
        } else {
        	$cond[] = $this->nameQuote($this->primary_key)
                  . " = "
                  . $this->quote($pkey);
        }    	
    	
    	$where = " WHERE " . implode(" AND ",$cond);
                
        $query = "DELETE FROM " 
           . $this->nameQuote($this->tableName)
           . $where
           . " LIMIT 1";
           
        $this->setQuery($query);
        $result = $this->query();
    
        $this->id = null;
        return $result;           
    }
    
    /**
     * Load a row
     * 
     * Note: Use null as the row identifier to create a new row
     * 
     * @param mixed $oid Row Identifier (Primary key value)
     * @return true|object True on success, warning or error on failure
     */
   
    function load($oid = null) 
    {
    	    	    
        $this->fields = array();
        
        $myFields = $this->fields;
                         
        if ($oid === null) {
            // gen new row
            $this->_new = true;

            $this->id = null;
            $this->getId();
            
            $result = $this->loadColumns();
                        
            if ($this->isError($result)) {
                return $result;
            }

            $myColumns = $this->columns;
            
            foreach($myColumns as $col) {    
                $myFields[$col["name"]] = array(
                    "name"=>$col["name"],
                    "value"=>$col["default"],
                ); 
            }
            $this->fields = $myFields;
        } else {
            $this->_new = false;
            $this->id = null;
            $id =& $this->getId();
            
            // fetch data
   
            if (!isset($this->primary_key)) {
            	            	
            	if (empty($this->columns)) {
            		$this->loadColumns();
            	}
            	
            	$colTypes = array();
            	foreach($this->columns as $colInfo) {
            		$colTypes[$colInfo['name']] = $colInfo['_Type'];
            	}
            	
                $oid = VDBI_MultiFieldIdentity::decodeKey($oid);
                                
                $cond = array();
                foreach($oid as $key=>$value) {
                	if ($value !== null && $colTypes[$key] == 'timestamp') {
                        $t = $value;
                        $value = new VTime;
                        $value->setPHPTime($t);                		
                	}
                	
                	$cond[] = $this->nameQuote($key) . '=' . $this->quote($value);
                }
                $where = " WHERE " . implode(" AND ",$cond);
                            	
                $query = "SELECT * FROM "
                        . $this->nameQuote($this->tableName)
                        . $where
                        . " LIMIT 1";                
                            
            } else {
            	$oid = (string)$oid;            
                $query = "SELECT * FROM "
                        . $this->nameQuote($this->tableName)            
                        . " WHERE "
                        . $this->nameQuote($this->primary_key)
                        . " = "
                        . $this->quote($oid);
            }

            $this->setQuery($query);
            $result = $this->query();

            if ($this->isError($result)) {
                return $result;
            }
   
            $result = $this->loadAssocList();
            if ($this->isError($result)) {
                return $result;
            }
   
            $err = null;

            if (count($result) < 1) {
                $err = $this->raiseError("Row not found",null,false);
                return $err;
            }
            $rowdata = $result[0];
   
            if (!isset($this->columns)) {
            	$this->loadColumns();
            }
            
            $coltypes = array();
            foreach($this->columns as $cinfo) {
            	$coltypes[$cinfo['name']] = $cinfo['_Type'];
            }
            
            foreach($rowdata as $name=>$val) {
            	if ($val !== null && $coltypes[$name] == 'timestamp') {
            		$t = strtotime($val);
            		$val = new VTime();
            		$val->setPHPTime($t);
            	}
            	
                $myFields[$name] = array(
                    "name"=>$name,
                    "value"=>$val
                   ); 
            }
            $this->fields = $myFields;
            $this->id = null;
            $this->getId();                    
        }

        return true;
    }

    // end class VMysqlRow
} 

