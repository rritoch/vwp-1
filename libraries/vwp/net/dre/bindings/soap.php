<?php

/**
 * VWP SOAP Binding
 *
 * @todo Implement a custom Soap Server
 * @package VWP
 * @subpackage Libraries.Net.DRE.Bindings
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */ 

/**
 * Require Binding Support
 */

VWP::RequireLibrary('vwp.net.dre.binding');

/**
 * Require URL Routing support
 */

VWP::RequireLibrary('vwp.ui.route');

/**
 * Require POST Data support
 */

VWP::RequireLibrary('vwp.server.post');

/**
 * Require User Support
 */

VWP::RequireLibrary('vwp.user');

/**
 * Require Soap Translator Support
 */

VWP::RequireLibrary('vwp.net.dre.translators.soap');

/**
 * Require Soap Server Clone Support
 */

VWP::RequireLibrary('vwp.net.dre.bindings.soap.serverclone');


/**
 * VWP SOAP Binding
 *
 * @package VWP
 * @subpackage Libraries.Net.DRE.Bindings
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */ 

class SOAP_DREBinding extends VDREBinding 
{

	/**	 
	 * Access Point
	 * @var string $_access_point
	 * @access public	 
	 */
	
    public $_access_point = null;
    
    /**     
     * Service Document
     * 
     * @var object $_sevice_doc Service document
     * @access public
     */
    
    public $_service_doc = null;
    
    /**
     * Target Namespace
     *
     * @var string $_tns Target namespace
     * @access public
     */
    
    public $_tns = null;
    
    /**     
     * Service Name
     * 
     * @var string $_service_name Service name
     * @access public
     */
    
    public $_service_name = null;
    
    /**
     * Server Clone
     * 
     * @var object Server Clone
     * @access public
     */
    
    public $_soap_clone = null;
 
    /**
     * Soap translator
     * 
     * @var object Soap Translator
     * @access public
     */
    
    public $_translator = null;
   
    /**
     * Soap Server
     *
     * @var object Soap Server
     * @access public
     */
    
    public $_soap_server = null;
 
    /**     
     * WSDL Filename
     * 
     * @var string $_wsdl WSDL Filename
     * @access public
     */
    
    public $_wsdl = null;
 
    /**
     * Parameter types
     * 
     * @var array $_param_types Parameter types
     */
    
    public $_param_types = null;
 
    /**     
     * Clone counter
     * 
     * @var integer $_clones Clone count
     * @access private
     */
 
    static $_clones = 0;
 
    /**
     * Set SOAP Translator
     * 
     * @param object $ob Soap Translator
     * @access public
     */  
    
    function setTranslator($ob) 
    {
        $this->_translator = $ob;
    }
 
    /**
     * Make Server Clone Function 
     * 
     * @param string $method Method Name
     * @param string $rtype Return type
     * @return string Server Clone Function
     * @access public
     */
    
    function makeServerCloneFunc($method,$rtype) 
    {

        $nl = "\n";
  
        $f  = '';
  
        $f .= ' function '.$method.'($args) {'.$nl;  
        $f .= '  $this->_service->_last_request_raw = $args;'.$nl;
        $f .= '  $args = $this->_translator->decodeRequest("'.addslashes($method).'",$args,"'.addslashes($rtype).'",$this->_service,$this->_server);'.$nl;      
        $f .= '  $response = call_user_func_array(array($this->_service,__FUNCTION__),$args);'.$nl; 
        $f .= '  $response = $this->_translator->encodeResponse("'.addslashes($method).'",$response,"'.addslashes($rtype).'",$this->_service,$this->_server);'.$nl;  
        $f .= '  return $response;'.$nl;
        $f .= ' }';

        return $f;
    }
   
    /**
     * Make a server clone
     * 
     * @param string $service Service Name
     * @return object Server Clone
     */
    
    function makeServerClone($service) 
    {
            
        self::$_clones++;
  
        $className = 'VSOAPServerClone_'.self::$_clones;

        $classTemplate = array();
        $classTemplate[] = 'class ' . $className . ' extends VSOAPServerClone { ';
  
        $rtypes = $service->getReturnTypes();
  
        foreach($rtypes as $method=>$rtype) {
            $classTemplate[] = $this->makeServerCloneFunc($method,$rtype);
        }
    
        $classTemplate[] = '}';  
        eval(implode("\n",$classTemplate));       
        return new $className();
    }


