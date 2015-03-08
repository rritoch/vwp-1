<?php

/**
 * Virtual Web Platform - XSLT processor
 *  
 * This file provides a XSLT Processor
 *        
 * @package VWP
 * @subpackage Libraries.XML  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch 2011 - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

/**
 * Virtual Web Platform - XSLT processor
 *  
 * This class provides a XSLT Processor
 *        
 * @package VWP
 * @subpackage Libraries.XML  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch 2011 - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

class VXSLT extends VObject 
{

	/**
	 * Translate a document
	 * 
	 * @param object $srcDoc Source document
	 * @param object $xsltDoc XSLT Transformation document
	 * @access public
	 */
	
    public static function translate($srcDoc,$xsltDoc) 
    {
        $proc = new XSLTProcessor();
        $proc->importStylesheet($xsltDoc);        
        $t = $proc->transformToXML($srcDoc);        
        $tdoc = new DomDocument;
        $tdoc->loadXML($t);
        return $tdoc;                
    }
    
    // end class VXSLT
}

