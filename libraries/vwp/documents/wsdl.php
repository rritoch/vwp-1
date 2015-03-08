<?php

/**
 * Virtual Web Platform - WSDL Document support
 *  
 * This file provides the default API for
 * WSDL Documents.   
 * 
 * @package VWP
 * @subpackage Libraries.Documents  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License   
 */

/**
 * Require XML document support
 */

VWP::RequireLibrary('vwp.documents.xml'); 

/**
 * Virtual Web Platform - WSDL Document support
 *  
 * This class provides the default API for
 * WSDL Documents. This is the base class for all document types.   
 * 
 * @package VWP
 * @subpackage Libraries.Documents  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License   
 */
 
class WSDLDocument extends XMLDocument 
{

	/**	 
	 * Mime type
	 * 
	 * @var string $_mime_type Mime type
	 * @access public
	 */
	
    public $_mime_type = "text/xml";
 
    /**
     * XML Document
     *      
     * @var object $_xml_doc XML Document
     * @access public
     */
    
    public $_xml_doc = null;
 

    /**     
     * Get service method list
     * 
     * @param unknown_type $service
     * @access public
     */
    
    function getMethodList($service) 
    {
        if (empty($service)) {
            $rootNode = $this->_xml_doc->documentElement;
        } else {
            $rootNode = $this->_xml_doc->documentElement;
        }
  
        $result = array();
        $ns = "http://schemas.xmlsoap.org/wsdl/";
  
        $methods = $rootNode->getElementsByTagNameNS($ns,'operation');  
        for($ptr = 0; $ptr < $methods->length; $ptr++) {
            $name = $methods->item($ptr)->getAttribute('name');
            if (!in_array($name,$result)) {
                array_push($result,$name);
            }   
        }
  
        return $result;
    }
 
    /**     
     * Load WSDL File 
     * 
     * @param string $filename Filename
     * @access public
     */
    
    function loadWSDLFile($filename) 
    {
        return $this->loadXMLFile($filename);
    }
 
    /**     
     * Load WSDL Document from source data
     * 
     * @param string $data Data
     * @access public
     */
    
    function loadWSDL($data) 
    {
        return $this->loadXML($data);
    }
 
    /**
     * Get theme Template
     * 
     * @param string Theme Path  
     * @return string Path to template    
     */
  
    function getThemeTemplateFile($themePath) 
    {
        return $themePath.DS.'template.wsdl.php'; 
    }

    // end class WSDLDocument   
} 
