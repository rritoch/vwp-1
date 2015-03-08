<?php

/**
 * Virtual Web Platform - System Time
 *  
 * This file provides System Time Processing
 * 
 * @package VWP
 * @subpackage Libraries.System
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License    
 */

/**
 * Virtual Web Platform - System Time
 *  
 * This class provides System Time Processing
 * 
 * @package VWP
 * @subpackage Libraries.System
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License    
 */

class VTime extends VObject 
{
    /**     
     * Timezone Correction
     * 
     * @var integer $correction Timezone correction in seconds
     * @access private
     */
	
    static $correction = 0;

    /**     
     * Micro-Timezone Correction
     * 
     * @var integer $microcorrection Timezone correction in microseconds
     * @access private
     */
        
    static $microcorrection = 0;
    
    /**
     * Time adjustment
     * 
     * @var integer $adjustment Clock adjustment in seconds
     * @access private
     */
    
    static $adjustment = 0;
    
    /**
     * Micro time adjustment
     * 
     * @var integer $microadjustment Clock adjustment in micro-seconds
     * @access private
     */
    
    static $microadjustment = 0;
    
    /**
     * Time data
     * 
     * @var array $_time Time data
     */
    
    private $_time = array();
    
    /**
     * Dirty Flag
     * 
     * @var boolean $_dirty Dirty flag
     */
    
    private $_dirty = true;
    
    /**
     * Set a correction value
     * 
     * The correction value is an adjustment
     * for a clock which are set to the correct local time
     * but incorrect timezone.
     * 
     * @param integer Seconds
     * @access public
     */
                                            
    public static function setZoneCorrection($seconds) 
    {    
        self::$correction = $seconds;                         
    }

    /**
     * Set a micro correction value
     * 
     * The micro correction value is microdeviation
     * from the clocks timezone.
     * 
     * @param integer $microseconds Microseconds
     * @access public
     */

    public static function setMicroZoneCorrection($microseconds) 
    {
        self::$microcorrection = $microseconds % 1000000;    
    }
    
    /**
     * Set clock adjustment
     * 
     * @param integer $seconds Seconds
     * @access public
     */
    
    public static function setAdjustment($seconds) 
    {
        self::$adjustment = $seconds;
    }
    
    /**
     * Set micro clock adjustment
     * 
     * @param integer $microseconds Microseconds
     * @access public
     */
    
    public static function setMicroAdjustment($microseconds) 
    {
        self::$microadjustment = $microseconds % 1000000;
    }

    /**
     * Calibrate internal time data
     * 
     * @access public
     */
    
    function calibrate() 
    {
        if ($this->_dirty) {
        
            if (isset($this->_time['gmtime'])) {
                $ts = $this->_time["gmtime"];
            } else {
                $ts = time() + self::$adjustment;
                
                if (function_exists('microtime')) {
                    list($usec, $sec) = explode(" ", microtime());
                    $usec = floor($usec * 1000000) + self::$microadjustment;
                    if ($usec >= 1000000) {
                        $ts++;
                    } elseif ($usec < 0) {
                        $ts--;
                    }            
                }
            }
            
            $offset = isset($this->_time["offset"]) ? $this->_time["offset"] : idate('Z');
      
            $k = array('seconds','minutes','hours','mday',
                'wday','mon','year','yday','weekday','month','gmtime');
            
            $t1 = array_combine($k,explode(":",
                gmdate('s:i:G:j:w:n:Y:z:l:F:U',$ts + $offset)));
            
            $t1 = array_merge($t1,$this->_time);   
            
            if (isset($this->_time['yday'])) {
                $y = $this->_time['yday'] * 86400;
                $jts = mktime($t1["hours"], $t1["minutes"], $t1["seconds"] , 1, 1, $t1["year"]) + $y;
                $t1["mon"] = gmdate('n',$jts);
                $t1["mday"] = gmdate('j',$jts);       
            }
    
            $ts = mktime($t1["hours"], $t1["minutes"], $t1["seconds"] , $t1["mon"] , $t1["mday"], $t1["year"]);

            $k = array('seconds','minutes','hours','mday',
                'wday','mon','year','yday','weekday','month','gmtime');
            $this->_time = array_combine($k,explode(":",
                gmdate('s:i:G:j:w:n:Y:z:l:F:U',$ts + $offset)));

            if (!isset($this->_time["microseconds"])) {
                if (function_exists('microtime')) {
                    list($usec, $sec) = explode(" ", microtime());
                    $usec = floor($usec * 1000000) + self::$microcorrection + self::$microadjustment;
                } else {
                    $usec = 0;
                }          
                $this->_time["microseconds"] = $usec % 1000000; 
            }
                
            $this->_time['gmtime'] = $ts + self::$correction;
            $this->_time['offset'] = $offset;
            $this->_dirty = false;
        }
    }
    
