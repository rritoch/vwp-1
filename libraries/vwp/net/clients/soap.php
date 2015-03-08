<?php

/**
 * Virtual Web Platform - SOAP Client
 *  
 * This file provides the default SOAP client.   
 * 
 * @package VWP
 * @subpackage Libraries.Networking.Clients    
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License
 * @todo Implement a custom Soap Client    
 */

/**
 * Require Document Support
 */

VWP::RequireLibrary('vwp.documents.document');

/**
 * Require XML Support
 */

VDocument::RequireDocumentType('xml');

/**
 * Require PHP soap Extension
 */

VWP::RequireExtension('soap');

/**
 * Require default SOAP translator
 */

VWP::RequireLibrary('vwp.net.dre.translators.soap');

/**
 * Require Schema support
 */

VWP::RequireLibrary('vwp.xml.schema');

/**
 * Require SOAP Transport Support
 */

VWP::RequireLibrary('vwp.net.clients.soap.transport');


/**
 * Virtual Web Platform - SOAP Client
 *  
 * This class provides the default SOAP Client.
 * 
 * Note: Currently this system is using the PHP soap extension.
 *       There have been instances where server 500 errors are being
 *       generated while trying to access data provided by the SoapClient.
 *       It appears that these errors can be avoided with the use of static functions and static variables
 *       to store a copy of the data returned by the SoapClient. These copies
 *       are being created successfuly by sending data from the SoapClient to a static function which populates local variables with empty strings,
 *       appends the data provided by the SoapClient to the empty string, and lastly stores the
 *       new string in a static variable. As the php SoapClient is unrealiable it appears
 *       that a custom soap implementation is needed.
 * 
 * @package VWP
 * @subpackage Libraries.Networking.Clients  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License    
 */

class VSoapClient extends VObject 
{

    /**
     * @var object $_client Soap Transport Object
     * @access private
     */
       
    protected $_client;

    /**
     * @var string $_wsdl WSDL Filename
     * @access private
     */

    protected $_wsdl = null;

    /**
     * @var array $_options SOAP options
     * @access private
     */
   
    protected $_options = array();

    /**
     * @var array $_wsdl_doc WSDL Document
     * @access private
     */
   
    protected $_wsdl_doc = null;
 
 
    /**
     * @var string $_last_request Last SOAP request XML
     * @access private  
     */
     
    protected $_last_request = null;

    /**
     * @var string $_last_response Last SOAP response XML
     * @access private  
     */
     
    protected $_last_response = null;
  
    /**
     * Cache enabled
     * 
     * @var mixed $_soap_cache_enabled;  
     * @access private      
     */
 
    protected $_soap_cache_enabled = null; 
 
    /**
     * Request Headers
     * 
     * @var mixed $_pending_request_headers Request headers
     * @access private
     */        
 
    protected $_pending_request_headers = null;

    /**
     * Soap Message Translator
     * 
     * @var object $_translator Soap Translator
     * @access public    
     */
     
    public $_translator = null;

    /**  
     * @var boolean $_allow_transfer Allow transfer
     * @access public    
     */ 
 
    public $_allow_transfer = true;

    /**     
     * Last Location
     * 
     * @var string $_last_location last endpoint
     * @access public
     */
    
    public $_last_location = null;
 
    /**     
     * Static last request buffer
     * 
     * @var mixed $_s_last_request Last request
     * @access private
     */
       
    static $_s_last_request;

    /**     
     * Static last response buffer
     * 
     * @var mixed $_s_last_request Last request
     * @access private
     */    
    
    static $_s_last_response;
 
    /**     
     * Static last endpoint buffer
     * 
     * @var mixed $_s_last_url Last request
     * @access private
     */
     
    static $_s_last_url;

    /**     
     * Static translator buffer
     * 
     * @var mixed $_s_translator Static translator buffer
     * @access private
     */    
        
    static $_s_translator;

    /**     
     * Set allow transport flag
     * 
     * @param boolean $allow Allow transport
     * @access public
     */
  
