<?php

/**
 * Virtual Web Platform - Widgets
 *  
 * This file provides the widget system interface        
 * 
 * @package VWP
 * @subpackage Libraries.UI  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License
 */

// Restrict access
class_exists("VWP") || die();

/**
 * Require URI support
 */

VWP::RequireLibrary('vwp.uri');

/**
 * Require Listener Support
 */

VWP::RequireLibrary('vwp.ui.listener');

/**
 * Require Language Support
 */

VWP::RequireLibrary('vwp.language.text');

/**
 * Require Model Support
 */

VWP::RequireLibrary('vwp.model');

/**
 * Require Route Support
 */

VWP::RequireLibrary('vwp.ui.route');

/**
 * Virtual Web Platform - Widgets
 *  
 * This class provides the widget system interface.  A widget
 * provides a standard user interface object which is independent
 * of the theme or requested response format. The widget will interact
 * with models to perform the requested tasks and provide data for the layout.  
 * The widget is not responsible for how the data is displayed, that is handled by the layout.
 * The responsibility of the widget is to initiate the tasks requested and 
 * to provide sufficient data for any layout type, such as XML, SOAP, HTML, CSV, etc.             
 * 
 * @package VWP
 * @subpackage Libraries.UI  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License
 */

class VWidget extends VObject 
{
    
    /**
     * Name of Default Widget
     *      
     * @var string $_defaultWidgetID
     * @access private   
     */

    protected $_defaultWidgetID;
        
    /**
     * The base path of the widget
     *
     * @var string
     * @access protected
     */
    
    var $_basePath = null;
    
    /**
     * The id of the widget
     *
     * @var		array
     * @access	protected
     */
     
    var $_id = null;
        
    /**
     * The name of the parent widget or application
     *
     * @var		array
     * @access	protected
     */
     
    var $_parent_name = null;
    
    /**
     * Array of class methods
     *
     * @var	array
     * @access	protected
     */
     
    var $_methods 	= null;
    
    /**
     * Array of class methods to call for a given task.
     *
     * @var	array
     * @access	protected
     */
     
    var $_taskMap 	= null;
    
    /**
     * Current or most recent task to be performed.
     *
     * @var	string
     * @access	protected
     */
     
    var $_task 		= null;
    
    /**
     * The mapped task that was performed.
     *
     * @var	string
     * @access	protected
     */
     
    var $_doTask 	= null;
    
    /**
     * Block access flag
     * 
     * @var boolean Block access
     * @access private
     */
    
    protected $_blockAccess = false;
    
    /**
     * Widget Mode
     * 
     * @access private
     */
    
    public $_mode = null;
    
    /**
     * Widget cache
     * 
     * @var array Widget cache
     * @access private
     */
    
    public $_widgets = array();
    
    /**
     * Widget Output Format
     * 
     * @var string Output format
     * @access private
     */
    
    protected $_format = null;
    
    /**
     * Base URL
     * 
     * @var string $baseurl Base URL
     * @access public
     */
    
    public $baseurl;
        
    /**
     * The set of search directories for resources.
     *
     * @var array $path Search paths
     * @access public
     */
     
    public $_path = array(
     'widget'	=> array(),
     'template' => array(),
     'helper' => array(),  
     );
    
    /**
     * URL for redirection.
     *
     * @var	string $_redirect Redirect URL
     * @access public
     */
     
    public $_redirect 	= null;

    /**
     * Require Secure Line Flag
     * 
     * @var	boolean $_require_secure_line Require Secure Connection
     * @access public
     */
     
    public $_require_secure_line = false;

    
    /**
     * Redirect message.
     *
     * @var	string $_redirectMessage
     * @access private
     */
     
    protected $_redirectMessage = null;
    
    /**
     * Redirect message type.
     *
     * @var	string $_redirectMessageType Redirect message type
     * @access private
     */
    
    protected $_redirectMessageType = null;
    
    /**
     * Registered models
     *
     * @var array Models
     * @access public
     */
    
    public $_models = array();
        
    /**
     * The default model
     *
     * @var	string
     * @access public
     */
    
    public $_defaultModel = null;
    
    /**
     * Current Layout name
     *
     * @var string $_layout Current layout name
     * @access private
     */
     
    protected $_layout = 'default';
    
    /**
     * Layout extension
     *
     * @var string $_layoutExt Layout extension
     * @access public
     */
     
    public $_layoutExt = 'php';
    
    /** 
     * The name of the default template source file.
     *
     * @var string $_template Default template source filename
     * @access private
     */
     
    protected $_template = null;
    
    /**
     * The output of the template script.
     *
     * @var string
     * @access private
     */
     
    protected $_output = null;
    
    /**
     * Widget parameters
     * 
     * @var object $_params
     * @access private
     */
     
    protected $_params;
    
    /**
     * Callback for escaping.
     *
     * @var string $_excape
     * @access private
     */
     
    protected $_escape = 'htmlspecialchars';
    
    /**
     * Widget output charset; defaults to UTF-8
     *
     * @var string
     * @access private
     */
      
    protected $_charset = 'UTF-8';
    
    /**
     * Widget Visible Flag
     * 
     * @var boolean $visible Widget visible flag
     * @access public
     */         
    
    public $visible = true;
    
    /**
     * Widget Height
     * 
     * @var integer $height Widget Height
     * @access public
     */
       
    public $height = "auto";
    
    /**
     * Widget Width
     * 
     * @var integer $width Widget width
     * @access public
     */
       
    public $width = "auto";
    
    /**
     * Resource ID
     * 
     * @var string $_R_Id Resource ID
     * @access private
     */
    