    /**
     * Set time offset
     * 
     * @param integer $offset Seconds
     * @access public
     */
    
    function setOffset($offset) 
    {
        $this->_time["offset"] = $offset;
        $this->_dirty = true;
    }

    /**
     * Set time
     * 
     * @param integer $ts Seconds
     * @access public
     */
    
    function setTime($ts) 
    {
        $this->_time['gmtime'] = $ts;
        $k = array('seconds','minutes','hours','mday',
                'wday','mon','year','yday','weekday','month');
        foreach($k as $v) {
            unset($this->_time[$v]);
        }
        $this->_dirty = true;                
    }
    
    /**
     * Set time to PHP Timestamp
     * 
     * @param integer $t Seconds
     * @access public
     */
    
    function setPHPTime($t) 
    {
        $this->setTime($t + self::$correction);
    }

    /**
     * Set time to Julian Date
     * 
     * @param float $jdate Days
     * @access public
     */
    
    function setjDate($jdate) 
    {        
        $full = ($jdate - 2440587.5) * 86400;
        $nix = $full >= 0 ? floor($full) : ceil($full); 
        $mnix = (($full - $nix) * 1000000) % 1000000;        
        $this->_time["microseconds"] = $mnix;
        $this->_time["gmtime"] = $nix;
        $this->_dirty = true;    
    }
    
    /**
     * Set Year
     * 
     * @param integer $yy Year
     * @access public
     */
       
    function setYear($yy) 
    {        
        $this->_time["year"] = $yy;
        $this->_dirty = true;
    }

    /**
     * Set Day of year
     * 
     * @param integer $j Day
     * @access public
     */
    
    function setjDay($j) 
    {        
        unset($this->_time["day"]);
        unset($this->_time["month"]);
        $this->_time["yday"] = $j - 1;
        $this->_dirty = true;
    }

    /**
     * Set Month
     * 
     * @param integer $mm Month
     * @access public
     */
    
    function setMonth($mm) 
    {        
        unset($this->_time["jday"]);
        $this->_time["mon"] = $mm;
        $this->_dirty = true;
    }

    /**
     * Set Day of month
     * 
     * @param integer $dd Day
     * @access public
     */
    
    function setDay($dd) 
    {        
        unset($this->_time["jday"]);        
        $this->_time["mday"] = $dd;
        $this->_dirty = true;
    }

    /**
     * Set Hour
     * 
     * @param integer $H Hour
     * @access public
     */
    
    function setHour($H) 
    {        
        $this->_time["hours"] = $H;
        $this->_dirty = true;
    }

    /**
     * Set Hour 12
     * 
     * @param integer $H Hour
     * @access public
     */
    
    function setHour12($H) 
    {
    	$hh = $H == 12 ? 0 : $H;
        $ampm = $this->getAmPm();    	        
        $this->_time["hours"] = $hh + (12 * $ampm);
        $this->_dirty = true;
    }    
    
    /**
     * Set AmPm
     */
    
    function setAmPm($ampm) 
    {
    	$h = $this->getHour12();
    	$h = $h == 12 ? 0 : $h;
        $this->_time["hours"] = ($ampm > 0) ? $h + 12 : $h;        	          
        $this->_dirty = true;	
    }
    
    /**
     * Set Minutes
     * 
     * @param integer $M Minutes
     * @access public
     */
        
    function setMinutes($M) 
    {        
        $this->_time["minutes"] = $M;
        $this->_dirty = true;
    }

