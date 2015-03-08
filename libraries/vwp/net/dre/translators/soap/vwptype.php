<?php

/**
 * Virtual Web Platform - VWP Type SOAP Translator
 *  
 * This file provides the default VWP type translator
 * 
 * @package VWP
 * @subpackage Libraries.Networking.DRE  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License 
 */

/**
 * Require mapitem type support
 */

VWP::RequireLibrary('vwp.net.dre.translators.soap.mapitem');

/**
 * Virtual Web Platform - VWP Type SOAP Translator
 *  
 * This class provides the default VWP type translator   
 * 
 * @package VWP
 * @subpackage Libraries.Networking.DRE  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License   
 */
 
class VSOAPVWPTypeTranslator extends VSOAPTranslator 
{

	/**	 
	 * Array item element
	 * 
	 * @var string $_arrayItemID Array item ID
	 * @access public
	 */
	
    public $_arrayItemID = 'keyval';
    
	/**	 
	 * VWP Security Namespace
	 * 
	 * @var string $_secNS VWP Security Namespace
	 * @access public
	 */
        
    public $_secNS = 'http://standards.vnetpublishing.com/schemas/soap/2010/102/vwp-soap-security';

	/**	 
	 * VWP Data Type Namespace
	 * 
	 * @var string $_dataNS VWP Data Type Namespace	 
	 * @access public
	 */
        
    public $_dataNS = 'http://standards.vnetpublishing.com/schemas/soap/2010/102/vwp-soap-types';
    
    /**	 
	 * Schema Namespace
	 * 
	 * @var string $_schemaNS Schema namespace
	 * @access public
	 */
    
    public $_schemaNS = 'http://www.w3.org/2001/XMLSchema';

	/**	 
	 * Soap v1.0 namespace
	 * 
	 * @var string $_soapNS Soap namespace
	 * @access public
	 */
        
    public $_soapNS = "http://schemas.xmlsoap.org/soap/envelope";   
    
	/**	 
	 * Soap v1.1 namespace
	 * 
	 * @var string $_soapNS2 Soap namespace
	 * @access public
	 */
    
    public $_soapNS2 = "http://schemas.xmlsoap.org/soap/envelope/";

	/**	 
	 * Header error buffer
	 * 
	 * @var mixed $_header_error Header error
	 * @access public
	 */
    
    public $_header_error = null;
   
    /**     
     * Convert Object to Array
     * 
     * @param object $ob Object
     * @return array Object data
     * @access public
     */
    
    function ob2array($ob) 
    {
        if (is_array($ob)) {
   
            $keys = array_keys($ob);
            if (
                (count($keys) == 1) && 
                ($keys[0] == $this->_arrayItemID) && 
                (is_array($ob[$this->_arrayItemID]))
               ) {
                $data = $ob[$this->_arrayItemID];
                $ob = array();
                foreach($data as $o) {
                    $item = get_object_vars($o);
                    $key = $item["key"];
                    $val = $item["value"];
                    $ob[$key] = $this->ob2array($val);
                }    
            } else {
                foreach($ob as $key=>$val) {
                    $ob[$key] = $this->ob2array($val);
                }
            } 
            return $ob; 
        }
        
        if (is_object($ob)) {
            $vars = get_object_vars($ob);
            $ob = $this->ob2array($vars);    
        } 
        return $ob; 
    } 

    /**     
     * Decode string
     * 
     * @param string $data String value
     * @access public
     */
    
    function decodeString($data) 
    {
    	$data = (string)$data;
        return $data; 
    }

    /**
     * Decode array
     * 
     * @param object $data Encoded array
     * @return array Decoded array
     * @access public
     */
    
    function decodeArray($data) 
    {
  
        if (!is_object($data)) {
            return $data; // Error!
        }
  
        if (!isset($data->item)) {
            return array();
        }
             
        if (is_array($data->item)) {
            return $data->item;
        }    
        return array($data->item);  
    }
 
    /**
     * Decode map type
     * 
     * @param object $map Map object
     * @return array Decoded map
     */
    
