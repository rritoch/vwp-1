<?php

/**
 * Virtual Web Platform - Web Service support
 *  
 * This file provides the default API for
 * web services.   
 * 
 * @package VWP
 * @subpackage Libraries.Networking.DRE  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

/**
 * Require XML Support
 */

VDocument::RequireDocumentType('xml');

/**
 * Require URL Routing Support
 */

VWP::RequireLibrary('vwp.ui.route');

/**
 * Virtual Web Platform - Web Service support
 *  
 * This class provides the default API for
 * web services.   
 * 
 * @todo Merge VService with VWidget
 * @todo Implement Type Namespacing
 * @package VWP
 * @subpackage Libraries.Networking.DRE  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License   
 */

class VService extends VObject 
{

    /**
     * @var string $_last_request_raw Last Request
     * @access public
     */
       
    public $_last_request_raw = null;
 
    /**
     * @var mixed $_debug_log Debug log
     * @access public
     */
    
    public $_debug_log = '';
 
    /**
     * @var mixed $_access_point Access Point
     * @access public
     */
     
    public $_access_point = null;
 
    /**
     * @var string $_tns Target namespace
     * @access public
     */
     
    public $_tns = null;
 
    /**
     * @var object $_service_doc Service manifest document
     * @access public
     */   
 
    public $_service_doc = null;
 
    /**
     * @var array $_info Service settings
     * @access public
     */
     
    public $_info = array();
  
    /**
     * Document Cache TTL
     * 
     * @var Integer $_document_cache_ttl Cache TTL in Seconds 
     * @access public
     */
  
    public $_document_cache_ttl = 86400; 
    
 
    /**
     * Resource ID
     * 
     * @param string $_R_Id Resource ID
     * @access public
     */
        
    public $_R_Id = null;
  
    /**
     * Service Name
     * 
     * @var string $_name Service name
     * @access public
     */
  
    public $_name = null;
 
    /**
     * Service ID
     * 
     * @var array $_S_Id Service Identifier
     * @access public
     */
     
    public $_S_Id = null;
 
    /**
     * Get Service Name
     * 
     * @return string Service Name
     * @access public
     */
 
    function getName() 
    {
        return $this->_name;
    }

    /**
     * Get Resource ID
     * 
     * @return string Resource ID
     * @access public
     */
     
    function getResourceID() 
    {
  
        $user = VUser::getCurrent();
        $shellob = $user->getShell();
  
        // Check Cache
        if ((!is_string($this->_R_Id)) || empty($this->_R_Id)) {
      
            $appID = $shellob->getVar('app');     
            if (empty($appID)) {
                $app =& VApplication::getCurrent();
                if (is_object($app)) {
                    $appID = $app->getName();
                }
            }
       
            if (!empty($appID)) {      
                $route = VRoute::getInstance();    
                $widgetID = $shellob->getVar('widget');
                if (empty($widgetID)) {
                    $R_Id = 'index.php?format=wsdl&app='.urlencode($appID);        
                } else {   
                    $R_Id = 'index.php?format=wsdl&app='.urlencode($appID).'&widget='.urlencode($widgetID);
                }          
                $this->_R_Id = $route->encode($R_Id);
            }
        }
           
        return $this->_R_Id;
    }

    /**
     * Get Service ID
     * 
     * @param string $method Service Method
     * @access public
     */
 
    function getServiceID($method = null) 
    {
        if ($this->_S_Id === null) {
            if (!empty($this->_name)) {      
                $this->_S_Id = array(
                    "service"=>$this->_name,     
                );
            }
        }
  
        $S_Id = $this->_S_Id;
  
        if ($S_Id !== null) {  
            if (!empty($method)) {
                $S_Id["method"] = $method;     
            }
        }
        return $S_Id;       
    }      
        
    /**
     * Set Client ID
     *
     * @param array $C_Id Client ID
     * @access public     
     */
       
    function setClient($C_Id) 
    {
        if ($C_Id === null) {
            if (isset($this->_info['C_Id'])) {
                unset($this->_info['C_Id']);
            }
        } else {
            $this->_info['C_Id'] = $C_Id;
        }
    }
  