    function setAllowTransport($allow) 
    {
        $this->_allow_transfer = $allow;
        if (is_object($this->_client)) {
            $this->_client->_allow_transfer = $allow;
        }
    }
 
    /**
     * Get last endpoint
     * 
     * @return string Last endpoint URL
     * @access public
     */
    
    function getLastLocation() 
    {
        return $this->_last_location;
    }
 
    /**
     * Filter request dispatcher
     * 
     * Note: This function is called after data serialization and before
     *       the transport is used to provide applications the ability
     *       to create custom serialization features.
     *       
     * @param string $request Soap Request
     * @param string $location Request endpoint URL
     * @return string Fully serialized request
     * @access public
     */
    
    public static function filterRequest($request,$location) 
    {
 
        // Cast Request To String  
        $niceRequest = '';
        $niceRequest .= $request;
  
        $niceLocation = '';
        $niceLocation .= $location;
  
        self::$_s_last_url = $niceLocation;
      
        if (isset(self::$_s_translator)) {   
            $niceRequest = self::$_s_translator->filterClientRequest($niceRequest);     
            self::$_s_last_request = $niceRequest; //$request;      
        }  
        return $niceRequest;
    }

    /**     
     * Filter response dispatcher
     *
     * Note: This function is called after the SOAP response is received
     *       from the transport layer and before the data is deserialized.
     *       This allows applications to implement custom deserialization
     *       features. The data returned by this function is sent to the 
     *       deserializer.
     *       
     * @param string $response
     * @return string Virtual Soap response
     * @access public 
     */
    
    public static function filterResponse($response) 
    {
        // Cast Request To String  
        $niceResponse = '';
        $niceResponse .= $response;
   
        if (isset(self::$_s_translator)) {
            $niceResponse = self::$_s_translator->filterClientResponse($niceResponse);     
            self::$_s_last_response = $niceResponse;   
        }  
        return $niceResponse;
    }
      
    /**
     * Reserved for future use
     *   
     * @access private  
     */   

    function _preprocessWSDL() 
    {
        // run all imports!
    }
  
    /**
     * Locate a WSDL PortType Node
     * 
     * @param string $name PortType QName identifier
     * @return object Node on success, error or warning otherwise
     * @access public
     */          
    
    function getPortType($name) 
    {
        if (substr($name,0,4) == "tns:") {
            $tnsName = $name;
            $name = substr($name,4);
        }
  
        $wsdlNS = 'http://schemas.xmlsoap.org/wsdl/';
        $portTypes = $this->_wsdl_doc->getElementsByTagNameNS($wsdlNS,'binding');
        if ($portTypes->length > 0) {
            if (empty($name)) {
                return $portTypes->item(0);
            }
            for($p=0;$p < $bindings->length; $p++) {
                if ($portTypes->item($p)->getAttribute('name') == $name) {
                    return $portTypes->item($p);
                }
            }
        }   
        return VWP::raiseWarning('Operation not found!',get_class($this),null,false);        
    }

    /**
     * Locate a WSDL Operation Node
     * 
     * @param object $node Node to search (either a PortType or Binding Node)  
     * @param string $name Method name
     * @return object Node on success, error or warning otherwise
     * @access public
     */  
 
    function getOperation($node,$name) 
    {
        $wsdlNS = 'http://schemas.xmlsoap.org/wsdl/';
        $operations = $this->_wsdl_doc->getElementsByTagNameNS($wsdlNS,'binding');
        if ($operations->length > 0) {
            if (empty($name)) {
                return $$operations->item(0);
            }
            for($p=0;$p < $operations->length; $p++) {
                if ($operations->item($p)->getAttribute('name') == $name) {
                    return $operations->item($p);
                }
            }
        }     
        return VWP::raiseWarning('Operation not found!',get_class($this),null,false);        
    }

    /**
     * Locate a WSDL Binding Node
     *   
     * @param string $name Binding QName identifier
     * @return object Node on success, error or warning otherwise
     * @access public
     */ 
 
