<?php

/**
 * Virtual Web Platform - Single Field Identity
 *  
 * 
 * @package VWP
 * @subpackage Libraries.DBI.Types  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License 
 * @todo Implement database drivers via plugin support    
 */

/**
 * Virtual Web Platform - Single Field Identity
 *  
 * 
 * @package VWP
 * @subpackage Libraries.DBI.Types  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License 
 * @todo Implement database drivers via plugin support    
 */

class VDBI_SingleFieldIdentity extends VObject 
{
	/**
	 * Data access object
	 * 
	 * @var object $dao Data access object
	 * @access public
	 */
	
    protected $dao;

    /**
     * Key value
     *      
     * @var string Value
     * @access public
     */
    
    protected $value = null;
    
    /**
     * Get Name
     * 
     * @return string Name
     * @access public
     */
    
    public function getName() 
    {
        return "SingleFieldIdentity";	
    }    
    
    /**
     * Get Key
     * 
     * @return mixed Key value
     * @access public
     */    
    
    public function getKey()
    {
    	return $this->value;
    }
    
    /**
     * Get Key as String
     * 
     * @return string Key
     * @access public
     */    
    
    public function getKeyAsString()
    {
        return $this->getKey();	
    }
    
    /**
     * Get Data Access Object
     * 
     * @return object Data access object
     * @access public
     */    
    
	public function &getDAO() 
	{
		return $this->dao;
	}

	/**
	 * Convert To String
	 * 
	 * @return string Key
	 * @access public
	 */
		
	function __toString() 
	{
	    return $this->getKeyAsString();	
	}	
	
	/**
	 * Class Constructor
	 * 
	 * @param object $dao Data access object
	 * @access public
	 */	
	
    function __construct($dao) 
    {
    	$this->dao =& $dao;    	
    	    	
    	$this->value = $this->dao->get($this->dao->primary_key);    	    
    	
    }

    // end class VDBI_SingleFieldIdentity
}
