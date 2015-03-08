<?php

/**
 * VWP Zip archive driver 
 * 
 * This file contains the VWP Zip archive driver  
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
 * Requires zlib php extension
 */

VWP::RequireExtension('zlib');


/**
 * VWP Zip archive driver 
 * 
 * This class provides the VWP Zip archive driver  
 *     
 * @package    VWP
 * @subpackage Libraries.Archive.Drivers
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License
 */
 
class VArchiveZip extends VArchive 
{
	
    /**
     * ZIP compression methods.
     * 	 
     * @var $_methods array
     * @access private
     */
	 
    protected $_methods = array (
                             0x0 => 'None',
                             0x1 => 'Shrunk',
                             0x2 => 'Super Fast',
                             0x3 => 'Fast',
                             0x4 => 'Normal',
                             0x5 => 'Maximum',
                             0x6 => 'Imploded',
                             0x8 => 'Deflated'
                            );

    /**
     * Beginning of central directory record.
     * 	 
     * @var string $_ctrlDirHeader Central Directory Header
     * @access private
     */
	 
    protected $_ctrlDirHeader = "\x50\x4b\x01\x02";

    /**
     * End of central directory record.
     * 	 
     * @var string $_ctrlDirEnd Central Directory Footer
     * @access private
     */
  
    protected $_ctrlDirEnd = "\x50\x4b\x05\x06\x00\x00\x00\x00";

    /**
     * Beginning of file contents.
     * 	 
     * @var string $_fileHeader File Header
     * @access private
     */
	 
    protected $_fileHeader = "\x50\x4b\x03\x04";

    /**
     * ZIP file data buffer
     * 
     * @var string $_data File data
     * @access private
     */
	 
    protected $_data = null;

    /**
     * ZIP file metadata array
     *     
     * @var array $_metadata Metadata
     * @access private
     */
    
    protected $_metadata = null;

    /**
     * Create a ZIP compressed file from an array of file data.
     *     
     * @param string $archive Path to save archive
     * @param array $files	 Array of files to add to archive
     * @param array $options Compression options [unused]
     * @param string $compress Compression method
     * @param string $addPath add Path
     * @param string $removePath remove path
     * @param boolean $autoExt Auto extension
     * @param boolean $cleanUp Clean Up
     * @return boolean|object True if successful object or warning on failure
     * @access public
     */
  
    function create($archive, $files, $options = array (), $compress = 'zip', $addPath = '', $removePath = '', $autoExt = false, $cleanUp = false) 
    {
	 
        // Initialize variables
        $contents = array();
        $ctrldir  = array();

        foreach ($files as $file) {
            $this->_addToZIPFile($file, $contents, $ctrldir);
        }
   
        return $this->_createZIPFile($contents, $ctrldir, $archive);
    }

    /**
     * Extract a ZIP compressed file to a given path
     *
     * @param string $archive Path to ZIP archive to extract
     * @param string $destination Path to extract archive into
     * @param array $options Extraction options [unused]
     * @return boolean True if successful
     * @access public  
     */

    function extract($archive, $destination, $options = array ()) 
    {
        if (!$this->_vfile->exists($archive)) {
            return VWP::raiseError("Archive ($archive) does not exist", get_class($this), null,false);
        }

        if ($this->hasNativeSupport()) {
            $result = $this->_extractNative($archive, $destination, $options);
        } else {
            $result = $this->_extract($archive, $destination, $options);
        }  
        return $result;
    }

    /**
     * Method to determine if the server has native zip support for faster handling
     *
     * @return boolean True if php has native ZIP support
     * @access public
     */
  
    function hasNativeSupport() 
    {
        return (function_exists('zip_open') && function_exists('zip_read'));
    }

    /**
     * Checks to see if the data is a valid ZIP file.
     *
     * @param string $data	ZIP archive data buffer
     * @return boolean True if valid, false if invalid.
     * @access public  
     */
  
    function checkZipData(&$data) 
    {
        if (strpos($data, $this->_fileHeader) === false) {
            return VWP::raiseWarning("Invalid zip file!",get_class($this),null,false);
        }
        return true;
    }

    /**
     * Extract a ZIP compressed file to a given path using a php based algorithm that only requires zlib support
     *
     * @param string $archive Path to ZIP archive to extract
     * @param string $destination	Path to extract archive into
     * @param array $options Extraction options [unused]
     * @return boolean True if successful
     * @access private  
     */
 