    protected $_R_Id = null;
    
    /**
     * Request Listener
     * 
     * @var VRequestListener $_listener;
     * @access private
     */
    
    protected $_listener = null;
    
    
    /**
     * Require a secure line
     * 
     * Note: If the require parameter is not provided, or is null
     *       this function simply returns the currently stored value.
     *       
     * @param boolean $require a secure line
     * @return boolean Require secure line flag
     * @access public
     */
    
    function requireSecureLine($require = null) 
    {
        if ($require !== null) {
            $this->_require_secure_line = $require ? true : false;
        }
        return $this->_require_secure_line ? true : false;
    }
    

    /**
     * Get Resource ID
     * 
     * @return string Resource ID
     * @access public
     */
    
    function getResourceID() 
    { 
         
        // Check Cache
        if (is_string($this->_R_Id) && !empty($this->_R_Id)) {
            return $this->_R_Id;
        }
            
        if ($this->is('VApplication')) {
            $R_Id = 'index.php?app='.$this->getID();
        } else {
            $R_Id = 'index.php?app='.$this->_app_id.'&widget='.$this->getID();     
        }
     
        $route =& VRoute::getInstance();  
        $R_Id = $route->encode($R_Id);
        $this->_R_Id = $R_Id;  
        return $this->_R_Id;
    }

    /**
     * Set Resource ID
     *      
     * @param string $R_Id Resource ID
     * @access public
     */
    
    function setResourceID($R_Id) 
    {  
        $this->_R_Id = $R_Id;
    }
    
    /**
     * Block access to widget
     */
    
    function blockAccess() 
    {
        $this->_blockAccess = true;
    }
    
    /**
     * Check widget visibility
     * 
     * @return boolean Visibile flag
     * @access public
     */
         
    function isVisible() 
    {
        return $this->_visible ? true : false;
    }
    
    /**
     * Check if widget is the current active widget
     *
     * @return boolean true if widget is currently active widget
     * @access public
     */
    
    function isActive() 
    {
    	$bcfg = array('alias'=>'content');    	
        $screenInfo = array();     
        foreach($bcfg as $key=>$val) {
            $info = array(urlencode($key),urlencode($val));
            $info = urlencode(implode(':',$info));
            array_push($screenInfo,$info);
        }     
        $screenId = implode(':',$screenInfo);    	
    	
        return $screenId == v()->shell()->getScreen();    	
    }
    
    /**
     * Set widget visibility
     * 
     * @param boolean $visible Visible
     * @access public
     */ 
     
    function setVisible($visible) 
    {
        $this->visible = $visible ? true : false; 
    }
    
    /**
     * Run service request
     * 
     * @param string $resourceName Resource ID
     * @param string $serviceName Service ID
     * @access public
     */
    
    function runService($resourceName,$serviceName = null) 
    {
    
        $args = func_get_args();

        if (empty($serviceName)) {
            $serviceName = $this->getID();
        }
     
        $wsdl = null;
        $serviceType = null;    
        $this->assignRef('wsdl',$wsdl);
     
        $filename = v()->filesystem()->path()->find(
                          $this->_path['widget'],
                          $this->_createFileName( 'service', array( 'name' => $serviceName, 'type' => $serviceType) )
                    );
     
        if (empty($filename)) 
        {
            return VWP::raiseError("Service $serviceName not found!",get_class($this),null,false);
        }
     
        $path = dirname($filename);
     
        $service =& $this->getService($serviceName);
        if (VWP::isWarning($service)) {
            return $service;
        }
        $service->setAccessPoint('index.php?app='.urlencode($resourceName).'&widget='.urlencode($serviceName));
        $wsdl = $service->getWSDL($path.DS.$serviceName.'_service.xml');
        if (VWP::isWarning($wsdl)) {
            $err = $wsdl;
            $wsdl = null;
            return $err;
        }
     
        $this->assignRef('wsdl',$wsdl);     
        $service->run();         
        return true;
    }
    
    /**
     * Class constructor
     * 
     * @access public
     */
         
