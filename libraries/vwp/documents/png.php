<?php
/**
 * Virtual Web Platform - PNG Response Document
 *  
 * This file provides the default API for
 * PNG Response Documents.   
 * 
 * @package VWP
 * @subpackage Libraries.Documents  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

/**
 * Require Image Document
 */

VWP::RequireLibrary('vwp.documents.image');


/**
 * Virtual Web Platform - PNG Response Document
 *  
 * This class provides the default API for
 * PNG Response Documents.   
 * 
 * @package VWP
 * @subpackage Libraries.Documents  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

class PNGDocument extends ImageDocument 
{
	/**
	 * Mime Type
	 * 
	 * @var string $_mime_type Mime type
	 */
	
	public $_mime_type = "image/jpeg";		
}