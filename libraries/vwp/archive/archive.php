<?php

/**
 * Virtual Web Platform - Archive Support
 *  
 * This file provides Archive Support   
 * 
 * @package VWP
 * @subpackage Libraries.Archive  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License   
 */

// Restricted access
class_exists("VWP") or die();

/**
 * Require file support
 */

VWP::RequireLibrary('vwp.filesystem.file');

/**
 * Require folder support
 */

VWP::RequireLibrary('vwp.filesystem.folder');

/**
 * Virtual Web Platform - Archive Support
 *  
 * This Class provides Archive Support   
 * 
 * @package VWP
 * @subpackage Libraries.Archive  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License 
 */

class VArchive extends VObject 
{

    /**
     * Link to VFile object
     * 
     * @var object $_vfile;   
     * @access public      
     */
        
    var $_vfile;

    /**
     * Link to VFolder object
     * 
     * @var object $_vfolder;   
     * @access public      
     */
        
    var $_vfolder;
  
    /**
     * Get a VArchive object
     * 
     * @return object VArchive object
     * @access public
     */
              
    public static function &getInstance() 
    {
        static $arch;
   
        if (!isset($arch)) {
            $arch = new VArchive;
        }
  
        return $arch;
    }
  
    /**
     * Extract an archive
     *    	
     * @param	string $archivename The name of the archive file
     * @param	string $extractdir Directory to unpack into
     * @return boolean|object True for success warning or error otherwise
     * @access public
     */
 
    function extract( $archivename, $extractdir) 
    {

        $untar = false;
        $result = false;
        $ext = $this->_vfile->getExt(strtolower($archivename));
  
        //check if a tar is embedded
  
        if ($this->_vfile->getExt($this->_vfile->stripExt(strtolower($archivename))) == 'tar') {
            $untar = true;
        }

        switch ($ext) {
        	
            case 'zip':
                $driver =& VArchive::getDriver('zip');
                if (!VWP::isWarning($driver)) {
                    $result = $driver->extract($archivename, $extractdir);
                }
                break;
                
            case 'tar':
                $driver =& VArchive::getDriver('tar');
                if (VWP::isWarning($driver)) {
                    return $driver;
                }
                $result = $driver->extract($archivename, $extractdir);    
                break;
                
            case 'tgz':
                $untar = true; // This format is a tarball
                
            case 'gz':	// GZip Compressed
            	
            case 'gzip':
                $driver =& VArchive::getDriver('gzip');
                if (VWP::isWarning($driver)) {
                    return $driver;
                }    
                $tmpfname = $this->_vfile->mktemp("gzip");
                if (VWP::isWarning($tmpfname)) {
                    return $tmpfname;
                }
                $gzresult = $driver->extract($archivename, $tmpfname);
                if (VWP::isWarning($gzresult)) {
                    $this->_vfile->delete($tmpfname);
                    return $gzresult;
                }
      
                if ($untar) {
                    // Untar the file
                    $tdriver =& VArchive::getDriver('tar');
                    if (VWP::isWarning($tdriver)) {
                        $this->_vfile->delete($tmpfname);
                        return $tdriver;
                    }
                    $result = $tdriver->extract($tmpfname, $extractdir);     
                } else {
                    $path = v()->filesystem()->path()->clean($extractdir);
                    $result = $this->_vfolder->create($path);
                    if (!VWP::isWarning($result)) {
                        $result = $this->_vfile->copy($tmpfname,$path.DS.$this->_vfile->stripExt($this->vfile->getName(strtolower($archivename))));
                    }
                }
                $this->_vfile->delete($tmpfname);       
                break;
                
            case 'tbz2':
                $untar = true; // This bzip2 tar file
            case 'bz2':	// bzip2 compressed file
            case 'bzip2':
                $driver =& VArchive::getDriver('bzip2');
                if (VWP::isWarning($driver)) {
                    return $driver;
                } 
     
                $tmpfname = $this->_vfile->mktemp('bzip');
                $bzresult = $adapter->extract($archivename, $tmpfname);
                if (VWP::isError($bzresult)) {
                    $this->_vfile->delete($tmpfname);
                    return $bzresult;
                }
    
                if ($untar) {
                    // Try to untar the file
                    $tadapter =& VArchive::getDriver('tar');
                    if (VWP::isWarning($tdriver)) {
                        return $tdriver;
                    }
          
                    $result = $tadapter->extract($tmpfname, $extractdir);
                } else {
                    $path = v()->filesystem()->path()->clean($extractdir);
                    $this->_vfolder->create($path);
                    $result = $this->_vfile->copy($tmpfname,$path.DS.$this->_vfile->stripExt($this->_vfile->getName(strtolower($archivename))));
                }
                $this->_vfile->delete($tmpfname);   
                break;
            default:
                return VWP::raiseWarning(VText::_('UNKNOWNARCHIVETYPE'),"VArchive",10,false);				
                break;
        }
        return $result;
    }

