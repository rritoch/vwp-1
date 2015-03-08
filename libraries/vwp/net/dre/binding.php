<?php

/**
 * Virtual Web Platform - Binding interface
 *   
 * @package VWP
 * @subpackage Libraries.Networking.DRE  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License   
 */

/**
 * Virtual Web Platform - Binding interface
 *   
 * 
 * @package VWP
 * @subpackage Libraries.Language  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License   
 */


class VDREBinding extends VObject 
{

    /**
     * Run the binding
     * 
     * Note: Must be implemented by the binding type class
     * 
     * @param string $service Service ID
     * @param mixed $result Service binding result
     * @access public    
     */
         
    function run($service, &$result) 
    {
 
    }

    /**
     * Add binding to WSDL document
     * 
     * Note: Must be implemented by the binding type class
     *     
     * @param object $wsdl_doc WSDL Document
     * @param string $tns Target Namespace
     * @param object $service_doc Service definition document
     * @param string $uri Access point URI  
     * @access public        
     */   

    function registerWSDL($wsdl_doc,$tns,$service_doc, $uri) 
    {
 
    }
    
    // end class registerWSDL
}
 