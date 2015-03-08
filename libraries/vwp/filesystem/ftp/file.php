<?php

/**
 * Virtual Web Platform - FTP File Support
 *  
 * This file provides FTP File Support   
 * 
 * @package VWP
 * @subpackage Libraries.Filesystem.FTP
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

// Restrict access

class_exists('VWP') || die();

/**
 * Require Local File Support
 */

VWP::RequireLibrary('vwp.filesystem.local.file');

/**
 * Virtual Web Platform - FTP File Support
 *  
 * This class provides FTP File Support   
 * 
 * @package VWP
 * @subpackage Libraries.Filesystem.FTP
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */
 
class VFTPFile extends VFile 
{

    /**
     * FTP Client
     * 
     * @var object $_client FTP Client
     * @access private
     */
 
    protected $_client;
 
    /**
     * FTP Folder
     * 
     * @var VFTPFolder $_vfolder FTP Folder
     * @access private
     */
   
    protected $_vfolder;

    /**
     * Get instance of File object
     * 
     * @return VFTPFile File object
     * @access public      
     */
 
    public static function &getInstance(&$client) 
    {
 	    $fs =& Filesystem::getInstance($client,'ftp'); 	 
        $vfile =& v()->filesystem($fs)->file();
        return $vfile;
    }

    /**
     * Get instance of Folder object
     * 
     * @return VFTPFolder Folder object
     * @access public      
     */

    function &getFolderInstance() 
    {
     
        if (!isset($this->_vfolder)) {
           $this->_vfolder =& $this->_fs->folder();
        }
        return $this->_vfolder;
    }


    /**
     * Get modification time
     * 
     * @param string $file Filename
     * @return integer|object Unix timestamp on success, warning or error otherwise
     * @access public  
     */
    
    function getMTime($file) 
    {
 
        $response = $this->_client->mdtm($file);
        if (VWP::isWarning($response)) {
            return $response;
        }
   
        $time = $response->getMTime();    
        if ($time === false) {   
            $errmsg = "Unable to get file modification time.";
            return VWP::raiseError($errmsg,get_class($this).":getMTime",$err["type"],false);
        }
        return $time;
    }

    /**
     * Gets the extension of a file name
     *
     * @param string $file The file name
     * @return string|object The file extension on success, error or warning on failure
     * @access public
     */
	 
    function getExt($file) 
    {  
        if (is_string($file)) {   
            $dot = strrpos($file, '.') + 1;
            return substr($file, $dot);
        }  
        return VWP::raiseWarning("Invalid filename!",get_class($this)."::getExt",null,false);
    }

    /**
     * Strips the last extension off a file name
     *
     * @param string $file The file name
     * @return string The file name without the extension
     * @access public  
     */

    function stripExt($file) 
    {
        return preg_replace('#\.[^.]*$#', '', $file);
    }  
  
    /**
     * Makes file name safe to use
     *
     * @param string $file The name of the file [not full path]
     * @return string The sanitised string
     * @access public
     */

    function makeSafe($file) {
        $regex = array('#(\.){2,}#', '#[^A-Za-z0-9\.\_\- ]#', '#^\.#');
        return preg_replace($regex, '', $file);
    }

    /**
     * Make a temporary file
     * 
     * @param string $prefix Filename prefix
     * @param string $dir Directory to create file
     * @return string|object Filename on success, error or warning otherwise
     * @access public  
     */
             
    function mktemp($prefix = 'php', $dir = false) 
    {
        if ($dir === false) {
            $cfg = VWP::getConfig();
            $dir = $cfg->temp_dir;
        }
        
        $charlist = '0123456789abcdefghijklmnopqrstuvwxyz';
  
        $vfolder =& $this->getFolderInstance();
        
        if (!$vfolder->exists($dir)) {
            $result = $vfolder->create($dir);
            if (VWP::isWarning($result)) {
                return $result;
            }
        }

        $used = $vfolder->files($dir,'.');
        if (VWP::isWarning($used)) {
            return $used;
        }

        $r1 = time();
        $r1_ext = "";
  
        while($r1 > 0) {
           $idx = $r1 % $base;
           $r1_ext .= substr($charlist,$idx,1);
           $r1 = $r1 - $idx;
           $r1 = $r1 / $base;
        }        

        $filename = null;
        $ctr = 0;   
        while($filename === null || ($ctr < 99 && in_array($filename,$used))) {
            $ctr++;
   
            $r2 = rand();
            $filename = $prefix.$r1_ext;

            while($r2 > 0) {
                $idx = $r2 % $base;
                $filename .= substr($charlist,$idx,1);
                $r2 = $r2 - $idx;
                $r2 = $r2 / $base;
            }      
        }

        if (in_array($filename,$used)) {   
            return VWP::raiseError("Temporary folder is full!",get_class($this),null,false);
        }

        $tempfile = $dir.DS.$filename;
        $result = $this->write($tempfile,'');
  
        if (VWP::isWarning($result)) {
            return $result;
        } 
        
        return $tempfile;
    }
      
    /**
     * Write contents to a file
     *
     * @param string $file The full file path
     * @param string $buffer The buffer to write
     * @return boolean True on success
     * @access public
     */
  
    function write($file, $buffer) 
    {
    
        $vfolder =& $this->getFolderInstance();
   
        if (VWP::isWarning($vfolder)) {
            return $vfolder;
        }
   
        if (!$vfolder->exists(dirname($file))) {
            $vfolder->create(dirname($file));
        }
      
        $ret = $this->_client->stor($file,$buffer,"I");
  
        if (VWP::isWarning($ret)) {
            return $ret;
        }  		
        return true;
    }

