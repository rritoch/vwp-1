<?php

/**
 * Virtual Web Platform - HTTP Serializer
 *  
 * This class provides a HTTP Serializer
 *        
 * @package VWP
 * @subpackage Libraries.XML  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

/**
 * Virtual Web Platform - HTTP Serializer
 *  
 * This class provides a HTTP Serializer
 *        
 * @package VWP
 * @subpackage Libraries.XML  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

class VXMLHTTPSerializer extends VObject 
{

	/**
	 * Convert a value to a node
	 * 
	 * @param object $doc Document
	 * @param mixed $val Value
	 * @access public
	 */
	
    private function _value2Node($doc,$val) 
    {
        
        if (is_array($val)) {
            $node = $doc->createElement('array');
          
            foreach($val as $k=>$v) {
          
                $itemNode = $doc->createElement('item');
                $itemNode->appendChild($doc->createElement('key',XMLDocument::xmlentities($k)));
                $valNode = $doc->createElement('value');
                $valNode->appendChild(self::_value2Node($doc,$v));
                $itemNode->appendChild($valNode);
                $node->appendChild($itemNode);
            }
                              
        } else {
            $node = $doc->createElement('string',XMLDocument::xmlentities($val));          
        }
            
        return $node;
        
    }

    /**
     * Extract value from node
     * 
     * @param object $node Node
     * @return mixed Value
     * @access public
     */
    
    protected static function _node2value($node) 
    {
        
        $ret = null;
        
        $type = null;
        for($i=0; $i < $node->childNodes->length;$i++) {
            $c = $node->childNodes->item($i);
            
            if ($c->nodeType == XML_ELEMENT_NODE) {
                $type = $c->nodeName;
                $typeNode = $c;
                $i = $node->childNodes->length;
            }  
        }
                        
        switch($type) {
            case "string":
                $ret = $typeNode->nodeValue;
                break;
                
            case "boolean":
                $ret = trim($typeNode->nodeValue) == 'true' ? true: false;
                break; 
            case "array":
                $ret = array();
                for($i=0; $i < $typeNode->childNodes->length;$i++) {
                    $item = $typeNode->childNodes->item($i);

                    
                    if ($item->nodeName == 'item') {
                        $key = null;
                        $value = null;                    
                        for($cp=0; $cp < $item->childNodes->length;$cp++) {
                            $c = $item->childNodes->item($cp);
                                
                            if (($key == null) && ($c->nodeName == 'key')) {
                                $key = $c->nodeValue;
                            }
                            if ($c->nodeName == 'value') {
                                $value = self::_node2value($c);                                   
                            }
                        }    
                                                            
                        if (VWP::isWarning($value)) {
                            $ret = $value;
                            $i = $typeNode->childNodes->length;
                        } elseif ($key == null) {
                            $ret = VWP::raiseWarning('Array item is missing a key!','VXMLHTTPRequest',null,false);
                            $i = $typeNode->childNodes->length;
                        } else {
                            $ret[$key] = $value;
                        }
                    }  
                }                
                break;
            default:
                $ret = VWP::raiseWarning("Unknown Data Type '$type'",'VXMLHTTPRequest',null,false);
                break;
        }
        
        return $ret;
        
    }
    
    /**
     * Transform Request Arguments
     * 
     * @param object $xslDoc XSL Document
     * @access public
     */
    
    public static function transformRequestArgs($xslDoc) {
        $proc = new XSLTProcessor();
        $proc->importStylesheet($xslDoc);
        $xmlDoc = self::getRequest();
                
        
        $t = $proc->transformToXML($xmlDoc);
        
        $tdoc = new DomDocument;
        $tdoc->loadXML($t);
        return $tdoc;    
    }

    /**
     * Serialize Request Arguments
     * 
     * @param object $xslDoc XSL Document
     * @access public
     */
    
    public static function serializeRequestArgs($xslDoc) 
    {
        
        $tdoc = self::transformRequestArgs($xslDoc);
        
        $args = array();
        $arg_list = $tdoc->getElementsByTagName('arg');
        
        for($i=0;$i<$arg_list->length; $i++) {
            $node = $arg_list->item($i);
            $arg = self::_node2value($node);
            if (VWP::isWarning($arg)) {
                return $arg;
            }
            array_push($args,$arg);
        }
        
        return $args;
    }
    
    /**
     * Get Request
     * 
     * @return object Request Document
     * @access public
     */
    
    public static function getRequest() 
    {
    
        $doc = new DomDocument;
        $nl = "\n";
      
        $doc->loadXML('<' . '?xml version="1.0" ?' . '>'. $nl.'<xmlhttprequest></xmlhttprequest>');

        $getNode = $doc->createElement('get');
        $postNode = $doc->createElement('post');
        
        $user =& VUser::getCurrent();
        $shellob =& $user->getShell();
            
        $get = $shellob->getAll('get');
        $route = $shellob->getAll('route');      
        $get = array_merge($route,$get);
            
        $post = $shellob->getAll('post');
            
        foreach($get as $key=>$val) {
            $newNode = $doc->createElement('var');
            $newNode->setAttribute('name',$key);          
            $newNode->appendChild(self::_value2Node($doc,$val));
            $getNode->appendChild($newNode);
        }

        foreach($post as $key=>$val) {
            $newNode = $doc->createElement('var');
            $newNode->setAttribute('name',$key);      
            $newNode->appendChild(self::_value2Node($doc,$val));
            $postNode->appendChild($newNode);          
        }      
      
        $doc->documentElement->appendChild($getNode);
        $doc->documentElement->appendChild($postNode);      
        return $doc;                              
    }

    // End VXMLHTTPRequest
}
