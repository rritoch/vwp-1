<?php

/**
 * Virtual Web Platform - Event System
 *  
 * This file provides the event system        
 * 
 * @package VWP
 * @subpackage Libraries.System  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License   
 */

/**
 * Require filesystem support
 */

VWP::RequireLibrary('vwp.filesystem');

/**
 * Virtual Web Platform - Event System
 *  
 * This class provides the event system and base
 * class for all event listeners.         
 * 
 * @package VWP
 * @subpackage Libraries.System  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License   
 */

class VEvent extends VObject 
{
 
    /**
     * Event Cache
     * 
     * @var array $_events Event cache
     * @access private  
     */
         
    static $_events = array();

    /**
     * Event stop flag
     * 
     * @var array $_stop Stop processing event
     * @access private  
     */
 
    static $_stop = false;
 
    /**
     * Event source file
     * 
     * @var string $_source Source file
     * @access private
     */
           
    protected $_source = null;
 
    /**
     * Stop processing event
     * 
     * @access public
     */
         
    public static function stop() 
    {
        self::$_stop = true;
    }
 
    /**
     * Set source filename
     *   
     * @access public
     */
       
    function setSource($filename) 
    {
        $this->_source = $filename;
    }
 
    /**
     * Load an event
     * 
     * @param string $type Event Type
     * @param string $id Event name
     * @param string $filename Event source file
     * @return object Event on success, error or warning on failure  
     */
             
    public static function loadEvent($type,$id,$filename) 
    {
        $type = strtolower($type);
        $id = strtolower($id);
        $className =   ucfirst($id) . 'Event' . ucfirst($type);
  
        if (!class_exists($className)) {
            $vfile =& VFilesystem::local()->file();
   
            if (!$vfile->exists($filename)) {
                return VWP::raiseWarning("File not found [$filename]","VEvent::loadEvent",ERROR_FILENOTFOUND,false);
            }
            require_once($filename);
        }
        if (class_exists($className)) {
            $e = new $className;
            $e->setSource($filename);
            return $e;
        }
        return VWP::raiseWarning("Event not found","VEvent::loadEvent",ERROR_CLASSNOTFOUND,false);    
    }
 
    /**
     * Get an instance of an event
     * 
     * @param string $type Event type
     * @param string $id Event name
     * @return object Event on success, error or warning on failure
     * @access public  
     */
           
    public static function &getInstance($type = null, $id = null) 
    {
        static $base;
    
        if (empty($type) || empty($id)) {
            if (!isset($base)) {
                $base = new VEvent;
            }
            return $base;
        }
  
        $type = strtolower($type);
        $id = strtolower($id);
  
        if (!isset(self::$_events[$type])) {
            self::$_events[$type] = array();
        }
  
        if (!isset(self::$_events[$type][$id])) {
            $filename = VWP::getVarPath('vwp').DS.'events'.DS.$type.DS.$id.'.php';
            self::$_events[$type][$id] = self::loadEvent($type,$id,$filename);   
        }
  
        return self::$_events[$type][$id];     
    }
 
    /**
     * Get Event Handler ID
     * 
     * @return string|null Event ID
     * @access public  
     */
         
    function getId() 
    {
        $class = strtolower(get_class($this));
        $parts = explode("event",$class);
        if (count($parts) < 2) {
            return null;
        }
        $type = array_pop($parts);
        $name = implode("event",$parts);
        return ucfirst($name);    
    }

    /**
     * Get Event Handler Type
     * 
     * @return string|null Event Type
     * @access public  
     */
 
    function getType() 
    {
        $class = strtolower(get_class($this));
        $parts = explode("event",$class);
        if (count($parts) < 2) {
            return null;
        }
        $type = array_pop($parts);
        $name = implode("event",$parts);
        return ucfirst($type); 
    }
 
    /**
     * Install event handler
     * 
     * @access public
     */
         
    function install() 
    {
        $id = strtolower($this->getId());
        $type = strtolower($this->getType());
  
        if (!isset(self::$_events[$id][$type])) {
            self::$_events[$id][$type] = $this;
        }
  
        if (!empty($this->_source)) {
            $dest = VWP::getVarPath('vwp').DS.'events'.DS.$type.DS.$id.'.php';
            if ($this->_source != $dest) {
                $vfile =& VFilesystem::local()->file();
                $vfile->copy($this->_source,$dest);
            }   
        }
    }
 
    /**
     * Uninstall event handler
     * 
     * @access public
     */
         
    function uninstall() 
    {
        $id = strtolower($this->getId());
        $type = strtolower($this->getType());
  
        if (isset(self::$_events[$id][$type])) {
            unset(self::$_events[$id][$type]);
        }
  
        $vfile =& VFilesystem::local()->file();
  
        $source = VWP::getVarPath('vwp').DS.'events'.DS.$type.DS.$id.'.php';       
        $vfile->delete($source);   
    }
 
