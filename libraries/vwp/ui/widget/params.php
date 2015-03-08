<?php

/**
 * Widget Parameters  
 * 
 * This file provides support for widget parameters
 * 
 * @todo Make widget parameters DOM 3 compliant
 * @package    VWP
 * @subpackage Libraries.UI.Widget
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License
 */

/**
 * Require Filesystem Support
 */

VWP::RequireLibrary('vwp.filesystem');


/**
 * Widget Parameters  
 * 
 * This class provides the base class for widget parameters
 *    
 * @package    VWP
 * @subpackage Libraries.UI.Widget
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License
 */

class VWidgetParams extends VObject 
{
 
    /**
     * @var array $_paths Paths indexed by type
     * @access private  
     */
     
    public $_paths = array();
 
    /** 
     * @param array $_models Model cache
     * @access private   
     */
     
    public $_models = array();
 
    /**
     * @var string $_default_model Default model
     * @access public
     */
        
    public $_default_model = null;

    /**
     * Widget Name
     * 
     * @var string $_name Widget name
     * @access public      
     */
     
    public $_name = null;

    /**
     * Parent Name
     * 
     * @var string $_parent_name Parent widget name
     * @access public      
     */
     
    public $_parent_name = null;
  
    /**
     * Widget title
     * 
     * @var string $title Widget title    
     * @access public  
     */
     
    public $title = null;
 
    /**
     * Load reference
     * 
     * @param string $ref Reference ID
     * @return true|object True on success, error or warning otherwise      
     */
     
    function loadRef($ref) 
    {
        $doc = new DomDocument;
        $filename = VWP::getVarPath('vwp').DS.'ref'.DS.$ref.'.xml';
        $vfile =& v()->filesystem()->file();
  
        if (!$vfile->exists($filename)) {  
            return VWP::raiseWarning("No resource defined.",__CLASS__,null,false);  
        }
        $result = $vfile->read($filename);
        if (VWP::isWarning($result)) {
            return $result;
        }
  
        VWP::noWarn();
        $result = @$doc->loadXML($result);
        VWP::noWarn(false);
        if (!$result) {
            return VWP::raiseWarning("Invalid refrence document.",get_class($this),null,false);
        }
  
        $vlist = $doc->documentElement->childNodes;
  
        for($i = 0; $i < $vlist->length; $i++) {
            $v = $vlist->item($i);   
            $this->set($v->nodeName,$v->nodeValue);   
        }      
        return true;
    }
  
    /**
     * Save reference
     * 
     * @param string $ref Reference ID
     * @return true|object True on success, error or warning otherwise
     * @access public      
     */
     
    function saveRef($ref) 
    {
 
        VWP::RequireLibrary('vwp.documents.xml');
  
        // Setup File
  
        $filename = VWP::getVarPath('vwp').DS.'ref'.DS.$ref.'.xml';
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
     * Method to get the widget name
     *
     * The model name by default parsed using the classname, or it can be set
     * by passing a $config['name'] in the class constructor
     *
     * @return string The name of the model
     * @access public
     */
  
    function getName() 
    {
        $name = $this->_name;

        if (empty( $name )) {
            $r = null;
            if (!preg_match('/WidgetParams((widget)*(.*(widget)?.*))$/i', get_class($this), $r)) {
                VWP::raiseError (500, "VWidget::getName() : Cannot get or parse class name.");
            }
            if (strpos($r[3], "widget")) {
                VWP::raiseWarning("Your classname contains the substring 'widget'. ".
                      "This causes problems when extracting the classname from the name of your objects widget. " .
                      "Avoid Object names with the substring 'widget'.",
                      get_class($this) . "::getName"                        
                );
            }
            $name = strtolower( $r[3] );
        }
        return $name;
    }


    /**
     * Method to get the view parent name
     *
     * The model name by default parsed using the classname, or it can be set
     * by passing a $config['parent_name'] in the class constructor
     *     
     * @return string The name of the model
     * @access public 
     */
	 
    function getParentName() 
    {
        $name = $this->_parent_name;
        if (empty( $name )) {
            $r = null;
            if (!preg_match('/^(.*)_WidgetParams_/i', get_class($this), $r)) {
                VWP::raiseError ("Cannot get or parse class name.",500, get_class($this) . '::getParentName',500);
            }
            $name = strtolower( $r[1] );
        }
        return $name;
    }

    /**
     * Method to load and return a model object.
     *     
     * @param string $name The name of the model.
     * @param string $prefix Optional model prefix.
     * @param array	$config Configuration array for the model. Optional.
     * @return mixed Model object on success, null error or warning otherwise
     * @access private 
     */

    function &_createModel( $name, $prefix = '', $config = array()) 
    {
        $result = null;

        // Clean the model name
        $modelName	 = preg_replace( '/[^A-Z0-9_\\.]/i', '', $name );
        $classPrefix = preg_replace( '/[^A-Z0-9_]/i', '', $prefix );
  
        $classFile = $modelName.'.php';
    
        if (isset($this->_paths["models"])) {
            $inc = $this->_paths["models"];       
            $file = v()->filesystem()->path()->find($inc,$classFile);
            $modelsPath = dirname($file);

            if (!isset($this->_models[$modelsPath])) {
                $this->_models[$modelsPath] = array();
            }
   
            if (isset($this->_models[$modelsPath][$modelName])) {
                return $this->_models[$modelsPath][$modelName];
            }      
   
            if (file_exists($modelsPath.DS.$classFile)) {
                require_once($modelsPath.DS.$classFile);
            }
   
            $className = $classPrefix.$modelName;
        
            if (class_exists($className)) {
                $this->_models[$modelsPath][$modelName] = new $className($config);
            }
   
            if (!isset($this->_models[$modelsPath][$modelName])) {    
                $this->_models[$modelsPath][$modelName] = VWP::raiseWarning("Model ($modelName) not found!","VWidgetParams",null,false);
            }      
            return $this->_models[$modelsPath][$modelName];
        } 
    
        return $result;
    } 
 
    /**
     * Method to get a model object, loading it if required.
     *
     * @access public
     * @param string $name The model name. Optional.
     * @param string $prefix The class prefix. Optional.
     * @param array $config Configuration array for model. Optional.
     * @return object The model.	 
     */
	 
    function &getModel( $name = null, $prefix = '', $config = array() ) 
    {
	
        if ($name === null) {
            $name = $this->_defaultModel;
        }
		
        if ( empty( $name ) ) {
            $name = $this->getName();
        }

        if ( empty( $prefix ) ) {
            $prefix = $this->getParentName() . '_Model_';
        }
    
        $model = & $this->_createModel( $name, $prefix, $config );

        if (!is_object($model)) {
            $model = VWP::raiseWarning("Model not found [name , prefix] ($name, $prefix)",get_class($this),null,false);
        }
        return $model;   
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
 
    function getDefinitions() 
    {
        return array();
    }
 
    /**
     * Add a search path
     * 
     * @param string $type Path type
     * @param string $path Path
     * @access public  
     */
           
    function addPath($type,$path) 
    {
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
  
        if (isset($config["name"])) {
            $this->_name = $config["name"];
        }
 
        if (isset($config["parent_name"])) {
            $this->_parent_name = $config["parent_name"];
        }
    }
  
    // end class VWidgetParams
} 