    function getBinding($name = null) 
    {
        $wsdlNS = 'http://schemas.xmlsoap.org/wsdl/';
        $bindings = $this->_wsdl_doc->getElementsByTagNameNS($wsdlNS,'binding');
        if ($bindings->length > 0) {
            if (empty($name)) {
                return $bindings->item(0);	
            }
            for($p=0;$p < $bindings->length; $p++) {
                if ($bindings->item($p)->getAttribute('name') == $name) {
                    return $bindings->item($p);
                }
            }
        }
   
        return VWP::raiseWarning('Binding not found!',get_class($this),null,false);      
    }
 
    /**
     * Locate the access point URL in WSDL document
     * 
     * @param string $service Service to locate
     * @return string Access point
     * @access public
     */
         
    function getAccessPoint($service = null) 
    {
        $addr_list = $this->_wsdl_doc->documentElement->getElementsByTagNameNS('http://schemas.xmlsoap.org/wsdl/soap/','address');
        if ($addr_list->length < 1) {
            return VWP::raiseWarning('Access point not found!',get_class($this),null,false);
        }
        return $addr_list->item(0)->getAttribute('location'); 
    }
 
    /**
     * Send a SOAP message using a translator
     * 
     * @param string $method Method
     * @param array $params List of parameters
     * @param string $targetNS Target namespace
     * @return mixed Decoded SOAP response
     * @access public    
     */
           
    function ezCall($method,$params,$targetNS, $debug = false) 
    {
  
        $args = $this->_translator->encodeRequest($method,$params,$targetNS);  
        $result = $this->call($method,$args,$debug);  
        if (VWP::isWarning($result)) {
            return $result;
        }  
        $ret = $this->_translator->decodeResponse($method,$result);    
        return $ret;     
    }

    /**
     * Send a SOAP Message
     * 
     * @param string $method Method
     * @param mixed $data Raw SOAP Message
     * @return mixed Decoded SOAP response on success, error or warning otherwise   
     */
            
    function call($method,$data,$debug = false) 
    {
        if (!is_object($this->_client)) {
            return VWP::raiseWarning('Client not connected!',get_class($this),null,false);
        }

        $this->_client->_debug_vsoap = $debug;
  
        $this->_last_request = null;
        $this->_last_response = null;
        $this->_last_location = null;
  
        self::$_s_last_request = null;
        self::$_s_last_response = null;
        self::$_s_last_url = null;
        self::$_s_translator =& $this->_translator;
          
        try {
            $result = call_user_func_array(array($this->_client,$method),array($data));            
        } catch (Exception $e) {         
   
            $detail = '';
   
            if (isset($e->detail)) {
                $detail = $e->detail;
            }

            $faultstring = '';
            if (isset($e->faultstring)) {
                $faultstring = $e->faultstring;
            }
            $errmsg = $faultstring . ' : ' . $detail;   
   
            $errsys = get_class($this);
   
            if (isset($e->faultcode)) {
                $errno = $e->faultcode;
            } else {
                $errno = null;
            }
   
            if (!is_numeric($errno)) {
                $errsys = $errsys . '[' . $errno . ']';
                $errno = null;
            }
        
            $result = VWP::raiseWarning($errmsg,$errsys,$errno,false);   
        }
        
        $this->_last_request = self::$_s_last_request;
        $this->_last_response = self::$_s_last_response;
        $this->_last_location = self::$_s_last_url;
   
        if (!VWP::isWarning($result)) {
            $this->_client->_allow_transfer = $this->_allow_transfer;  
        }
    
        return $result; 
    }
 
    /**
     * Get Last Soap Request Message XML
     * 
     * @return string|object XML on success, error or warning otherwise
     * @access public
     */
         
    function getLastRequest() 
    {
        return $this->_last_request;
    }

    /**
     * Get Last Soap Response Message XML
     * 
     * @return string|object XML on success, error or warning otherwise
     * @access public
     */
 
    function getLastResponse() 
    {
        return $this->_last_response;
    }
 
    /**
     * Connect to SOAP Transport
     *
     * @param boolean $localWSDL use local copy of WSDL file
     * @return boolean|object True on success, error or warning otherwise
     * @access public
     */
         
