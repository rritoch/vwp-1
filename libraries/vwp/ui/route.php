<?php

/**
 * Virtual Web Platform - SEF URL Routing
 *  
 * This file provides the URL Routing
 *  
 * @todo Implement aliasing of routes to hide application names       
 * @package VWP
 * @subpackage Libraries.UI  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License
 */

/**
 * Require URI Support
 */

VWP::RequireLibrary('vwp.uri');

/**
 * Require filesystem support
 */

VWP::RequireLibrary('vwp.filesystem');


/**
 * Virtual Web Platform - SEF URL Routing
 *  
 * This file provides the URL Routing
 *        
 * @package VWP
 * @subpackage Libraries.UI  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License
 */

class VRoute extends VObject
{

    /**
     * @var string $_app_root Application root segment
     * @access private  
     */
     
    static $_app_root = 'app';
 
    /**
     * @var string $_sef_mode SEF Mode
     * @access private
     */     

    static $_sef_mode;
  
    /**
     * File object
     * @var object $_vfile File object
     */
       
    static $_vfile;
 
    /**
     * App Routers 
     */
  
    static $_app_routers = array();
 
    /**
     * Widget Routers
     */
     
    static $_widget_routers = array();
 
    /**
     * Encode route    
     * 
     * Child routers should override this class
     *  taking an array of variables by refrence as an argument
     *  and returning an array of segments, removing
     *  any variables which no longer need to be 
     *  in the query string.
     *            
     * @param string $uri URI (not destructed but must be in a variable)
     * @return string Uri  
     */

    public function encode(&$uri) 
    {
        static $in_encode = 0;
  
        if ($in_encode > 0) {
            return array(); // no recursion
        }
  
        $in_encode++;
     
        if ((self::$_sef_mode != 'sef') && (self::$_sef_mode != 'rw_sef')) {
            $in_encode--;
            return $uri;
        }
      
        if (substr($uri,0,9) !== 'index.php') {
            $in_encode--;
            return $uri;
        }
  
        $info = VURI::parse($uri);
        if (!isset($info["query"])) {
            $info["query"] = '';
        }
    
        $vars = VURI::parseQuery($info["query"]);
    
        $rmapp = false;
        $segments = array();
  
        if (isset($vars['app']) && is_string($vars['app']) && (!empty($vars['app']))) {
            $app = $vars['app'];
      
            $segments[] = '';
            $segments[] = self::$_app_root;
            $segments[] = $app;
   
            //  Process route
   
            if (isset($vars['widget'])) {
                $widget = $vars['widget'];
            }
            
            $appRouter =& self::getInstance($app);   
            if (!VWP::isWarning($appRouter)) {
                $app_segs = $appRouter->encode($vars);
                foreach($app_segs as $seg) {
                    $segments[] = $seg;
                }
            }
   
            if (isset($widget) && (!empty($widget))) {
                $widgetRouter =& self::getInstance($app,$widget);
                if (!VWP::isWarning($widgetRouter)) {
                    $widget_segs = $appRouter->encode($vars);
                    foreach($widget_segs as $seg) {
                        $segments[] = $seg;
                    }
                }   
            }
   
            // Clear app
   
            unset($vars['app']);
        }

        $format = isset($vars['format']) ? $vars['format'] : 'html';
  
        unset($vars['format']);
        $path_extra = '';
        if (count($segments) > 0) {
  	
             $path_extra = implode('/',$segments).'.'.$format;
        } else {
  	         $path_extra = '/index.'.$format;
        }
  
        $query = VURI::createQuery($vars);
        $query_prefix = '';
        if (strlen($query) > 0) {
            $query_prefix = '?';
        }
  
        $in_encode--;        
        return rtrim(strtolower(VURI::base()),'/') .'/index.php' . $path_extra.$query_prefix.$query;    
    }  
 
    /**
     * Decode Route
     *      
     * @param array $segments Path segments (will be destructed)
     * @return array Query
     * @access public
     */
           