    /**
     * Set Seconds
     * 
     * @param integer $S Seconds
     * @access public
     */    
    
    function setSeconds($S) 
    {
        $this->_time["seconds"] = $S;
        $this->_dirty = true;
    }
    
    /**
     * Set Micro Seconds
     * 
     * @param integer $uS Micro-Seconds
     * @access public
     */     
    
    function setuSeconds($uS) 
    {
        $this->_time["microseconds"] = $uS % 1000000;
    }

    /**
     * Get timezone correction
     * 
     * @return integer Zone correction in seconds
     * @access public
     */
    
    public static function getZoneCorrection() 
    {    
        return self::$correction;                         
    }

    /**
     * Get micro-timezone correction
     * 
     * @return integer Zone correction in microseconds
     */    
    
    public static function getMicroZoneCorrection($microseconds) 
    {
        return self::$microcorrection;    
    }
    
    /**
     * Get Clock Offset
     * 
     * @return integer Clock offset in seconds
     * @access public
     */
    
    function getOffset() 
    {
        $this->calibrate();
        return $this->_time['offset'];
    }
    
    /**
     * Get GMT time in seconds
     *
     * @return integer GMT time in seconds
     * @access public
     */
    
    function getTime() 
    {
        $this->calibrate();
        return $this->_time['gmtime'];
    }
    
    /**
     * Get time in seconds
     * 
     * @return integer Time in seconds
     * @access public     
     */
    
    function getPHPTime() 
    {
        return $this->getTime() - self::$correction;
    }

    /**
     * Get Julean Date
     * 
     * @return float Julean date
     * @access public     
     */
    function getjDate() 
    {
        $this->calibrate();
        $nix = $this->_time['gmtime'];        
        $base = (((float) $nix) / 86400.0) + 2440587.5;  
        $ubase = $this->_time["microseconds"] / 86400000000;
        return $base + $ubase;  
    }
    

    /**
     * Get Year
     * 
     * @return integer Year
     * @access public
     */
    
    function getYear() 
    {
        $this->calibrate();        
        return $this->_time['year'];
    }
    
    /**
     * Get Day of year
     * 
     * @return integer Day of year
     * @access public
     */
    
    function getjDay() 
    {
        return $this->_time["yday"] + 1;
    }
    
    /**
     * Get month
     * 
     * @return integer Month
     * @access public     
     */
    
    function getMonth() 
    {
        $this->calibrate();
        return $this->_time['mon'];
    }
    
    /**
     * Get day of month
     * 
     * @return integer Day of month
     * @access public
     */
    
    function getDay() 
    {
        $this->calibrate();
        return $this->_time['mday'];    
    }
    
    /**
     * Get Hour
     * 
     * @return integer Hour
     * @access public
     */
    
    function getHour() 
    {
        $this->calibrate();
        return $this->_time["hours"];
    }
    
    /**
     * Get Hour Short
     * 
     * @return integer Hour
     * @access public
     */

    function getHour12() {
    	$this->calibrate();
    	
    	$h = $this->_time["hours"] < 12 ? $this->_time['hours'] : $this->_time['hours'] - 12; 
    	return  $h == 0 ? 12 : $h;
    }
    
    /**
     * Get AM/PM
     * 
     * @return integer [0-1] 0 AM, 1 PM
     */

    function getAmPm() 
    {
      $this->calibrate();
      return $this->_time['hours'] > 11 ? 1 : 0;	
    }
    
    /**
     * Get Minutes
     * 
     * @return integer Minutes
     * @access public
     */
    
    function getMinutes() 
    {
        $this->calibrate();
        return $this->_time["minutes"];
    }    
    
    /**
     * Get Seconds
     * 
     * @return integer Seconds
     * @access public     
     */
    
    function getSeconds() {
        $this->calibrate();
        return $this->_time["seconds"];    
    }
    
    /**
     * Get Microseconds
     * 
     * @return integer Microseconds
     * @access public     
     */    
    
    function getuSeconds() 
    {
        $this->calibrate();
        return $this->_time["microseconds"];     
    }
    
    // end class VTime
}