    function __construct( $config = array() ) 
    {
        parent::__construct();
    
        //Initialize private variables
    
        $this->_redirect	= null;
        $this->_redirectMessage = null;
        $this->_redirectMessageType = 'message';
        $this->_taskMap = array();
        $this->_methods = array();
        $this->_data = array();
            
        // Get the methods only for the final controller class
        $thisMethods	= get_class_methods( get_class( $this ) );
        $baseMethods	= get_class_methods( 'VWidget' );
        $methods = array_diff( $thisMethods, $baseMethods );
    
        // Add default display method
        $methods[] = 'display';
    
        // Iterate through methods and map tasks
        foreach ( $methods as $method ) {
            if ( substr( $method, 0, 1 ) != '_' ) {
                $this->_methods[] = strtolower( $method );
                // auto register public methods as tasks
                $this->_taskMap[strtolower( $method )] = $method;
            }
        }
    
        //set the widget name
        
        if (array_key_exists('id', $config))  {
            $this->_id = $config['id'];
        } else {
            $this->_id = $this->getID();
        }
    
        $this->_defaultWidgetID = $this->getDefaultWidgetID();
        
        // Set Widgets App Name
    
        if (!$this->is('VApplication')) {
            if ( array_key_exists( 'app_id', $config ) ) {
                $this->_app_id = $config["app_id"];
            } else {
                $this->_app_id = VApplication::getCurrentApplicationID();
            }
        }
    
        $this->_R_Id = $this->getResourceID();
        
        // Set a base path for use by the widget
        if (array_key_exists('base_path', $config)) {
            $this->_basePath	= $config['base_path'];
        } else {
            $this->_basePath = VApplication::getCurrentApplicationPath();
        }
    
         // Set a widgets path for use by the widget
         if (array_key_exists('widgets_path', $config)) {
             $this->_path["widget"][]	= $config['widgets_path'];
         } else {
             $p = explode(".",$this->_id);   
             while(count($p) > 0) {
                 array_pop($p);
                 $suffix = '';
                 foreach($p as $s) {
                     $suffix .= DS.$s.DS.'widgets';
                 }          
                 $this->_path["widget"][] = $this->_basePath	.DS.'widgets'.$suffix;    
             }
         }	
        
         // Set a model path for use by the widget
         if (array_key_exists('model_path', $config)) {
             $this->addModelPath($this->_id, $config['model_path']);
         } else {
       
             if ($this->is('VApplication')) {
                 $this->addModelPath($this->_id, $this->_basePath.DS.'models');
             } else {
          
                 $p = explode(".",$this->_id);   
                 array_unshift($p,$this->_app_id);
       
                $suffix = '';
                $appid_mods = array();
                foreach($p as $s) {
                    if (count($appid_mods) > 0) {
                        $suffix .= DS.'widgets'.DS.$s;
                    }
                    array_push($appid_mods,$s);
                    $appId = implode('.',$appid_mods);
                      
                    $this->addModelPath($appId,$this->_basePath.$suffix.DS.'models');           
                }                     
            }           
        }  
      	
        // If the default task is set, register it as such
        if ( array_key_exists( 'default_task', $config ) ) {
            $this->registerDefaultTask( $config['default_task'] );
        } else {
            $this->registerDefaultTask( 'display' );
        }
      
        // set the charset (used by the variable escaping functions)
        if (array_key_exists('charset', $config)) {
            $this->_charset = $config['charset'];
        }
    
        // user-defined escaping callback
        if (array_key_exists('escape', $config)) {
            $this->setEscape($config['escape']);
        }
    
        // set the default template search path
        if (array_key_exists('template_path', $config)) {
            // user-defined dirs            
            $this->setPath('template', $config['template_path']);
        } else {   
            $n = str_replace('.',DS.'widgets'.DS,strtolower($this->_id));
            $this->setPath('template', $this->_basePath.DS.'widgets'.DS.$n.DS.'layouts');
        }	
    
        // set the default helper search path
        if (array_key_exists('helper_path', $config)) {
            // user-defined dirs
            $this->setPath('helper', $config['helper_path']);
        } else {
            $this->setPath('helper', $this->_basePath.DS.'helpers');
        }
        // set the layout
        if (array_key_exists('layout', $config)) {
            $this->setLayout($config['layout']);
        } else {
            $this->setLayout('default');
        }
    
        $this->baseurl = VURI::base(true);
    
        $doc =& VWP::getDocument();  
        $this->setEscape($doc->_escape);
    								
    }
    
    /**
     * Run selected task
     * 
     * @param string $task Task name
     * @param string $mode Execute mode
     * @param array $args Arguments
     * @return mixed Task result on success, error or warning on failure
     * @access public
     */
            
    function runTask( $task , $mode = null, $args = null) 
    {
         
        $this->_task = $task;
        $this->_mode = $mode;
     
        $task = strtolower( $task );
        if (isset( $this->_taskMap[$task] )) {
            $doTask = $this->_taskMap[$task];
        } elseif (isset( $this->_taskMap['__default'] )) {
            $doTask = $this->_taskMap['__default'];
        } else {
            return VWP::raiseError(VText::_('Task ['.$task.'] not found'),get_class($this),404 );
        }
    
        // Record the actual task being fired
        $this->_doTask = $doTask;
    
        // Make sure we have access
        if ($this->authorizeTask( $doTask )) {
      
            if ($args !== null) {
                $retval = call_user_func_array(array($this,$doTask),$args);
            } else {         
                $retval = $this->$doTask();
            }
            return $retval;
        } else {   
            return VWP::raiseWarning(VText::_('Access Forbidden'),get_class($this), 403, false);
        }
    }
    
    /**
     * Authorization check
     *
     * @param string $task	The task to check access on
     * @return boolean True if authorized	 
     * @access public
     */
     
    function authorizeTask( $task ) 
    {
        if ($this->_blockAccess) {
            return false;
        }     
        $user =& VUser::getCurrent();     
        $S_Id = array(
            'task'=>$task,
            'mode'=>'deny',
           );     
        if ($user->deny('Block task: ' . $task,$this->getResourceID(),$S_Id)) {
            return false;  
        }     
        return true;
    }
    
    /**
     * Get Widget Output Format
     *
     * @return string Output format
     * @access public
     */
    
    function getFormat() 
    {
    	if (!isset($this->_format)) {
            $this->_format = 'html';
            $doc = & VWP::getDocument();
            $this->_format = $doc->getDocumentType();
    	}
    	return $this->_format;     	
    }
    
    /**
     * Set Widget Output Format
     * 
     * @param string $format
     * @access public
     */
    
    function setFormat($format) {
    	$this->_format = $format;
    }
    
    /**
     * Display the widget
     *
     * This function displays the widget via the appropriate layout and provides the default task.
     * 
     * This method should normally be called by all tasks using parent::display($tpl). Most widgets
     * will need to override this method to provide data to the layout when the default task is requested.
     * The overriding task should then call parent::display($tpl) once all data is prepared for the layout.
     *     
     * @param	mixed $tpl Optional
     * @access public	 
     */
     
