<?php

/**
 * Virtual Web Platform - SOAP Client Transport
 *  
 * This file handles sending soap messages.
 * 
 * @package VWP
 * @subpackage Libraries.Networking.Clients  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License    
 */

/**
 * Require Network Support
 */

VWP::RequireLibrary('vwp.net');

/**
 * Require HTTP client support
 */

VNet::RequireClient('http');

/**
 * Virtual Web Platform - SOAP Client Transport
 *  
 * This class handles sending soap messages.
 * 
 * @package VWP
 * @subpackage Libraries.Networking.Clients  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License    
 */

class VSoapClientHandler extends SoapClient 
{
   
	/**	 
	 * Debug level
	 * 
	 * 0 : No Debug
	 * 1 : Debug Request
	 * 2 : Debug Response
	 * 
	 * @var integer $_debug_vsoap
	 * @access public
	 */
	
    public $_debug_vsoap;

    /**
     * Allow transfer
     *
     * @var boolean $_allow_transfer Allow data transfer
     * @access public
     */
    
    public $_allow_transfer = true;

    /**
     * Send SOAP Request
     * 
     * @param string $request
     * @param string $location Endpoint URL
     * @param string $action Soap Action
     * @param string $version Soap Version
     * @param integer $one_way One way connection
     * @access public
     */
    
    function __doRequest($request, $location, $action, $version, $one_way = 0) 
    {
  
        static $httpclient;
  
        $request = VSoapClient::filterRequest($request,$location);
    
        if ($this->_debug_vsoap == 1) {
            print_r(array("Debug Soap Request",htmlentities($request)));
            die();
        }
  
        if ($this->_allow_transfer) {   
            $ns = 'http://schemas.xmlsoap.org/soap/envelope/';    
            if (!isset($httpclient)) {
                $this->_httpclient = VHTTPClient::getInstance();  
            }
         
            $options = array('raw_post'=>true,
                         "http_header"=>array('Content-Type: text/xml; charset=utf-8',
                         'SOAPAction: "'.addslashes($action).'"'));

            $result = $this->_httpclient->wpost($location,$request,$options);

            if ($this->_debug_vsoap == 2) {
                print_r(array("Debug Soap Response",htmlentities($result)));
                die();
            }
     
            if (VWP::isWarning($result)) {
                throw new SoapFault('server',$result->errmsg);
            }
        } else {   
            throw new SoapFault('server','Transport Disabled!');
        }
  
        $result = VSoapClient::filterResponse($result);
    
        return $result;
    }

    /**
     * Class constructor
     * 
     * @param string $wsdl
     * @param array $options
     * @access public
     */
    
    function __construct($wsdl, $options) 
    {
        SoapClient::__construct($wsdl,$options);  
    }
 
    // end class VSoapClientHandler
}
