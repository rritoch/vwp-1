<?php

/**
 * Virtual Web Platform - Path Support
 *  
 * This file provides Path Support   
 * 
 * @todo Rename VPath class to VLocalPath and create VPath abstract class
 * @package VWP
 * @subpackage Libraries.Filesystem.Local
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License
 */



/**
 * Virtual Web Platform - Path Support
 *  
 * This class provides Path Support   
 * 
 * @package VWP
 * @subpackage Libraries.Filesystem.Local
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License 
 */

class VPath extends VFilesystemDriver
{
	
    /**
     * Get instance of Folder object
     * 
     * @return object Folder object
     * @access public      
     */
    
    static function &getInstance(&$client) 
    {
 	    $vfolder =& VFilesystem::local()->path();
 	    return $vfolder;
    }	

    /**
     * Clean path name
     * 
     * @param string $path Path
     * @param string $ds Directory separator
     * @return string Clean path name
     * @access public  
     */
             
    function clean($path, $ds=DS) 
    {
        $path = trim($path);
        if (empty($path)) {
            $path = $ds;
        } else {
            // Remove double slashes and backslahses and convert all slashes and backslashes to DS
            $path = preg_replace('#[/\\\\]+#', $ds, $path);
        }
        return $path;
    }
 
 
    /**
     * Checks if a path's permissions can be changed
     *
     * @param	string	$path	Path to check
     * @return	boolean	True if path can have mode changed
     * @access public
     */

    function canChmod($path) 
    {
        $perms = fileperms($path);
        if ($perms !== false) {
            if (@ chmod($path, $perms ^ 0001)) {
                @chmod($path, $perms);
                return true;
            }
        }
        return false;
    }

    /**
     * Chmods files and directories recursivly to given permissions
     *
     * @param string $path Root path to begin changing mode [without trailing slash]
     * @param string $filemode Octal representation of the value to change file mode to [null = no change]
     * @param string $foldermode Octal representation of the value to change folder mode to [null = no change]
     * @return boolean True if successful [one fail means the whole operation failed] 
     * @access public
     */
  
    function setPermissions($path, $filemode = '0644', $foldermode = '0755') 
    {

        // Initialize return value
        $ret = true;

        if (is_dir($path)) {
            $dh = opendir($path);
            while ($file = readdir($dh)) {
                if ($file != '.' && $file != '..') {
                    $fullpath = $path.'/'.$file;
                    if (is_dir($fullpath)) {
                        if (!$this->setPermissions($fullpath, $filemode, $foldermode)) {
                            $ret = false;
                        }
                    } else {
                        if (isset ($filemode)) {
                            if (!@ chmod($fullpath, octdec($filemode))) {
                                $ret = false;
                            }
                        }
                    } // if
                } // if
            } // while
            
            closedir($dh);
            if (isset ($foldermode)) {
                if (!@ chmod($path, octdec($foldermode))) {
                    $ret = false;
                }
            }
        } else {
            if (isset ($filemode)) {
                $ret = @ chmod($path, octdec($filemode));
            }
        } // if
        return $ret;
    }

    /**
     * Get the permissions of the file/folder at a give path
     *
     * @param string $path	The path of a file/folder
     * @return string|object Filesystem permissions on success, error or warning otherwise
     * @access public
     */

    function getPermissions($path) 
    {
        $path = $this->clean($path);
        $mode = @ decoct(@ fileperms($path) & 0777);
        if (strlen($mode) < 3) {
            return '---------';
        }
        $parsed_mode = '';
        for ($i = 0; $i < 3; $i ++) {
            // read
            $parsed_mode .= ($mode { $i } & 04) ? "r" : "-";
            // write
            $parsed_mode .= ($mode { $i } & 02) ? "w" : "-";
            // execute
            $parsed_mode .= ($mode { $i } & 01) ? "x" : "-";
        }
        return $parsed_mode;
    }

