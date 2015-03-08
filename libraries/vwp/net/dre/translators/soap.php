<?php

/**
 * Virtual Web Platform - SOAP Translator
 *  
 * This file provides the base class for soap translators
 * 
 * @package VWP
 * @subpackage Libraries.Networking.DRE  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License
 * @todo Implement custom type creation via a WSDL processor    
 */

/**
 * Require VWP Type Translator 
 */

VWP::RequireLibrary('vwp.net.dre.translators.soap.vwptype');

/**
 * Virtual Web Platform - SOAP Translator
 *  
 * This class provides the base class for soap translators
 * 
 * @package VWP
 * @subpackage Libraries.Networking.DRE  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License 
 */

class VSOAPTranslator extends VObject 
{

 
    /**
     * Decode Request
     * 
     * Used by Soap Servers to decode a request message
     * 
     * @param string $method Method
     * @param mixed $ob Data
     * @param string $rtype Data Type
     * @param object $service Service Object
     * @param object $server Server Object
     * @access public             
     */
         
    function decodeRequest($method, $ob,$rtype,$service, $server) 
    { 
        return null;
    } 

    /**
     * Encode Response
     * 
     * Used by Soap Servers to encode a response message
     * 
     * @param string $method Method
     * @param mixed $ob Data
     * @param string $rtype Return type
     * @param object $service Sertice object
     * @param object $server Server object
     * @access public
     */

    function encodeResponse($method, $ob,$rtype,$service, $server) 
    {
        return null;
    }


    /**
     * Encode Request
     * 
     * Used by Soap Clients to encode a request message
     * 
     * @param string $method Method
     * @param mixed $params Parameters
     * @param string $targetNamespace Target namespace
     * @access public
     */
         
    function encodeRequest($method, $params,$targetNamespace) 
    {  
        return null;
    } 

    /**
     * Decode Response
     * 
     * Used by Soap Clients to encode a response message
     * 
     * @param string $method Method
     * @param mixed $data Data
     * @access public
     */

    function decodeResponse($method, $data) 
    {
        return $data;
    }

    /**
     * Encode request headers
     * 
     * Used by Soap Client
     * 
     * @param mixed $headers Headers
     * @access public
     */
     
    function encodeRequestHeaders($headers) 
    {
        return null;
    }

    /**
     * Decode Response Headers
     * 
     * Used by Soap Client
     * 
     * @param string $responseXML Response XML    
     */
     
    function decodeResponseHeaders($responseXML) 
    {
 
    }
 
    /**
     * Filter Request
     * 
     * @param string $request XML Request
     * @return string $request XML Request
     * @access public
     */
           
    function filterClientRequest($request) 
    {
        return $request;
    }

    /**
     * Filter Response
     * 
     * @param string $response XML Request
     * @return string $response XML Request
     */
           
    function filterClientResponse($response) 
    {
        return $response;
    }

    // end class VSOAPTranslator
}
