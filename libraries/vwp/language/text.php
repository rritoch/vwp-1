<?php

/**
 * Virtual Web Platform - Text Processor
 *  
 * This file provides Muli-Language text processing
 * 
 * @package VWP
 * @subpackage Libraries.Language  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

// Restricted access

class_exists('VWP') || die();

/**
 * Require Language support
 */

VWP::RequireLibrary('vwp.language.language');

/**
 * Virtual Web Platform - Text Processor
 *  
 * This class provides Muli-Language text processing
 * 
 * @package VWP
 * @subpackage Libraries.Language  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License 
 */

class VText extends VObject 
{

    /**
     * Translates a string into the current language
     *
     * @param string $string The string to translate
     * @param boolean $jsSafe Make the result javascript safe
     * @access public     
     */
  
    public static function _($string, $jsSafe = false) 
    {
        $lang =& VWP::getLanguage();
        return $lang->_($string, $jsSafe);
    }

    /**
     * Passes a string thru an sprintf
     * 
     * Reserved for future use
     *      
     * @param format The format string
     * @param mixed Mixed number of arguments for the sprintf function
     * @access public  
     */

    function sprintf($string) 
    {
        $lang =& VWP::getLanguage();
        $args = func_get_args();
        if (count($args) > 0) {
            $args[0] = $lang->_($args[0]);
            return call_user_func_array('sprintf', $args);
        }
        return '';
    }

    /**
     * Passes a string thru an printf
     *
     * Reserved for future use
     *     
     * @access	public
     * @param	format The format string
     * @param	mixed Mixed number of arguments for the sprintf function
     */

    function printf($string) 
    {
        $lang =& VWP::getLanguage();
        $args = func_get_args();
        if (count($args) > 0) {
            $args[0] = $lang->_($args[0]);
            return call_user_func_array('printf', $args);
        }
        return '';
    }
    
    // end class VText
} 