    /**
     * Checks for snooping outside of the file system root
     *
     * @param string $path	A file system path to check
     * @return string|object A cleaned version of the path on success, error or warning otherwise
     */

    function check($path) 
    {
        if (strpos($path, '..') !== false) {
            return VWP::raiseError( 'Use of relative paths not permitted',get_class($this)."::check",null,false); // don't translate
        }
        $path = $this->clean($path);
        if (strpos($path, $this->clean(VPATH_ROOT)) !== 0) {
            return VWP::raiseError( 'Snooping out of bounds @ '.$path,'check',get_class($this)."::check",null,false); // don't translate   
        }
        return $path;
    }


    /**
     * Test if path is absolute
     * 
     * @param string $path Path to test  
     * @return boolean True if path is absolute
     * @access public      
     */
     
    function isAbsolute($path) 
    {
  
        // Windows path
        if (substr($path,1,1) == ":") {
            return true;
        }
  
        $path = $this->clean($path);
        return substr($path,0,1) == DS;
    }
 
    /**
     * Combine Paths
     * 
     * Usage: $this->combine($path1,$path2,...);
     *      
     * @param string $path1 Path 1
     * @return string Combined path
     */
           
    function combine($path1) 
    {
        $ret = $path1;
  
        $paths = func_get_args();
        foreach($paths as $path) {
            if ($this->isAbsolute($path)) {
                $ret = $path;
            } else {
                $ret .= DS.$path;
            }
        }
        return $this->clean($ret);
    }
 
    /**
     * Method to determine if script owns the path
     *
     * @static
     * @param string $path	Path to check ownership
     * @return boolean True if the php script owns the path passed
     */
  
    function isOwner($path) 
    {
        VWP::RequireLibrary('vwp.filesystem.file');  

        $length = 16;
        $salt = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $len = strlen($salt);
        $randmsg = '';
        $stat = @stat(__FILE__);
        if (empty($stat) || !is_array($stat)) {
            $stat = array(php_uname());	
        }
        mt_srand(crc32(microtime() . implode('|', $stat)));
        for ($i = 0; $i < $length; $i ++) {
            $randmsg .= $salt[mt_rand(0, $len -1)];
        }
  
        $tmp = md5($randmsg);
      
        $ssp = ini_get('session.save_path');
        $jtp = VPATH_SITE.DS.'tmp';

        // Try to find a writable directory
        $dir = is_writable('/tmp') ? '/tmp' : false;
        $dir = (!$dir && is_writable($ssp)) ? $ssp : false;
        $dir = (!$dir && is_writable($jtp)) ? $jtp : false;

        $vfile =& v()->filesystem()->file();
  
        if ($dir) {
            $test = $dir.DS.$tmp;
            // Create the test file
            $vfile->write($test, '');

            // Test ownership
   
            $return = (fileowner($test) == fileowner($path));

            // Delete the test file
  
            $vfile->delete($test);

            return $return;
        }

        return false;
    }

    /**
     * Searches the directory paths for a given file.
     *
     * @access protected
     * @param array|string	$path	An path or array of path to search in
     * @param string $file	The file name to look for.
     * @return mixed The full path and file name for the target file, or boolean false if the file is not found in any of the paths. 
     */

    function find($paths, $file) 
    {
        settype($paths, 'array'); //force to array

        // start looping through the path set
        foreach ($paths as $path) {
            // get the path to the file
            $fullname = $path.DS.$file;

            // is the path based on a stream?
            if (strpos($path, '://') === false) {
                // not a stream, so do a realpath() to avoid directory traversal attempts on the local file system. 
                $path = realpath($path); 
                $fullname = realpath($fullname);
            }
   
            if (file_exists($fullname) && substr($fullname, 0, strlen($path)) == $path) {
                return $fullname;
            }
        }

        // could not find the file in the set of paths
        return false;
    }
            
    // end class VPath
} 