    function decodeMap($map) 
    {
  
        if (!is_object($map)) {
            return $map; // Error!
        }
    
        $ret = array();
        $items = $map->mapitem;
  
        if (is_object($items)) {
            $item = $items;
            if (isset($item->key)) {
                $k = $item->key;
            } else {
                return array('error','decodeMap','INV_DATA');
            }
            $v = isset($item->value) ? $item->value : null;
            $ret[$k] = $v;
        } elseif (is_array($items)) {
            foreach($items as $item) {
                if (isset($item->key)) {
                    $k = $item->key;
                } else {
                    return array('error','decodeMap','INV_DATA');
                }
                $v = isset($item->value) ? $item->value : null;
                $ret[$k] = $v;   
            }  
        }
        return $ret;
    }
 
    /**
     * Decode data table
     * 
     * @param object $table Table object
     * @return array Data table
     * @access public
     */
    
    function decodeTable($table) 
    {
        $ret = array();
        $tbl = $table->DataTableRow;
  
        // Deal with known bug... arrays with one item get collapsed
        if (is_object($tbl)) {
            if (isset($tbl->mapitem)) {
                $ret[] = $this->decodeMap($tbl);
            } else {
                // ERRR!!!!
                return array('error');
            }   
        } elseif (is_array($tbl)) {
            foreach($tbl as $map) {
                $ret[] = $this->decodeMap($map);
            }  
        } else {
            // ERRR!!!!
            return array('error');
        }
        return $ret;
    }

    /**
     * Encode a SOAP parameter
     * 
     * @param mixed $value Parameter value
     * @param string $type Parameter type
     * @param string $typeNS Parameter type Namespace
     * @param string $name Paramter Name
     * @param string $targetNamespace Target Namespace
     * @return object Soap encoded object
     * @access public
     */
                  
    public static function encodeParam($value,$type, $typeNamespace, $name, $targetNamespace) 
    {
  
        $param = null;
        switch($type) {
            case "string":
                $param =  new SoapVar($value,XSD_STRING,null,null,$name,$targetNamespace);   
                break;  
            case "array":        
                $items = array();
                foreach($value as $item) {
                    $items[] = new SoapVar($item,XSD_STRING,null,null,'item',$typeNamespace);
                }
                $param =  new SoapVar($items,SOAP_ENC_ARRAY,'Array',$typeNamespace,$name,$targetNamespace);   
                break;
            case "map":
                // Must use BUGGY PHP encoding to avoid server 500 error!
    
                if ($value === null) {
     
                    $items = new stdClass;
                    //$items->mapitem = null;      
                    // $param =  new SoapVar($items,SOAP_ENC_OBJECT,null,null,$name,$targetNamespace);
                    $param =  new SoapVar($items,SOAP_ENC_OBJECT,'Mapping',$typeNamespace,$name,$targetNamespace);
        
                } else if (count(array_keys($value)) == 1) {
                    $tmp = array_keys($value);
                    $k = $tmp[0];
                    $v = $value[$k];
                    $k = new SoapVar($k,XSD_STRING,null,null,'key',$typeNamespace);
                    $v = new SoapVar($v,XSD_STRING,null,null,'value',$typeNamespace);
                    $item = new VSOAPVWPType_mapitemtype($k,$v);     
                    $items = new stdClass;
                    $items->mapitem = new SoapVar($item,SOAP_ENC_OBJECT,null,null,'mapitem',$typeNamespace);      
                    $param =  new SoapVar($items,SOAP_ENC_OBJECT,null,null,$name,$targetNamespace);     
                } else {    
                    $items = array();
                    foreach($value as $k=>$v) {
                        $k = new SoapVar((string)$k,XSD_STRING,null,null,'key',$typeNamespace);
                        $v = new SoapVar((string)$v,XSD_STRING,null,null,'value',$typeNamespace);      
                        $item = new VSOAPVWPType_mapitemtype($k,$v);
                        $items[] = new SoapVar($item,SOAP_ENC_OBJECT,null,null,'mapitem',$typeNamespace);    
                    }    
                    $param =  new SoapVar($items,SOAP_ENC_ARRAY,'Mapping',$typeNamespace,$name,$targetNamespace);
                }
                break;
            case "table":
                $rows = array();
                foreach($value as $ent) {
                    $items = array();
                    foreach($ent as $k=>$v) {
                        $k = new SoapVar((string)$k,XSD_STRING,null,null,'key',$typeNamespace);
                        $v = new SoapVar((string)$v,XSD_STRING,null,null,'value',$typeNamespace);      
                        $item = new VSOAPVWPType_mapitemtype($k,$v);
                        $items[] = new SoapVar($item,SOAP_ENC_OBJECT,'mapitemtype',$typeNamespace,'mapitem',$typeNamespace);    
                    }    
                    $rows[] =  new SoapVar($items,SOAP_ENC_ARRAY,'Mapping',$typeNamespace,'DataTableRow',$typeNamespace);                
                }

                $param =  new SoapVar($rows,SOAP_ENC_ARRAY,'DataTable',$typeNamespace,$name,$targetNamespace);   
                break;   
            default:
               $param = array($name=>$value);
        }
        return $param;
    }
 