    function display($tpl = null) 
    {	 
         		     
        $widgetName = $this->getID();
        $widgetLayout = $this->_layout;
    
        // Get/Create the model
        if ($model = & $this->getModel($widgetName)) {
            // Push the default model into the widget
            $this->setModel($model, true);
        }
             	    	
        $theme = VWP::getTheme();
        $themeType = VWP::getThemeType();
    
        $format = $this->getFormat(); 
        
        // Check Driver
     
        $themedriverFilename = VPATH_BASE.DS.'themes'.DS.$themeType.DS.$theme.DS.'driver.php';
        $themedriverClassName = $theme . 'ThemeDriver';
        
        if (file_exists($themedriverFilename)) {
            require_once($themedriverFilename);
            $themeDriver = new $themedriverClassName();
            $themeDriver->setWidget($this);
                     
            if ($themeDriver->getLayout($widgetLayout,$format)) {            	
                return;
            }
        }

                
        // Check Theme
     
        $themeBase = VPATH_BASE.DS.'themes'.DS.$themeType.DS.$theme;
     
        $appName = basename(VApplication::getCurrentApplicationPath());  
                 
        $templateDir = $themeBase.DS.'apps'.DS.$appName.DS.$widgetName;  
     
        $layoutFile = $this->_createFilename('template',array('name' => $widgetLayout));
                      
        $layoutFilename = $templateDir.DS.$layoutFile;  
                
        $vfile =& v()->filesystem()->file();
        
        if ($vfile->exists($layoutFilename)) {        	
            include($layoutFilename);
            return;
        }
                   
        // Load Default
       	       	
        $result = $this->loadTemplate($tpl);
     						
        if (VWP::isError($result)) {
            return $result;
        }
        
        echo $result;		
    }
    
    /**
     * Get display output for MWL based architecture
     *
     * @access public
     * @param mixed $tpl Optional	 
     */	
     
    function getDisplay($tpl = null) 
    {   
        ob_start();    
        $this->display($tpl);
        $result = ob_get_contents();
        ob_end_clean();
        return $result;
    }
     
    /**
     * Redirects the browser or returns false if no redirect is set.
     *     
     * @return	boolean	False if no redirect exists.
     * @access	public	 
     */
     
    function redirect() 
    {
        if ($this->_redirect) {   
            VWP::redirect($this->_redirect);
        }
        return false;
    }
    
    /**
     * Get Parameters
     * 
     * @return object Parameters on success, error or warning otherwise
     * @access public
     */
    
    function &getParams() 
    {
     
        if (!isset($this->_params)) {   
            $f = v()->filesystem()->path()->find($this->_path["widget"],$this->_createFileName( 'widget', array( 'name' => $this->getID()) ));
            if (empty($f)) {
                $e = VWP::raiseWarning("Unable to extract widget path.",get_class($this),null,false);
                return $e;
            } else {
                $classFile = dirname($f).DS.'params.php';
                $vfile =& v()->filesystem()->file();
       
                if ($vfile->exists($classFile)) {
                    $className = $this->getClassPrefix().'_WidgetParams_'.$this->getID();
                    require_once($classFile);
                    if (!class_exists($className)) {
                        $e = VWP::raiseWarning("Unable to find ($className).",get_class($this),null,false);
                        return $e;       
                    }
                    $this->_params = new $className;
                }   
            }
        }
        return $this->_params;
    } 
        
    /**
     * Method to get a model object, loading it if required.
     *
     * @param string $name The model name. Optional.     
     * @param array $config Configuration array for model. Optional.
     * @return object The model
     * @access public	 
     */
     
    function &getModel( $name = null, $config = array() ) 
    {
    
        if ($name === null) {
            $name = $this->_defaultModel;
        }
    	
        if ( empty( $name ) ) {
            $name = $this->getID();
        }
           
        $model = & $this->_createModel( $name, $config );
        if (!VWP::isWarning($model)) {
            // task is a reserved state
            $model->setState( 'task', $this->_task );
        }
        return $model;
    }
    
    /**
     * Adds to the stack of model paths in LIFO order.
     *
     * @param string $appId Application ID
     * @param string $path Path
     * @access public
     */
     
    function addModelPath($appId, $path) 
    {
        $args = func_get_args();            
        VModel::addIncludePath($path,$appId);
    }
    
    /**
     * Gets the available tasks
     *        
     * @return array Array of task names.
     * @access	public	 
     */
     
    function getTasks() 
    {
        return $this->_methods;
    }
    
    /**
     * Get the last or current task performed by this widget
     *     
     * @return string The task that was or is being performed.
     * @access public	 
     */
     
    function getTask() 
    {
       return $this->_task;
    }

    /**
     * Set default widget ID
     * 
     * @param string $widgetName Widget Name
     * @return string Widget Name;
     */
                 
    function setDefaultWidgetID($widgetId) 
    {
        $this->_defaultWidgetId = $widgetId;
        return $this->_defaultWidgetID;
    }

    /**
     * Get ID of default widget
     * 
     * @return string Default Widget ID;
     */  
    
    function getDefaultWidgetID() 
    {
        if (!isset($this->_defaultWidgetID)) {
            return $this->getID();
        }
        return $this->_defaultWidgetID;
    } 
     
    /**
     * Method to get a reference to the current widget and load it if necessary.
     *
     * @access public
     * @param string $name The widget name. Optional, defaults to the application name.
     * @param string $prefix The class prefix. Optional.
     * @param array	$config Configuration array for view. Optional.
     * @return object Reference to the widget or an error.	 
     */
     
