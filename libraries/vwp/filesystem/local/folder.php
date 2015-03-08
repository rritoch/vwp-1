<?php

/**
 * Virtual Web Platform - Folder Support
 *  
 * This file provides Folder Support   
 * 
 * @todo Rename VFolder class to VLocalFolder and create VFolder abstract class
 * @package VWP
 * @subpackage Libraries.Filesystem.Local
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License    
 */

// Restricted access
class_exists('VWP') or die();

/**
 * Require Filesystem Support
 */

VWP::RequireLibrary('vwp.filesystem');

/**
 * Require Path Support
 */

VWP::RequireLibrary('vwp.filesystem.local.path');

/**
 * Virtual Web Platform - Folder Support
 *  
 * This file provides Folder Support   
 * 
 * @package VWP
 * @subpackage Libraries.Filesystem.Local
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License 
 */

class VFolder extends VFilesystemDriver 
{
	
    /**
     * @var integer $_folder_tree_index Folder tree index
     * @access private   
     */
      
    public static $_folder_tree_index;

    
    /**
     * Make a temporary folder
     * 
     * @param string $prefix Folder name prefix
     * @param string $dir Directory to create file
     * @return string|object Folder name on success, error or warning otherwise
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
        if (file_exists($tempfile)) { 
        	unlink($tempfile); 
        }
        mkdir($tempfile);
        if (is_dir($tempfile)) { 
        	return $tempfile; 
        }
        return VWP::raiseError("Unable to create temporary directory $tempfile [$prefix,$dir]","VFolder",null,false);
    }    

    /**
     * Get instance of Folder object
     * 
     * @return VFolder $client Folder object
     * @access public      
     */
    
    static function &getInstance(&$client) 
    {
 	    $vfolder =& VFilesystem::local()->folder();
 	    return $vfolder;
    }
 
    /**
     * Get instance of File object
     * 
     * @return object File object
     * @access public      
     */ 
 
    function & getFileInstance() 
    {
        $vfile =& v()->filesystem()->file();
 	    return $vfile;
    }    
    

    /**
     * Create a folder -- and all necessary parent folders.
     *
     * @param string $path A path to create from the base path.
     * @param integer $mode Directory permissions to set for folders created.
     * @return boolean True if successful.
     * @access public
     */

    function create($path = '', $mode = 0755) 
    {
    
        // Initialize variables
        static $nested = 0;
  
        // Check to make sure the path valid and clean
        $path = $this->_fs->path()->clean($path);

        // Check if parent dir exists
        $parent = dirname($path);
  
        if (!$this->exists($parent)) {
            // Prevent infinite loops!
            $nested++;
            if (($nested > 20) || ($parent == $path)) {
                $nested--;
                return VWP::raiseWarning(VText::_('Infinite loop detected'),get_class($this) . ':create',null,false);          
            }

            // Create the parent directory
            $cr = $this->create($parent, $mode);
            if (VWP::isWarning($cr)) {
                $nested--;
                return $cr;
            }
            // OK, parent directory has been created
            $nested--;
        }

        // Check if dir already exists
        if ($this->exists($path)) {
            return true;
        }

        // Check for safe mode
    
        // We need to get and explode the open_basedir paths
        $obd = ini_get('open_basedir');

        // If open_basedir is set we need to get the open_basedir that the path is in
        if ($obd != null) {
            if (VPATH_ISWIN) {
                $obdSeparator = ";";
            } else {
                $obdSeparator = ":";
            }
  
            // Create the array of open_basedir paths
            $obdArray = explode($obdSeparator, $obd);
            $inBaseDir = false;
            // Iterate through open_basedir paths looking for a match
            foreach ($obdArray as $test) {
                $test = $this->_fs->path()->clean($test);
                if (strpos($path, $test) === 0) {
                    $obdpath = $test;
                    $inBaseDir = true;
                    break;
                }
            }
  
            if ($inBaseDir == false) {
                // Return false because the path to be created is not in open_basedir
                return VWP::raiseWarning(VText::_('Path not in open_basedir paths'), get_class($this). ":create",null,false);
            }
        }

        // First set umask
        $origmask = @umask(0);

        // Create the path
        if (!$ret = @mkdir($path, $mode)) {
            @umask($origmask);
            return VWP::raiseWarning(VText::_('Could not create directory' ) . ' Path: ' . $path,get_class($this) . ':create',null,false);
        }

        // Reset umask
        @umask($origmask);		
        return $ret;
    }

    /**
     * Delete a folder.
     *
     * @param string $path The path to the folder to delete.
     * @return boolean True on success.
     * @access public	 
     */