    /**
     * Create a SOAP message
     * 
     * @param string $method Method to call
     * @param array $encparams Encoded parameters
     * @param string $targetNS Target Namespace
     * @return object Soap encoded object
     * @access public
     */
 
    public static function bindParams($method,$encparams,$targetNS) 
    {
  
        $args = new stdClass;
        if (count(array_keys($encparams)) > 0) {  
            foreach($encparams as $key=>$val) {  
                $args->$key = $val;
            }
        } else {           
            $args = new SoapVar($args,SOAP_ENC_OBJECT,'Void',$targetNS);
        }
        //$cmd = new SoapVar($args,SOAP_ENC_OBJECT,$method,$targetNS,$method,$targetNS);
        $cmd = new SoapVar($args,SOAP_ENC_OBJECT,null,null,$method,$targetNS);
        return $cmd;
    }
 
    /**
     * Encode Request
     * 
     * @param string $method Method
     * @param mixed $params Parameters
     * @param string $targetNS Target namespace
     * @return object Soap encoded request
     * @access public
     */
    
    function encodeRequest($method,$params, $targetNS) 
    {
        $encparams = array();     
        foreach($params as $param) {
            $encparams[$param["name"]] = self::encodeParam($param["value"],$param["type"],$this->_dataNS,$param["name"],$targetNS);  
        }  
        $args = self::bindParams($method,$encparams,$targetNS);
        return $args;
    }
 
    /**
     * Decode soap request
     * 
     * @param string $method Method
     * @param mixed $ob Request data
     * @param string $rtype Data type
     * @param object $service Service object
     * @param object $server Server object
     * @return mixed decoded request
     * @access public
     */
    
    function decodeRequest($method, $ob,$rtype,$service, $server) 
    { 
  
        $this->_decodeRequestHeaders($method,$service,$server);
    
        if (is_object($ob)) {
            $rqst = get_object_vars($ob); 
        } else {
            $rqst = array(); // Error Land!
        }
      
        $args = array();
  
        foreach($rqst as $argName=>$data) {
            if (is_object($data)) {
                if (isset($data->enc_stype)) {
                    switch($data->enc_stype) {
                        case "String":
                            $data = $this->decodeString($data->enc_value);
                            break;
                        case "Array":
                            $data = $this->decodeArray($data->enc_value);
                            break;
                        case "Mapping":
                            $data = $this->decodeMap($data->enc_value);
                            break;
                        case "DataTable":
                            $data = $this->decodeTable($data->enc_value);
                            break;
                        default:
                            // error!!! Unknown type
                    }           
                } else {
                    if (isset($data->item)) {
                        $data = $this->decodeArray($data);
                    } elseif (isset($data->mapitem)) {
                        $data = $this->decodeMap($data);
                    } elseif (isset($data->DataTableRow)) {
                        $data = $this->decodeTable($data);
                    }
                }
            }   
            $args[] = $data;    
        }
        return $args;
    }

    /**
     * Encode Boolean Type
     * 
     * @param boolean $value Value
     * @param string $name Field name
     * @param string $targetNS Target Namespace
     * @return object Soap encoded boolean
     * @access public
     */
    
    function encodeBoolean($value,$name,$targetNS) 
    {
        $encval =  new SoapVar($value,XSD_BOOLEAN,'boolean','http://www.w3.org/2001/XMLSchema',$name,$targetNS);
        return $encval;
    }
 
    /**     
     * Encode string
     * 
     * @param string $value Value
     * @param string $name Field name
     * @param string $targetNS Target namespace
     * @return object Soap encoded string
     * @access public
     */
    
    function encodeString($value,$name,$targetNS) 
    {  
        $encval =  new SoapVar($value,XSD_STRING,'String',$this->_dataNS,$name,$targetNS);    
        return $encval;
    }