    function &getWidget( $name = '', $prefix = '', $config = array() ) 
    {
        static $widgets;
                
        if ( !isset( $widgets ) ) {
            $widgets = array();
        }
             
        if ( empty( $prefix ) ) {
            $prefix = $this->getClassPrefix($name) . '_Widget_';
        }
       
        if (!isset($config['id'])) {
            $config['id'] = $name;
        }
     
        if ( empty( $widgets[$name] ) ) {
            if ( $widget = & $this->_createWidget( $name, $prefix, $config ) ) {
                $widgets[$name] = & $widget;
            } else {
       
                $result = VWP::raiseWarning(
                           VText::_( 'Widget not found [name,  prefix]:' ) . ' ' . $name . ',' . $prefix,
                    $this->getType(),
                           null,
                           false);
                return $result;
            }
        }
     
        return $widgets[$name];
    }
    
    /**
     * Method to get a reference to the current widget and load it if necessary.
     *
     * @access public
     * @param string $name The widget name. Optional, defaults to the application name.
     * @param string $type The widget type. Optional.
     * @param string $prefix The class prefix. Optional.
     * @param array	$config Configuration array for view. Optional.
     * @return object Reference to the widget or an error.	 
     */
     
    function &getService( $name = '', $type = '', $prefix = '', $config = array() ) 
    {
        static $services;
         
        if ( !isset( $services ) ) {
            $services = array();
        }
    
        if ( empty( $name ) ) {
            $name = $this->getDefaultWidgetID();
        }    
         
        if ( empty( $prefix ) ) {
            $prefix = $this->getClassPrefix($name) . 'Service';
        }
    
        if ( empty( $services[$name] ) ) {
            if ( $service = & $this->_createService( $name, $prefix, $type, $config ) ) {
                $services[$name] = & $service;
            } else {
       
                $result = VWP::raiseWarning(
                           VText::_( 'Service not found [name, type, prefix]:' ) . ' ' . $name . ',' . $type . ',' . $prefix,
                    get_class($this),
                           500,
                           false);
                return $result;
            }
        }
     
        return $services[$name];
    }
            
    /**
     * Add one or more view paths to the widget search stack, in LIFO order.
     *     
     * @param string|array The directory, or list of directories to add.
     * @access public
     */
     
    function addWidgetPath( $path ) 
    {
        $this->addPath( 'widget', $path );
    }
    
    /**
     * Register a task to a method
     *     
     * @param string The task.
     * @param string The name of the method in the derived class to perform for this task.
     * @access public 
     */
    
    function registerTask( $task, $method ) 
    {
        if ( in_array( strtolower( $method ), $this->_methods ) ) {
            $this->_taskMap[strtolower( $task )] = $method;
        }
    }
    
    /**
     * Register the default task to perform if the requested task is not found.
     *
     * @param string The name of the method in the derived class to perform if a named task is not found.
     * @return void
     * @access public  
     */
     
    function registerDefaultTask($method) 
    {
        $this->registerTask( '__default', $method );
    }
        
    /**
     * Set a URL for browser redirection.
     *
     * @param string $url URL to redirect to.
     * @access public
     */
     
    function setRedirect($url) 
    {
        if (substr($url,0,10) == 'index.php?') {
            $route =& VRoute::getInstance();
            $url = $route->encode($url);
        }
     
        $this->_redirect = $url;
    }
        
    /**
     * Method to load and return a model object.
     *     
     * @param string $name The name of the model.     
     * @param array	$config Configuration array for the model. Optional.
     * @return object Model object on success, error or warning otherwise
     * @access private
     */
    
    protected function &_createModel( $name, $config = array()) 
    {
        $result = null;
     
        // Clean the model name
        $modelName	 = preg_replace( '/[^A-Z0-9_\\.]/i', '', $name );
     
        $parts = array();
     
        if ($this->is('VApplication')) {
            array_push($parts,$this->_id);
        } else {
            array_push($parts,$this->_app_id);
            if (!empty($this->_id)) {
                array_push($parts,$this->_id);
            }      
        }
    
        $appId = implode('.',$parts);  
        $result =& VModel::getInstance($appId, $modelName, $config);
        return $result;
    }
    
    /**
     * Method to load and return a widget object. This method that uses a default
     * set path to load the widget class file.
     *
     * @param string $name The name of the widget.
     * @param string $prefix Optional prefix for the widget class name.  
     * @param array $config Configuration array for the widget. Optional.
     * @return object Widget object on success, null, error or warning otherwise
     * @access private
     */
     
    protected function &_createWidget( $name, $prefix = '', $config = array() ) 
    {
       
        $result = null;
       
        // Clean the widget name
        
        $widgetName = preg_replace( '/[^A-Z0-9_\\.]/i', '', $name );
        $classPrefix = preg_replace( '/[^A-Z0-9_]/i', '', $prefix );
     
        $parts = explode(".",$widgetName);  
        $widgetId = array_pop($parts);
     
        // Build the view class name
        $widgetClass = $classPrefix . ucfirst($widgetId);
        
        
        if ( !class_exists( $widgetClass ) ) {		 
            			       
            $filename = $this->_createFileName( 'widget', array( 'name' => $widgetName ) );
                        
            $path = v()->filesystem()->path()->find(
                     $this->_path['widget'],
                     $filename       
                  );			
    		
            if ($path) {
                require_once $path;
    
                if ( !class_exists( $widgetClass ) ) {       
                    $errMsg = VText::_( 'Widget not found [class, file]:' ). ' ' . $widgetClass . ', ' . $path;
                    $result = VWP::raiseWarning($errMsg,$this->getType(),ERROR_CLASSNOTFOUND,false);
                    return $result;
                }
            } else {        
                return $result;
            }
        }
        $config["name"] = $name;
        $wid = count($this->_widgets);  
        $this->_widgets[$wid] = new $widgetClass($config);
        return $this->_widgets[$wid];
    }
    