    function delete($path) 
    {
  
        $vfile = $this->getFileInstance();
	 
        // Sanity check
        if (!$path) {
            // Bad programmer! Bad Bad programmer!
            return VWP::raiseWarning(VText::_('Attempt to delete base directory'),get_class($this) . ":delete",500,false);   
        }

        // Initialize variables
	
        // Check to make sure the path valid and clean
        $path = $this->_fs->path()->clean($path);

        // Is this really a folder?
        if (!is_dir($path)) {
            return VWP::raiseWarning(VText::_('Path is not a folder'), 'Path: ' . $path,get_class($this) . ":delete",24,false);
        }

        // Remove all the files in folder if they exist
        $files = $this->files($path, '.', false, true, array());
        if (!empty($files)) {
            $w = $vfile->delete($files);      
            if (VWP::isWarning($w)) {
                return $w;
            }
        }

        // Remove sub-folders of folder
        $folders = $this->folders($path, '.', false, true, array());
        foreach ($folders as $folder) {
            if (is_link($folder)) {
                // Don't descend into linked directories, just delete the link.				
                $w = $vfile->delete($folder);
                if (VWP::isWarning($w)) {
                    return $w;
                }
            } else {
                $w = $this->delete($folder);
                if (VWP::isWarning($w)) {
                    return $w;
                }
            }
        }

        // In case of restricted permissions we zap it one way or the other
        // as long as the owner is either the webserver or the ftp
        if (@rmdir($path)) {
            $ret = true;
        } else {
            $ret = VWP::raiseWarning(VText::_('Could not delete folder') . ' Path: ' . $path,get_class($this) . ":delete",null,false);
        }
        return $ret;
    }

    /**
     * Copy a folder and the folders contents
     * 
     * @param string $src Source folder
     * @param string $dest Destination folder
     * @param boolean $force Overwrite existing files
     * @param object $sourceSystem Source folder object
     * @return true|object True on success, error or warning otherwise
     * @access public    
     */
                 
    function copy($src, $dest, $path = '', $force = false, $sourceSystem = null) 
    {
        // Initialize variables

        if ($path) {
            $src = $this->_fs->path()->clean($path . DS . $src);
            $dest = $this->_fs->path()->clean($path . DS . $dest);
        }

        // Eliminate trailing directory separators, if any
        $src = rtrim($src, DS);
        $dest = rtrim($dest, DS);

        if (!$this->exists($src)) {
            return VWP::raiseError(VText::_('Cannot find source folder'),get_class($this).":copy",-1,false);
        }

        if ($this->exists($dest) && !$force) {
            return VWP::raiseError(VText::_('Folder already exists'),get_class($this).":copy",-1,false);
        }

        // Make sure the destination exists
  
        $r = $this->create($dest);
        if (VWP::isWarning($r)) {
            return VWP::raiseError(VText::_('Unable to create target folder'),get_class($this).":copy",-1,false);
        }
    
        if (!($dh = @opendir($src))) {
            return VWP::raiseError(VText::_('Unable to open source folder'),get_class($this).":copy",-1,false);
        }

        // Walk through the directory copying files and recursing into folders.
        while (($file = readdir($dh)) !== false) {
            $sfid = $src . DS . $file;
            $dfid = $dest . DS . $file;
            switch (filetype($sfid)) {
                case 'dir':
                    if ($file != '.' && $file != '..') {
                        $ret = $this->copy($sfid, $dfid, null, $force);
                        if ($ret !== true) {
                            return $ret;
                        }
                    }
                    break;
                case 'file':
                    if (!@copy($sfid, $dfid)) {
                        return VWP::raiseError(VText::_('Copy failed'),get_class($this).":copy",-1,false);
                    }
                    break;
            }
        }
        return true;
    }
        
    /**
     * Moves a folder.
     *
     * @param string The path to the source folder.
     * @param string The path to the destination folder.
     * @param string An optional base path to prefix to the file names.
     * @return mixed True on success, error or warning otherwise
     * @access public
     */

    function move($src, $dest, $path = '') 
    {
        // Initialize variables

        if ($path) {
            $src = $this->_fs->path()->clean($path . DS . $src);
            $dest = $this->_fs->path()->clean($path . DS . $dest);
        }

        if (!$this->exists($src) && !is_writable($src)) {
            return VWP::raiseWarning(VText::_('Cannot find source folder'),get_class($this).":move",null,false);
        }

        if ($this->exists($dest)) {
            return VWP::raiseWarning(VText::_('Folder already exists'),get_class($this).":move",null,false);
        }
 
        if (!@rename($src, $dest)) {
            return VWP::raiseWarning(VText::_('Rename failed'),get_class($this)."::move",null,false);
        }
  
        return true;
    }

    /**
     * Check if folder exists
     *
     * @param string $path Folder path
     * @return boolean True if path is a folder
     * @access public
     */

    function exists($path) 
    {
        return is_dir($this->_fs->path()->clean($path));
    }

    /**
     * Get file list
     * 
     * @param string $path Folder path
     * @param string $filter Folder regular expression filter
     * @param boolean $recurse Recurse into sub-folders
     * @param boolean $fullpath Return full path names
     * @param array $exclude Excluded folders
     * @return array File list on success, error or warning otherwise
     * @access public  
     */                

