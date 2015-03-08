<?php

/**
 * Theme Parameters  
 * 
 * This file provides support for theme targets
 *    
 * @package    VWP
 * @subpackage Libraries.Themes
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

/**
 * Require XML Document Support
 */

VWP::RequireLibrary('vwp.documents.xml');

/**
 * Require Widget Support
 */

VWP::RequireLibrary('vwp.ui.widget');

/**
 * Require Frame Support
 */

VWP::RequireLibrary('vwp.ui.frame');

/**
 * Require Filesystem Support
 */

VWP::RequireLibrary('vwp.filesystem');


/**
 * Theme Parameters  
 * 
 * This class provides support for theme targets
 * 
 * Note: Support function names start with underscore '_' to ensure they are not registered as tasks
 * 
 * @package    VWP
 * @subpackage Libraries.Themes
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

class VThemeTarget extends VWidget 
{

	/**
	 * Target Name
	 * 
	 * @var string $name Target Name
	 * @access public
	 */
	
    var $name;
    
    /**
     * Theme ID
     * 
     * @var string $themeId Theme ID
     * @access public
     */
    
    var $themeId;
    
    /**
     * Theme Type
     * 
     * @var string Theme Type
     * @access public
     */
    
    var $themeType;
 
    /**
     * Target Items
     * 
     * @var array Target Items
     * @access private
     */
    
    var $_items;
   
    /**
     * Target Screens
     * 
     * @var array
     * @access private
     */
    
    public $_screens;
    
    /**
     * Current Frame ID
     * 
     * @var string $_frameId
     * @access private
     */
    
    protected $_frameId = null;
    
    /**
     * Registered Targets
     * 
     * @var array $_targets Registered Targets
     * @access public
     */
    
    static $_targets = array();
 
    /**
     * Get Screens
     * 
     * @return array $_screens;
     * @access public
     */
    
    function _getScreens() 
    {
    	return $this->_screens;
    }
    
    /**
     * Get Theme Settings
     * 
     * @param string $themeType
     * @param string $themeId
     * @return array Theme settings
     * @access public
     */
    
    public static function _getThemeSettings($themeType,$themeId) 
    {
        $basePath =  VWP::getVarPath('vwp').DS.'themes'.DS.'targets'.DS.$themeType;
        $vfile =& v()->filesystem()->file();
        $settings = array();
        $settings["targets"] = array();

        $filename = $basePath.DS.$themeId.'.xml';
  
        if ($vfile->exists($filename)) {
            $src = $vfile->read($filename);
            $doc = new DomDocument;
            $doc->loadXML($src);
   
            // Get Targets
   
            $targets = $doc->getElementsByTagName('target');
            for($i=0;$i < $targets->length; $i++) {
                $node = $targets->item($i);
                $info = array();
                $frameId = $node->getAttribute('frame');
                if (!empty($frameId)) {
                    $info["frame"] = $frameId;
                }
                $settings["targets"][$node->nodeValue] = $info;
            }
        }
    
        return $settings;
    } 
 
    /**
     * Assign Frames
     * 
     * @param string $themeType Theme Type
     * @param string $themeId Theme ID
     * @param string $assignments Frames to assign
     * @return boolean|object True on success, error or warning otherwise
     * @access public
     */
    
    public static function _assignFrames($themeType, $themeId, $assignments) 
    {
        $basePath =  VWP::getVarPath('vwp').DS.'themes'.DS.'targets'.DS.$themeType;
        $filename = $basePath.DS.$themeId.'.xml';
        $vfile =& v()->filesystem()->file();
  
        if ($vfile->exists($filename)) {
            $src = $vfile->read($filename);
        } else {
            $src = '<' . '?xml version="1.0" ?' . '>' . "\n" . '<theme_settings></theme_settings>';
            $src = $vfile->write($filename,$src);
            if (!VWP::isWarning($src)) {
                $src = $vfile->read($filename);
            }
        }
  
        if (VWP::isWarning($src)) {
            return $src;
        }
  
        $doc = new DomDocument;
        $doc->loadXML($src);
        $targets = $doc->getElementsByTagName('target');
  
        $tnodes = array();
  
        $found = false;
        for($i=0;$i < $targets->length; $i++) {
            $node = $targets->item($i);
            $tnodes[$node->nodeValue] = $node;  
        }
  
        foreach($assignments as $targetId=>$frameId) {
            if (!isset($tnodes[$targetId])) {    
                $tnodes[$targetId] = $doc->createElement('target',XMLDocument::xmlentities($targetId));
                $tnodes[$targetId] = $doc->documentElement->appendChild($tnodes[$targetId]);   
            }
            $tnodes[$targetId]->setAttribute('frame',$frameId);   
        }
      
        $src = $doc->saveXML();
        return $vfile->write($filename,$src);
    }
 
    /**
     * Load Targets
     * 
     * @param string $themeType Theme Type
     * @param string $themeId Theme ID
     * @return boolean|object True on success, error or warning otherwise
     * @access public
     */
    
    public static function _loadTargets($themeType = null, $themeId = null) 
    {
        if (empty($themeId)) {
            $themeId = VWP::getTheme();
        }

        if (empty($themeType)) {
            $themeId = VWP::getThemeType();
        }

  
        if (empty($themeId) || empty($themeType)) {
            return VWP::raiseError('No current theme!','VThemeTargets::_listTargets',500,false);
        }
  
        $basePath = VPATH_BASE.DS.'themes'.DS.$themeType.DS.$themeId;
  
        $targetDefFilename = $basePath.DS.'targets.xml';
  
        $vfile =& v()->filesystem()->file();
  
        if (!$vfile->exists($targetDefFilename)) {
            return VWP::raiseWarning('Theme does not support targets!','VThemeTargets::_listTargets',null,false);
        }
  
        $src = $vfile->read($targetDefFilename);
        if (VWP::isWarning($src)) {
            return $src;
        }
  
        $doc = new DomDocument;
        $result = $doc->loadXML($src);
  
        if (!$result) {
            return VWP::raiseWarning('Theme has invalid targets file!','VThemeTargets::_listTargets',null,false);   
        }
  
        if (!isset(self::$_targets[$themeType])) {
            self::$_targets[$themeType] = array();
        }
  
        if (!isset(self::$_targets[$themeType][$themeId])) {
            self::$_targets[$themeType][$themeId] = array();
        }
  
        $targetList = $doc->getElementsByTagName('target');
    
        for($t=0;$t < $targetList->length; $t++) {
  
            $targetNode = $targetList->item($t);
   
            $info = array();
            $len = $targetNode->childNodes->length;
         
            for($i=0;$i < $len; $i++) {
                $infoNode = $targetNode->childNodes->item($i);
                if ($infoNode->nodeType == XML_ELEMENT_NODE) {
                    $info[$infoNode->nodeName] = $infoNode->nodeValue;
                } 
            }
   
            if (isset($info["name"])) {
                $targetName = $info["name"]; 
                self::$_targets[$themeType][$themeId][$targetName] = new VThemeTarget($targetName,$themeType,$themeId,array("name"=>"ThemeTarget".ucfirst($targetName)));
            }
        }
  
        return true;     
    }

    /**
     * List Targets
     * 
     * @param string $themeId Theme ID
     * @return array|object Frame list on success, error or warning otherwise
     * @access public
     */
             
    public static function _listTargets($themeType = null, $themeId = null) 
    {
  
        if (empty($themeType)) {
           $themeType = VWP::getThemeType();
        }
  
        if (empty($themeId)) {
            $themeId = VWP::getTheme();   
        }
  
        if (empty($themeId) || empty($themeType)) {
            return VWP::raiseError('No current theme!','VThemeTargets::_listTargets',500,false);
        }
  
        if (!isset(self::$_targets[$themeType])) {
            self::$_targets[$themeType] = array();
        }
        if (!isset(self::$_targets[$themeType][$themeId])) {
            $result = self::_loadTargets($themeType,$themeId);
            if (VWP::isWarning($result)) {
                return $result;
            }
        }
        $result = array_keys(self::$_targets[$themeType][$themeId]);
  
        return $result;
    }
 
    /**
     * Get Target
     * 
     * @param string $frameName Frame Id
     * @param string $themeId Theme Id
     * @return object Frame on success, error or warning otherwise
     * @access public
     */
             
    public static function &_getTarget($targetName, $themeType = null, $themeId = null) 
    {
  
        if (empty($themeId)) {
            $themeId = VWP::getTheme();
        }

        if (empty($themeType)) {
            $themeType = VWP::getThemeType();
        }
  
        if (empty($themeId) || empty($themeType)) {
            return VWP::raiseError('No current theme!','VThemeFrame::_getFrame',500,false);
        } 
 
        if (!isset(self::$_targets[$themeType])) {
            self::$_targets[$themeType] = array();
        }
 
        if (!isset(self::$_targets[$themeType][$themeId])) {
            $err = self::_loadTargets($themeType,$themeId);
            if (VWP::isWarning($err)) {
                return $err;
            }   
        }
  
        $fullTargetName = $themeType . ':' . $themeId . ':' . $targetName;
  
        if (!isset(self::$_targets[$themeType][$themeId][$targetName])) {
            $err2 = VWP::raiseWarning('Target \'' . $fullTargetName . '\' not found!','VThemeTarget::_getTarget',null,false);
            return $err2;
        }
  
        return self::$_targets[$themeType][$themeId][$targetName];
    }
 
    /**
     * Get Theme Driver
     *
     * @param string $themeId Theme ID
     * @return object Returns theme driver if found, null otherwise
     * @access public    
     */
     
    function _getThemeDriver($themeId = null) 
    {
 
        if (empty($themeId)) {
            $themeId = $this->themeId;
        }
  
        if (empty($themeId)) {
            return VWP::raiseError('No current theme!',get_class($this),500,false);
        } 

        $themeType = $this->themeType;
  
        $themedriverFilename = VPATH_BASE.DS.'themes'.DS.$themeType.DS.$themeId.DS.'driver.php';
        $themedriverClassName = $themeId . 'ThemeDriver';
     
        if (file_exists($themedriverFilename)) {
            require_once($themedriverFilename);   
            $themeDriver = new $themedriverClassName();
        } else {
            $themeDriver = null;
        }
        return $themeDriver;
    }

    /**
     * Get Target Header
     * 
     * @param string $format Document Type
     * @return string Item Header
     * @access public
     */
        
    function _getTargetHeader($format = 'html') 
    {
        $driver = $this->_getThemeDriver();
        if (is_object($driver)) {
            $header = '';
            if (method_exists($driver,'getTargetHeader')) {
                $header = $driver->getTargetHeader($this->name,$format);
            }
        } else {  
            // otherwise check appropriate theme path .../frames/<framename>/item_header.php
            $header = '';
            $themedriverFilename = VPATH_BASE.DS.'themes'.DS.$this->themeType.DS.$this->themeId.DS.'targets'.DS.$this->name.DS.$format.DS.'target_header.php';
            $vfile =& v()->filesystem()->file();
            if ($vfile->exists($themedriverFilename)) {
                ob_start();
                require($themedriverFilename);    
                $header = ob_get_contents();
                ob_end_clean();
            } 
        }
  
        if (VWP::isWarning($header)) {
            $header->ethrow();
            $header = '';
        }
        return $header; 
    }

    /**
     * Get Target Footer
     * 
     * @param string $format Document Type
     * @return string Item Footer
     * @access public
     */
        
    function _getTargetFooter($format = 'html') 
    {
        $driver = $this->_getThemeDriver();
        if (is_object($driver)) {
            $footer = '';
            if (method_exists($driver,'getTargetFooter')) {
                $footer = $driver->getTargetFooter($this->name,$format);
            }
        } else {  
   
            $footer = '';
            $themedriverFilename = VPATH_BASE.DS.'themes'.DS.$this->themeType.DS.$this->themeId.DS.'targets'.DS.$this->name.DS.$format.DS.'target_footer.php';
            $vfile =& v()->filesystem()->file();
            if ($vfile->exists($themedriverFilename)) {
                ob_start();
                require($themedriverFilename);    
                $footer = ob_get_contents();
                ob_end_clean();
            }    
        }
  
        if (VWP::isWarning($footer)) {
            $footer->ethrow();
            $footer = '';
        }
        return $footer;  
    }
    
    
    /**
     * Get Item Header
     * 
     * @param string $app Application Name
     * @param string $widget Widget Name
     * @param string $format Document Type
     * @return string Item Header
     * @access public
     */
  
    function _getItemHeader($app,$widget,$format = 'html') 
    {
        $driver = $this->_getThemeDriver();
        if (is_object($driver)) {
            $header = '';
            if (method_exists($driver,'getItemHeader')) {
                $header = $driver->getItemHeader($this->name,$app,$widget,$format);
            }
        } else {  
            // otherwise check appropriate theme path .../frames/<framename>/item_header.php
            $header = '';
            $themedriverFilename = VPATH_BASE.DS.'themes'.DS.$this->themeType.DS.$this->themeId.DS.'targets'.DS.$this->name.DS.$format.DS.'item_header.php';
            $vfile =& v()->filesystem()->file();
            if ($vfile->exists($themedriverFilename)) {
                ob_start();
                require($themedriverFilename);    
                $header = ob_get_contents();
                ob_end_clean();
            } 
        }
  
        if (VWP::isWarning($header)) {
            $header->ethrow();
            $header = '';
        }
        return $header; 
    }

    /**
     * Get Item Footer
     * 
     * @param string $app Application Name
     * @param string $widget Widget Name
     * @param string $format Document Type
     * @return string Item Footer
     * @access public
     */
        
    function _getItemFooter($app,$widget,$format = 'html') 
    {
        $driver = $this->_getThemeDriver();
        if (is_object($driver)) {
            $footer = '';
            if (method_exists($driver,'getItemHeader')) {
                $footer = $driver->getItemFooter($this->name,$app,$widget);
            }
        } else {  
   
            $footer = '';
            $themedriverFilename = VPATH_BASE.DS.'themes'.DS.$this->themeType.DS.$this->themeId.DS.'targets'.DS.$this->name.DS.$format.DS.'item_footer.php';
            $vfile =& v()->filesystem()->file();
            if ($vfile->exists($themedriverFilename)) {
                ob_start();
                require($themedriverFilename);    
                $footer = ob_get_contents();
                ob_end_clean();
            }    
        }
  
        if (VWP::isWarning($footer)) {
            $footer->ethrow();
            $footer = '';
        }
        return $footer;  
    }
  
    /**
     * Get Widget Path
     * 
     * @param string $app Application Name
     * @param string $widget Widget Name
     * @return string Widget Path
     * @access public
     */
        
    function _getWidgetPath($app,$widget) 
    { 
        $path = VPATH_BASE.DS.'Applications'.DS.$app.DS.'widgets';
        return $path;  
    }
 
    /**
     * Decode Frame Item
     * 
     * @param object $fitem Frame Item     
     * @return array $item Item Info
     * @access public
     */
    
    function _decodeFrameItem($fitem) 
    {
    
        $item = $fitem->getProperties();
      
        $parts = explode('.',$item["widget"]);
        $item["app"] = array_shift($parts);
        $widget = implode('.',$parts);
        if (empty($widget)) {
            $widget = $item["app"];
        }
        $item["widget"] = $widget;    
        return $item;
    }
 
    /**
     * Display the target
     * 
     * @param mixed $tpl Optional
     * @return boolean|object True on success, error or warning otherwise
     * @access public
     */
    
    function display($tpl = null) 
    {

    	$this->_screens = array();
        $out = '';

        $stdio = new VStdio();
        $doc =& VWP::getDocument();
  
        $dtype = $doc->getDocumentType();
        
        $itemid = 0;

        $out = $this->_getTargetHeader($dtype);
        $stdio->write($out);
        
        foreach($this->_items as $fitem) { 
   
            $item = $this->_decodeFrameItem($fitem);
            
            if ($fitem->allowAccess($this->_frameId,(string)$itemid) && $item['disabled'] == 0 && $item['visible'] == 1) {
            
                $app = $item["app"];
                $widgetName = $item["widget"];
                           
                $appId = $fitem->widget;
         
                $out = $this->_getItemHeader($app,$widgetName,$dtype);
                $stdio->write($out);      

                $itemIO = new VStdio();
   
                $bcfg = array();
                $bcfg['target'] = $this->name;
                $bcfg['item'] = $itemid;
         
                $screenId = $doc->createScreenBuffer($bcfg);
      
                $this->_screens[] = $screenId;
            
                $itemIO->setOutBuffer($doc,$screenId);
         
                $user =& VUser::getCurrent();
                $shell =& $user->getShell();
   
                $env = array();
                $env['get'] = $item;
                $env['any'] = $item;
   
                $pscreenId = VEnv::getVar('screen',null,'post');
                
                if (($pscreenId === null) || ($pscreenId == $itemIO->getScreenId())) {            
                    $env["post"] = VEnv::getChannel('post');
                    foreach($env['post'] as $key=>$val) {
                        $env['any'][$key] = $val;
                    }                                                
                }

                $result = $shell->execute($appId,$env,$itemIO);
   
                $data = $itemIO->getOutBuffer();
      
                $stdio->write($data);
            
                $out = $this->_getItemFooter($app,$widgetName,$dtype);
                $stdio->write($out);
            }
            
            $itemid++;
        }      

        $out = $this->_getTargetFooter($dtype);
        $stdio->write($out);        
                        
        echo $stdio->getOutBuffer();  
  
        return true;
    }
 
    /**
     * Load Target Items
     *      
     * @access public
     */
    
    function _loadItems() 
    {
        $this->_items = array();
        $settings = $this->_getThemeSettings($this->themeType,$this->themeId);
        if (isset($settings["targets"][$this->name])) {
            $cfg = $settings["targets"][$this->name];
            if (isset($cfg["frame"])) {
                $f =& VUIFrame::getInstance($cfg["frame"]);
                if (VWP::isWarning($f)) {
                    $f->ethrow();
                } else {
                	$this->_frameId = $cfg["frame"];
                    $i = 0;
                    $item =& $f->getItem($i);
                    while(!VWP::isWarning($item)) {
                        $this->_items[] =& $f->getItem($i++);      
                        $item =& $f->getItem($i);
                    }          
                }
            }
        }  
    }
 
    /**
     * Class Constructor
     * 
     * @param string $frameName Frame ID
     * @param string $themeId Theme ID
     * @param array $options Frame Options
     * @access public
     */             

    function __construct($targetName, $themeType = null, $themeId = null, $options = array()) 
    {  
        if (empty($themeId)) {
            $themeId = VWP::getTheme();
        }

        if (empty($themeId)) {
            $themeType = VWP::getThemeType();
        }
   
        if ((!empty($themeId)) && ((!empty($themeType)))) 
        { 
            if (!isset(self::$_targets[$themeType])) {
                self::$_targets[$themeType] = array();
            }
            
            if (!isset(self::$_targets[$themeType][$themeId])) {
                self::$_targets[$themeType][$themeId] = array();
            }
                
            if (!isset(self::$_targets[$themeType][$themeId][$targetName])) {
                self::$_targets[$themeType][$themeId][$targetName] = true; // No inf loops
                self::$_targets[$themeType][$themeId][$targetName] = $this;
                self::$_targets[$themeType][$themeId][$targetName]->themeId = $themeId;
                self::$_targets[$themeType][$themeId][$targetName]->themeType = $themeType;  
                self::$_targets[$themeType][$themeId][$targetName]->_id = $targetName;
                self::$_targets[$themeType][$themeId][$targetName]->name = $targetName;
                self::$_targets[$themeType][$themeId][$targetName]->_loadItems();       
                parent::__construct($options);
            }      
        }    
    }

    // end class VThemeTarget
}
