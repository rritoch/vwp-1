<?php

/**
 * Virtual Web Platform - Filesystem Driver
 *  
 * This file provides Filesystem Driver Support   
 * 
 * @package VWP
 * @subpackage Libraries.Filesystem
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License   
 */


/**
 * Virtual Web Platform - Filesystem Driver
 *  
 * This file provides Filesystem Driver Support   
 * 
 * @package VWP
 * @subpackage Libraries.Filesystem
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License   
 */
 

class VFilesystemDriver extends VObject 
{
	
	/**	 
	 * Filesystem
	 * 
	 * @var object $_fs Filesystem
	 * @access private
	 */
	
	protected $_fs;
	
    /**
     * Class Constructor
     * 
     * @param VFilesystem $filesystem
     * @access public
     */
 
    function __construct(&$filesystem) 
    {
    	parent::__construct();
        $this->_fs =& $filesystem;	
    }
		
}
