<?php

/**
 * VWP Bzip2 archive driver 
 * 
 * This file contains the VWP BZip2 archive driver  
 *     
 * @package    VWP
 * @subpackage Libraries.Archive.Drivers
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

// Restricted access
class_exists('VWP') or die();


/**
 * Requires bz2 php extension
 */

VWP::RequireExtension('bz2');

/**
 * VWP Bzip2 archive driver 
 * 
 * This class provides the VWP BZip2 archive driver  
 *     
 * @package    VWP
 * @subpackage Libraries.Archive.Drivers
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License 
 */

class VArchiveBzip2 extends VArchive 
{

    /**
     * Bzip2 file data buffer
     *   
     * @var string $_data file buffer
     * @access private
     */
  
    protected $_data = null;
 
    /**
     * Extract a Bzip2 compressed file to a given path
     *
     * @param string $archive Path to Bzip2 archive to extract
     * @param string $destination Path to extract archive to
     * @param array $options Extraction options [unused]
     * @return boolean True if successful
     * @access public  
     */
  
    function extract($archive, $destination, $options = array ()) 
    {
        // Initialize variables
        $this->_data = null;

        if (!extension_loaded('bz2')) {
            return VWP::raiseWarning('BZip2 Not Supported',get_class($this).":extract",100,false);
        }

        $this->_data = $this->_vfile->read($archive);

        if (VWP::isWarning($this->_data)) {
            return VWP::raiseError('Unable to read archive',get_class($this).":extract",100,false);
        }
      
        $buffer = bzdecompress($this->_data);
        if (empty ($buffer)) {
            return VWP::raiseError('Unable to decompress data',get_class($this).":extract",100,false);
        }

        $wr = $this->_vfile->write($destination, $buffer);
        if (VWP::isWarning($wr)) {
            return VWP::raiseError('Unable to write archive',get_class($this).":extract",100,false);
        }
        return true;
    }
    
	// end class VArchiveBzip2 	
} 