    function decode(&$segments) 
    {
        $vars = array();
        if (count($segments) < 2) {
            return $vars;
        }
  
        //This one better be empty!!!
        array_shift($segments);
   
        $root = array_shift($segments);
        if (strlen($root) < 1) {
           // Short circuit
            return $vars;
        }
  
        if ($root == self::$_app_root) {
            // Process app path
            if (count($segments) < 1) {
                return $vars;	
            }
            $app = array_shift($segments);
            $vars["app"] = $app;
   
            // Process application router
          
            $appRouter =& self::getInstance($app);
            if (!VWP::isWarning($appRouter)) {     
                $appVars = $appRouter->decode($segments);
                $vars = array_merge($vars,$appVars);
            }         
   
            if (isset($vars["widget"])) {
                if (!empty($vars["widget"])) {
    
                    // Process widget router    
                    $widgetRouter =& self::getInstance($app,$vars["widget"]);
                    if (!VWP::isWarning($widgetRouter)) {
                        $widgetVars = $widgetRouter->decode($segments);
                        $vars = array_merge($vars,$appVars);
                    }    
                }    
            }
   
            return $vars;
        }
  
        return $vars; 
    }

    /**
     * Get router
     * 
     * @param string $app Application ID
     * @param string $widget Widget ID
     * @return VRoute Router
     * @access public
     */
    
    public static function &getInstance($app = null,$widget = null) 
    {
        static $rootRouter;
  
        if (!isset($routeRouter)) {
            $rootRouter = new VRoute;
        }
  
        if (empty($app)) {
            return $rootRouter;
        }
   
        if (empty($widget)) {
            if (!isset(self::$_app_routers[$app])) { 
                $routefile = VPATH_ROOT.DS.'Applications'.DS.$app.DS.'router.php';
                $routeclass = ucfirst($app).'VRoute';
                if (self::$_vfile->exists($routefile)) {
                    require_once($routefile);
                    if (class_exists($routeclass)) {
                        self::$_app_routers[$app] = new $routeclass;     
                    } else {
                        self::$_app_routers[$app] = VWP::raiseWarning("Router $routeclass not found!",'VRoute',ERROR_CLASSNOTFOUND,false);     
                    }
                } else {
                    self::$_app_routers[$app] = VWP::raiseWarning("Router $routeclass not found!",'VRoute',ERROR_FILENOTFOUND,false);
                }
            }
            return self::$_app_routers[$app];
        }


        if (!isset(self::$_widget_routers[$app])) {
            self::$_widget_routers = array();
        }
  
        if (!isset(self::$_widget_routers[$app][$widget])) {
            $wsegs = explode('.',$widget);
            $widgetPath = 'widgets'.DS.implode(DS.'widgets'.DS,$wsegs);         
            $routefile = VPATH_ROOT.DS.'Applications'.DS.$app.DS.$widgetPath.DS.'router.php';
   
            $w = ucfirst(array_pop($wsegs));   
            $parent = ucfirst($app);
            foreach($wsegs as $p) {
                $parent .= ucfirst($p);
            }
   
            $routeclass = $parent.'VRoute'.$w;
            if (self::$_vfile->exists($routefile)) {
                require_once($routefile);
                if (class_exists($routeclass)) {
                    self::$_widget_routers[$app][$widget] = new $routeclass;         
                } else {
                    self::$_widget_routers[$app][$widget] = VWP::raiseWarning("Router $routeclass not found!",'VRoute',ERROR_CLASSNOTFOUND,false);    
                }
            } else {
                self::$_widget_routers[$app][$widget] = VWP::raiseWarning("Router $routeclass not found!",'VRoute',ERROR_FILENOTFOUND,false);
            }
        }
  
        return self::$_widget_routers[$app][$widget];  
    }
 
    /**
     * Initialize Router
     * 
     * @access private
     */   

    public static function _init() 
    {
        if (!isset(self::$_vfile)) {
            self::$_vfile =& VFilesystem::local()->file();
        }
        if (!isset(self::$_sef_mode)) {
            $config = VWP::getConfig();
            self::$_sef_mode = $config->sef_mode;  
        }   
    }
    // end class VRoute 
} 

VRoute::_init();