    /**
     * Get archive driver
     * 
     * @param string $type Archive type
     * @return object Archive Driver on success, error or warning otherwise
     * @access public  
     */
        
    public static function &getDriver($type) 
    {
        static $drivers;

        if (!isset($drivers)) {
            $drivers = array();
        }

        if (!isset($drivers[$type])) {
            // Try to load the adapter object
            $class = 'VArchive'.ucfirst($type);

            if (!class_exists($class)) {
                $path = dirname(__FILE__).DS.'drivers'.DS.strtolower($type).'.php';
                if (file_exists($path)) {
                    require_once($path);
                    $drivers[$type] = new $class();
                } else {
                    $drivers[$type] = VWP::raiseError(VText::_('Unable to load archive'),"VArchive",500,false);
                }
            } else {
                $drivers[$type] = new $class();
            }
        }
        return $drivers[$type];
    }

    /**
     * Clean file path
     * 
     * Removes double slashes and returns a clean path
     * 
     * @param string $path Path to clean
     * @return string Clean path name
     * @access public      
     */
         
    function _cleanPath($path) 
    {
        $pc = v()->filesystem()->path()->clean($path,DS);
        $oparts = explode(DS,$pc);
        $parts = array();
        $relative = strlen($oparts[0]) > 0;
        foreach($oparts as $name) {
            if (strlen($name) > 0) {
                array_push($parts,$name);
            }
        }
        if (!$relative) {
            array_unshift($parts,'');
        }
        return implode(DS,$parts);
    }
 
    /**
     * Reformat a list of files into an archive file list
     * 
     * @param array $files File list
     * @param string $addPath Archive path prefix to add to output
     * @param string $removePath Strip source path prefix from input
     * @return array Archive formatted file list
     * @access public  
     */            
  
    function _createArchiveFileList(&$files, $addPath,$removePath) 
    {
	 
        $vfile =& v()->filesystem()->file();
	 
        for($fh = 0; $fh < count($files); $fh++) {
            $orig = $files[$fh];
            $files[$fh] = array();
   
            // Get Modified Time
            $result = $vfile->getMTime($orig);
            if (VWP::isError($result)) {
                return $result;
            }        
            $files[$fh]["time"] = $result;
    
            // Get File Data
            $result = $vfile->read($orig);    
            if (VWP::isError($result)) {
                return $result;
            }    
            $files[$fh]["data"] = $result;
      
            // Get File Name
            $split = explode($removePath,$orig);
            if (strlen($split[0]) < 1) {
                array_shift($split);
            }
            if (strlen($addPath) > 0) {
                $addPath .= DS;	
            }        
            $files[$fh]["name"] = $this->_cleanPath($addPath.implode($removePath,$split));   
        }
   
        return true;
    }

    /**
     * Create an archive
     *    	
     * @param string $archive  The name of the archive
     * @param mixed $files The name of a single file or an array of files     
     * @param array $options Archive options
     * @param string $compress The compression for the archive  
     * @param string $addPath Path to add within the archive
     * @param string $removePath Path to remove within the archive
     * @param boolean $autoExt Automatically append the extension for the archive
     * @param boolean $cleanUp Remove for source files
     * @access public
     */	 

    function create($archive, $files,  $options = array(), $compress = 'tar', $addPath = '', $removePath = '', $autoExt = false, $cleanUp = false) 
    {
		
        $vfile =& v()->filesystem()->file();
        
        $clean = array();
		
        if (is_string($files)) {
            $files = array ($files);
        }
		
        $alg = explode(".",$compress);
    
        foreach($alg as $method) {
    
            if ($autoExt) {
                $archive .= '.'.$method;
            }
   
            $orig_files = $files;
		 
            $result = $this->_createArchiveFileList($files,$addPath,$removePath);
          
            if (VWP::isError($result)) {
                $vfile->delete($archive);
                return $result;
            }
          
            $drv = & VArchive::getDriver($method);
     
            if (VWP::isError($drv)) {
                $vfile->delete($archive);
                return $drv;
            }
     
            $result = $drv->create($archive,$files,$options);
     
            if (VWP::isError($result)) {      
                $vfile->delete($archive);
                return $result;     
            }

            if ($cleanUp) {
                $clean = array_merge($orig_files,$clean);
            }

            $files = array($archive);          
            $removePath = dirname($archive);          
        }	

        $vfile->delete($clean);    
        return $archive;
    }

    /**
     * Class constructor
     * 
     * @access public
     */
         	
    function __construct() {
        $this->_vfile =& v()->filesystem()->file();
        $this->_vfolder =& v()->filesystem()->folder();
    }
 
} // end class
