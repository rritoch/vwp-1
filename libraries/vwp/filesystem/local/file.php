<?php

/**
 * Virtual Web Platform - File Support
 *  
 * This file provides File Support   
 * 
 * @package VWP
 * @subpackage Libraries.Filesystem.Local
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License   
 */
 
// Restricted access

class_exists('VWP') || die();


/**
 * Virtual Web Platform - File Support
 *  
 * This class provides File Support   
 * 
 * @todo Rename VFile class to VLocalFile and create VFile abstract class
 * @package VWP
 * @subpackage Libraries.Filesystem.Local
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License   
 */

class VFile extends VFilesystemDriver
{
	
    /**
     * Get instance of File object
     * 
     * @return object File object
     * @access public      
     */
     
    public static function &getInstance(&$client) 
    {
        $vfile =& VFilesystem::local()->file();
        return $vfile;
    }

    /**
     * Get instance of Folder object
     * 
     * @return object Folder object
     * @access public      
     */

    function & getFolderInstance() 
    {
 	    $vfolder =& $this->_fs->folder(); 
        return $vfolder;
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
    	$file = $this->_fs->path()->clean($file);

    	if (!file_exists($file)) {
    	    return VWP::RaiseWarning('File not found!',__CLASS__,ERROR_FILENOTFOUND,false);	
    	}
    	
        $time = filemtime($file);
        if ($time === false) {
            $err = error_get_last();
            $errmsg = "Unable to get file modification time:" . $err["message"];
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
        	$file = $this->_fs->path()->clean($file);
        	$parts = explode(DS,$file);
        	$filename = array_pop($parts);   
            $dot = strrpos($filename, '.');
            if ($dot === false) {
                return null;	
            }
            return substr($filename, $dot + 1);
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
            $dir = VPATH_BASE.DS.$cfg->temp_dir;       
            $tempfile=tempnam($dir,$prefix);
        } else {
            $tempfile=tempnam($dir,$prefix);
        }
   
        if ($tempfile === false) {
            $err = error_get_last();
            return VWP::raiseError("tempnam($dir,$prefix) failed: " . $err["message"],"VFolder",null,false);
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
        if (!file_exists(dirname($file))) {   
            $vfolder =& self::getFolderInstance();
            $result = $vfolder->create(dirname($file));
            if (VWP::isWarning($result)) {
                return $result;
            }
        }

        $file = $this->_fs->path()->clean($file);
        VWP::nowarn();
        $ret = @ file_put_contents($file, $buffer);
        VWP::nowarn(false);
        if ($ret === false) {
            $err = VWP::getLastError();
            $ret = VWP::raiseError($err[1],get_class($this) . ":write",$err[0],false);
        }
        return $ret;
    }    
    
    /**
     * Copies a file
     *
     * @param string $src The path to the source file
     * @param string $dest The path to the destination file
     * @param string $path An optional base path to prefix to the file names
     * @return true|object True on success, error or warning otherwise
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
    
        $vfolder = $this->getFolderInstance();
        if (!$vfolder->exists($destFolder)) {
            $vfolder->create($destFolder);
        }
  
        if (is_object($sourceSystem)) {
            $sfile = & $sourceSystem->getFileInstance();
   
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
        } else {
            if (!@ copy($src, $dest)) {
                return VWP::raiseWarning(VText::_('Copy failed'),get_class($this).":copy",21,false);		 
            }
        }
  
        return true;
    }

    /**
     * Delete a file or array of files
     *
     * @param mixed $file The file name or an array of file names
     * @return true|object  True on success, error or warning otherwise
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

            // Make file writable
            VWP::noWarn();
            @chmod($file, 0777);
            VWP::noWarn(false);

            // Delete file
            
            VWP::noWarn();
            $lr = @unlink($file);
            VWP::noWarn(false);
            if ($lr) {
                // Do nothing
            } else {
                $filename = basename($file);
                return VWP::raiseWarning(VText::_('Delete failed') . ": '$filename'",get_class($this) . ":delete",null,false);				
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

        //Check src path
        if (!is_readable($src) && !is_writable($src)) {
            return VWP::raiseWarning(JText::_('Cannot find, read or write file') . ": '$src'",get_class($this)."::move",21,false);   
        }

        if (!@ rename($src, $dest)) {
            return VWP::raiseWarning(JText::_('Rename failed'),get_class($this)."::move",21,false);   
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
        $data = null;
        if($amount && $chunksize > $amount) { 
        	$chunksize = $amount; 
        }
        
        VWP::noWarn();
        if (false === $fh = fopen($filename, 'rb', $incpath)) {
            VWP::noWarn(false);
            return VWP::raiseError(VText::_('Unable to open file') . ": '$filename'",get_class($this).":read",21,false);
        }
        VWP::noWarn(false);
        
        clearstatcache();
        if ($offset) {
        	   fseek($fh, $offset);
        }
        if ($fsize = @ filesize($filename)) {
            if ($amount && $fsize > $amount) {
                $data = fread($fh, $amount);
            } else {
                $data = fread($fh, $fsize);
            }
        } else {
            $data = '';
            $x = 0;
            // While Not the end of the file AND less than the max amount we want
            while (!feof($fh) && (!$amount || strlen($data) < $amount)) {
                $data .= fread($fh, $chunksize);
            }
        }
        fclose($fh);
        return $data;
    }



    /**
     * Moves an uploaded file to a destination folder
     *
     * @param string $src The name of the php (temporary) uploaded file
     * @param string $dest The path (including filename) to move the uploaded file to
     * @return boolean True on success, error or warning otherwise
     * @access public
     */

    function upload($src, $dest) 
    {
        $ret = false;
        // Ensure that the path is valid and clean
        $dest = $this->_fs->path()->clean($dest);

        // Create the destination directory if it does not exist
        $baseDir = dirname($dest);
        if (!file_exists($baseDir)) {			
            $vfolder = self::getFolderInstance();
            $vfolder->create($baseDir);
        }
    
        if (is_writeable($baseDir) && move_uploaded_file($src, $dest)) { // Short circuit to prevent file permission errors
            if ($this->_fs->path()->setPermissions($dest)) {
                $ret = true;
            } else {
                $ret = VWP::raiseWarning(VText::_('WARNFS_ERR01'),get_class($this)."::upload",21,false);
            }
        } else {
            $ret = VWP::raiseWarning(VText::_('WARNFS_ERR02'),get_class($this)."::upload",21,false);
        }
        return $ret;
    }

    /**
     * Check if file exists
     *
     * @param string $file File path
     * @return boolean True if path is a file
     */
	 
    function exists($file) 
    {
        return is_file($this->_fs->path()->clean($file));
    }

    /**
     * Returns the name, sans any path
     *
     * param string $file File path
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
    
    // end class VFile
} 
