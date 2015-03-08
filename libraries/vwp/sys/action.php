<?php

/**
 * Virtual Web Platform - Action System
 *  
 * This file provides actions for the notify system
 *        
 * @package VWP
 * @subpackage Libraries.System  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

/**
 * Virtual Web Platform - Action System
 *  
 * This class provides actions for the notify system
 *        
 * @package VWP
 * @subpackage Libraries.System  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

class VAction extends VObject 
{

    /**
     * @var integer $_id Action ID
     * @access private  
     */
       
    protected $_id = null;



    /**
     * @var string $event Event
     * @access public 
     */

    public $event;

    /**
     * @var string $eclass Event Class
     * @access public  
     */

    public $eclass;

    /**
     * @var mixed $callback Callback Action
     * @access public  
     */

    public $callback;    
    
    /**
     * Do Action
     * 
     * @param string $event Event
     * @param string $class Event class
     * @return boolean True on success
     * @access public
     */
  
    function doAction($params = array()) 
    {
    	
        if ((is_array($this->callback)) && (is_object($this->callback[0]))) {
            $fn = $this->callback[1];     
            $this->callback[0]->$fn($this->event,$this->eclass,$params);
            return true;
        }
  
        if (is_array($this->callback) && empty($this->callback[0])) {
            return false;
        }
  
        call_user_func($$this->callback,$event,$class,$params);
    
        return true;
    }
    
    /**
     * Class Constructor
     * 
     * @param integer $id Action ID
     * @param mixed $callback Callback
     * @param string $event Event
     * @param string $eclass Event class
     * @access public
     */       
 
    function __construct($id,$callback,$event,$eclass = null) 
    {
        parent::__construct();
        $this->_id = $id;
        $this->callback = $callback;
        $this->event = $event;
        $this->eclass = $eclass;  
    }
 
    // End class VAction
}
