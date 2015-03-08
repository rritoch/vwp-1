<?php

/**
 * Virtual Web Platform - MsSQL Table Row
 *  
 * This file provides the driver for
 * MsSQL Row Access.   
 * 
 * @package VWP
 * @subpackage Libraries.DBI.Drivers.MsSQL
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

/**
 * Require MsSQL Table support
 */

VWP::RequireLibrary('vwp.dbi.drivers.mssql.table');

/**
 * Virtual Web Platform - MsSQL Table Row
 *  
 * This class provides the driver for
 * MsSQL Row Access.   
 * 
 * @package VWP
 * @subpackage Libraries.DBI.Drivers.MsSQL
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */


class VMssqlRow extends VMssqlTable 
{

    /**
     * @var array $fields Fields
     * @access public
     */

    public $fields;

    /**
     * Set the value of a field
     * 
     * @param string $key Field identifier
     * @param mixed $val Value
     * @access public        
     */
 
    function set($key,$val = null) 
    { 
        if (!isset($this->fields[$key])) {
            $this->fields[$key] = array(
                "name"=>$key,
                "value"=>$val,
               );
        }
        $this->fields[$key]["value"] = $val;
        return $val;    
    }
 
    /**
     * Save row
     * 
     * Note: If the table does not have a  primary key 
     *    this function will fail with an error. If the
     *    columns primary key has a null value a new row
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
        foreach($this->columns as $col) {
            $columns[$col["name"]] = $col;  
        }
  
        if (empty($pkey)) {
            return VWP::raiseError("Unable to save a row without a primary key!");
        }

        if ($this->fields[$pkey]["value"] !== null) {
            $parts = array();
   
            foreach($this->fields as $n=>$v) {
                if ($n != $pkey) {
                    if ($columns[$n]["_DATA_TYPE"] != "varbinary") {
                        array_push($parts, $this->nameQuote($n) . " = " . $this->quote($v["value"]));
                    } else {
                        array_push($parts, $this->nameQuote($n) . " = " . "CAST(" . $this->quote($v["value"]) . " AS VARBINARY(MAX))");     
                    }
                }                
            }
   
            $query = "UPDATE "
                     . $this->nameQuote($this->tableName)
                     . " SET "
                     . implode(",",$parts)
                     . " WHERE "
                     . $this->nameQuote($pkey)
                     . " = "
                     . $this->quote($this->fields[$pkey]["value"]);
            
            $this->setQuery($query);
            $result = $this->query($query);
            if ($this->isError($result)) {
                return $result;
            }
                
        } else {
            $sql_cols = array();
            $sql_vals = array();
   
            foreach($this->fields as $n=>$v) {
                if ($n !== $pkey) {    
                    array_push($sql_cols,$this->nameQuote($n));
                    if ($columns[$n]["_DATA_TYPE"] != "varbinary") {
                        array_push($sql_vals,$this->quote($v["value"]));
                    } else {
                        array_push($sql_vals,"CAST(" . $this->quote($v["value"]) . " AS VARBINARY(MAX))");
                    }
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
   
            $query = "SELECT @@IDENTITY";
            $this->setQuery($query);
            $result = $this->query($query);
            if ($this->isError($result)) {
                return $this->raiseWarning($result->errmsg,null,false);
            }
            $result = $this->loadResult();
   
            $this->fields[$pkey] = array(
                "name"=>$pkey,
                "value"=>$result
            );                
        }
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
        $pkey = $this->primary_key;
        $id = $this->fields[$pkey]["value"];
        $query = "DELETE FROM "
           . $this->nameQuote($this->tableName)
           . " WHERE "
           . $this->nameQuote($pkey)
           . " = "
           . $this->quote($id);
        $this->setQuery($query);
        $result = $this->query();    
        return $result;           
    }
  
    /**
     * Load a row
     * 
     * @param mixed $oid Row Identifier (Primary key value)
     * @return true|object True on success, warning or error on failure
     * @access public
     */
         
    function load($oid = null) 
    {
        $this->fields = array();
                 
        if ($oid === null) {
            // gen new row
   
            $result = $this->loadColumns();
            if ($this->isError($result)) {
                return $result;
            }
      
            foreach($this->columns as $col) {    
                $this->fields[$col["name"]] = array(
                    "name"=>$col["name"],
                    "value"=>$col["default"],
                   ); 
            }
        } else {
        
            // fetch data
   
            if ($this->primary_key === null) {
                $top = $oid + 1;
                $query = "SELECT TOP "
                           . $top
                           . " * FROM "
                           . $this->nameQuote($this->tableName);            
            } else {            
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
            if ($this->primary_key === null) {
                if (count($result) < $top) {
                    $err = $this->raiseError("Row not found",null,false);
                    return $err;
                }
                $rowdata = $result[$top - 1];
            } else {
                if (count($result) < 1) {
                    $err = $this->raiseError("Row not found",null,false);
                    return $err;
                }
                $rowdata = $result[0];
            }
  
            foreach($rowdata as $name=>$val) {
                $this->fields[$name] = array(
                    "name"=>$name,
                    "value"=>$val
                   ); 
            }        
        }

        return true;
    }
    
    // end class VMssqlRow
} 
