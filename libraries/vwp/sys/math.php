<?php

/**
 * Virtual Web Platform - Math System
 *  
 * This file provides various Math functions
 *        
 * @package VWP
 * @subpackage Libraries.System  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

/**
 * Virtual Web Platform - Math System
 *  
 * This file provides various Math functions
 *        
 * @package VWP
 * @subpackage Libraries.System  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

class VMath extends VObject
{
	
	/**
	 * Expand Float
	 * 
	 * Remove exponential notation from a float value
	 * 
	 * @param float $float
	 * @return string Expanded float
	 */
	
	static function expandFloat($float) 
	{
	    $float_str = (string)((float)$float);
		
        // if there is an E in the float string
        if(($pos = strpos(strtolower($float_str), 'e')) !== false) {

            // get either side of the E, e.g. 1.6E+6 => exp E+6, num 1.6
            $exp = substr($float_str, $pos+1);
            $num = substr($float_str, 0, $pos);
       
            // strip off num sign, if there is one, and leave it off if its + (not required)
            if((($num_sign = $num[0]) === '+') || ($num_sign === '-')) {
                $num = substr($num, 1);	
            } else {
                $num_sign = '';	
            }
            if ($num_sign === '+') {
            	$num_sign = '';
            }
       
            // strip off exponential sign ('+' or '-' as in 'E+6') if there is one, otherwise throw error, e.g. E+6 => '+'
            if ((($exp_sign = $exp[0]) === '+') || ($exp_sign === '-')) {
                $exp = substr($exp, 1);	
            } else {
                return VWP::raiseWarning("Could not convert exponential notation to decimal notation: invalid float string '$float_str'", __CLASS__,null,false);
            }
       
            // get the number of decimal places to the right of the decimal point (or 0 if there is no dec point), e.g., 1.6 => 1
            $right_dec_places = (($dec_pos = strpos($num, '.')) === false) ? 0 : strlen(substr($num, $dec_pos+1));
            
            // get the number of decimal places to the left of the decimal point (or the length of the entire num if there is no dec point), e.g. 1.6 => 1
            $left_dec_places = ($dec_pos === false) ? strlen($num) : strlen(substr($num, 0, $dec_pos));
       
            // calculate number of zeros from expponent
            
            $num_zeros = ($exp_sign === '+') ? $exp - $right_dec_places : $exp - $left_dec_places;
       
            // build a string with $num_zeros zeros
            $zeros = str_pad('', $num_zeros, '0');
       
            // strip decimal from num, e.g. 1.6 => 16
            if( $dec_pos !== false) {
            	$num = str_replace('.', '', $num);
            }
       
            $float_str = ($exp_sign === '+') ? $num_sign.$num.$zeros : $num_sign.'0.'.$zeros.$num;
        }
            
        return $float_str;		
	}
	
	// end class VMath
}