    /**
     * Encode array
     * 
     * @param array $value Value
     * @param string $name Field name
     * @param string $targetNS Target namespace
     * @return object Soap encoded array
     */
    
    function encodeArray($value,$name,$targetNS) 
    {
        $items = array();
        foreach($value as $item) {
            $items[] = new SoapVar($item,XSD_STRING,'string',$this->_schemaNS,'item',$this->_dataNS);
        }  
        $encval =  new SoapVar($items,SOAP_ENC_ARRAY,'Array',$this->_dataNS,$name,$targetNS);
        return $encval;
    }

    /**
     * Encode mapping
     * 
     * @param array $value Map data
     * @param string $name Field name
     * @param string $targetNS Target namespace
     * @return object Soap encoded mapping
     * @access public
     */
    
    function encodeMap($value,$name,$targetNS) 
    {
        // Must use BUGGY PHP encoding to avoid server 500 error!
    
        if (count(array_keys($value)) == 1) {
            $tmp = array_keys($value);
            $k = $tmp[0];
            $v = $value[$k];
            $item = new VSOAPVWPType_mapitemtype($k,$v);
     
            $items = new stdClass;
            $items->mapitem = new SoapVar($item,SOAP_ENC_OBJECT);
      
            $encval =  new SoapVar($items,SOAP_ENC_OBJECT,'Mapping',$this->_dataNS,$name,$targetNS);     
        } else {    
           $items = array();
           foreach($value as $k=>$v) {
               $k = new SoapVar((string)$k,XSD_STRING,'string',$this->_schemaNS,'key',$this->_dataNS);
               $v = new SoapVar((string)$v,XSD_STRING,'string',$this->_schemaNS,'value',$this->_dataNS);
               $item = new VSOAPVWPType_mapitemtype($k,$v);
               $items[] = new SoapVar($item,SOAP_ENC_OBJECT,'mapitem',$this->_dataNS,'mapitem',$this->_dataNS);    
           }    
           $encval =  new SoapVar($items,SOAP_ENC_ARRAY,'Mapping',$this->_dataNS,$name,$targetNS);
        }
        return $encval;
    }
  
    /**
     * Encode data table
     * 
     * @param array $value Data
     * @param string $name Field name
     * @param string $targetNS Target namespace
     * @return object Soap encoded data table
     * @access public
     */
    
    function encodeDataTable($value,$name,$targetNS) 
    {
  
        $rows = array();
        foreach($value as $ent) {
            $items = array();
            foreach($ent as $k=>$v) {
                $k = new SoapVar((string)$k,XSD_STRING,'string',$this->_schemaNS,'key',$this->_dataNS);
                $v = new SoapVar((string)$v,XSD_STRING,'string',$this->_schemaNS,'value',$this->_dataNS);
                $item = new VSOAPVWPType_mapitemtype($k,$v);
                $items[] = new SoapVar($item,SOAP_ENC_OBJECT,'mapitem',$this->_dataNS,'mapitem',$this->_dataNS);    
            }    
            $rows[] =  new SoapVar($items,SOAP_ENC_ARRAY,'Mapping',$this->_dataNS,'DataTableRow',$this->_dataNS);           
        }

        $param =  new SoapVar($rows,SOAP_ENC_ARRAY,'DataTable',$this->_dataNS,$name,$targetNS);   
 
        return $param; 
    }
 
    /**
     * Decode a SOAP response
     * 
     * @param string $method Method
     * @param mixed $data Raw SOAP response
     * @return mixed Decoded SOAP Response
     * @access public    
     */   
 