    /**
     * Get active events
     * 
     * @param string $type Event type
     * @return array active events
     * @access public
     */
 
    public static function getActive($type) 
    {
    	static $cache_active = array();
    	
    	if (!isset($cache_active[$type])) {
    		$active = array();
    		$vfile =& VFilesystem::local()->file();
    		$epath = VWP::getVarPath('vwp').DS.'events'.DS.$type;
    		
            if ($vfile->exists($epath.DS.'active.txt')) {
               $data = $vfile->read($epath.DS.'active.txt');
               $o1 = explode("\n",strtolower($data));
               foreach($o1 as $val) {
                   $id = trim($val);
                   if (!empty($id)) {
                       if (!in_array($id,$active)) {
                           array_push($active,$id);
                       }
                   }   
               }
           }
           $cache_active[$type] = $active;
    	}
    	
    	return $cache_active[$type];
    }
 
    /**
     * Get event order
     *
     * @param string $type Event type
     * @return array ordering
     */
 
    public static function getOrder($type) 
    {
        static $order_cache = array();

        if (!isset($order_cache[$type])) {
        	$vfile =& VFilesystem::local()->file();
        	$epath = VWP::getVarPath('vwp').DS.'events'.DS.$type;
        	
            $ordering = array();
  
            if ($vfile->exists($epath.DS.'ordering.txt')) {
                $data = $vfile->read($epath.DS.'ordering.txt');
                $o1 = explode("\n",$data);
                foreach($o1 as $val) {
                    $id = trim($val);
                    if (!empty($id)) {
                        array_push($ordering,$id);
                    }   
                }
            }

            $active = self::getActive($type);
            
            $order = array();
            $orig_order = array();
            if (isset(self::$_events[$type])) {
                $orig_order = array_keys(self::$_events[$type]);
            }
    
            foreach($ordering as $id) {
               if (
                   (!in_array($id,$order))  
                   && (in_array($id,$orig_order)) 
                   && (in_array($id,$active))
                ) {
                   array_push($order,$id);
               }
            }
  
            foreach($orig_order as $id) {
                if (
                    (!in_array($id,$order))
                    && (in_array($id,$active))
                   ) {
                    array_push($order,$id);
                }
            }
            $order_cache[$type] = $order;     	
        }

        return $order_cache[$type];
    }
 
    /**
     * Dispatch an event
     * 
     * @param string $type Event type
     * @param string $name Event name
     * @param mixed $options Event options      
     * @return array Event result
     */
           
    public static function dispatch_event($type,$name,$options = null) 
    {

        // Initialize variables

        $fs =& VFilesystem::local();	
        if (VWP::isWarning($fs)) {
             $fs->ethrow();
             return array("result"=>false,"trace"=>array(array('system'=>'EVENTS','result'=>false)));                
        }
 
        $type = strtolower($type);
        $vfolder =& VFilesystem::local()->folder();     
        $vfile =& VFilesystem::local()->file();
    
        $epath = VWP::getVarPath('vwp').DS.'events'.DS.$type;
        $efiles = $vfolder->files($epath);
        $registered_events = array();
        $result = false;
        $rvals = array();
        $evt = "on".$name;
        $default_evt = "_onEvent";
        $old_stop = self::$_stop;
        self::$_stop = false;
  
        // Get active events
  
        $active = self::getActive($type);
      
        // Cache active events
        if (!VWP::isWarning($efiles)) 
        {
            foreach($efiles as $fn) {   
                if (substr($fn,strlen($fn) - 4) == ".php") {
                    $id = strtolower(substr($fn,0,strlen($fn) - 4));
                    if (in_array($id,$active)) {
                        // cache...
                        self::getInstance($type,$id);      
                    }  
                }
            }
        }
  
        // setup ordering
    
        $order = self::getOrder($type);
   
        // Fire events
      
        foreach($order as $id) 
        {
            $ob = self::$_events[$type][$id];
            if (!self::$_stop) {
                if ((!VWP::isWarning($ob)) && (is_object($ob))) {
                    $fired = false;
                    if (method_exists($ob,$evt)) {
                        $r = $ob->$evt($options,$result);
                        $v = array(
                            "system"=>$id,
                            "result"=>$r
                           );      
                        array_push($rvals,$v);       
                    } elseif (method_exists($ob,$default_evt)) {
                        $r = $ob->$default_evt($type,$name,$options,$result);
                        $v = array(
                             "system"=>$id,
                             "result"=>$r
                            );       
                        array_push($rvals,$v);       
                    }           
                }
            }  
        }
  
        // Complete
  
        self::$_stop = $old_stop;
        return array("result"=>$result,"trace"=>$rvals);
    }
 
    // end class VEvent 
} 
