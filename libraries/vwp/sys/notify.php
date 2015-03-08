<?php

/**
 * Virtual Web Platform - Notify System
 *  
 * This file provides the Notify System
 *        
 * @package VWP
 * @subpackage Libraries.System  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License   
 */

class_exists('VWP') || die();

/**
 * Virtual Web Platform - Notify System
 *  
 * This class provides the Notify System
 *        
 * @package VWP
 * @subpackage Libraries.System  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License
 */


class VNotify extends VObject 
{

    /**
     * @var array $events Event buffer
     * @access private  
     */
     
    static $events = array();

    /**
     * @var array $event_ctr Action Counter
     * @access private  
     */

    static $event_ctr = 0;

    /**
     * Add an event listener
     * 
     * @param mixed $callback Callback function
     * @param string $event Event
     * @param string $eclass Event Class
     * @return VAction Action object          
     */
     
    public static function &addAction($callback,$event,$eclass = null) 
    {
        self::$event_ctr++;
        $id = self::$event_ctr;
     
        $cl = $eclass;
  
        if (empty($cl)) {
            $cl = "_";
        }
  
        if (!isset(self::$events[$cl])) {
            self::$events[$cl] = array();
        }
  
        if (!isset(self::$events[$cl][$event])) {
            self::$events[$cl][$event] = array();
        }
  
        self::$events[$cl][$event][$id] = new VAction($id,$callback,$event,$eclass);   
        return self::$events[$cl][$event][$id];
    }

    /**
     * Remove an event listener
     * 
     * @param object $action Action to be removed
     * @access public    
     */

    function removeAction($action) 
    {
 
        $eclass = $action->eclass;
        $event = $action->event;
        $id = $action->_id;
  
        $cl = $eclass;
        if (empty($cl)) {
            $cl = "_";
        }
  
        if (isset(self::$events[$cl][$event][$id])) {
            unset(self::$events[$cl][$event][$id]);
            $result = true;
        } else {
            $result = VWP::raiseWarning('Event not found!',null,'VNotify',false);
        }
  
        if (count(array_keys(self::$events[$cl][$event])) < 1) {
            unset(self::$events[$cl][$event]);
        }

        if (count(array_keys(self::$events[$cl])) < 1) {
            unset(self::$events[$cl]);
        }
  
        return $result;
    }
 
    /**
     * Notify that an event has occurred
     * 
     * @param string $event Event
     * @param string $eclass Event class
     * @return integer Number of actions called
     * @access public        
     */
     
    public static function Notify($event,$eclass = null) 
    {
        $ctr = 0;
  
        $cl = $eclass;
        if (empty($cl)) {
            $cl = "_";
        }
  
        if (isset(self::$events[$cl][$event])) {
            foreach($events[$cl][$event] as $id=>$action) {
                if ($action->doAction()) {
                    $ctr++;
                }
            }
        }
        return $ctr;
    }
    
    // end class VNotify
} 
