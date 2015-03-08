<?php
/**
 * VWP System Tickets
 * 
 * @package VWP
 * @subpackage Libraries.System  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License    
 */

/**
 * VWP System Tickets
 * 
 * @package VWP
 * @subpackage Libraries.System  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License    
 */


class VTicket extends VObject 
{
	/**
	 * Ticket ID
	 * 
	 * @var string $id Ticket ID
	 * @access protected
	 */
	
	protected $id;

	/**
	 * Ticket Name
	 * 
	 * @var string $name Ticket Name
	 * @access protected
	 */	
	
	protected $name;
	
	/**
	 * Ticket Expires
	 * 
	 * @var integer $expires Ticket Expires (time in seconds)
	 * @access protected
	 */	
	
	protected $expires;

	/**
	 * Ticket Characters
	 * 
	 * @var string $baseChars Ticket encoding character pool
	 * @access protected
	 */
	
	protected static $baseChars = "DEFGHIJKLMNOPQRSTUVW";
	
	/**
	 * Encode integer
	 * 
	 * @param integer $i
	 * @return string Encoded integer
	 * @access public
	 */
	
	static function encodeInt($i) 
	{
		$i = floor($i);
		$base = strlen(self::$baseChars);		
        $val = '';
        
        while ($i > 0) {
	       $r = $i %  $base;		   
		   $val = substr(self::$baseChars,$r,1) . $val;
		   $i = ($i - $r) / $base;
		}
		
		if (strlen($val) < 1) {			
			$val = substr(self::$baseChars,0,1);
		}
		
		return $val;
	}
	
	/**
	 * Decode integer
	 * 
	 * @param string $val Encoded integer
	 * @return integer Decoded integer value
	 * @access public
	 */
	
	static function decodeInt($val) 
	{
		
		$base = strlen(self::$baseChars);
		$i = 0;
								
		while (strlen($val) > 0) {
			$c = substr($val,0,1);
			$r = strpos(self::$baseChars,$c);
			$val = substr($val,1);
			$i = ($i * $base) + $r;			
		}
		return $i;
	}
	
	/**
	 * Encode string
	 * 
	 * @param string $str Original string
	 * @return string Encoded string
	 * @access public
	 */
	
	static function encodeString($str) {
		$val = array();
		
        foreach (str_split($str) as $c) 
        { 
            $val[] = self::encodeInt(ord($c)); 
        }
		
        return implode(':',$val);
	}
	
	/**
	 * Decode String
	 * 
	 * @param string $val Encoded string
	 * @return string $val Decoded string
	 * @access public
	 */
	
	static function decodeString($val) 
	{
		$val = explode(':',$val);		
		$str = '';		
		foreach($val as $c) {
			$str .= chr(self::decodeInt($c));
		}
		return $str;
	}

	/**
	 * Get Ticket Filename
	 * 	 
	 * @param string $name Ticket name
	 * @param string $uid Unique ID
	 * @param integer $expires Expire time
	 * @access private
	 */
	
	private static function _getFilename($name,$uid,$expires) 
	{
		static $basepath;
		
		if (!isset($basepath)) {
			$basepath = VWP::getVarPath('vwp').DS.'tickets';
		}
		$name = v()->filesystem()->file()->makeSafe($name);
		$filename = $basepath.DS.$name.'_'.$uid.'_'.$expires.'.php';
        return $filename; 
	}
	
	/**
	 * Encode ticket
	 * 
	 * @param string $name Ticket name
	 * @param string $uid Unique ID
	 * @param integer $expires Expire time in seconds	 
	 * @access private
	 */
	
	private static function _encodeTicket($name,$uid,$expires) 
	{
	    $encUid = self::encodeInt($uid);
	    $encName = self::encodeString($name);
	    $encExpires = self::encodeInt($expires);
	    return $encUid . '-' . $encName . '-' . $encExpires;
	}
	
	/**
	 * Get ticket ID
	 * 
	 * @return string Encoded ticket identifier
	 * @access public	 
	 */
	
