<?php

/**
 * Virtual Web Platform - Paging support
 *  
 * This file provides paging support
 * 
 * @package VWP
 * @subpackage Libraries.DBI  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License   
 */

// Restricted access
class_exists("VWP") or die();

/**
 * Virtual Web Platform - Paging support
 *  
 * This class provides paging support to database responses
 * 
 * @package VWP
 * @subpackage Libraries.DBI  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License   
 */

class VDatabasePaging extends VObject 
{

    /**
     * @var integer $_page_size Page size
     * @access private
     */
       
    protected $_page_size = null;

    /**
     * @var integer $_pagenum Current page number
     * @access private
     */
   
    protected $_pagenum = null;

    /**
     * @var integer $_pageing_mode Paging mode
     * @access private
     */
   
    protected $_paging_mode = "page";
 
    /**
     * Set page length
     * 
     * @param integer $size Page length
     * @access public
     */
           
    function setPageLength($size) 
    {
        $this->_page_size = $size;
        $this->_paging_mode = "page";
    }

    /**
     * Set page number
     * 
     * @param integer $pagenum Page number
     * @access public
     */
 
    function setPage($pagenum) 
    {
        $this->_pagenum = $pagenum;
        $this->_paging_mode = "page";
    }
    
    /**
     * Get paging mode
     * 
     * @return string Paging mode
     * @access public
     */
    
    function getMode() 
    {
    	return $this->_paging_mode;    
    }
    
    /**     
     * Get page number
     * 
     * @return integer Page Number
     * @access public
     */
    
    function getPageNum() 
    {
    	return $this->_pagenum;
    }
    
    /**
     * Get page size
     * 
     * @return integer Page size
     * @access public
     */
    
    function getPageSize() 
    {
    	return $this->_page_size;
    }
        
    // end class VDatabasePaging
} 
