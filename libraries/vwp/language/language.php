<?php

/**
 * Virtual Web Platform - Language support
 *  
 * This file provides Muli-Language support
 * 
 * @package VWP
 * @subpackage Libraries.Language  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License   
 */

/**
 * Virtual Web Platform - Language support
 *  
 * This class provides Muli-Language support.
 * This is the base class for all language processors. 
 * 
 * @package VWP
 * @subpackage Libraries.Language  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License   
 */


class VLanguage extends VObject 
{
 
    /**
     * Translate phrase
     * 
     * @param string $string Phrase
     * @param boolean Javascript safe
     * @return string Translated phrase
     */
                    
    public static function _($string, $jsSafe = false) 
    {
        return $string;
    }
  
    // end class VLanguage
} 


