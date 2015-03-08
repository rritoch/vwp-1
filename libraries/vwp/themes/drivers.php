<?php

/**
 * Virtual Web Platform - Theme driver
 *  
 * This file provides theme driver support.   
 * 
 * @package VWP
 * @subpackage Libraries.Themes  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

/**
 * Virtual Web Platform - Theme driver
 *  
 * This class is the base class for all theme drivers   
 * 
 * @package VWP
 * @subpackage Libraries.Themes  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

class VThemeDriver extends VObject 
{

    /**
     * Widget
     * 
     * @var string $widget
     * @access public  
     */
        
    var $widget;
 
    /**
     * Set Widget
     * 
     * @param VWidget $widget Widget
     * @access public
     */
    
    function setWidget($widget) 
    {
       $this->widget = & $widget;
    }

    /**
     * Get Item Header
     * 
     * Should be implemented by derived classes
     *      
     * @param unknown_type $panelName
     * @param unknown_type $app
     * @param unknown_type $widget
     * @access public
     */
    
    function getItemHeader($panelName,$app,$widget) 
    {
        return '';
    }
 
    /**
     * Get Target Header
     * 
     * Should be implemented by derived classes
     *      
     * @param string $targetName
     * @param string $format
     * @access public
     */     
    
    function getTargetHeader($targetName,$format) 
    {
    	return '';
    }
    
    /**
     * Get Item Footer
     * 
     * Should be implemented by derived classes
     *      
     * @param unknown_type $panelName
     * @param unknown_type $app
     * @param unknown_type $widget
     * @access public
     */    
    
   function getItemFooter($panelName,$app,$widget) 
   {
       return '';
   }
 
    /**
     * Get Item Footer
     * 
     * Should be implemented by derived classes
     * 
     * @param string $targetName
     * @param string $format
     * @access public
     */    
   
   function getTargetFooter($targetName,$format) 
   {
       return '';	
   }
   
   // end class VThemeDriver
} 