    /**
     * Copies a file
     *
     * @param string $src The path to the source file
     * @param string $dest The path to the destination file
     * @param string $path An optional base path to prefix to the file names
     * @return boolean True on success
     */
	 
    function copy($src, $dest, $path = null,$sourceSystem = null) 
    {
  
        // Initialize variables

        // Prepend a base path if it exists
        if ($path) {
            $src = $path.DS.$src;
            $dest = $path.DS.$dest;
        }
  
        $src = $this->_fs->path()->clean($src);
        $dest = $this->_fs->path()->clean($dest);  
  
        $destFolder = dirname($dest);
        $vfolder =& $this->getFolderInstance();
        if (!$vfolder->exists($destFolder)) {
            $vfolder->create($destFolder);
        }
  
        if (is_object($sourceSystem)) {
            $sfile =& $sourceSystem->getFileInstance();
        } else {
            $sfile =& $this;
        }
        
        // Read File from source   
        $data = $sfile->read($src);
        if (VWP::isWarning($data)) {
            return $ret;
        }
   
        // Write file to destination
        $ret = $this->write($dest,$data);
        if (VWP::isWarning($ret)) {
            return $ret;
        }  

        return true;  
    }

    /**
     * Delete a file or array of files
     *
     * @param mixed $file The file name or an array of file names
     * @return boolean  True on success
     * @access public
     */

    function delete($file) 
    {
        // Initialize variables

        if (is_array($file)) {
            $files = $file;
        } else {
            $files[] = $file;
        }
 
        foreach ($files as $file) {
            $file = $this->_fs->path()->clean($file);

            $this->_client->chmod($file,0777);
   			  
            $lr = $this->_client->dele($file);
            if (VWP::isWarning($lr)) {
                return $lr;
            }
        }
 
        return true;
    }

    /**
     * Moves a file
     *
     * @param string $src The path to the source file
     * @param string $dest The path to the destination file
     * @param string $path An optional base path to prefix to the file names
     * @return boolean|object True on success, error or warning otherwise
     * @access public 
     */
  
    function move($src, $dest, $path = '') 
    {
        // Initialize variables

        if ($path) {
            $src = $this->_fs->path()->clean($path.DS.$src);
            $dest = $this->_fs->path()->clean($path.DS.$dest);
        }

        if (!$this->exists($src)) {
            return VWP::raiseWarning(VText::_('Cannot find source file'),get_class($this).":move",null,false);
        }

        if ($this->exists($dest)) {
            return VWP::raiseWarning(VText::_('File already exists'),get_class($this).":move",null,false);
        }
 
        $lr = $this->_client->rnfr($src);
        if (VWP::isWarning($lr)) {
           return $lr;
        }
            
        $lr = $this->_client->rnto($dest);
        if (VWP::isWarning($lr)) {
            return $lr;
        }
        
        return true;
    }

    /**
     * Read the contents of a file
     *
     * @param string $filename The full file path
     * @param boolean $incpath Use include path
     * @param int $amount Amount of file to read
     * @param int $chunksize Size of chunks to read
     * @param int $offset Offset of the file
     * @return mixed Returns file contents on success, error or warning on failure
     * @access public
     */
  
    function read($filename, $incpath = false, $amount = 0, $chunksize = 8192, $offset = 0) 
    {
    	// Initialize variables
        
    	$filename = $this->_fs->path()->clean($filename);
        
    	if ($incpath !== false) {
    		$filename = $this->_fs->path()->combine($incpath,$filename);
    	}

        // File exists?
        if (!$this->exists($filename)) {
            return VWP::raiseWarning(VText::_('File not found') .  ' File: ' . $filename,get_class($this) . ":read",21,false);			
        }
    
        $reply = $this->_client->retr($filename);
        if ($this->_client->isWarning($reply)) {
            return $reply;
        }
        
        $data = $reply->getData();    	
    	
    	if ($offset) {
    		$data = substr($data,$offset);
    	}
    	
    	if ($amount) {
    	    $data = substr($data,0,$amount);	
    	}
    	
        return $data;
    }

    /**
     * Moves an uploaded file to a destination folder
     *
     * UNSUPPORTED!!!
     * 
     * @param string $src The name of the php (temporary) uploaded file
     * @param string $dest The path (including filename) to move the uploaded file to
     * @return boolean|object Returns a warning as this method is not supported by this driver
     * @access public
     */

    function upload($src, $dest) 
    {
    	return VWP::raiseWarning('Unsupported method',get_class($this),null,false);
    }
    
    
    /**
     * Check if file exists
     *
     * @param string $file File path
     * @return boolean True if path is a file
     * @access public
     */
	 
    function exists($file) 
    {
        $vfolder =& $this->getFolderInstance();
        $files = $vfolder->files(dirname($file));
        if (VWP::isWarning($files)) {
            return false;
        }
        return in_array(basename($file),$files);  
    }
 
    /**
     * Returns the name, sans any path
     *
     * @param string $file File path
     * @return string filename
     */
  
    function getName($file) 
    {
        $slash = strrpos($file, DS);
        if ($slash !== false) {
            return substr($file, $slash + 1);
        } else {
            return $file;
        }
    } 

    /**     
     * Class constructor
     * 
     * @param VFilesystem $filesystem Filesystem
     * @access public
     */
    
    function __construct(&$filesystem) 
    {
        parent::__construct($filesystem);
        $this->_client =& $this->_fs->client();        	
    } 
    
    // end class VFTPFile
} 
