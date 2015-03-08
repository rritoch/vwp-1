<?php

/**
 * Virtual Web Platform - Filter support
 *  
 * This file provides filter support
 * 
 * @package VWP
 * @subpackage Libraries.DBI  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License    
 */

class_exists("VWP") or die();

/**
 * Virtual Web Platform - filter support
 *  
 * This class provides filter support to database tables
 * 
 * @package VWP
 * @subpackage Libraries.DBI  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License   
 */

class VDatabaseFilter extends VObject 
{
    
    /**
     * Connector
     * 
     * @var string $m_connector Connector   
     * @access public
     */
                 
    public $m_connector = "and";
    
    /**
     * @var array $m_values Match values
     * @access public
     */
          
    public $m_values = array();

    /**
     * @var array $m_maxresults Maximum results
     * @access public
     */

    public $m_maxresults = null;

    /**
     * @var array $m_offset Offset
     * @access public
     */

    public $m_offset = 0;
   
    /**
     * @var array $m_operators Match operators
     * @access public
     */
   
    public $m_operators = array();

    /**
     * @var array $s_keys Sort Keys
     * @access public
     */
    
    public $s_keys = array();

    /**
     * Set Maximum Results
     * 
     * @param integer Maximum results
     */
              
    function setMaxResults($max) 
    {
        $this->m_maxresults = $max;
    }
    
    /**
     * Set offset
     * 
     * @param integer $index Offset
     * @access public               
     */
    
    function setOffset($index) 
    {
        $this->m_offset = $index;
    }         
    
    /**
     * Insert a sort Field
     * 
     * @param string $field Field
     * @param integer $dir Direction (1 Ascending, -1 Decending)
     * @param integer $before Index to place key at
     * @return boolean|object True on success, error or warning on failure     
     */
                  
    function insertSortKey($field,$dir = 1, $before = null) 
    {
        
        if ($before === null) {
            if (isset($this->s_keys[$field])) {
                unset($this->s_keys[$field]);
            }
            
            $this->s_keys[$field] = ($dir < 0) ? -1 : 1;
                        
        } else {
            $old_keys = $this->s_keys;
            
            $this->s_keys = array();
        
            $s = array_keys($old_keys);
            $len = count($s);

            $used = false;
            
            for($i = 0; $i < $len; $i++) {
                if ($i == $before) {
                    $this->s_keys[$field] = ($dir < 0) ? -1 : 1;
                    $used = true;    
                }
                $this->s_keys[$s[$i]] = $old_keys[$s[$i]];       
            }
            
            if (!$used) {
                $this->s_keys[$field] = ($dir < 0) ? -1 : 1;            
            }            
        }
        
        return true;    
    }
     
    /**
     * Add condition
     * 
     * @param string $key Variable name
     * @param string Match operator
     * @return true|object True on success, error or warning on failure   
     * @access public
     */
                       
    function addCondition($key,$op,$val) 
    {
        switch($op) {
            case "=":
            case "<=":
            case ">=":
            case "<>":
            case "<":
            case ">":
            // do nothing
                break;
            default:
                return VWP::raiseWarning("Invalid operator",get_class($this),null,false);
        }
    
        $this->m_operators[$key] = $op;
        $this->m_values[$key] = $val;   
    }
   
    /**
     * Remove condition
     * 
     * @param string $key Variable name
     * @param string Match operator
     * @return true|object True on success, error or warning on failure   
     * @access public
     */
     
    function removeCondition($key,$op,$val) 
    { 
        unset($this->m_operators[$key]);
        unset($this->m_values[$key]);
        return true;
    }
    
    /**
     * Set filter connector
     * 
     * Allowed values and,or
     * 
     * @param string $connector Filter connector
     * @return string Connector value  
     * @access public
     */
                         
    function setConnector($connector) 
    {
        switch($connector) {
            case "or":
                $this->m_connector = "or";
                break;
            default:
                $this->m_connector = "and";
                break;   
        }
        return $this->m_connector;
    }
    
    // end class VDatabaseFilter  
} 

 