    /**
     * Disable SOAP cache
     *
     * @return boolean True on success
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
            $_soap_cache_enabled = false;
        }
  
        return $result;
    }

    /**
     * Enable SOAP cache (DISABLED)
     * 
     * Note: This function is currently disabled
     *      
     * @param string $ttl Cache file Time To Live in seconds  
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
            $_soap_cache_enabled = true;
        }    
        return $result;  
    }

    /**
     * Run the binding
     * 
     * @param object $service Service object  
     * @param mixed $result Service binding result
     * @access public    
     */
         
    function run($service,&$result) 
    {
 
        $doc =& VWP::getDocument();
        $docType = get_class($doc);
 
        $cacheTTL = $service->getCacheTTL();
  
        if ($docType !== 'SOAPDocument') {
            return false;
        }  
  
        if (!empty($this->_soap_server)) {
            $result = VWP::raiseWarning('SOAP Server is busy!',get_class($this),null,false);
            return true;
        }
  
        // create WSDL File
        $vfile =& v()->filesystem()->file();
  
        $this->_wsdl = $vfile->mktemp();
        if (VWP::isWarning($this->_wsdl)) {
            $result = $this->_wsdl;
            return true;
        }
     
        $nl = "\n";
        $clean_wsdl =
             '<' . '?xml version="1.0" encoding="utf-8" ?' . '>'.$nl   
            . '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" targetNamespace="'.$this->_tns.'" xmlns:tns="'.$this->_tns.'">'.$nl
            .' <types>'.$nl
            .'  <xsd:schema elementFormDefault="qualified" targetNamespace="'.$this->_tns.'" />'.$nl
            .' </types>'.$nl
            .' <service name="'.XMLDocument::xmlentities($this->_service_name).'"/>'.$nl
            .'</definitions>'.$nl;
  
        $tmp = new DomDocument;
        $tmp->loadXML($clean_wsdl);
        $this->registerWSDL($tmp,$this->_tns,$this->_service_doc,$this->_access_point);
        $wsdl = $tmp->saveXML();
        $vfile->write($this->_wsdl,$wsdl);
    
        // Setup server
  
        if (empty($cacheTTL)) {      
            $options = array('cache_wsdl'=>WSDL_CACHE_NONE);
            $this->disableCache();
        } else {
            $this->enableCache($cacheTTL);
        }
  
        // $options['uri'] = $this->_tns;
  
        $this->_soap_server = new SoapServer($this->_wsdl,$options);
        //$this->_soap_server = new SoapServer(null,$options);
  
        // Setup clone
    
        $this->_soap_clone = $this->makeServerClone($service);
        $this->_soap_clone->_service = $service;
        $this->_soap_clone->_translator = $this->_translator;
        $this->_soap_clone->_server =& $this->_soap_server;  
        $this->_soap_server->setObject($this->_soap_clone);
 
        $user =& VUser::getCurrent();
        $cb = $user->createCallback(array($this,'sendResponse'));

        // Register response callback    
        VWP::addCleanupTask($cb);  
  
        return true;
    }

    /**
     * Send Soap Response
     * 
     * @access public
     */
    
    function sendResponse() 
    {
    
        $d = VRawPost::getData();
        if (empty($d)) { 
            $this->_soap_server->handle();
        } else {
            $this->_soap_server->handle($d);
        }
  
        // Delete WSDL File
        $vfile =& v()->filesystem()->file();
        $vfile->delete($this->_wsdl);   
    }
 
    /**
     * Add binding to WSDL document
     * 
     * @param object $wsdl_doc WSDL Document
     * @param object $service_doc Service definition document
     * @param string $uri Access point URI  
     * @access public        
     */   