    function _extract($archive, $destination, $options) 
    {
        // Initialize variables
        $this->_data = null;
        $this->_metadata = null;

        
        if (!extension_loaded('zlib')) {
            return VWP::raiseError("Zlib Not Supported",get_class($this),null,false);
        }
       
        $this->_data = $this->_vfile->read($archive);
  
        if (VWP::isWarning($this->_data)) {
            $this->set('error.message', 'Unable to read archive');
            return VWP::raiseWarning("Unable to read archive :" . $this->_data->errmsg,get_class($this),$this->_data->errno,false);
        }

        if (VWP::isWarning($this->_getZipInfo($this->_data))) {
            return VWP::raiseWaring("Missing zip archive headers!",get_class($this),null,false);
        }

        $vpath =& v()->filesystem()->path();
        
        for ($i=0,$n=count($this->_metadata);$i<$n;$i++) {
            if (substr($this->_metadata[$i]['name'], -1, 1) != '/' && substr($this->_metadata[$i]['name'], -1, 1) != '\\') {
                $buffer = $this->_getFileData($i);
                $path = $vpath->clean($destination.DS.$this->_metadata[$i]['name']);
                // Make sure the destination folder exists

                $w = $this->_vfolder->create(dirname($path));    
                if (VWP::isWarning($w)) {
                    return $w;
                }
    
                $w = $this->_vfile->write($path, $buffer);
                if (VWP::isWarning($w)) {
                    return $w;
                }
            }
        }
        return true;
    }

    /**
     * Extract a ZIP compressed file to a given path using native php api calls for speed
     *
     * @param string $archive Path to ZIP archive to extract
     * @param string $destination Path to extract archive into
     * @param array $options Extraction options [unused]
     * @return boolean|object True if successful, error or warning otherwise
     * @access private    
     */
  
    function _extractNative($archive, $destination, $options) 
    {
        $archive = v()->filesystem()->path()->clean($archive);
        $destination = v()->filesystem()->path()->clean($destination);
  
        if ($zip = zip_open($archive)) {
            if (is_resource($zip)) {
                // Make sure the destination folder exists
                $mk = $this->_vfolder->create($destination);
                if (VWP::isWarning($mk)) {     
                    return $mk;
                }
                // Read files in the archive
                while ($file = @zip_read($zip)) {
                    if (zip_entry_open($zip, $file, "r")) {
                        if (substr(zip_entry_name($file), strlen(zip_entry_name($file)) - 1) != "/") {
                            $buffer = zip_entry_read($file, zip_entry_filesize($file));
                            $wr = $this->_vfile->write($destination.DS.zip_entry_name($file), $buffer);
                            if (VWP::isWarning($wr)) {      
                                return $wr;
                            }
                            zip_entry_close($file);
                        }
                    } else {
                        return VWP::raiseError('Unable to read entry',get_class($this).":_extractNative",null,false);
                    }
                }
                @zip_close($zip);
            } else {
                return VWP::raiseError('Unable to open archive',get_class($this).":_extractNative",510,false);   
            }
        } else {
            return VWP::raiseError('Unable to open archive',get_class($this).":_extractNative",500,false);
        }
        
        return true;
    }

   /**
    * Get the list of files/data from a ZIP archive buffer.
    *
    * <pre>
    * KEY: Position in zipfile
    * VALUES: 'attr'    --  File attributes
    *         'crc'     --  CRC checksum
    *         'csize'   --  Compressed file size
    *         'date'    --  File modification time
    *         'name'    --  Filename
    *         'method'  --  Compression method
    *         'size'    --  Original file size
    *         'type'    --  File type
    * </pre>
    *
    * @param string $data The ZIP archive buffer.
    * @return array Archive metadata array
    * @access private  
    */
 