    /**
     * Get Response Headers
     * 
     * @param string $method Service Method
     * @access public
     */
  
    function getSecurityHeaderInfo($method) 
    {
    
        $info = array();

        // Auto-Assign Resource and Service Info
 
        $R_Id = $this->getResourceID();  
        if ($R_Id !== null) {
            $info["R_Id"] = $R_Id;
        }
    
        $S_Id = $this->getServiceID($method);
        if ($S_Id !== null) {
            $info["S_Id"] = $S_Id;
        }
        
        //  Assign Basic Header Info
  
        $basicInfo = array('C_Id','UR_List','URM_List','Authorize_Client_URL');

        foreach($basicInfo as $infoItem) {
            if (isset($this->_info[$infoItem])) {
                $info[$infoItem] = $this->_info[$infoItem];
            }
        }
            
        if (count(array_keys($info)) < 1) {
            return null;
        }
            
        return $info; 
    }
     
    /**
     * List Service Methods
     * 
     * @return array Service methods
     * @access public
     */
  
    function getMethodList() 
    {
        $thisMethods = get_class_methods( get_class( $this ) );
        $baseMethods = get_class_methods( 'VService' );
        $allmethods = array_diff( $thisMethods, $baseMethods ); 
        $methods = array();  
        foreach($allmethods as $m) {
            if (substr($m,0,1) != "_") {
                $methods[] = $m;
            }
        }  
        return $methods; 
    }
 
    /**
     * Get return types
     * 
     * @return array Return types indexed by method name
     * @access public
     */
 
    function getReturnTypes() 
    {
        $rtypes = array();
        $methods = $this->_service_doc->getElementsByTagName('method');
        for($p = 0;$p < $methods->length; $p++) {
            $m = $methods->item($p);
            $name = $m->getAttribute('name');
            $r = $m->getElementsByTagName('return');
            $rtype = "void";
            if ($r->length > 0) {
                $r = $r->item(0);
                $rtype = $r->getAttribute('type');
            }
            $rtypes[$name] = $rtype;
        }
        return $rtypes;
    }
     
    /**
     * Set access point
     * 
     * @param string $uri Access point URI
     * @access public
     */
           
    function setAccessPoint($uri) 
    {
        $this->_access_point = $uri;
    }

    /**
     * Get WSDL Source
     * 
     * @param string $service_manifest Service Manifest Filename  
     * @return string WSDL Source
     * @access public
     */
 
    function getWSDL($service_manifest) 
    {
 
        $vfile =& v()->filesystem()->file();
        // Process service document
  
        $this->_service_doc = new DomDocument;
        $data = $vfile->read($service_manifest);
  
        if (VWP::isWarning($data)) 
        {
            return $data;
        }
  
        VWP::noWarn();
        $r = $this->_service_doc->loadXML($data);
        VWP::noWarn(false);
        if (!$r) {
            $r = VWP::getLastError();
            return VWP::raiseWarning($r[1],get_class($this),null,false);
        }
  
        $description = '';  
        $tmp = $this->_service_doc->getElementsByTagName('description');
        if ($tmp->length > 0) {
            $description = $tmp->item(0)->nodeValue; 
        }
  
        $tmp = $this->_service_doc->getElementsByTagName('name');
        if ($tmp->length > 0) {
            $this->_name = $tmp->item(0)->nodeValue; 
        }
  
        $service_name = $this->_name;  
        $clean_service_name = empty($this->_name) ? '' : $this->_name;
        $clean_service_name = preg_replace( '/[^A-Z0-9_\\.]/i', '', $clean_service_name );
  
        // Process WSDL Document
  
        $nl = "\n";
  
        $this->_tns = 'urn:'.$clean_service_name;
    
        $wsdl = 
             '<' . '?xml version="1.0" encoding="utf-8" ?' . '>'.$nl
           . '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" targetNamespace="'.$this->_tns.'" xmlns:tns="'.$this->_tns.'">'.$nl
           . '<!-- '.$nl
           . '  This file was generated automatically by the Virtual Web Platform'.$nl
           . '  Virtual Web Platform (tm) - (c) Ralph Ritoch 2010 - http://www.vnetpublishing.com' . $nl.$nl
           . '-->'.$nl 
           . ' <types>'.$nl
           . '  <xsd:schema elementFormDefault="qualified" targetNamespace="'.$this->_tns.'" />'.$nl
           . ' </types>'.$nl
           . ' <service name="' . XMLDocument::xmlEntities($service_name) . '">'.$nl
           . '  <documentation>'.XMLDocument::xmlEntities($description).'</documentation>'.$nl
           . ' </service>'.$nl      
           . '</definitions>';
  
        $wsdl_doc = new DomDocument;
        $wsdl_doc->loadXML($wsdl);
  
        // generate WSDL
  
        $bindings = $this->getBindings();

        foreach($bindings as $name) {
            $binding =& $this->getBinding($name);
            if (VWP::isWarning($binding)) {
                return $binding;
            }
            $binding->registerWSDL($wsdl_doc,$this->_tns,$this->_service_doc, $this->_access_point);   
        }
  
        return $wsdl_doc->saveXML();
    }
 