    function registerWSDL($wsdl_doc,$tns,$service_doc, $uri) 
    {
    	
        $this->_access_point = $uri;
        $this->_service_doc = $service_doc;
        $this->_tns = $tns;
  
        $service_name = '';
        $tmp = $this->_service_doc->getElementsByTagName('name');
        if ($tmp->length > 0) {
            $service_name = $tmp->item(0)->nodeValue;  
        }
        $this->_service_name = $service_name;
  
        $bindingPrefix = 'VWPSOAP';
    
        $service_id = $bindingPrefix.$service_name;        
        $service_id = preg_replace( '/[^A-Z0-9_\\.]/i', '', $service_id );
    
        $portName = $service_id.'Port';
        $portTypeName = $service_id.'PortType';
        $bindingName = $service_id.'Binding';
  
        $securityRequestHeaderName = 'vwpsec:SecurityInfoRequest';
        $securityResponseHeaderName = 'vwpsec:SecurityInfoResponse';
  
        $baseTypes = array(
            'keyval_item' => $bindingPrefix.'KeyValueData',
            'keyval' => $bindingPrefix.'ArrayOfKeyValueData',
            'map' => 'vwpsoap:Mapping',
            'keyvalarray' => $bindingPrefix.'TableOfKeyValueData',
            'array' => 'vwpsoap:Array',   
            'void' => $bindingPrefix.'Void',
        );

        $refBaseTypes = array(   
            'boolean' => 'xsd:boolean',
            'string' => 'vwpsoap:String',
            'keyval' => $bindingPrefix.'ArrayOfKeyValueData',
            'map' => 'vwpsoap:Mapping',
            'table' => 'vwpsoap:DataTable',
            'keyvalarray' =>  $bindingPrefix.'TableOfKeyValueData',
            'array' => 'vwpsoap:Array',
            'void' => $bindingPrefix.'Void',
        );

        $this->_param_types = array(   
            'boolean' => 'xsd:boolean',
            'string' => 'xsd:string',
            'keyval' => $bindingPrefix.'ArrayOfKeyValueData',
            'map' => 'vwpsoap:Mapping',
            'table' => 'vwpsoap:DataTable',
            'keyvalarray' =>  $bindingPrefix.'TableOfKeyValueData',
            'array' => $bindingPrefix.'ArrayOfStrings',
            'void' => $bindingPrefix.'Void',
        );


        // Add Default Data Types
    
        $myTypes = array();
  
        $schemaNS = "http://www.w3.org/2001/XMLSchema";
  
        $vwpsoapNS = "http://standards.vnetpublishing.com/schemas/soap/2010/102/vwp-soap-types";
        $vwpsoapsecNS = "http://standards.vnetpublishing.com/schemas/soap/2010/102/vwp-soap-security";
  
        $wsdl_doc->documentElement->setAttribute('xmlns:vwpsoap',$vwpsoapNS);
        $wsdl_doc->documentElement->setAttribute('xmlns:vwpsec',$vwpsoapsecNS);
  
        $tmp = $wsdl_doc->getElementsByTagName('types');
        $types = $tmp->item(0);
        $tmp = $types->getElementsByTagNameNS($schemaNS,'schema'); 
        $schema = $tmp->item(0);

        $requestHeader = 'SecurityInfoRequest';
        $responseHeader = 'SecurityInfoResponse';

        // Import 
        $type = $wsdl_doc->createElement('xsd:import');
        
        //$type->setAttribute('namespace',$vwpsoapNS);
        //$type->setAttribute('schemaLocation',$vwpsoapNS.'?t='.time());  

        $type->setAttribute('namespace',$vwpsoapsecNS);
        $type->setAttribute('schemaLocation',$vwpsoapsecNS.'/?t='.time());

        $myTypes[] = $type; 
     
        // Add request header message

        $msgName = $requestHeader;  
        $msg = $wsdl_doc->createElement('message');
        $msg->setAttribute('name',$msgName);
        $part = $wsdl_doc->createElement('part');
        $part->setAttribute('name',$securityRequestHeaderName);
        $part->setAttribute('element',$securityRequestHeaderName);
        $msg->appendChild($part);
        $myMessages[$msgName] = $msg;

        // Add response header message
    
        $msgName = $responseHeader;
        $msg = $wsdl_doc->createElement('message');
        $msg->setAttribute('name',$msgName);
        $part = $wsdl_doc->createElement('part');
        $part->setAttribute('name',$securityResponseHeaderName);
        $part->setAttribute('element',$securityResponseHeaderName);
        $msg->appendChild($part);
        $myMessages[$msgName] = $msg;  
    
        // Import Methods
  
        $portTypeOperations = array();
        $bindingOperations = array();
  
        $methods = $service_doc->getElementsByTagName('method');
        for($m = 0; $m < $methods->length; $m++) {
            // Init method
   
            $method = $methods->item($m);
            $name = $method->getAttribute('name');
   
            $requestName = $name; //.'Request';
            $responseName = $name.'Response';
            $compatRequestName = $name;
            $compatResponseName = $name;
   
            $requestPart = $requestName;
            $responsePart = $responseName;
      
            $arguments = $method->getElementsByTagName('argument');
            $tmp = $method->getElementsByTagName('return');
            $ret = $tmp->item(0); 
  
            // Add Request Type
   
            $type = $wsdl_doc->createElement('xsd:element');
            $type->setAttribute('name',$requestName);
            if ($arguments->length > 0) {
                $complexType = $wsdl_doc->createElement('xsd:complexType');
                $sequence = $wsdl_doc->createElement('xsd:sequence');

                for($p = 0; $p < $arguments->length; $p++) {
             
                    // This arg typing may only be needed for IBM style
                     
                    $argName = $arguments->item($p)->getAttribute('name');
                    $argType = $arguments->item($p)->getAttribute('type');
               
                    if (isset($refBaseTypes[$argType]) && ($argType != "void")) {
                        // Build Content
    
                        // Build element

                        $arg = $wsdl_doc->createElement('xsd:element');
                        $arg->setAttribute('minOccurs',"0");
                        $arg->setAttribute('maxOccurs',"1");
                        $arg->setAttribute("name",$argName);
      
                        $arg->setAttribute("type",$refBaseTypes[$argType]);
      
                        $sequence->appendChild($arg);     
                    }
                }
                $complexType->appendChild($sequence);
                $type->appendChild($complexType);  
            }
               
            $myTypes[] = $type;
   
            // Add Response Type
   
            $returnType = $ret->getAttribute('type');
   
            $type = $wsdl_doc->createElement('xsd:element');
            $type->setAttribute('name',$responseName);
            $argType = $returnType;
            if (isset($refBaseTypes[$argType]) && ($argType != "void")) {
                $type->setAttribute('type',$refBaseTypes[$argType]);
            }
                  
            $myTypes[] = $type;
   
            // Add request message

            $msgName = $service_id.$requestName;
            $requestMessage = $msgName;  
            $msg = $wsdl_doc->createElement('message');
            $msg->setAttribute('name',$msgName);
            $part = $wsdl_doc->createElement('part');
            $part->setAttribute('name',$requestPart);
            $part->setAttribute('element',/*'tns:'.*/ $requestName);
            $msg->appendChild($part);
            $myMessages[$msgName] = $msg;   
      
            // Add response message
  
            $msgName = $service_id.$responseName;
            $responseMessage = $msgName;   
            $msg = $wsdl_doc->createElement('message');
            $msg->setAttribute('name',$msgName);
            $part = $wsdl_doc->createElement('part');
            $part->setAttribute('name',$responsePart);
            //  $part->setAttribute('element','tns:'.$responseName);
            $part->setAttribute('type',$refBaseTypes[$returnType]);
            $msg->appendChild($part);
            $myMessages[$msgName] = $msg;
     
            // Add port type operation
   
            $op = $wsdl_doc->createElement('operation');
            $op->setAttribute('name',$name);
            $tmp = $method->getElementsByTagName('summary');
            if ($tmp->length > 0) {
                $d = $wsdl_doc->createElement('documentation',XMLDocument::xmlentities($tmp->item(0)->nodeValue));
                $op->appendChild($d);
            }   
            $e = $wsdl_doc->createElement('input');
            $e->setAttribute('message','tns:'.$requestMessage);
            $op->appendChild($e);
            $e = $wsdl_doc->createElement('output');
            $e->setAttribute('message','tns:'.$responseMessage);                              
            $op->appendChild($e);
            $portTypeOperations[] = $op;
                        
            // Add binding operation
   
            $op = $wsdl_doc->createElement('operation');
            $op->setAttribute('name',$name);
   
            // Binding Input
            
            $e = $wsdl_doc->createElement('input');
            $body = $wsdl_doc->createElement('soap:body');
            $body->setAttribute('parts',$requestPart);
            $body->setAttribute('use','literal');
            $body->setAttribute('namespace',$tns);
            $header = $wsdl_doc->createElement('soap:header');
            $header->setAttribute('part',$securityRequestHeaderName);
            $header->setAttribute('use','literal');
            $header->setAttribute('message','tns:'.$requestHeader);   
            $e->appendChild($header);
            $e->appendChild($body);
            $op->appendChild($e);
   
            // Binding output
   
            $e = $wsdl_doc->createElement('output');
            $body = $wsdl_doc->createElement('soap:body');
            $body->setAttribute('parts',$responsePart);   
            $body->setAttribute('use','literal');
            $body->setAttribute('namespace',$tns);
            $header = $wsdl_doc->createElement('soap:header');
            $header->setAttribute('part',$securityResponseHeaderName);
            $header->setAttribute('use','literal');
            $header->setAttribute('message','tns:'.$responseHeader);   
            $e->appendChild($header);
            $e->appendChild($body);
            $op->appendChild($e);
   
            // Binding Fault
          
            $e = $wsdl_doc->createElement('fault');
            $body = $wsdl_doc->createElement('soap:body');   
            $body->setAttribute('use','literal');
            $body->setAttribute('namespace',$tns);
            $e->appendChild($body);
            $op->appendChild($e);
   
            // Link Binding 
            $bindingOperations[] = $op;
        }
      
        // Create Port Type
  
        $portType = $wsdl_doc->createElement('portType');
        $portType->setAttribute('name',$portTypeName);
        foreach($portTypeOperations as $op) {
            $portType->appendChild($op);
        }
  
        // Create Binding
  
        $binding = $wsdl_doc->createElement('binding');
        $binding->setAttribute('name',$bindingName);
        $binding->setAttribute('type','tns:'.$portTypeName);
        $sbind = $wsdl_doc->createElement('soap:binding');
        $sbind->setAttribute('style','document');
        $sbind->setAttribute('transport',"http://schemas.xmlsoap.org/soap/http");
        $binding->appendChild($sbind);  
        foreach($bindingOperations as $op) {
            $binding->appendChild($op);
        }
        
        // Create port
  
        $tmp = $wsdl_doc->getElementsByTagName('service');
        $service = $tmp->item(0);
        $port = $wsdl_doc->createElement('port');
        $port->setAttribute('name',$portName);
        $port->setAttribute('binding','tns:'.$bindingName);
        $url = $this->_access_point;
        if (strpos($url,'?') === false) {
            $url .= '?format=soap';
        } else {
            $url .= '&format=soap';
        }
        $url = VRoute::getInstance()->encode($url);
        $access_point = $wsdl_doc->createElement('soap:address');
        $access_point->setAttribute('location',XMLDocument::xmlentities($url));
        $port->appendChild($access_point);
        $service->appendChild($port);
      
        // Rebuild Document
    
        // Grab Service
        $service = $wsdl_doc->documentElement->removeChild($service);  

        // add types
  
        foreach($myTypes as $type) {
            $schema->appendChild($type);
        }

        // add messages
  
        foreach($myMessages as $name=>$msg) {
            $wsdl_doc->documentElement->appendChild($msg);
        }
  
        // add port type
  
        $wsdl_doc->documentElement->appendChild($portType);
  
        // add binding
        $wsdl_doc->documentElement->appendChild($binding);
  
        // add service
        $wsdl_doc->documentElement->appendChild($service);         
    }

    /**
     * Class Constructor
     * 
     * @access public
     */
    
    function __construct() 
    {
        VWP::RequireExtension('soap');
 
        parent::__construct();
        if (empty($this->_translator)) {
            $this->_translator = new VSOAPVWPTypeTranslator;
        } 
    }

    // end class SOAP_DREBinding
} 
