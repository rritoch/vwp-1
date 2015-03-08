<?php

/**
 * VWP Events Model 
 *  
 * @package VWP
 * @subpackage Models
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @link http://www.vnetpublishing.com VNetPublishing.Com 
 * @copyright (c) Ralph Ritoch - All rights reserved
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License
 */

/**
 * Require Model Support
 */

VWP::RequireLibrary("vwp.model");

/**
 * Events Model 
 *  
 * @package VWP
 * @subpackage Models
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @link http://www.vnetpublishing.com VNetPublishing.Com 
 * @copyright (c) Ralph Ritoch - All rights reserved
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License
 */

class VWP_Model_Events extends VModel 
{

	/**
	 * Get path to events
	 * 
	 * @retrun string Path
	 * @access public	 
	 */
	
    function getEventBase() 
    {
        return VWP::getVarPath('vwp').DS.'events';
    }
 
    /**
     * Get list of enabled events
     * 
     * @param string $type Event type
     * @return array Enabled events
     * @access public
     */
    
    function getEnabled($type) 
    {
        $enabled = array();
        $eventBase = $this->getEventBase();
  
        if ($this->_vfile->exists($eventBase.DS.$type.DS.'active.txt')) {
            $data = $this->_vfile->read($eventBase.DS.$type.DS.'active.txt');
            $elist = explode("\n",$data);
            foreach($elist as $ent) {
                $ent = trim($ent);
                if ((!empty($ent)) && (!in_array($ent,$enabled))) {
                    array_push($enabled,$ent);
                }
            }   
        }
        return $enabled;
    }
 
    /**     
     * Enable events
     * 
     * @param string $type Event Type
     * @param array $events Events
     * @access public
     */
    
    function setEnabled($type,$events) 
    {
        $eventBase = $this->getEventBase();
        $data = implode("\r\n",$events);
        return $this->_vfile->write($eventBase.DS.$type.DS.'active.txt',$data);
    }

    /**     
     * Get ordered events
     * 
     * @param string $type Event Type
     * @return array Events in configuration order
     */

    function getOrder($type) 
    {
        $enabled = array();
        $eventBase = $this->getEventBase();
  
        if ($this->_vfile->exists($eventBase.DS.$type.DS.'active.txt')) {
            $data = $this->_vfile->read($eventBase.DS.$type.DS.'ordering.txt');
            $elist = explode("\n",$data);
            foreach($elist as $ent) {
                $ent = trim($ent);
                if ((!empty($ent)) && (!in_array($ent,$enabled))) {
                    array_push($enabled,$ent);
                }
            }   
        }
        return $enabled;
    }
 
    /**     
     * Set events order
     * 
     * @param string $type Event Type
     * @param array $events Events
     * @return boolean|object True on success, error or warning otherwise
     * @access public
     */
    
    function setOrder($type,$events) 
    {
        $eventBase = $this->getEventBase();
        $data = implode("\r\n",$events);
        return $this->_vfile->write($eventBase.DS.$type.DS.'ordering.txt',$data);
    } 
 
    /**
     * Enable Event
     * 
     * @param array|string Event or events
     * @return boolean|object True on success
     * @access poublic
     */
           
    function enable($id) 
    {
        $events = $id;
  
        // Convert event into an array
  
        if (is_string($events)) {
            $events = array($events);
        }
    
        // Organize events by type
  
        $e = array();  
        foreach($events as $event) {
            $parts = explode(":",$event);
            $type = array_shift($parts);
            $id = array_shift($parts);
            if (isset($e[$type])) {
                array_push($e[$type],$id);
            } else {
                $e[$type] = array($id);
            }   
        }
  
        // Process events
    
        foreach($e as $type=>$events) {
            $elist = $this->getEnabled($type);
   
            foreach($events as $event) {
                if (!in_array($event,$elist)) {
                    array_push($elist,$event); 
                }   
            }
            $this->setEnabled($type,$elist);
        }
  
        return true;
    }

    /**
     * Disable Event
     * 
     * @param array|string Event or events
     * @return boolean|object True on success
     * @access public
     */
           
    function disable($id) 
    {
        $events = $id;
  
        // Convert event into an array
  
        if (is_string($events)) {
            $events = array($events);
        }
  
        // Organize events by type
  
        $e = array();  
        foreach($events as $event) {
            $parts = explode(":",$event);
            $type = array_shift($parts);
            $id = array_shift($parts);
            if (isset($e[$type])) {
                array_push($e[$type],$id);
            } else {
                $e[$type] = array($id);
            }   
        }
  
        // Process events
    
        foreach($e as $type=>$events) {
            $elist = $this->getEnabled($type);
            $newlist = array();
   
            foreach($elist as $event) {
                if (!in_array($event,$events)) {
                    array_push($newlist,$event); 
                }   
            }
            $this->setEnabled($type,$newlist);
        }
  
        return true;
    }
  