    /**
     * Method to load and return a service object. This method first looks in the
     * current template directory for a match, and failing that uses a default
     * set path to load the view class file.
     *
     * Note the "name, prefix, type" order of parameters, which differs from the
     * "name, type, prefix" order used in related public methods.
     *     
     * @param string $name The name of the service.
     * @param string $prefix optional prefix for the service class name.
     * @param string $type The type of service.
     * @param array $config Configuration array for the service. Optional.
     * @return object Service object on success, null, error or warning otherwise
     * @access private
     */
     
    function &_createService( $name, $prefix = '', $type = '', $config = array() ) 
    {
       
        $result = null;
       
        // Clean the service name
     
        $serviceName = preg_replace( '/[^A-Z0-9_\\.]/i', '', $name );
        $classPrefix = preg_replace( '/[^A-Z0-9_]/i', '', $prefix );
        $serviceType = preg_replace( '/[^A-Z0-9_]/i', '', $type );
    
        $parts = explode(".",$serviceName);  
        $serviceBasename = array_pop($parts);
     
        // Build the service class name
        $serviceClass = $classPrefix . ucfirst($serviceBasename);
           
        if ( !class_exists( $serviceClass ) ) {		 
            VWP::RequireLibrary( 'vwp.filesystem.path' );			
       			
            $path = v()->filesystem()->path()->find(
                $this->_path['widget'],
                $this->_createFileName( 'service', array( 'name' => $serviceName, 'type' => $serviceType) )
            );			
    		
            if ($path) {
                require_once $path;
    
                if ( !class_exists( $serviceClass ) ) {
                    $result = VWP::raiseWarning(
                      VText::_( 'Service class not found [class, file]:' )
                            . ' ' . $serviceClass . ', ' . $path,__CLASS__,500,false);
                    return $result;
                }
            } else {
                return $result;
            }
        }
        $config["name"] = $name;
        $result = new $serviceClass($config);
        return $result;
    }
        
    /**
     * Adds to the search path for templates and resources.
     *     
     * @param string $type The path type (e.g. 'model', 'view').
     * @param string|array The path to search.     
     * @access public
     */
    
    function addPath( $type, $path ) 
    {

        settype( $path, 'array' );

        // loop through the path directories
    
        foreach ( $path as $dir ) {
            $dir = trim( $dir );    
            // add trailing separators as needed
            if ( substr( $dir, -1 ) != DIRECTORY_SEPARATOR ) {
                $dir .= DIRECTORY_SEPARATOR;
            }
    
            // add to the top of the search dirs
            array_unshift( $this->_path[$type], $dir );
        }
    }
    
    /**
     * Create the filename for a resource.
     *     
     * @param string $type The resource type to create the filename for.
     * @param array	$parts An associative array of filename information. Optional.
     * @return string Filename
     * @access	private	 
     */
     
    protected function _createFileName( $type, $parts = array() ) 
    {
        $filename = '';
        switch ( $type ) {
            case 'widget':
       
                $name = $parts['name'];              
                $s = explode(".",$name);
                $baseName = array_pop($s);
                $prefix = '';
                foreach($s as $p) {
                    $prefix.= $p.DS.'widgets'.DS;
                }   				
                $filename = $prefix.strtolower($baseName).DS.strtolower($baseName).'.php';
                break;
            case 'service':
       
                $name = $parts['name'];
                $s = explode(".",$name);
                $baseName = array_pop($s);
                $prefix = '';
                foreach($s as $p) {
                    $prefix.= $p.DS.'widgets'.DS;
                }   				
                $filename = $prefix.strtolower($baseName).DS.strtolower($baseName).'_service'.'.php';
                break;
            case 'template' :
                $doc = & VWP::getDocument();  
                $format = $this->getFormat();   
                $filename = $format.DS.strtolower($parts['name']).'.'.$this->_layoutExt;                          
             break;
            case 'requestlistener':
            	// relative to widget path
                $name = $parts['name'];              
                $s = explode(".",$name);
                $baseName = array_pop($s);
                
                $prefix = '';
                /*
                foreach($s as $p) {
                    $prefix.= $p.DS.'widgets'.DS;
                }
                */   				
                $filename = $prefix.strtolower($baseName).DS.'listeners'.DS.strtolower($parts['proto']).'.php';                
                
            	break;
    			
       default :
        $filename = strtolower($parts['name']).'.php';
        break;
      }
    
      return $filename;		
    }
    
    /**
     * Assigns variables to the widget
     *
     * You are not allowed to set variables that begin with an underscore
     * these are reserved and private properties.     
     *
     * @param string|object|array $arg1 Data object, associative array or variable name
     * @param mixed $arg2 Value      
     * @return boolean True on success, false on failure.
     * @access public
     */
    
    function assign($arg1, $arg2 = null) 
    {
    
        // assign by object
        if (is_object($arg1)) {
            // assign public properties
            foreach (get_object_vars($arg1) as $key => $val) {
                if (substr($key, 0, 1) != '_') {
                    $this->$key = $val;
                }
            }
            return true;
        }
    
        // assign by associative array
        if (is_array($arg1)) {
            foreach ($arg1 as $key => $val) {
                if (substr($key, 0, 1) != '_') {
                    $this->$key = $val;
                }
            }
            return true;
        }
    
        // assign by string name and mixed value.
        if (is_string($arg1) && substr($arg1, 0, 1) != '_' && func_num_args() > 1) {
            $this->$arg1 = $arg2;
            return true;
        }
    
        return false;
    }
    
