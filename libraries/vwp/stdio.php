<?php

/**
 * Virtual Web Platform - Stdio Buffer
 *  
 * This file provides the stdio buffer        
 * 
 * @package VWP
 * @subpackage Libraries  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

// Restrict access
class_exists('VWP') || die(); // restrict access

/**
 * Virtual Web Platform - Session Manager
 *  
 * This class provides the stdio buffer        
 * 
 * @package VWP
 * @subpackage Libraries  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

class VStdio extends VObject 
{

    /**
     * Output buffer
     * 
     * @var string $_out_buffer Output Buffer
     * @access private
     */
                  
    private $_out_buffer = '';
    
    /**
     * Screen ID
     *      
     * @var string $_screenId
     * @access private
     */
    
    private $_screenId = null;

    /**
     * Screen Document
     *      
     * @var string $_screenDoc
     * @access private
     */    
    
    private $_screenDoc = null;
    
    /**     
     * Get Screen ID
     * 
     * @return string Screen ID
     * @access public
     */
    
    public function getScreenId() {
        return $this->_screenId;
    }
    
    /**
     * Set current output buffer
     * 
     * @param VDocument $doc Document object
     * @param string $screenId Screen ID
     * @access public
     */
    
    public function setOutBuffer(&$doc,$screenId) {
         $this->_screenId = $screenId;    
         $this->_screenDoc =& $doc;
    }
    
    /**
     * Write to output buffer
     *          
     * @param string $data Data to write
     * @access public     
     */
              
    public function write($data) 
    {
        
        if (empty($this->_screenId)) {
            $this->_out_buffer .= $data;
        } else {                        
            $this->_screenDoc->appendBuffer($this->_screenId,$data);
        }
    }

    /**
     * Get contents of output buffer
     *          
     * @return string Output buffer content
     * @access public     
     */
    
    public function getOutBuffer() 
    {
        if (empty($this->_screenId)) {
            return $this->_out_buffer;
        }        
        return $this->_screenDoc->getBuffer($this->_screenId);        
    }
    
    // end VStdio class
}