    function files($path, $filter = '.', $recurse = false, $fullpath = false, $exclude = array('.svn', 'CVS')) 
    {
        // Initialize variables
        $arr = array();

        // Check to make sure the path valid and clean
        $path = $this->_fs->path()->clean($path);

        // Is the path a folder?
        if  (!is_dir($path)) {
             return VWP::raiseWarning(VText::_('Path is not a folder') . ' Path: ' . $path,get_class($this) . ":files",21,false);
        }

        // read the source directory
        $handle = opendir($path);
        while (($file = readdir($handle)) !== false) {
            if (($file != '.') && ($file != '..') && (!in_array($file, $exclude))) {
                $dir = $path . DS . $file;
                $isDir = is_dir($dir);
                if ($isDir) {
                    if ($recurse) {
                        if (is_integer($recurse)) {
                               $arr2 = $this->files($dir, $filter, $recurse - 1, $fullpath);
                        } else {
                               $arr2 = $this->files($dir, $filter, $recurse, $fullpath);
                        }
				
                        $arr = array_merge($arr, $arr2);
                    }
                } else {
                    if (preg_match("/$filter/", $file)) {
                        if ($fullpath) {
                            $arr[] = $path . DS . $file;
                        } else {
                            $arr[] = $file;
                        }
                    }
                }
            }
        }
        closedir($handle);
        asort($arr);
        return $arr;
    }

    /**
     * Get folder list
     * 
     * @param string $path Folder path
     * @param string $filter Folder regular expression filter
     * @param boolean $recurse Recurse into sub-folders
     * @param boolean $fullpath Return full path names
     * @param array $exclude Excluded folders
     * @return array Folder list on success, error or warning otherwise  
     */  

    function folders($path, $filter = '.', $recurse = false, $fullpath = false, $exclude = array('.svn', 'CVS')) 
    {
        // Initialize variables
        $arr = array();

        // Check to make sure the path valid and clean
        $path = $this->_fs->path()->clean($path);

        // Is the path a folder?
        if (!is_dir($path)) {
            return VWP::raiseWarning(VText::_('Path is not a folder') . ' Path: ' . $path,get_class($this) . ':folders', 21,false);			
        }

        // read the source directory
        $handle = opendir($path);
        while (($file = readdir($handle)) !== false) {
            if (($file != '.') && ($file != '..') && (!in_array($file, $exclude))) {
                $dir = $path . DS . $file;
                $isDir = is_dir($dir);
                if ($isDir) {
                    // Removes filtered directories
                    if (preg_match("/$filter/", $file)) {
                        if ($fullpath) {
                            $arr[] = $dir;
                        } else {
                            $arr[] = $file;
                        }
                    }
                    if ($recurse) {
                        if (is_integer($recurse)) {
                            $arr2 = $this->folders($dir, $filter, $recurse - 1, $fullpath);
                        } else {
                            $arr2 = $this->folders($dir, $filter, $recurse, $fullpath);
                        }
                        $arr = array_merge($arr, $arr2);
                    }
                }
            }
        }
        
        closedir($handle);

        asort($arr);
        return $arr;
    }

    /**
     * Get a folder tree
     * 
     * @param string $path Folder path
     * @param string $filter Regular expression filter
     * @param integer $maxlevel Maximum depth to recurse into
     * @param integer $level Current level
     * @param mixed $parent Parent folder
     * @return array Folder tree on success, error or warning otherwise
     * @access public    
     */             

    function listFolderTree($path, $filter, $maxLevel = 3, $level = 0, $parent = 0) 
    {
        $dirs = array ();
        if ($level == 0) {			
            self::$_folder_tree_index = 0;
        }
        if ($level < $maxLevel) {
            $folders = $this->folders($path, $filter);
            if (VWP::isWarning($folders)) {
                return $folders;
            }
            
            $vpath =& $this->_fs->path();
            
            // first path, index foldernames
            foreach ($folders as $name) {
                $id = ++self::$_folder_tree_index;
                $fullName = $vpath->clean($path . DS . $name);
                $dirs[] = array(
		            'id' => $id,
		            'parent' => $parent,
		            'name' => $name,
		            'fullname' => $fullName,
		            'relname' => str_replace(VPATH_BASE, '', $fullName)
		         );
                $dirs2 = $this->listFolderTree($fullName, $filter, $maxLevel, $level + 1, $id);
                if (VWP::isWarning($dirs2)) {
                    return $dirs2;
                }    
                $dirs = array_merge($dirs, $dirs2);
            }
        }
        return $dirs;
    }

    /**
     * Test if folder is empty
     * 
     * @param string $path Path
     * @return boolean True if path is empty
     * @access public
     */
  
    function is_empty($path)  
    {
 
        $files = $this->files($path,'.',false,array());
        $folders = $this->folders($path);
  
        if ((count($files) + count($folders)) < 1) {
            return true;
        }
        return false;
    }
 
    /**
     * Makes path name safe to use.
     *
     * @access	public
     * @param	string The full path to sanitise.
     * @return	string The sanitised string.
     */
  
    function makeSafe($path) 
    {
        $ds = (DS == '\\') ? '\\' . DS : DS;
        $regex = array('#[^A-Za-z0-9:\_\-' . $ds . ' ]#');
        return preg_replace($regex, '', $path);
    }

 
    // end class VFolder
}
 