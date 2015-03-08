<?php

/**
 * VWP - DBI Query Ratio Type
 *  
 * This file provides the Ratio Type        
 * 
 * @package VWP
 * @subpackage Libraries.DBI.Types.Query  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

/**
 * VWP - DBI Query Ratio Type
 *  
 * This class provides the Ratio Type        
 * 
 * @package VWP
 * @subpackage Libraries.DBI.Types.Query  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

class VDBIQueryType_Ratio extends VObject 
{

	/**	 
	 * Ratio Segment Field
	 * 
	 * @var string $segment Ratio Segment Field
	 * @access public
	 */
	
	public $segment;
	
	/**	 
	 * Ratio Segment Summary Type
	 * 
	 * @var string $domain_type Ratio Segment Summary Type
	 * @access public
	 */	
	
	public $segment_type;

	/**	 
	 * Ratio Domain Field
	 * 
	 * @var string $domain Ratio Domain Field
	 * @access public
	 */
		
	public $domain;

	/**	 
	 * Ratio Domain Summary Type
	 * 
	 * @var string $domain_type Ratio Domain Summary Type
	 * @access public
	 */	
	
	public $domain_type;
	
	/**
	 * Ratio Operator
	 * 
	 * @var string $operator
	 */
	
	public $operator;
	
	/**
	 * Data
	 * 
	 * @var array Data
	 * @access private
	 */
	
	protected $_data;
	
	/**
	 * Encode Array
	 * 
	 * @param unknown_type $a
	 * @access public
	 */
	
	function encodeArray($a) 
	{
	    $str = '';

	    $encA = array();
	    foreach($a as $msg) {	    	
	    	$msg = str_replace('_','_e',(string)$msg);
	    	$encA[] = $msg;
	    }
	    return (implode('_s',$encA));
	}

	/**
	 * Decode Array
	 * 
	 * @param unknown_type $str
	 * @access public
	 */
	
	function decodeArray($str) {
	    $encA = explode('_s',$str);
	    $a = array();
	    foreach($encA as $msg) {
	    	$msg = str_replace('_e','_',(string)$msg);
	    	$a[] = $msg;
	    }
	    return $a;	
	}	
	
	/**
	 * Set Data
	 * 
	 * @param array $data
	 * @access public
	 */
	
	function setData($data) 
	{
		$this->_data = $data;
	}
	
	/**
	 * Get Ratio Value
	 * 
	 * @return mixed Value
	 * @access public
	 */
	
	function getValue() 
	{
		//print_r($this);
		
		$segval = 0.0;
		$domainval = 0.0;
						
		$segparts =$this->decodeArray($this->segment);
		$domainparts = $this->decodeArray($this->domain);

		$segop = empty($this->segment_type) ? 'plain' : $this->segment_type;
		$segtable = $this->encodeArray(array('t',$segparts[1],$segparts[2]));
		$segfield = $this->encodeArray(array('f',$segparts[1],$segparts[2],$segparts[3],$segop));

		$domainop = empty($this->segment_type) ? 'plain' : $this->segment_type;
		$domaintable = $this->encodeArray(array('t',$domainparts[1],$domainparts[2]));
		$domainfield = $this->encodeArray(array('f',$domainparts[1],$domainparts[2],$domainparts[3],$domainop));		
		
		
		if (isset($this->_data[$segtable][$segfield][$segop])) {
			$segval = $this->_data[$segtable][$segfield][$segop];
		}
	
		if (isset($this->_data[$domaintable][$domainfield][$domainop])) {						
			$domainval = $this->_data[$domaintable][$domainfield][$domainop];
		}		
				
		$segval = (float)$segval;
		$domainval = (float)$domainval;
		
		switch($this->operator) {
			case "-":
				$result = $domainval - $segval;
				break;
			case "+":
				$result = $domainval + $segval;
				break;
			case "*":
				$result = $segval * $domainval;
				break;
			default:
		        if ($domainval == 0) {
		            return '-';
		        } else { 
		            $result = $segval/$domainval;		
		        }
		        break;
		}
		return $result;
	}
	
	/**
	 * Class Constructor
	 * 
	 * @param string $segment Segment Field
	 * @param string $segment_type Segment Summary Type
	 * @param string $domain Domain Field
	 * @param string $domain_type Domain Summary Type
	 */
	
	function __construct($segment = null,$segment_type = null,$operator = null,$domain = null,$domain_type = null) 
	{
	    parent::__construct();
	    $this->operator = $operator;
	    $this->segment = $segment;
	    $this->segment_type = $segment_type;
	    $this->domain = $domain;
	    $this->domain_type = $domain_type;	
	}

	// end class VDBIQueryType_Ratio
}
