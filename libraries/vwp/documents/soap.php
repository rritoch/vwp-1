<?php

/**
 * Virtual Web Platform - Soap Document support
 *  
 * This file provides the default API for
 * XML Documents.   
 * 
 * @package VWP
 * @subpackage Libraries.Documents  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License   
 */

/**
 * Require xml document support
 */

VWP::RequireLibrary('vwp.documents.xml');

/**
 * Virtual Web Platform - SOAP Document support
 *  
 * This class provides the default API for
 * SOAP Documents. This is the base class for all document types.   
 * 
 * @package VWP
 * @subpackage Libraries.Documents  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License   
 */
 
class SOAPDocument extends XMLDocument 
{

	/**	 
	 * Mime Type
	 * 
	 * @var string $_mime_type Mime type	 
	 * @access public
	 */
	
    public $_mime_type = "text/xml";
     
    /**
     * Display document
     * 
     * @access public
     */
 
    function render() 
    {      
        // Binding must handle this! Since 
        // some PHP SoapServer handlers lock output buffers       
    }
  
    /**
     * Class constructor
     * 
     * @access public
     */
         
    function __construct() 
    {
  
        parent::__construct();   
        $this->header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); 
        $this->header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        $this->header('Cache-Control: no-store, no-cache, must-revalidate'); 
        $this->header('Cache-Control: post-check=0, pre-check=0', false); 
        $this->header('Pragma: no-cache');
        if (!empty($this->_mime_type)) {
            $this->header('Content-Type: '.$this->_mime_type . ';charset=' . $this->_charset);
        }
        $this->sendHeaders();      
    }

    // end class SOAPDocument
} 
