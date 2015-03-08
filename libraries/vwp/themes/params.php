<?php

/**
 * Theme Parameters  
 * 
 * This file provides support for theme parameters
 *    
 * @package    VWP
 * @subpackage Libraries.Themes
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

/**
 * Requires file support
 */

VWP::RequireLibrary('vwp.filesystem.file');

/**
 * Requires folder support
 */

VWP::RequireLibrary('vwp.filesystem.folder');

/**
 * Theme Parameters  
 * 
 * This class provides support for theme parameters
 *    
 * @package    VWP
 * @subpackage Libraries.Themes
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

class VThemeParams extends VObject 
{
 
    /**
     * @var array $_paths Paths indexed by type
     * @access private  
     */
     
    var $_paths = array();
 

    /**
     * Theme Id
     * 
     * @var string $_id Theme Id
     * @access public      
     */
     
    var $_id = null;

    /**
     * Theme Type
     * 
     * @var string $_theme_type Theme type
     */
 
    var $_theme_type = null;
 
    /**
     * Get Instance of Theme Parameters Object
     * 
     * @param string $themeType Theme Type
     * @param string $themeId Theme ID
     * @return VThemeParams Theme parameters
     * @access public
     */
 
    static function &getInstance($themeType,$themeId) 
    {
    	static $themeParams = array();
    	
    	$vfile =& v()->filesystem()->file();
    	
    	if (!isset($themeParams[$themeType])) {
    		$themeParams[$themeType] = array();
    	}
    	
    	if (!isset($themeParams[$themeType][$themeId])) {
    		
    		$filename = VPATH_BASE.DS.'themes'.DS.$themeType.DS.$themeId.DS.'params.php';
    		$className = ucfirst($themeType).'_ThemeParams_'.ucfirst($themeId);
    		if ($vfile->exists($filename)) {
    			require_once($filename);
    			if (class_exists($className)) {
    			    $themeParams[$themeType][$themeId] = new $className;
    			    $themeParams[$themeType][$themeId]->loadData();	
    			}
    		} else {
    			$themeParams[$themeType][$themeId] = VWP::raiseWarning('No custom theme parameters!','VThemeParams',null,false);
    		}
    	}
    	
    	return $themeParams[$themeType][$themeId];
    }
    
    /**
     * Load theme parameter data
     *      
     * @return true|object True on success, error or warning otherwise
     * @access public      
     */
     
    function loadData() 
    {
    	$themeId = $this->getThemeId();
    	$themeType = $this->getThemeType();
    	
        $doc = new DomDocument;
        $filename = VWP::getVarPath('vwp').DS.'themes'.DS.'params'.DS.$themeType.DS.$themeId.'.xml';
        $vfile =& v()->filesystem()->file();
        if (!$vfile->exists($filename)) {
        	return VWP::raiseWarning('No parameters defined!',__CLASS__,null,false);
        }
        
        $result = $vfile->read($filename);
        if (VWP::isWarning($result)) {
          return $result;
        }
  
        VWP::noWarn();
        $result = @$doc->loadXML($result);
        VWP::noWarn(false);
        if (!$result) {
            return VWP::raiseWarning("Invalid theme parameters document.",get_class($this),null,false);
        }
  
        $vlist = $doc->documentElement->childNodes;
  
        for($i = 0; $i < $vlist->length; $i++) {
            $v = $vlist->item($i);   
            $this->set($v->nodeName,$v->nodeValue);   
        }
      
        return true;
    }
 
 
    /**
     * Save theme parameter data
     * 
     * @param string $ref Reference ID
     * @return true|object True on success, error or warning otherwise
     * @access public      
     */
     
    function saveData() 
    {
 
        VWP::RequireLibrary('vwp.documents.xml');
        
    	$themeId = $this->getThemeId();
    	$themeType = $this->getThemeType();  

    	// Setup File        
        $filename = VWP::getVarPath('vwp').DS.'themes'.DS.'params'.DS.$themeType.DS.$themeId.'.xml';
        
        $vfile =& v()->filesystem()->file();
        $vfolder =& v()->filesystem()->folder();
        
        if (!$vfolder->exists(dirname($filename))) {
            $vfolder->create(dirname($filename));
        } 

        // Generate Document  
        $doc = new DomDocument;
        $data = '<' . '?xml version="1.0" encoding="utf-8" ?' . '>' . "\n" . "<ref></ref>";  
        $doc->loadXML($data);
        $vlist = $this->getProperties();
        foreach($vlist as $key=>$val) {
            if ($key !== "#text") {    
                $v = $doc->createElement($key,XMLDocument::xmlentities($val));       
                $doc->documentElement->appendChild($doc->createTextNode("\n "));
                $doc->documentElement->appendChild($v);
            }   
        }  
        $doc->documentElement->appendChild($doc->createTextNode("\n "));
        $data = $doc->saveXML();
        $result = $vfile->write($filename,$data);
        return true;
    }
 
 
    /**
     * Method to get the theme name
     *
     * The theme name by default parsed using the classname, or it can be set
     * by passing a $config['name'] in the class constructor
     *
     * @return string The name of the theme
     * @access public
     */
  
    function getThemeId() 
    {
        $themeId = $this->_id;

        if (empty( $themeId )) {
            $r = null;
            if (!preg_match('/ThemeParams_(.*)$/i', get_class($this), $r)) {
                VWP::raiseError (500, "VThemeParam::getName() : Cannot get or parse class name.");
            }
            $themeId = strtolower( $r[1] );
        }
        return $themeId;
    }


    /**
     * Method to get the theme type
     *
     * The model name by default parsed using the classname, or it can be set
     * by passing a $config['theme_type'] in the class constructor
     *
     * @access public
     * @return string The theme type
     */
	 
    function getThemeType() 
    {
        $themeType = $this->_theme_type;        
        if (empty( $themeType )) {
            $r = null;
            if (!preg_match('/^(.*)_ThemeParams_/i', get_class($this), $r)) {
                VWP::raiseError ("Cannot get or parse class name.",500, get_class($this) . '::getThemeType',500);
            }
            $themeType = strtolower( $r[1] );
        }
        return $themeType;
    }

   
    /**
     * Get parameter definitions
     *   
     * <pre>
     *   
     *  Note: This function should be overridden by child classes that need to define
     *        parameters.  
     *      
     *  Required Attributes:
     *   type : string Parameter type [string/select/boolean]
     *   label : string Field label        
     * </pre>
     *     
     * @return array Parameter definitions indexed by paramater ID
     * @access public      
     */   
 
    function getDefinitions() {
        return array();
    }
 
    /**
     * Add a search path
     * 
     * @param string $type Path type
     * @param string $path Path
     * @access public  
     */
           
    function addPath($type,$path) {
        if (!isset($this->_paths[$type])) {
            $this->_paths[$type] = array();
        }
        $this->_paths[$type][] = $path;
    }
 
    /**
     * Class constructor
     * 
     * @param array $config Configuration settings
     * @access public  
     */
         
    function __construct($config = array()) 
    {
        parent::__construct();
  
        if (isset($config["id"])) {
            $this->_id = $config["id"];
        }
 
        if (isset($config["theme_type"])) {
            $this->_theme_type = $config["theme_type"];
        }
    }
  
    // end class VThemeParams
}
 