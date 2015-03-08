<?php

/**
 * Widget Reference  
 * 
 * This file provides widget references
 *    
 * @package    VWP
 * @subpackage Libraries.UI
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License
 */

VWP::RequireLibrary('vwp.ui.widget.params');

/**
 * Widget Reference  
 * 
 * This class provides widget references
 *    
 * @package    VWP
 * @subpackage Libraries.UI
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License
 */

class VWidgetReference extends VObject 
{

	const XMLNS = 'http://standards.vnetpublishing.com/schemas/vwp/2011/02/WidgetReference';
    const XMLNS_LOC = 'http://standards.vnetpublishing.com/schemas/vwp/2011/02/WidgetReference/';
    
	protected $_id;
	
	protected $widgetId;
	
	/**
	 * Widget Parameters
	 * 
	 * @var VWidgetParams $params Widget Parameters
	 */
	
	protected $params; 
	
	static $_references = array();
	
	public static function clean($refId) 
	{
		$parts = explode('/',$refId);
		$f =& v()->filesystem()->file();
		$len = count($parts);
		for($i=0;$i<$len; $i++) {
		    $parts[$i] = $f->makeSafe($parts[$i]);
		}
		return implode('/',$parts);
	}	
	
	function delete() 
	{
		$filename = self::_getFilename($this->_id);
		if (isset(self::$_references[$this->_id])) {
		    unset(self::$_references[$this->_id]);
		}
		$this->_id = null;
		return v()->filesystem()->file()->delete($filename);
	}
	
	function setParams(&$params) 
	{
		$this->params =& $params;
	}
		
	function save() 
	{
		
		if (empty($this->_id)) {
			return VWP::raiseWarning('Reference destroyed!',__CLASS__,null,false);
		}
		
		VWP::RequireLibrary('vwp.documents.xml');
		
		$doc = new DOMDocument;
		
		$xsi = 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"';
		$base = '<' . '?xml version="1.0" ?' . '>'."\n";
		$base .= '<widget_reference '.$xsi.' xmlns="'.self::XMLNS.'" xsi:schemaLocation="'.self::XMLNS.' '. self::XMLNS_LOC.'" />';
		
		$doc->loadXML($base);
		
		$widget = $doc->createElementNS(self::XMLNS,'widget');
		if (!empty($this->widgetId)) {
		    $v = $doc->createTextNode(XMLDocument::xmlentities($this->widgetId));
		    $widget->appendChild($v);
		}
		$params = $doc->createElementNS(self::XMLNS,'params');
		
		$data = isset($this->params) ? $this->params->getProperties() : array();
		foreach($data as $name=>$value) {
			$item = $doc->createElementNS(self::XMLNS,'param');
			$item->setAttribute('name',XMLDocument::xmlentities($name));
			$v = $doc->createTextNode(XMLDocument::xmlentities($value));
			$item->appendChild($v);
			$v = $doc->createTextNode("\n  ");
			$params->appendChild($v);
			$params->appendChild($item);
		}
		
		$v = $doc->createTextNode("\n ");
		$doc->documentElement->appendChild($v);
		$doc->documentElement->appendChild($widget);
		$v = $doc->createTextNode("\n ");
		$doc->documentElement->appendChild($v);
		$doc->documentElement->appendChild($params);
		$v = $doc->createTextNode("\n");
		$doc->documentElement->appendChild($v);
		$xml = $doc->saveXML();
		
		$filename = self::_getFilename($this->_id);
		
		return v()->filesystem()->file()->write($filename,$xml);		
	}
	
	protected static function _getFilename($refId)
	{
		$refId = self::clean($refId);
		$path = v()->getVarPath('vwp').DS.'ref';
		$ext = v()->filesystem()->path()->clean($refId) . '.xml';
	    
		$result = $path.DS.$ext;				
		return $result;	   
	}
	
	public static function exists($refId) 
	{
		$refId = self::clean($refId);
		if (isset(self::$_references[$refId])) {
			return true;
		}
		return v()->filesystem()->file()->exists(self::_getFilename($refId));
	}
	
	
	public static function &create($refId) 
	{		
		if (self::exists($refId)) {
		    $ret = VWP::raiseWarning('Reference ID in use!',__CLASS__,null,false);	
		} else {
			self::$_references[$refId] = new VWidgetReference($refId);
			$ret =& self::$_references[$refId];
		}
		return $ret;		
	}
	
	public static function &load($refId)
	{
	
	   if (isset(self::$_references[$refId])) {
	       return self::$_references[$refId];
      }
	
		$refId = self::clean($refId);
		if (!self::exists($refId)) {
			$e = VWP::raiseWarning('Widget not found',__CLASS__,null,false);			
			return $e; 
		}

		$xml = v()->filesystem()->file()->read(self::_getFilename($refId));
		
		$doc = new DOMDocument;
		
		VWP::noWarn(true);
		$ok = $doc->loadXML($xml);
		VWP::noWarn(false);
		
		if(!$ok) {
            $e = VWP::raiseWarning('Reference file is corrupt!',__CLASS__,null,false);            
            return $e;		
		}

		$list = $doc->getElementsByTagNameNS(self::XMLNS,'widget_reference');
		
		if ($list->length < 1) {
			// compat?			
			
			$list = $doc->getElementsByTagName('ref');
			if ($list->length > 0) {
                // upgrade!
                $params = new VWidgetParams;
                $vlist = $list->item(0)->childNodes;
  
                for($i = 0; $i < $vlist->length; $i++) {
                    $v = $vlist->item($i);   
                    $params->set($v->nodeName,$v->nodeValue);   
                }

                self::$_references[$refId] = new VWidgetReference($refId);
                $ret =&  self::$_references[$refId];
                $ret->setParams($params);
                $ret->save();
			} else {
				$ret = VWP::raiseWarning('Reference file is corrupt!',__CLASS__,null,false);                
			}
		} else {
		    $ref = $list->item(0);
		    self::$_references[$refId] = new VWidgetReference($refId);
		    $ret =  self::$_references[$refId];

		    $len = $ref->childNodes->length;
		    $have_widget = false;
		    $have_params = false;
		    $widgetId = null;
		    $params = null;
		    
		    for($i=0;$i < $len;$i++) {
		    	$item = $ref->childNodes->item($i);
		    	if ($item->nodeType == XML_ELEMENT_NODE) {
		    		switch($item->localName) {
		    			case "widget":
		    				if (!$have_widget) {
		    				    $have_widget = true;
		    				    $widgetId = $item->nodeValue; 
		    				}
		    				break;
		    			case "params":
		    				if ($have_widget && !$have_params) {
		    					$have_params = true;
		    					$params = VWidgetParams::loadParams($widgetId);		    					
		    				    $paraml = $item->getElementsByTagNameNS(self::XMLNS,'param');
		    				    $l2 = $paraml->length;
		    				    for($p=0;$p < $l2;$p++) {
		    					   $pn = $paraml->item($p);
		    					   $name = $pn->getAttribute('name');
		    					   $value = $pn->nodeValue;
		    					   $params->set($name,$value);		    					   
		    				    }
		    				}
		    				break;
		    			default:
		    				break;
		    		}
		    	}
		    }
		    
		    $ret->widgetId = $widgetId;
		    $ret->setParams($params);		    
		}				
		
		return $ret;						
	}


	function __construct($refId) 
	{
		$refId = self::clean($refId);
	    $this->_id = $refId;	
	}
	
	// end class VWidgetReference
}
