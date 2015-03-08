<?php

/**
 * VWP - DBI Query Datasources Type
 *  
 * This class provides the Datasources Type        
 * 
 * @package VWP
 * @subpackage Libraries.DBI.Types.Query  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

/**
 * VWP - DBI Query Datasources Type
 *  
 * This class provides the Datasources Type        
 * 
 * @package VWP
 * @subpackage Libraries.DBI.Types.Query  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

class VDBIQueryType_Datasources extends VObject 
{ 

	/**
	 * Query
	 * 
	 * @var VDBI_Query Query
	 * @access private
	 */
	
	protected $query;
	
	/**
	 * Data
	 * 
	 * @var array $_data Data
	 * @access private
	 */
	
	protected $_data;
	
	/**
	 * Runtime Form Settings
	 * 
	 * @var array Form Settings
	 * @access public
	 */
	
	protected $runtime_form;
	
	/**
	 * Helper
	 * 
	 * @var VDBI_QueryHelper $_helper Query Helper
	 * @access private
	 */
	
	protected $_helper;
	
	/**
	 * Get Database Alias By Index
	 * 
	 * @param integer $index Index
	 * @return string|object Alias on success, error or warning otherwise
	 * @access public
	 */
	
	function getDatabaseAliasByIndex($index) 
	{
		if ($index < 0) {
			return null;
		}
		
	    $dataSourcesNode = $this->_helper->_getCoreNode('datasources');
	    if (VWP::isWarning($dataSourcesNode)) {
            return $dataSourcesNode;
        }

        $databases = $dataSourcesNode->getElementsByTagNameNS(VDBI_Query::NS_QUERY_1_0,'database');
        
        if ($index < $databases->length) {
        	$node = $databases->item($index);
        	return (string)$node->getAttribute('alias');
        }
        return null;            
	}
	
	/**
	 * Get Database Type By Index
	 * 
	 * @param integer $index Index
	 * @return string|object Type on success, error or warning otherwise
	 * @access public
	 */	
	
	function getDatabaseTypeByIndex($index) 
	{
		if ($index < 0) {
			return null;
		}
		
	    $dataSourcesNode = $this->_helper->_getCoreNode('datasources');
	    if (VWP::isWarning($dataSourcesNode)) {
            return $dataSourcesNode;
        }

        $databases = $dataSourcesNode->getElementsByTagNameNS(VDBI_Query::NS_QUERY_1_0,'database');
        
        if ($index < $databases->length) {
        	$node = $databases->item($index);
        	return (string)$node->getAttribute('type');
        }
        return null;		
	}	
	
	/**
	 * Get Database Settings
	 * 
	 * @param string $databaseId Database Id
	 * @access public
	 */
	
	function getDatabaseSettings($databaseId) 
	{
		
	    $databaseNode = null;
	    $dataSourcesNode = $this->_helper->_getCoreNode('datasources');
	    if (VWP::isWarning($dataSourcesNode)) {
            return $dataSourcesNode;
        }

        $databases = $dataSourcesNode->getElementsByTagNameNS(VDBI_Query::NS_QUERY_1_0,'database');
	    $len = $databases->length;
	    
	    for($idx=0;$idx<$len;$idx++) {	    	
	    	if ($databaseId == (string)$databases->item($idx)->getAttribute('alias')) {
	    	    $databaseNode = $databases->item($idx);
	    	    $idx = $len;
	    	}
	    }
	    
	    if ($databaseNode === null) {
	    	return VWP::raiseWarning("Database '$databaseId' not found!",__CLASS__,null,false);
	    }
	    
	    $vars = $databaseNode->getElementsByTagNameNS(VDBI_Query::NS_QUERY_1_0,'var');
	    $data = array();
	    $len = $vars->length;
	    for($idx=0;$idx<$len;$idx++) {
	    	$node = $vars->item($idx);
	    	$name = (string)$node->getAttribute('name');
	    	$value = $this->_helper->_resolveValue($node,true,true);
	    	$data[$name] = $value;
	    }
	    
	    return $data;	    
	}
		
	/**
	 * Get runtime form field
	 *  
	 * Note: Returns null if field does not exist
	 * 
	 * @param integer $fieldIdx Field index
	 * @return array|object Form info array(label,name,type,nullable,options) on success, error or warning otherwise
	 * @access public
	 */
	
	function getRuntimeFormField($fieldIdx) 
	{

		if (!isset($this->runtime_form)) {
			
			$rtNode = $this->_getRuntimeFormNode();
			if (VWP::isWarning($rtNode)) {
				return $rtNode;
			}
			
			$this->runtime_form = array();
			
			if ($rtNode !== null) {
				$idx = 0;
				$fieldNode = $this->_getRuntimeFormFieldNode($idx);
				while($fieldNode !== null && !VWP::isWarning($fieldNode)) {
					
					$type = strtolower($fieldNode->nodeName);

					$label = $fieldNode->getAttribute('label');
				    $name = $fieldNode->getAttribute('name');					
					$nullable = $fieldNode->getAttribute('nullable') == 'true' ? true : false;					
					$options = array();
					
					$fieldInfo = array($label,$name,$type,$nullable,$options);
					
					switch($type) {
						case "time_field":
							$fieldInfo[4]['reference'] = $fieldNode->getAttribute('reference');
							$fieldInfo[4]['format'] = $fieldNode->getAttribute('format'); 
							$fieldInfo[4]['dateonly'] = $fieldNode->getAttribute('dateonly') == 'true' ? true : false; 
							break;
						case "select_field":
							$optNodes = $fieldNode->getElementsByTagNameNS(self::NS_QUERY_1_0,'option');
							$osz = $optNodes->length;
							for($oidx = 0; $oidx < $osz; $oidx++) {
							    $opt = array();
							    $node = $optNodes->item($oidx);
							    $opt['label'] = $node->nodeValue;
							    
							    $alist = array('type','value','value_field','label_field','src');
							    foreach($alist as $attr) {
                                    $val = $node->getAttribute($val);
                                    if (!empty($val)) {
                                    	$opt[$attr] = $val;
                                    }							    	
							    }
                                $fieldInfo[4][] = $opt;
							}							
							break;
						case "text_field":
							break;
						default:
							break;
					}
					
					$this->runtime_form[] = $fieldInfo;
					$idx++;
				    $fieldNode = $this->_getRuntimeFormFieldNode($idx);	
				}
			}
		}
		
		if ($fieldIdx < count($this->runtime_form)) {
			return $this->runtime_form[$fieldIdx]; 			
		}
		return null;
	}	
	
	/**
	 * Insert Runtime Form Field
	 * 
	 * @param string $label Field Label
	 * @param string $name Field ID
	 * @param string $type Field type
	 * @param boolean $nullable Nullable
	 * @param array $options Options
	 * @param integer $beforeIdx Before field index
	 * @return integer|object Field index on success, error or warning otherwise
	 * @access public
	 */
	
	function insertRuntimeFormField($label,$name,$type,$nullable,$options,$beforeIdx)
	{
		
		$check = $this->getRuntimeFormField($beforeIdx == null ? 0 : $beforeIdx);
		if (VWP::isWarning($check)) {
			return $check;
		}

		$rtNode = $this->_makeRuntimeFormNode();
		
		if (VWP::isWarning($rtNode)) {
			return $rtNode;
		}
						
		switch($type) {
			case "text_field":
				$newNode = $this->_doc->createElementNS(self::NS_QUERY_1_0,'text_field');				
				break;
				
			case "select_field":				
				$newNode = $this->_doc->createElementNS(self::NS_QUERY_1_0,'select_field');				
				foreach($options as $opt) {
					
					$label = isset($opt['label']) ? $opt['label'] : '';					
					$optNode = $this->_doc->createElementNS(self::NS_QUERY_1_0,'option',XMLDocument::xmlentities($label));
					
					if ($opt['type'] == 'query') {
					    $optNode->setAttribute('type', 'query');

						if (isset($opt['src'])) {
					        $optNode->setAttribute('src',XMLDocument::xmlentities((string)$opt['src']));
					    }					    
					    
						if (isset($opt['value_field'])) {
					        $optNode->setAttribute('value_field',XMLDocument::xmlentities((string)$opt['value_field']));
					    }
					
					    if (isset($opt['label_field'])) {
					        $optNode->setAttribute('label_field',XMLDocument::xmlentities((string)$opt['label_field']));
					    }
					
					} else {
						$optNode->setAttribute('type', 'static');
					    
						if (isset($opt['value'])) {
					        $optNode->setAttribute('value',XMLDocument::xmlentities((string)$opt['value']));
					    }	
					}
					
					$newNode->appendChild($optNode);
				}
				break;
				
			case "time_field":
				$newNode = $this->_doc->createElementNS(self::NS_QUERY_1_0,'time_field');
				$reference = isset($options['reference']) ? $options['reference'] : 'local';				
				$format = isset($options['format']) ? $options['format'] : 'date';
				$newNode->setAttribute('dateonly',isset($options['dateonly']) && $options['dateonly'] ? 'true' : 'false');				
				$newNode->setAttribute('reference',XMLDocument::xmlentities($reference));
				$newNode->setAttribute('format',XMLDocument::xmlentities($format));				  
			    break;
			    				
			default:
				return VWP::raiseWarning("Invalid field type",__CLASS__,null,false);		
		}

		$newNode->setAttribute('name',XMLDocument::xmlentities($name));
		$newNode->setAttribute('label',XMLDocument::xmlentities($label));
		$newNode->setAttribute('nullable',$nullable ? 'true' : 'false');		
		
        $before = null;
        if ($beforeIdx === null) {
        	$newIdx = count($this->runtime_form);
        	$rtNode->appendChild($newNode);
        	$this->runtime_form[] = array($label,$name,$type,$nullable,$options);
        } else {
        	$before = $this->_getRuntimeFormFieldNode($beforeIdx);
        	if ($before === null) {
        	    $newIdx = count($this->runtime_form);
        	    $rtNode->appendChild($newNode);
        	    $this->runtime_form[] = array($label,$name,$type,$nullable,$options);        		
        	} else {
        		$newIdx = $beforeIdx;         		       		
        		$rtNode->insertBefore($newNode,$before);
                $nlist = $this->runtime_form;        
                $this->runtime_form = array();
                $sz = count($nlist);
                for($idx = 0; $idx < $sz + 1; $idx++) {
        	        if ($idx == $newIdx) {
        	            $this->runtime_form[$idx] = array($label,$name,$type,$nullable,$options); 	
        	        } elseif ($idx < $newIdx) {
                        $this->runtime_form[$idx] = $nlist[$idx]; 		
        	        } else {
        		        $this->runtime_form[$idx + 1] = $nlist[$idx];
        	        }
                }        		        		
        	}
        }
        return $newIdx;
	}
	
	/**
	 * Delete runtime form field
	 * 
	 * @param integer $fieldIdx Field index
	 * @return boolean|object True on success on success, error or warning otherwise
	 * @access public
	 */	

	function deleteRuntimeFormField($fieldIdx)
	{
	    $oldField = getRuntimeFormField($fieldIdx);
	    
	    if (VWP::isWarning($oldField)) {
	    	return $oldField;
	    }
	    
	    if ($oldField !== null) {
	    	$rtNode = $this->_getRuntimeFormNode();
	    	$fieldNode = $this->_getRuntimeFormFieldNode($fieldIdx);
	    	$rtNode->removeChild($fieldNode);	    	
	    	$nlist = $this->runtime_form;
	    	$this->runtime_form = array();
	    	$sz = count($nlist);
	    	for($idx = 0; $idx < $sz;$idx++) {
	    		if ($idx != $fieldIdx) {
	    			$this->runtime_form[] = $nlist[$idx];
	    		}
	    	}
	    }

	    return true;
	}
	
	/**
	 * Get DOM Runtime Form Node
	 * 
	 * @return DOMElement|object Runtime form Node on success, error or warning otherwise
	 * @access private
	 */
	
	protected function _getRuntimeFormNode() 
	{
	    $dataSourcesNode = $this->_getCoreNode('datasources');
	    if (VWP::isWarning($dataSourcesNode)) {
	    	return $dataSourcesNode;
	    }
	    
        $nodeList = $dataSourcesNode->getElementsByTagNameNS(self::NS_QUERY_1_0,'runtime_form');
	    if ($nodeList->length > 1) {
            return VWP::raiseWarning('Ambiguous configuration!',get_class($this),null,false);
        }
        
        if ($nodeList->length > 0) {
        	return $nodeList->item(0);
        }
        
        return null;
	}

	/**
	 * Get DOM Runtime Form Field Node
	 * 
	 * @param integer $fieldIdx Field index
	 * @return DOMElement|object Runtime form Node on success, error or warning otherwise
	 * @access private
	 */	
	
	protected function _getRuntimeFormFieldNode($fieldIdx) 
	{
		$rtNode = $this->_getRuntimeFormNode();
		if (VWP::isWarning($rtNode) || $rtNode === null) {
			return $rtNode;			
		}
		
		$sz = $rtNode->childNodes->length;
		
		$curIdx = 0;
		for($idx = 0; $idx < $sz; $idx++) {
			if ($rtNode->childNodes->item($idx)->nodeType == XML_ELEMENT_NODE) {
				if ($curIdx == $fieldIdx) {
					return $rtNode->childNodes->item($idx);
				}
		        $curIdx++;		
			}
		}
	    return null;
	}

	/**
	 * Make runtime form node
	 * 
	 * @return DOMElement|object DOM Element on success, error or warning otherwise
	 * @access public
	 */
	
    protected function _makeRuntimeFormNode() 
    {
    	$rtNode = $this->_getRuntimeFormNode();
    	if ($rtNode !== null) {
    		return $rtNode;
    	}
    	
    	$dataSourcesNode = $this->_getCoreNode('datasources');
    	if (VWP::isWarning($dataSourcesNode)) {
    		return $dataSourcesNode;
    	}
    	
    	$rtNode = $this->_doc->createElementNS(self::NS_QUERY_1_0,'runtime_form');
    	
    	$dataSourcesNode->appendChild($rtNode);
    	
    	return $rtNode;
    }	
	
    /**
     * Class Constructor
     * 
     * @param unknown_type $query
     * @access public
     */
    
    function __construct($query) 
    {
    	$this->query = $query;
    	$this->_helper =& $query->getHelper();
    }
    
    // end class VDBIQueryType_Datasources
}
