<?php

/**
 * VWP Tar driver 
 * 
 * This file contains the VWP Tar driver  
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
 * VWP Tar driver 
 * 
 * This class provides the VWP Tar driver  
 *     
 * @package    VWP
 * @subpackage Libraries.Archive.Drivers
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License 
 */
 
class VArchiveTar extends VArchive 
{

    /**
     * Tar file types.
     *   
     * @var array $_types File types
     * @access private
     */
  
    protected $_types = array (
                          0x0 => 'Unix file',
                          0x30 => 'File',
                          0x31 => 'Link',
                          0x32 => 'Symbolic link',
                          0x33 => 'Character special file',
                          0x34 => 'Block special file',
                          0x35 => 'Directory',
                          0x36 => 'FIFO special file',
                          0x37 => 'Contiguous file'
                         );

    /**
     * Tar file flags.
     *   
     * @var array $_flags File flags
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
     * Tar file data buffer
     *   
     * @var string $_data
     * @access private
     */
  
    protected $_data = null;

    /**
     * Tar file metadata array
     *   
     * @var array $_metadata File metadata
     * @access private  
     */
  
    protected $_metadata = null;

    /**
     * Extract a Tar file to a given path
     *  
     * @param string $archive Path to ZIP archive to extract
     * @param string $destination Path to extract archive into
     * @param array $options Extraction options [unused]
     * @return boolean True if successful
     * @access public    
     */

    function extract($archive, $destination, $options = array ()) 
    {
        
        $vpath =& v()->filesystem()->path();
        
        // Initialize variables
        $this->_data = null;
        $this->_metadata = null;
        $archive = v()->filesystem()->path()->clean($archive);
        $destination = v()->filesystem()->path()->clean($destination);  
        $this->_data = $this->_vfile->read($archive);
        if (VWP::isWarning($this->_data)) {   
            return VWP::raiseError('Unable to read archive',get_class($this).":extract",100,false);
        }

        $tir = $this->_getTarInfo($this->_data); 
        if (VWP::isWarning($tir)) {
            return $tir;
        }

        for ($i=0,$n=count($this->_metadata);$i<$n;$i++) {
            $type = strtolower( $this->_metadata[$i]['type'] );
            if ($type == 'file' || $type == 'unix file') {
                $buffer = $this->_metadata[$i]['data'];
                $path = $vpath->clean($destination.DS.$this->_metadata[$i]['name']);
                // Make the destination folder
                $mkf = $this->_vfolder->create(dirname($path));
                if (VWP::isWarning($mkf)) {     
                    return VWP::raiseError('Unable to create destination',get_class($this).":extract",100,false);
                }
    
                $wr = $this->_vfile->write($path,$buffer);
                if (VWP::isWarning($wr)) {
                    return VWP::raiseError('Unable to write entry',get_class($this).":extract",100,false);    
                }
            }
        }
        return true;
    }

    /**
     * Get the list of files/data from a Tar buffer.
     *
     * <pre>
     * KEY: Position in the array
     * VALUES: 'attr'  --  File attributes
     *         'data'  --  Raw file contents
     *         'date'  --  File modification time
     *         'name'  --  Filename
     *         'size'  --  Original file size
     *         'type'  --  File type
     * </pre>
     *   
     * @param string $data The Tar buffer.
     * @return array Archive metadata array
     * @access private     
     */

    function _getTarInfo(&$data) 
    {
        $position = 0;
        $return_array = array ();

        while ($position < strlen($data)) {
            $info = @ unpack("a100filename/a8mode/a8uid/a8gid/a12size/a12mtime/a8checksum/Ctypeflag/a100link/a6magic/a2version/a32uname/a32gname/a8devmajor/a8devminor", substr($data, $position));
            if (!$info) {
                return VWP::raiseError('Unable to decompress data',get_class($this).":_getTarInfo",100,false);    
            }

            $position += 512;
            $contents = substr($data, $position, octdec($info['size']));
            $position += ceil(octdec($info['size']) / 512) * 512;

            if ($info['filename']) {
                $file = array (
                          'attr' => null,
                          'data' => null,
                          'date' => octdec($info['mtime']), 
                          'name' => trim($info['filename']), 
                          'size' => octdec($info['size']), 
                          'type' => isset ($this->_types[$info['typeflag']]) ? $this->_types[$info['typeflag']] : null
                         );
     
                if (($info['typeflag'] == 0) || ($info['typeflag'] == 0x30) || ($info['typeflag'] == 0x35)) {
                    /* File or folder. */
                    $file['data'] = $contents;

                    $mode = hexdec(substr($info['mode'], 4, 3));
                    $file['attr'] = (($info['typeflag'] == 0x35) ? 'd' : '-') .
                        (($mode & 0x400) ? 'r' : '-') .
                        (($mode & 0x200) ? 'w' : '-') .
                        (($mode & 0x100) ? 'x' : '-') .
                        (($mode & 0x040) ? 'r' : '-') .
                        (($mode & 0x020) ? 'w' : '-') .
                        (($mode & 0x010) ? 'x' : '-') .
                        (($mode & 0x004) ? 'r' : '-') .
                        (($mode & 0x002) ? 'w' : '-') .
                        (($mode & 0x001) ? 'x' : '-');
                }
                $return_array[] = $file;
            }
        }
        $this->_metadata = $return_array;
        return true;
    }
    
    // end class VArchiveTar
} 