    /**
     * Get list of bindings
     * 
     * @return array Bindings
     * @access public
     */
           
    function getBindings() 
    {
        static $binding_list;
  
        if (!isset($binding_list)) {
            $vfolder =& v()->filesystem()->folder();
            $vfile =& v()->filesystem()->file();
            $b_files = $vfolder->files(VPATH_LIB.DS.'vwp'.DS.'net'.DS.'dre'.DS.'bindings');
            if (VWP::isWarning($b_files)) {
                $binding_list = array();
            } else {
                $binding_list = array();
                foreach($b_files as $filename) {
                    if ($vfile->getExt($filename) == "php") {
                        $name = $vfile->stripExt($filename);
                        array_push($binding_list,$name);
                    }
                }  
            }
        } 
        return $binding_list;
    }
  
    /**
     * Get a binding object
     * 
     * @param string $name Binding name
     * @return object Binding object on success, error or warning otherwise
     */
         
    function &getBinding($name) 
    {
        static $ob_bindings = array();
  
        if (!isset($ob_bindings[$name])) {
            $vfile =& v()->filesystem()->file();
            $_inc = VPATH_LIB.DS.'vwp'.DS.'net'.DS.'dre'.DS.'bindings';
            $filename = $_inc.DS.$name.'.php';
            $className = strtoupper($name) . '_DREBinding';
   
            if ($vfile->exists($filename)) {
                require_once($filename);
                if (class_exists($className)) {
                    $ob_bindings[$name] = new $className();
                } else {
                    $ob_bindings[$name] = VWP::raiseWarning("Binding $className not found!",get_class($this),null,false);
                }        
            } else {
                $ob_bindings[$name] = VWP::raiseWarning("Binding $name not found!",get_class($this),null,false);
            }
        }
  
        return $ob_bindings[$name];  
    }
 
    /**
     * Run service binding
     * 
     * @return mixed Binding service result
     */
    
    function run() 
    {
        // get bindings
        $bindings = $this->getBindings();
  
        $handled = false;
        $result = null;
    
        foreach($bindings as $binding) {
            if ($handled !== true) {
                $b =& $this->getBinding($binding);
                if (!VWP::isWarning($b)) {
                    $handled = $b->run($this,$result);
                }
            }
        }
  
        if ($handled !== true) {
            return VWP::raiseWarning('No binding available for the current request!',get_class($this),null,false);
        }
  
        return $result;
    }

    /**
     * Get Cache TTL
     * 
     * @return integer Cache TTL in seconds
     * @access public
     */
     
    function getCacheTTL() 
    {
        return $this->_document_cache_ttl;
    }
 
    /**
     * Disable Document cache
     *   
     * @access public    
     */
 
    function disableCache() 
    {
        $this->_document_cache_ttl = 0;
    }
 
    /**
     * Enable Document cache
     * 
     * @param string $ttl Cache file Time To Live in seconds  
     * @access public    
     */
 
    function enableCache($ttl = 86400) 
    {
        $this->_document_cache_ttl = $ttl;
    }
    
    // end class VService
} 