    function connect($localWSDL = true) {
  
        if ($localWSDL) { 
            $vfile =& v()->filesystem()->file();  
            $src = $vfile->read($this->_wsdl);
            if (VWP::isWarning($src)) {   
                return $src;
            }  
            $this->_wsdl_doc = new DomDocument;
            VWP::noWarn();  
            $r = $this->_wsdl_doc->loadXML($src);
            VWP::noWarn(false);
            if (!$r) {
                $err = VWP::getLastError();
                return VWP::raiseWarning($err[1],get_class($this),null,false);  
            }
            $this->_preprocessWSDL();  
            $this->_options = array();
        }
     
        // Process WSDL Schema
        
        $schema = VSchema::getSchema($this->_wsdl); 
        if (VWP::isWarning($schema)) {
            return $schema;
        }
      
        $schemaerr = $schema->getError(null,false);
    
        if (VWP::isWarning($schemaerr)) {
            return $schemaerr;
        }
    
        // Build transport layer

        try {
            $this->_client = new VSoapClientHandler($this->_wsdl,$this->_options);   
        } catch (Exception $e) {
            return VWP::raiseWarning($e->getMessage(),get_class($this),null,false); 
        }
        if (!empty($this->_pending_request_headers)) {
            $this->setRequestHeaders($this->_pending_request_headers);
        }

        return true;   
    }
 
    /**
     * Set WSDL Filename
     * 
     * @param string $wsdl WSDL Filename
     * @access public    
     */  
 
    function setWSDL($wsdl) 
    {
        $this->_wsdl = $wsdl;
    }

    /**
     * Set request headers
     * 
     * Note: The headers are sent through the translator to ensure proper serialization
     * 
     * @param mixed $headers Request headers
     * @access public    
     */
  
    function setRequestHeaders($headers) 
    {  
      
        if (isset($this->_client) && is_object($this->_client)) {      
            $encHeaders = $this->_translator->encodeRequestHeaders($headers);
            if ($encHeaders !== null) {
                $this->_client->__setSoapHeaders($encHeaders);
            }   
        } else {
            $this->_pending_request_headers = $headers;
        }     
    }   

    /**
     * Get response headers
     *
     * Note: Deserialization is done by the Soap translator
     * 
     * @return mixed Unserialized response headers
     * @access public 
     */
    
    function getResponseHeaders() 
    {
        $lastResponse = $this->getLastResponse();  
        $encHeaders = $this->_translator->decodeResponseHeaders($lastResponse);  
        return $encHeaders;    
    }
 
    /**
     * Set Client Options
     * 
     * @param array $opt Soap Client Options
     * @access public    
     */  
 
    function setOptions($opt) 
    {
        $this->_options = $opt;
    }
 
    /**
     * Disable SOAP cache
     * 
     * @access public    
     */
     
    function disableCache() 
    {
   
        $result = true;
        if (!ini_set("soap.wsdl_cache_enabled","0")) {
            $result = false;
        }

        if (!ini_set("soap.wsdl_cache_ttl","0")) {
            $result = false;
        }
  
        if ($result) {
            $this->_soap_cache_enabled = false;
        }
  
        return $result;
    }

    /**
     * Enable SOAP cache
     * 
     * @param string $ttl Cache file Time To Live in seconds
     * @return boolean True on success  
     * @access public    
     */
 
    function enableCache($ttl = 86400) 
    {
        $result = true;
        if (!ini_set("soap.wsdl_cache_enabled","1")) {
            $result = false;
        }
        if (!ini_set("soap.wsdl_cache_ttl","$ttl")) {
            $result = false;
        }
        if ($result) {
            $this->_soap_cache_enabled = true;
        }    
        return $result;  
    }

    /**
     * Set Soap Translator
     * 
     * @param object $translator Soap Translator
     * @access public
     */
    
    function setTranslator($translator) 
    {
        $this->_translator = $translator;
    }
 
    /**
     * Class constructor
     *
     * @access public
     */
    
    function __construct() 
    {
        parent::__construct();
        $this->_translator = new VSOAPVWPTypeTranslator;
    } 
    
    // end class VSoapClient
} 