    /**
     * Assign variable to the widget by reference
     *
     * Note: You are not allowed to set variables that begin with an underscore
     * these are reserved and private properties.
     * 
     * @return boolean True on success, false on failure.
     */
    
    function assignRef($key, &$val) 
    {
        if (is_string($key) && substr($key, 0, 1) != '_') {
            $this->$key = $val;
            return true;
        }
        return false;
    }
    
    /**
     * Replace PHP Tags
     * 
     * @param string $txt Source string
     * @return string PHP encoded string
     * @access public
     */
              
    function noPHP($txt) 
    {
        $o = '<' . '?';
        $c = '?' . '>';
     
        $txt = str_replace($o,$o . "php echo '<' . '?'; " . $c,$txt);
        $txt = str_replace($c,$o . "php echo '?' . '>'; " . $c,$txt);
        return $txt; 
    }
    
    /**
     * Escapes a value for output in a layout.
     *
     * @param  mixed $var The output to escape.
     * @return mixed The escaped value.
     * @access public
     */
    
    function escape($var) 
    {
        if (in_array($this->_escape, array('htmlspecialchars', 'htmlentities'))) {
            return call_user_func($this->_escape, (string)$var, ENT_COMPAT, $this->_charset);
        }
        return call_user_func($this->_escape, (string)$var);
    }

    /**
     * Get the layout.
     *
     * @access public
     * @return string The layout name
     */
    
