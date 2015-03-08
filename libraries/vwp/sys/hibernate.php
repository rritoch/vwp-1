<?php

/**
 * Virtual Web Platform - Hibernate System
 *  
 * This file provides the hibernate system        
 * 
 * @package VWP
 * @subpackage Libraries.System  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License   
 */

/**
 * Require Event Support
 */

VWP::RequireLibrary('vwp.sys.events');

/**
 * Virtual Web Platform - Hibernate System
 *  
 * This class provides the hibernate system        
 * 
 * @package VWP
 * @subpackage Libraries.System  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License   
 */

class VHibernateDriver extends VObject 
{
 
    /**
     * Cleanup Tasks
     * 
     * @var array $_cleanup_tasks Cleanup tasks
     * @access private  
     */
     
    static $_cleanup_tasks = array();
 
    /**
     * In cleanup mode flag
     * 
     * @var boolean $_in_cleanup Cleanup active
     * @access private
     */
       
    static $_in_cleanup = false;

     /**
      * @var boolean $_cur_cleanup_task Current cleanup task
      * @access private
      */
       
    static $_cur_cleanup_task = null;

    /**
     * Sleep signature
     * 
     * @var mixed $_signature secure sleep signature
     * @access private
     */
              
    private static $_signature = null;

    /**
     * System Wakeup
     * 
     * @return string sleep signature
     * @access public
     */
              
    public static function wake() 
    {
        
        if (self::$_signature !== null) {
            return VWP::raiseError('Duplicate wake request!',__CLASS__);
        }
        
        list($usec, $sec) = explode(' ', microtime());
        $seed = (float) $sec + ((float) $usec * 100000);
        srand($seed);
        self::$_signature = rand(1,255) * rand(1,255) + 1;
        
        VEvent::dispatch_event("hibernate","load");
        
        VNotify::Notify('wake',__CLASS__);
        
        return self::$_signature;        
    }
            
    /**
     * Add Cleanup Task
     * 
     * @param string|array Task function
     * @param string|array Abort Function
     * @access public           
     */    

    public static function addCleanupTask($taskFunc, $abortFunc = null) 
    {
        array_push(self::$_cleanup_tasks,array($taskFunc,$abortFunc));
    }
     

    /**
     * Process cleanup abort task
     *   
     * @access private    
     */
    
    public static function _cleanupAbort() 
    {
        if (self::$_in_cleanup) {        
            $abortFunc =& self::$_cur_cleanup_task[1];
        
            if (($abortFunc !== null) && ($abortFunc !== false)) {
                if (
                 is_array($abortFunc) && 
                 (count($abortFunc) > 1) && 
                 is_object($abortFunc[0]) &&
                 method_exists($abortFunc[0],$abortFunc[1])
                 ) {            
                    $abortFunc[0]->$abortFunc[1]();
                } else {
                    if (is_callable($abortFunc)) {
                        call_user_func($abortFunc);
                    }
                }  
            }
        }
        self::$_in_cleanup = false; 
    }


    /**
     * Hibernate system
     * 
     * @param mixed $key Sleep key       
     * @access private    
     */

    public static function sleep($key) 
    {
        if (self::$_signature != $key) {
            return VWP::raiseError('Invalid sleep request!',__CLASS__);
        }
    
        VEvent::dispatch_event("hibernate","unload");
        
        VNotify::Notify('sleep',__CLASS__);
               
        self::$_signature = 'A';
        
        self::$_in_cleanup = true; // inside cleanup
        register_shutdown_function(array(__CLASS__,'_cleanupAbort'));

        // Ignore STOP!  
        VWP::noStop();
  
  
        // Process cleanup tasks
        
        foreach(self::$_cleanup_tasks as $ctask) {   
            self::$_cur_cleanup_task = $ctask;
   
            if (
             is_array(self::$_cur_cleanup_task[0]) && 
             (count(self::$_cur_cleanup_task[0]) > 1) && 
             is_object(self::$_cur_cleanup_task[0][0]) &&
             method_exists(self::$_cur_cleanup_task[0][0],self::$_cur_cleanup_task[0][1])
             ) {
                // Object call
                    
                $func = self::$_cur_cleanup_task[0][1];        
                self::$_cur_cleanup_task[0][0]->$func();     
            } else {
                if (is_callable(self::$_cur_cleanup_task[0])) {
                    call_user_func(self::$_cur_cleanup_task[0]);
                }
            }     
        }

        self::$_in_cleanup = false; // outside cleanup

        // ALLOW Stop
        VWP::noStop(false);
 
    }
    
    // End class VHibernateDriver
}