    /**     
     * Set ordering of events
     * 
     * @param array $events
     * @access public
     */
    
    function setOrdering($events) 
    {
  
        // Organize events by type
  
        $e = array();  
        foreach($events as $event=>$order) {
            $parts = explode(":",$event);
            $type = array_shift($parts);
            $id = array_shift($parts);
            if (!isset($e[$type])) {
                $e[$type] = array();
            }
            $e[$type][$id] = $order;      
        }
  
        foreach($e as $type=>$events) {
            asort($events);         
            $events = array_keys($events);   
            $this->setOrder($type,$events);
        }
        return true;
    }
 
    /**     
     * Move an event
     * 
     * @param string $event Event Identifier
     * @param integer $dir Direction
     * @return boolean True on success
     * @access public
     */
    
    function move($event,$dir) 
    {
        if ($dir == 0) {
            return true; // nothing to do
        }
        $parts = explode(":",$event);
        $type = array_shift($parts);
        $id = array_shift($parts);
  
        $ordering = $this->getOrder($type);    
        if (!in_array($id,$ordering)) {
            if ($dir > 0) {
                array_push($ordering,$id);
            } else {
                array_unshift($ordering,$id);
            }
        } else {
            $o = $ordering;
            $ordering = array();
   
            if ($dir > 0) {
                foreach($o as $item) {
                    if ($item == $id) {
                        $hold = $id;
                    } else {
                        array_push($ordering,$item);
                        if (isset($hold)) {
                            array_push($ordering,$hold);
                            unset($hold);
                        }
                    }
                }
                if (isset($hold)) {
                    array_push($ordering,$hold);
                }   
            } else {
                $c = count($o);
                for($p=0;$p < $c;$p++) {
                    if ((($p + 1) < $c) && ($o[$p + 1] == $id)) {
                        array_push($ordering,$id);
                        array_push($ordering,$o[$p]);
                    } elseif (($p == 0) || ($o[$p] != $id)) {
                        array_push($ordering,$o[$p]);
                    }    
                }   
            }     
        }
        $this->setOrder($type,$ordering);
        return true;
    }
 
    /**
     * Get all events
     * 
     * @return array All events
     * @access public
     */
    
    function getAll($public = true) 
    {
        $eventBase = $this->getEventBase();  
        $types = $this->_vfolder->folders($eventBase);
  
        $event_list = array();
        foreach($types as $type) {
            $orig_items = $this->_vfolder->files($eventBase.DS.$type);
            $active = array();      
            if ($this->_vfile->exists($eventBase.DS.$type.DS.'active.txt')) {
                $data = $this->_vfile->read($eventBase.DS.$type.DS.'active.txt');
                $data = explode("\n",$data);
                foreach($data as $a) {
                    $a = trim($a);
                    if (!empty($a)) {
                        array_push($active,$a);
                    }
                }    
            }

            if ($this->_vfile->exists($eventBase.DS.$type.DS.'ordering.txt')) {
                $items = array();
                $data = $this->_vfile->read($eventBase.DS.$type.DS.'ordering.txt');
                $data = explode("\n",$data);
                foreach($data as $a) {
                    $a = trim($a);
                    if (!empty($a)) {
                        if ((!in_array($a.'.php',$items)) && (in_array($a.'.php',$orig_items))) {
                            array_push($items,$a.'.php');
                        }
                    }
                }
    
                foreach($orig_items as $a) {
                    if (!in_array($a,$items)) {
                        array_push($items,$a);
                    }
                }    
            } else {
                $items = $orig_items;
            }   
   
            $ctr = 2;
            foreach($items as $item) {    
                if (substr($item,strlen($item) - 4) == ".php") {
                    $name = substr($item,0,strlen($item) - 4);
     
                    if (in_array($name,$active)) {
                        $enabled = 1;
                    } else {
                        $enabled = 0;
                    }
     
                    $iteminfo = array(
                                       "_id"=>$type.':'.$name,
                                       "_order"=>$ctr,
                                       "name"=>$name,
                                       "type"=>$type,
                                       "_enabled"=>$enabled,
                                );
                    array_push($event_list,$iteminfo);
                }
                $ctr += 2;
            }  
        }
  
        return $event_list;
    }
    
    // end class VWP_Model_Events
} 