	function getId() 
	{
		if ($this->expires < time()) {
			return VWP::raiseWarning('Expired Ticket',get_class($this),null,false);
		}
		
		return self::_encodeTicket($this->name,$this->id,$this->expires);
	}
	
	/**
	 * Decode Ticket
	 * 
	 * @param string $ticket Encoded ticket
	 * @return array Decoded ticket array(name, unique id, expire time in seconds)
	 * @access private
	 */
	
	private static function _decodeTicket($ticket) 
	{		
		list($encUid,$encName,$encExpires) = explode('-',$ticket);
        $name = self::decodeString($encName);
        $uid = self::decodeInt($encUid);
        $expires = self::decodeInt($encExpires);		
		return array($name,$uid,$expires);
	}
	
	/**
	 * Create a ticket
	 * 
	 * @param string $name Ticket name
	 * @param integer $ttl Time to live in seconds
	 * @access public
	 */
	
	static function &create($name,$ttl) 
	{
       
		$expires = time() + $ttl;
		$uid = rand(0,256 * 127);
		$f = self::_getFilename($name,$uid,$expires);
				
		$path = dirname($f);
		
		$vfolder =& v()->filesystem()->folder();
		if (!$vfolder->exists($path)) {
          $vfolder->create($path);
      }
		
		$vfile =& v()->filesystem()->file();
		
		while ($vfile->exists($f)) {
			$uid += rand(1,7);
		    $f = self::_getFilename($uid,$expires);	
		}		
		$t = '<' . '?php die();';
		$vfile->write($f,$t);
		$ticket = new VTicket(self::_encodeTicket($name,$uid,$expires));
		return $ticket;
	}
	
	/**
	 * Load ticket by ID
	 * 
	 * @param string $id Ticket ID
	 * @access public 
	 */
	
	function load($id) {
	    list($name,$uid,$expires) = self::_decodeTicket($id);
	    $f = self::_getFilename($name,$uid,$expires);
	    if (v()->filesystem()->file()->exists($f)) {
	    	$this->name = $name;
	    	$this->id = $uid;
	    	$this->expires = $expires;
	    } else {
	    	$this->name = null;
	    	$this->id = null;
	    	$this->expires = 0;
	    } 			
	}
	
	/**
	 * Redeem Ticket
	 * 
	 * @return boolean|object True on success, error or warning otherwise
	 * @access public
	 */
	
	function redeem() 
	{
		if (!$this->check()) {
			return VWP::raiseWarning('Invalid or expired ticket',get_class($this),null,false);
		}
		$f = self::_getFilename($this->name,$this->id,$this->expires);
		if (v()->filesystem()->file()->exists($f)) {
		  v()->filesystem()->file()->delete($f);
		  $result = true;
		} else {
			$result = VWP::raiseWarning('Expired ticket',get_class($this));			
		}
		$this->name = null;
		$this->id = null;
		$this->expires = 0;
		return $result;
	}
	
	/**
	 * Check if ticket is still valid
	 * 
	 * @return boolean True if valid
	 * @access public
	 */
	
	function check() 
	{
	    return $this->expires > time();
	}
	
	/**
	 * Garbage Collection
	 * 
	 * @access public
	 */
	
	function gc() 
	{
		static $gc;
		
		if (!isset($gc)) {			
		    $t = time();
		    $basePath = VWP::getVarPath('vwp').DS.'tickets';
		    $files = v()->filesystem()->folder()->files($basePath);
		    if (VWP::isWarning($files)) {
              $files = array();
          }
          $vfile =& v()->filesystem()->file();
		    
		    foreach($files as $f) {
		    	$parts = explode('_',$f);
		    	$tmp = array_pop($parts);
		    	$expires = substr($tmp,0,strlen($tmp) - 4);
		    	if ($expires < $t) {
		            $vfile->delete($basePath.DS.$f);		
		    	}
		    }		    
		    $gc = true;
		}		
	}
	
	/**
	 * Class Constructor
	 * 
	 * @param string $id Ticket ID
	 * @access public
	 */
	
	function __construct($id = null) 
	{
	    if ($id !== null) {
	    	$this->gc();
	        $this->load($id);	
	    }	    
	}
	
	// End class VTicket	
}