    function _getZipInfo(& $data) 
    {
        // Initialize variables
        $entries = array ();

        // Find the last central directory header entry
        $fhLast = strpos($data, $this->_ctrlDirEnd);
        do {
            $last = $fhLast;		
        } while(($fhLast = strpos($data, $this->_ctrlDirEnd, $fhLast+1)) !== false);

        // Find the central directory offset
        $offset = 0;
        if($last) {
            $endOfCentralDirectory = unpack('vNumberOfDisk/vNoOfDiskWithStartOfCentralDirectory/vNoOfCentralDirectoryEntriesOnDisk/vTotalCentralDirectoryEntries/VSizeOfCentralDirectory/VCentralDirectoryOffset/vCommentLength', substr($data, $last+4));
            $offset = $endOfCentralDirectory['CentralDirectoryOffset'];
        }
		
        // Get details from Central directory structure.
        $fhStart = strpos($data, $this->_ctrlDirHeader, $offset);
        do {
            if (strlen($data) < $fhStart +31) {
                $this->set('error.message', 'Invalid ZIP data');
                return false;
            }

            $info = unpack('vMethod/VTime/VCRC32/VCompressed/VUncompressed/vLength', substr($data, $fhStart +10, 20));
            $name = substr($data, $fhStart +46, $info['Length']);

            $entries[$name] = array('attr' => null, 'crc' => sprintf("%08s", dechex($info['CRC32'] )), 'csize' => $info['Compressed'], 'date' => null, '_dataStart' => null, 'name' => $name, 'method' => $this->_methods[$info['Method']], '_method' => $info['Method'], 'size' => $info['Uncompressed'], 'type' => null);
            $entries[$name]['date'] = mktime((($info['Time'] >> 11) & 0x1f), (($info['Time'] >> 5) & 0x3f), (($info['Time'] << 1) & 0x3e), (($info['Time'] >> 21) & 0x07), (($info['Time'] >> 16) & 0x1f), ((($info['Time'] >> 25) & 0x7f) + 1980));

            if (strlen($data) < $fhStart +43) {
                $this->set('error.message', 'Invalid ZIP data');
                return false;
            }
            $info = unpack('vInternal/VExternal/VOffset', substr($data, $fhStart +36, 10));

            $entries[$name]['type'] = ($info['Internal'] & 0x01) ? 'text' : 'binary';
            $entries[$name]['attr'] = (($info['External'] & 0x10) ? 'D' : '-') .
                             (($info['External'] & 0x20) ? 'A' : '-') .
                             (($info['External'] & 0x03) ? 'S' : '-') .
                             (($info['External'] & 0x02) ? 'H' : '-') .
                             (($info['External'] & 0x01) ? 'R' : '-');
                             
            $entries[$name]['offset'] = $info['Offset'];

            // Get details from local file header since we have the offset
            $lfhStart = strpos($data, $this->_fileHeader, $entries[$name]['offset']);
            if (strlen($data) < $lfhStart +34) {
                $this->set('error.message', 'Invalid ZIP data');
                return false;
            }
            $info = unpack('vMethod/VTime/VCRC32/VCompressed/VUncompressed/vLength/vExtraLength', substr($data, $lfhStart +8, 25));
            $name = substr($data, $lfhStart +30, $info['Length']);
            $entries[$name]['_dataStart'] = $lfhStart +30 + $info['Length'] + $info['ExtraLength'];
        } while ((($fhStart = strpos($data, $this->_ctrlDirHeader, $fhStart +46)) !== false));
        	
        $this->_metadata = array_values($entries);
        
        return true;
    }

    /**
     * Returns the file data for a file by offsest in the ZIP archive
     *
     * @param int $key The position of the file in the archive.
     * @return string Uncompresed file data buffer
     * @access private
     */
  
    function _getFileData($key) 
    {
        if ($this->_metadata[$key]['_method'] == 0x8) {
            // If zlib extention is loaded use it
            if (extension_loaded('zlib')) {
                return @ gzinflate(substr($this->_data, $this->_metadata[$key]['_dataStart'], $this->_metadata[$key]['csize']));
            }
        } elseif ($this->_metadata[$key]['_method'] == 0x0) {
            /* Files that aren't compressed. */
            return substr($this->_data, $this->_metadata[$key]['_dataStart'], $this->_metadata[$key]['csize']);
        } elseif ($this->_metadata[$key]['_method'] == 0x12) {
            // If bz2 extention is sucessfully loaded use it
            if (extension_loaded('bz2')) {
                return bzdecompress(substr($this->_data, $this->_metadata[$key]['_dataStart'], $this->_metadata[$key]['csize']));
            }
        }
        return '';
    }

    /**
     * Converts a UNIX timestamp to a 4-byte DOS date and time format
     * (date in high 2-bytes, time in low 2-bytes allowing magnitude
     * comparison).
     *
     * @param int $unixtime The current UNIX timestamp.
     * @return int	The current date in a 4-byte DOS format.
     * @access private
     */

    function _unix2DOSTime($unixtime = null) 
    {
        $timearray = (is_null($unixtime)) ? getdate() : getdate($unixtime);
        if ($timearray['year'] < 1980) {
            $timearray['year'] = 1980;
            $timearray['mon'] = 1;
            $timearray['mday'] = 1;
            $timearray['hours'] = 0;
            $timearray['minutes'] = 0;
            $timearray['seconds'] = 0;
        }
        return (($timearray['year'] - 1980) << 25) | ($timearray['mon'] << 21) | ($timearray['mday'] << 16) | ($timearray['hours'] << 11) | ($timearray['minutes'] << 5) | ($timearray['seconds'] >> 1);
    }