    function getLayout() 
    {
        return $this->_layout;
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
     
    function getID() 
    {
    
        $name = $this->_id;    
        if (empty( $name )) {    
            $className = $this->getType();      
            $r = null;
            if (!preg_match('/_Widget_((.*(_widget_)?.*))$/i', $className, $r)) {
      
                $errMsg = "Invalid widget name ($className). Application widgets should be in the format (base)_Widget_(name)!  ".
                         "Widget libraries must override getID().";
                VWP::raiseError ($errMsg,'VWidget::getID()');        
            } elseif ((count($r) > 3) && (strpos($r[3], "widget"))) {
                VWP::raiseWarning("Your classname contains the substring 'widget'. ".
                         "This causes problems when extracting the classname from the name of your objects widget. " .
                         "Avoid Object names with the substring 'widget'.",
                         get_class($this) . "::getName"                        
                 );
            } else {      
                $name = strtolower( $r[2] );
            }   
        }
        return $name;
    }
        
    /**
     * Method to get the base class name
     *
     * @return string The name of the model
     * @access public
     */
    
    function getClassPrefix($widgetName = '') 
    {
     
        $className = $this->getType();
       
        $r = null;
        if (preg_match('/^(.*?)_Widget_(.*(_widget_)?.*)$/i', $className, $r)) {         
            if ((count($r) > 3) && (strpos($r[3], "_widget_"))) {      
                $errMsg = "Invalid widget name ($className). Application widgets should be in the format (base)_Widget_(name)!  ".
                         "Widget libraries must override getClassPrefix().";
                VWP::raiseWarning($errMsg,
                         'Widget::getClassPrefix');
            } else {
                $baseName = ucfirst($r[1]);
            }
     
        } else {
            if ($this->is('VApplication')) {    
                $baseName = ucfirst($this->getID());
                $parts = explode(".",$widgetName);
                array_pop($parts);
                $new_parts = array($baseName);
                foreach($parts as $n) {
                    array_push($new_parts,ucfirst(trim($n)));
                }
                $baseName = implode('_',$new_parts);       
           } else {
                $errMsg = "Invalid widget name ($className).  "
                 ."Widget libraries must override getClassPrefix().";
                VWP::raiseError($errMsg,'VWidget::getClassPrefix');
                $baseName = '';
           }
        }
    
        return $baseName;
    }
    
    /**
     * Method to get the widget parent name
     *
     * The parent name by default parsed using the classname, or it can be set
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
            if (!preg_match('/^(.*)Widget/i', get_class($this), $r)) {
                VWP::raiseError ("Cannot get or parse class name.",500, get_class($this) . '::getParentName',500);
            }
            $name = strtolower( $r[1] );
        }
        return $name;
    }
    
    /**
     * Method to add a model to the widget. 
     *     
     * @param object &$model The model to add.
     * @param boolean $default Set as default model
     * @return object The added model
     * @access public
     */
    
    function &setModel( &$model, $default = false ) 
    {
      
        if (VWP::isError($model)) {     
            return $model;
        }
       
        if (VWP::isWarning($model)) {     
            return $model;
        }
       
        $name = strtolower($model->getID());
        $this->_models[$name] = &$model;
        if ($default) {
            $this->_defaultModel = $name;
        }
        return $model;
    }
        
    /**
     * Set listener
     * 
     * @todo Implement Theme override of request listener
     * @param string $proto 
     * @access public 
     */
    
    public function setRequestListener($proto,$widgetId,$test = false) 
    {

    	// Locate Widget Path
    	    	
    	$widgetName = preg_replace( '/[^A-Z0-9_\\.]/i', '', $widgetId );
    	    	
    	$filename = $this->_createFileName( 'widget', array( 'name' => $widgetName ) );
                  	           		
        $path = v()->filesystem()->path()->find(
                     $this->_path['widget'],
                     $filename       
                  );			    	
                
        if ($path) {
        	
        	// setup class Parts
        	
        	$baseName = $this->getClassPrefix();
        	    	
    	    $prefix =  $baseName . '_' . $proto . 'Listener_';
    	        	    
    	    $parts = explode('.',$widgetId);
    	    $suffix = array_pop($parts);        	

        	// Load Class
        	
        	$widgetPath = dirname($path);
    	    	
    	    $className = $prefix . $suffix;

    	    if (!class_exists($className)) {
    	        $filename = dirname($widgetPath).DS.$this->_createFileName('requestlistener',array('name'=>$widgetId,'proto'=>$proto));
    	        if (v()->filesystem()->file()->exists($filename)) {
    	    	    require_once($filename);
    	        }    	
    	    }
    	
    	    if (class_exists($className)) {
    		    		        		
    		    $this->_listener = new $className;
    		    $this->_listener->setClassParts($baseName,$suffix);
    		    $this->_listener->setWidgetPath($widgetPath);
    		    
    	    }    	        	    
        }    	
    }
            
    /**
     * Sets the layout name to use
     *
     * @access	public
     * @param	string $template The template name.
     * @return	string Previous value
     */
    
    function setLayout($layout) 
    {
        $previous = $this->_layout;
        $this->_layout = $layout;		
        return $previous;
    }
    
    /**
     * Allows a different extension for the layout files to be used
     *
     * @param string The extension
     * @return string Previous value
     * @access public
     */
     
    function setLayoutExt( $value ) 
    {
        $previous = $this->_layoutExt;
        if ($value = preg_replace( '#[^A-Za-z0-9]#', '', trim( $value ) )) {
            $this->_layoutExt = $value;
        }
        return $previous;
    }
    
    /**
     * Sets the _escape() callback.
     *
     * @param mixed $spec The callback for _escape() to use.
     * @access public
     */
    
    function setEscape($spec) {
        $this->_escape = $spec;
    }
    
    /**
     * Get Request Listener
     * 
     * @return VRequestListener Request Listener
     * @access public
     */
    
    function &getRequestListener() 
    {    	
    	if (!is_object($this->_listener)) {
    	    $this->_listener = new VRequestListener;
    	}
    	return $this->_listener;
    }
    
    
    /**
     * Adds to the stack of view script paths in LIFO order.
     *
     * @param string|array The directory (-ies) to add.     
     */
     
    function addTemplatePath($path) 
    {
       $this->addPath('template', $path);
    }
    
    /**
     * Adds to the stack of helper script paths in LIFO order.
     *
     * @param string|array The path or list of paths to add.
     * @access public     
     */
      
    function addHelperPath($path) 
    {
        $this->addPath('helper', $path);
    }
    
    /**
     * Load a template file 
     *     
     * @param string $tpl The name of the template source file     
     * @return string The output of the the template script.
     * @access	public
     */
     
    function loadTemplate( $tpl = null) 
    {
        global $mainframe;
             
        // clear prior output
        $this->_output = null;
    
        //create the template file name based on the layout
        $file = isset($tpl) ? $this->_layout.'_'.$tpl : $this->_layout;
               
        // clean the file name
        $file = preg_replace('/[^A-Z0-9_\.-]/i', '', $file);
        $tpl  = preg_replace('/[^A-Z0-9_\.-]/i', '', $tpl);
    
        // load the template script
       
        $filetofind = $this->_createFileName('template', array('name' => $file));
                
        $this->_template = v()->filesystem()->path()->find($this->_path['template'], $filetofind);    
        if ($this->_template != false) 
        {
            // unset so as not to introduce into template scope
            unset($tpl);
            unset($file);
    
            // never allow a 'this' property
            if (isset($this->this)) {
                unset($this->this);
            }
    
            // start capturing output into a buffer
            ob_start();
            
            // parse the template
            include $this->_template;
         
            $this->_output = ob_get_contents();
            ob_end_clean();
            return $this->_output;
        } else {      
            return VWP::raiseError('Layout "' . $filetofind . '" not found',get_class($this).':loadTemplate',500,true);
        }
    }
    
    /**
     * Load a helper file
     *
     * @param string $tpl The name of the helper source file automatically searches the helper paths and compiles as needed.
     * @return boolean Returns true if the file was loaded
     * @access public
     */
    
    function loadHelper( $hlp = null) 
    {
        // clean the file name
        $file = preg_replace('/[^A-Z0-9_\.-]/i', '', $hlp);
    
        // load the template script
        VWP::RequireLibrary('vwp.filesystem.path');
        $helper = v()->filesystem()->path()->find($this->_path['helper'], $this->_createFileName('helper', array('name' => $file)));
        if ($helper != false) {
            // include the requested template filename in the local scope
            include_once $helper;
        }
    }
    
    /**
     * Sets an entire array of search paths for templates or resources.     
     *      
     * @param string $type The type of path to set, typically 'template'.
     * @param string|array $path The new set of search paths.
     * @access public
     */
    
    function setPath($type, $path) 
    {
     
        // clear out the prior search dirs
        $this->_path[$type] = array();
    
        // actually add the user-specified directories
        $this->addPath($type, $path);
    
        // always add the fallback directories as last resort
        switch (strtolower($type)) {
            case 'template':      
                // set the alternative template search dir
      
                $app = basename(VApplication::getCurrentApplicationPath());
                $themeId = VWP::getTheme();
                $themeType = VWP::getThemeType();					
                $fallback = VPATH_BASE.DS.'themes'.DS.$themeType.DS.$themeId.DS.'html'.DS.$app.DS.$this->getID();
                $this->addPath('template', $fallback);   
                break;
        }
    }
    
    /**
     * Build widget
     * 
     * @param string $task Task  
     * @return string Widget output
     * @access public
     */
            	
    function build($task = 'display') 
    {
        ob_start();    
        $this->runTask($task);
        $result = ob_get_contents();
        ob_end_clean();
        return $result;
    }
    
    // end class VWidget   	
} 	
