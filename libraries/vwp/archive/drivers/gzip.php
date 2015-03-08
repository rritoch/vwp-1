<?php

/**
 * VWP GZip archive driver 
 * 
 * This file contains the VWP GZip archive driver  
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
 * Requires zlib php extension
 */

VWP::RequireExtension('zlib');

/**
 * VWP GZip archive driver 
 * 
 * This class provides the VWP GZip archive driver  
 *     
 * @package    VWP
 * @subpackage Libraries.Archive.Drivers
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

class VArchiveGzip extends VArchive 
{
	
    /**
     * Gzip file flags.
     *   
     * @var array $_flags
     * @access private
     */

    protected $_flags = array (
                          'FTEXT' => 0x01,
                          'FHCRC' => 0x02,
                          'FEXTRA' => 0x04,
                          'FNAME' => 0x08,
                          'FCOMMENT' => 0x10
                         );

    /**
     * Gzip file data buffer
     *   
     * @var string $_data Data buffer
     * @access private
     */
  
    protected $_data = null;
    
    /**
     * Get file data offset for archive
     *  
     * @return int Data position marker for archive
     * @access private    
     */

    function _getFilePosition() 
    {
        // gzipped file... unpack it first
        $position = 0;
        $info = @ unpack('CCM/CFLG/VTime/CXFL/COS', substr($this->_data, $position +2));
        if (!$info) {
            return VWP::raiseError('Unable to decompress data',get_class($this).":extract",100,false);
        }

        $position += 10;

        if ($info['FLG'] & $this->_flags['FEXTRA']) {
            $XLEN = unpack('vLength', substr($this->_data, $position +0, 2));
            $XLEN = $XLEN['Length'];
            $position += $XLEN +2;
        }

        if ($info['FLG'] & $this->_flags['FNAME']) {
            $filenamePos = strpos($this->_data, "\x0", $position);
            $filename = substr($this->_data, $position, $filenamePos - $position);
            $position = $filenamePos +1;
        }

        if ($info['FLG'] & $this->_flags['FCOMMENT']) {
            $commentPos = strpos($this->_data, "\x0", $position);
            $comment = substr($this->_data, $position, $commentPos - $position);
            $position = $commentPos +1;
        }

        if ($info['FLG'] & $this->_flags['FHCRC']) {
            $hcrc = unpack('vCRC', substr($this->_data, $position +0, 2));
            $hcrc = $hcrc['CRC'];
            $position += 2;
        }

        return $position;
    }
    
    /**
     * Extract a Gzip compressed file to a given path
     *  
     * @param string $archive Path to ZIP archive to extract
     * @param string $destination Path to extract archive to
     * @param array $options Extraction options [unused]
     * @return boolean True if successful
     * @access public  
     */
 
    function extract($archive, $destination, $options = array ()) 
    {
        // Initialize variables
        $this->_data = null;

        $archive = v()->filesystem()->path()->clean($archive);
        $destination = v()->filesystem()->path()->clean($destination);
  
        if (!extension_loaded('zlib')) {
            return VWP::raiseWarning('Zlib Not Supported',get_class($this).":extract",100,false);
        }
  
        $this->_data = $this->_vfile->read($archive);
        if (VWP::isWarning($this->_data)) {
            return VWP::raiseError('Unable to read archive',get_class($this).":extract",100,false);
        }

        $position = $this->_getFilePosition();
        if (VWP::isWarning($position)) {
            return $position;
        }
  
        $buffer = gzinflate(substr($this->_data, $position, strlen($this->_data) - $position));
        if (empty ($buffer)) {
            return VWP::raiseError('Unable to decompress data',get_class($this).":extract",100,false);
        }

        $wr = $this->_vfile->write($destination, $buffer);
 
        if (VWP::isWarning($wr)) {
            return VWP::raiseError('Unable to write archive',get_class($this).":extract",100,false);
        }
        return true;
    }
    
	// end class VArchiveGzip
} 