    function decodeResponse($method, $data) 
    {
        //return VWP::raiseWarning(var_export($data,true),'',null,false);
  
        if (!is_object($data)) {
            return $data;
        }
        if (isset($data->DataTableRow)) {
            // Data Table
            $ret = array();
            if (is_object($data->DataTableRow)) { // handle buggy php support
                if (is_array($data->DataTableRow->mapitem)) {
                    $info = array();   
                    foreach($data->DataTableRow->mapitem as $v) {
                        if (isset($v->key)) {
                            $val = null;
                            if (isset($v->value)) {
                                $val = $v->value;
                            }      
                            $info[$v->key] = $val;
                        }     
                    }
                } else {    
                    $info = array( $data->DataTableRow->mapitem->key => $data->DataTableRow->mapitem->value);
                }
                $ret[] = $info;    
            } else {
                foreach($data->DataTableRow as $rowOB) {
                    $info = array();
                    if (is_array($rowOB->mapitem)) {
                        foreach($rowOB->mapitem as $v) {
                            if (isset($v->key)) {
                                $val = null;
                                if (isset($v->value)) {
                                    $val = $v->value;
                                }      
                                $info[$v->key] = $val;
                            }     
                        }            
                    } else {
                        $info[$rowOB->mapitem->key] = $rowOB->mapitem->value; 
                    }
                    $ret[] = $info;
                }
            }
            return $ret;
        }
  
        // Mapping
  
        if (isset($data->mapitem)) {
            $info = array();
            foreach($data->mapitem as $v) {
                if (isset($v->key)) {
                    $val = null;
                    if (isset($v->value)) {
                        $val = $v->value;
                    }      
                    $info[$v->key] = $val;
                }     
            }
            return $info;  
        }
  
        // Array
        if (isset($data->item)) {
     
            $info = array();
            if (is_array($data->item)) {
                foreach($data->item as $v) {
                    $info[] = $v;
                }
            } else {
                $info[] = $data->item;
            }
            return $info;  
        }
  
        return array(); 
    }
 
    /**
     * Encode soap response
     * 
     * @param string $method Method
     * @param mixed $ob Data
     * @param string $rtype Data type
     * @param object $service Service object
     * @param object $server Server object
     * @return mixed Encoded response
     * @access public
     */
    
    function encodeResponse($method, $ob,$rtype,$service, $server) 
    {

        if (VWP::isWarning($this->_header_error)) {
            $ob = $this->_header_error;  
        }

        if (VWP::isWarning($ob)) {
            $server->fault($ob->errno,$ob->errmsg,null,'Error in: '. $ob->errsystem);
        }
    
        $responseName = $method.'Response';
    
        switch($rtype) {
            case "table":
                $ob = $this->encodeDataTable($ob,$responseName,$service->_tns);
                break;
            case "map":
                $ob = $this->encodeMap($ob,$responseName,$service->_tns);
                break;
            case "array":
                $ob = $this->encodeArray($ob,$responseName,$service->_tns);
                break;
            case "string":
                $ob = $this->encodeString($ob,$responseName,$service->_tns);
                break;
            case "boolean":
                $ob = $this->encodeBoolean($ob,$responseName,$service->_tns);
                break;   
            default:
                break;
        }   
  
        $this->_encodeResponseHeaders($method,$service,$server);
  
        return $ob;
    }

    /**
     * Encode request headers
     * 
     * @param mixed $headers Request headers
     * @return array Request Headers
     * @access public
     */
    
    function encodeRequestHeaders($headers) 
    {
     
        $encHeaders = null;
     
        if (isset($headers["C_Id"])) {
           
            $tmp = new stdClass;
            $tmp->C_Id = $this->encodeParam($headers["C_Id"],
                       'map', $this->_dataNS, 'C_Id', $this->_secNS);

            $h1 = new SoapVar($tmp,SOAP_ENC_OBJECT,null,null,'SecurityInfoRequest',$this->_secNS);
            $h1 = new SoapHeader($this->_secNS, 'SecurityInfoRequest', $h1);  
            $encHeaders = array($h1);                               
        } 
 
        return $encHeaders;
    }
 
    /**
     * Decode Response Headers
     * 
     * @param string $responseXML response XML
     * @return array Response headers
     * @access public
     */
    
