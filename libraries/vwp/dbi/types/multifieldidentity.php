<?php

/**
 * Virtual Web Platform - Muti Field Identity
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
 * Virtual Web Platform - Muti Field Identity
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

class VDBI_MultiFieldIdentity extends VObject 
{
	/**
	 * Data Access Object
	 * 
	 * @var object $dao Data Object
	 * @access public
	 */
	
    protected $dao;

    /**
     * Key Value
     * 
     * @var mixed $value Value
     * @access public
     */
    
    protected $value = null;
    
    /**
     * Get Name 
     * 
     * @return string Name of Id
     * @access public
     */
    
    public function getName() 
    {
        return "MultiFieldIdentity";	
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
     * Decode Key
     * 
     * @param mixed $k Key
     * @return mixed Decoded key
     * @access public
     */
    
    public static function decodeKey($k) 
    {
        if (is_array($k)) {
        	return $k;
        }

        $ret = array();
        $k = (string)$k;
        
        $parts = explode("&",$k);
        foreach($parts as $item) {
        	$tmp = explode("=",$item);
        	$key = array_shift($tmp);
        	$value = implode("=",$tmp);
        	$ret[urldecode($key)] = urldecode($value);
        }
        return $ret;        
    }
    
    /**
     * Encode Key
     * 
     * @param mixed $k Key
     * @return string Encoded Key
     * @access public
     */
    
    public static function encodeKey($k) 
    {
    	if (!is_array($k)) {
    		return is_null($k) ? null : (string)$k;
    	}
    	
        $parts = array();
        foreach($k as $key=>$value) {
        	if (is_object($value) && method_exists($value,'isTime') && $value->isTime()) {
        		$parts[] = urlencode($key) . '=' . urlencode((string)$value->getPHPTime());
        	} else {
        	    $parts[] = urlencode($key) . '=' . urlencode((string)$value);
        	}
        }
        return implode('&',$parts);      
    }
    
    /**
     * Get Key as String
     * 
     * @return string Key
     * @access public
     */
    
    public function getKeyAsString()
    {               
        return self::encodeKey($this->getKey());  
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
    	
        if (!$this->dao->_new) {
    	    $this->value = $this->dao->getAll(false);
    	}    	    	
    }

    // end class VDBI_MultiFieldIdentity
}