    /**
     * Adds a "file" to the ZIP archive.
     *     
     * @param array $file File data array to add
     * @param array $contents An array of existing zipped files.
     * @param array $ctrldir An array of central directory information.
     * @access private     
     */
  
    function _addToZIPFile(& $file, & $contents, & $ctrldir) 
    {
        $data = & $file['data'];
        $name = str_replace('\\', '/', $file['name']);
  
        $ftime = null;
        if (isset ($file['time'])) {
            $ftime = $file['time'];
        }
    
        $dtime = dechex($this->_unix2DosTime($ftime));
        $hexdtime = chr(hexdec($dtime[6] . $dtime[7])) .
                    chr(hexdec($dtime[4] . $dtime[5])) .
                    chr(hexdec($dtime[2] . $dtime[3])) .
                    chr(hexdec($dtime[0] . $dtime[1]));

        /* Begin creating the ZIP data. */
                
        $fr = $this->_fileHeader; 
        $fr .= "\x14\x00"; /* Version needed to extract. */
        $fr .= "\x00\x00"; /* General purpose bit flag. */
        $fr .= "\x08\x00"; /* Compression method. */
        $fr .= $hexdtime; /* Last modification time/date. */
 
        /* "Local file header" segment. */
  
        $unc_len = strlen($data);
        $crc = crc32($data);
        $zdata = gzcompress($data);
        $zdata = substr(substr($zdata, 0, strlen($zdata) - 4), 2);
        $c_len = strlen($zdata);
        $fr .= pack('V', $crc); /* CRC 32 information. */
        $fr .= pack('V', $c_len); /* Compressed filesize. */
        $fr .= pack('V', $unc_len); /* Uncompressed filesize. */
        $fr .= pack('v', strlen($name)); /* Length of filename. */
        $fr .= pack('v', 0); /* Extra field length. */
        $fr .= $name; /* File name. */
  
        /* "File data" segment. */
        $fr .= $zdata;

        /* Add this entry to array. */
        $old_offset = strlen(implode('', $contents));
        $contents[] = & $fr;

        /* Add to central directory record. */
        $cdrec = $this->_ctrlDirHeader;
        $cdrec .= "\x00\x00"; /* Version made by. */
        $cdrec .= "\x14\x00"; /* Version needed to extract */
        $cdrec .= "\x00\x00"; /* General purpose bit flag */
        $cdrec .= "\x08\x00"; /* Compression method */
        $cdrec .= $hexdtime; /* Last mod time/date. */
        $cdrec .= pack('V', $crc); /* CRC 32 information. */
        $cdrec .= pack('V', $c_len); /* Compressed filesize. */
        $cdrec .= pack('V', $unc_len); /* Uncompressed filesize. */
        $cdrec .= pack('v', strlen($name)); /* Length of filename. */
        $cdrec .= pack('v', 0); /* Extra field length. */
        $cdrec .= pack('v', 0); /* File comment length. */
        $cdrec .= pack('v', 0); /* Disk number start. */
        $cdrec .= pack('v', 0); /* Internal file attributes. */
        $cdrec .= pack('V', 32); /* External file attributes - 'archive' bit set. */
        $cdrec .= pack('V', $old_offset); /* Relative offset of local header. */
        $cdrec .= $name; /* File name. */
        /* Optional extra field, file comment goes here. */

        // Save to central directory array. */
        $ctrldir[] = & $cdrec;
    }

    /**
     * Creates the ZIP file.
     * Official ZIP file format: http://www.pkware.com/appnote.txt
     *
     * @param array $contents An array of existing zipped files.
     * @param array $ctrldir An array of central directory information.
     * @param string $path	The path to store the archive.
     * @return boolean True if successful
     * @access private  
     */
	 
    function _createZIPFile(& $contents, & $ctrlDir, $path) 
    {
	  
        $data = implode('', $contents);
        $dir = implode('', $ctrlDir);

        $buffer = $data . $dir . $this->_ctrlDirEnd .
        /* Total # of entries "on this disk". */
        pack('v', count($ctrlDir)) .
        /* Total # of entries overall. */
        pack('v', count($ctrlDir)) .
        /* Size of central directory. */
        pack('V', strlen($dir)) .
        /* Offset to start of central dir. */
        pack('V', strlen($data)) .
        /* ZIP file comment length. */
           "\x00\x00";
               
        $w = $this->_vfile->write($path, $buffer);
        if (VWP::isWarning($w)) {
            return $w;
        }		
        return true;
    }
    
    // end class VArchiveZip
} 