    function decodeResponseHeaders($responseXML) 
    {
 
        $doc = new DomDocument;
        $valid = $doc->loadXML($responseXML); 
   
        if (!$valid) {
            return VWP::raiseWarning('Invalid SOAP response!',get_class($this),null,false);
        }
      
        $soapNS = $this->_soapNS;
        $soapNS2 = $this->_soapNS2;
   
        $secNS = $this->_secNS;
        $typeNS = $this->_dataNS;
   
        $items = $doc->getElementsByTagNameNS($soapNS,'Header');
      
        if ($items->length < 1) {
            $items = $doc->getElementsByTagNameNS($soapNS2,'Header');   
        }
   
   
        if ($items->length < 1) {
            return VWP::raiseWarning('getSecurityResponseHeader: Missing header! : ' . htmlentities($lastResponse),get_class($this),null,false);
            return array(); // Missing security header!
        }
   
        $header = $items->item(0);
   
        $items = $header->getElementsByTagNameNS($secNS,'SecurityInfoResponse');

        if ($items->length < 1) {
            return VWP::raiseWarning('getSecurityResponseHeader: Missing Security header! : ' . htmlentities($lastResponse),get_class($this),null,false);
            return array(); // Missing security header!
        }
      
        $secHeader = $items->item(0);
   
        $secResponseHeader = array();
   
        // C_Id [map]
   
        $items = $secHeader->getElementsByTagNameNS($secNS,'C_Id');
        if ($items->length > 0) {
            $m = array();
            $items = $items->item(0)->getElementsByTagNameNS($typeNS,'mapitem');
            for($p=0;$p < $items->length; $p++) {
                $key = null;
                $val = null;     
                $k = $items->item($p)->getElementsByTagNameNS($typeNS,'key');
                $v = $items->item($p)->getElementsByTagNameNS($typeNS,'value');     
                if ($k->length > 0) {
                    $key = $k->item(0)->nodeValue;
                }     
                if ($v->length > 0) {
                    $val = $v->item(0)->nodeValue;
                }
                if ($key !== null) {
                    $m[$key] = $val;
                }          
            }
            $secResponseHeader['C_Id'] = $m;
        }
       
        // R_Id [string]

        $items = $secHeader->getElementsByTagNameNS($secNS,'R_Id');
        if ($items->length > 0) {
            $secResponseHeader['R_Id'] = $items->item(0)->nodeValue;   
        }

        // S_Id [map]

        $items = $secHeader->getElementsByTagNameNS($secNS,'S_Id');
        if ($items->length > 0) {
            $m = array();
    
            $items = $items->item(0)->getElementsByTagNameNS($typeNS,'mapitem');
            for($p=0;$p < $items->length; $p++) {
                $key = null;
                $val = null;     
                $k = $items->item($p)->getElementsByTagNameNS($typeNS,'key');
                $v = $items->item($p)->getElementsByTagNameNS($typeNS,'value');     
                if ($k->length > 0) {
                    $key = $k->item(0)->nodeValue;
                }     
                if ($v->length > 0) {
                    $val = $v->item(0)->nodeValue;
                }
                if ($key !== null) {
                    $m[$key] = $val;
                }          
            }
            $secResponseHeader['S_Id'] = $m;   
        }

        // UR_List [array]

        $items = $secHeader->getElementsByTagNameNS($secNS,'UR_List');
        if ($items->length > 0) {
            $items = $items->item(0)->getElementsByTagNameNS($typeNS,'item');
            $result = array();
            for($p=0;$p < $items->length;$p++) {
                $result[] = $items->item($p)->nodeValue;
            }
            $secResponseHeader['UR_List'] = $result;    
        }

        // URM_List [array]

        $items = $secHeader->getElementsByTagNameNS($secNS,'URM_List');   
        if ($items->length > 0) {
            $items = $items->item(0)->getElementsByTagNameNS($typeNS,'item');
            $result = array();
            for($p=0;$p < $items->length;$p++) {
                $result[] = $items->item($p)->nodeValue;
            }
            $secResponseHeader['URM_List'] = $result;   
        }
   
        // Authorize_Client_URL [string]

        $items = $secHeader->getElementsByTagNameNS($secNS,'Authorize_Client_URL');
        if ($items->length > 0) {
            $secResponseHeader['Authorize_Client_URL'] = $items->item(0)->nodeValue;   
        }
   
        return $secResponseHeader;
    }   

    /**
     * Get security request header
     *      
     * @param object $doc Request document
     * @return array request header
     * @access public
     */
    
    function _getSecurityRequestHeader($doc) 
    {
  
        $soapNS = 'http://www.w3.org/2003/05/soap-envelope';
        $soapNS2 = 'http://schemas.xmlsoap.org/soap/envelope/';

        $secNS = 'http://standards.vnetpublishing.com/schemas/soap/2010/102/vwp-soap-security';
        $typeNS = 'http://standards.vnetpublishing.com/schemas/soap/2010/102/vwp-soap-types';
  
        $items = $doc->getElementsByTagNameNS($soapNS,'Header');
 
        if ($items->length < 1) { 
            $items = $doc->getElementsByTagNameNS($soapNS2,'Header');  
        }
   
        if ($items->length < 1) { 
            return array(); // Missing header! Bad Client!  
        }
  
        $header = $items->item(0);
   
        $items = $header->getElementsByTagNameNS($secNS,'SecurityInfoRequest');

        if ($items->length < 1) {    
             return array(); // Missing security header!
        }
      
        $secHeader = $items->item(0);
   
        $secResponseHeader = array();
   
        // C_Id [map]
   
        $items = $secHeader->getElementsByTagNameNS($secNS,'C_Id');
        if ($items->length > 0) {
            $m = array();
    
            $items = $items->item(0)->getElementsByTagNameNS($typeNS,'mapitem');
            for($p=0;$p < $items->length; $p++) {
                $key = null;
                $val = null;     
                $k = $items->item($p)->getElementsByTagNameNS($typeNS,'key');
                $v = $items->item($p)->getElementsByTagNameNS($typeNS,'value');     
                if ($k->length > 0) {
                    $key = $k->item(0)->nodeValue;
                }     
                if ($v->length > 0) {
                    $val = $v->item(0)->nodeValue;
                }
                if ($key !== null) {
                    $m[$key] = $val;
                }          
            }
            $secResponseHeader['C_Id'] = $m;
        }
 
        return $secResponseHeader;
    }
 
    /**
     * Decode request headers
     * 
     * @param string $method Method
     * @param object $service Service object
     * @param object $server Server Object
     * @access public
     */
    
    function _decodeRequestHeaders($method,$service,$server) 
    {
 
        $secHeader = array();
  
        $rawRequest =  VRawPost::getData();
  
        if (!empty($rawRequest)) {      
            $doc = new DomDocument;  
            $r = $doc->loadXML($rawRequest);
            if ($r) {
                $secHeader = $this->_getSecurityRequestHeader($doc);
            } 
        }
      
        if (isset($secHeader['C_Id'])) {
            // Login
            $credentials = array('C_Id'=>$secHeader['C_Id']);
        } else {
            // Fake Logout!
            $credentials = null;
        }

        $suid_mode = VUser::suid($credentials);
  
        //$u =& VUser::getCurrent();
        //$u->setError('debug:_decodeRequestHeaders:'.var_export($rawRequest,true));      
        if ($suid_mode && isset($secHeader['C_Id'])) {  
            $service->setClient($secHeader['C_Id']);
        } else {
            $service->setClient(null);
        }      
    }
 
    /**
     * Encode response headers
     * 
     * @param string $method Method
     * @param object $service Service object
     * @param object $server Server object
     * @access public
     */
    
    function _encodeResponseHeaders($method,$service,$server) 
    {
  
  
        $secinfo = $service->getSecurityHeaderInfo($method);
        if ($secinfo === null) {
            return null;
        }
     
        $secHeaderName = 'SecurityInfoResponse';
        $secHeader = new stdClass;
  
        if (isset($secinfo["C_Id"])) {
            $secHeader->C_Id = $this->encodeMap($secinfo["C_Id"],'C_Id',$this->_secNS);
        }
  
        if (isset($secinfo["R_Id"])) {
            $secHeader->R_Id = $this->encodeString($secinfo["R_Id"],'R_Id',$this->_secNS);  
        }

        if (isset($secinfo["S_Id"])) {
            $secHeader->S_Id = $this->encodeMap($secinfo["S_Id"],'S_Id',$this->_secNS);  
        }
   
        if (isset($secinfo["UR_List"])) {
            $secHeader->UR_List = $this->encodeArray($secinfo["UR_List"],'UR_List',$this->_secNS);  
        }
  
        if (isset($secinfo["URM_List"])) {
            $secHeader->URM_List = $this->encodeArray($secinfo["URM_List"],'URM_List',$this->_secNS);  
        }
  
        if (isset($secinfo["Authorize_Client_URL"])) {
            $secHeader->Authorize_Client_URL = $this->encodeString($secinfo["Authorize_Client_URL"],'Authorize_Client_URL',$this->_secNS);  
        }
    
        $secHeaderData = new SoapVar($secHeader,SOAP_ENC_OBJECT,$secHeaderName,$this->_secNS,$secHeaderName,$this->_secNS);  
        $secHeaderEnc = new SoapHeader($this->_secNS, $secHeaderName, $secHeaderData);  
        $server->addSoapHeader($secHeaderEnc); 
    }
    
    // end class VSOAPVWPTypeTranslator
} 
