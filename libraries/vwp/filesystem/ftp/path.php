<?php

/**
 * Virtual Web Platform - FTP Path Support
 *  
 * This file provides FTP Path Support   
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

VWP::RequireLibrary('vwp.filesystem.local.path');

/**
 * Virtual Web Platform - FTP Path Support
 *  
 * This class provides FTP Path Support   
 * 
 * @package VWP
 * @subpackage Libraries.Filesystem.FTP
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License   
 */

class VFTPPath extends VPath 
{
    
    /**
     * FTP Client
     * 
     * @var object $_client FTP Client
     * @access private
     */
           
    protected $_client;
    
    /**
     * Get instance of Path object
     * 
     * @return object Folder object
     * @access public      
     */
 
    public static function &getInstance(&$client) 
    {
 	    $fs =& Filesystem::getInstance($client,'ftp');
        $vpath =& v()->filesystem($fs)->path();
        return $vpath;
    }
 
    /**
     * Clean path name
     * 
     * @param string $path Path
     * @param string $ds Directory separator
     * @return string Clean path name
     * @access public  
     */
       
    function clean($path, $ds="/") 
    {
        $path = trim($path);
        if (empty($path)) {
            $path = '/';
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
     * @return	boolean	Returns true as this driver has no ability to test for this capability 
     * @access public
     */

    function canChmod($path) 
    {
        return true;
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

        $vfolder =& $this->_fs->folder();
        
        if ($vfolder->exists($path)) {
        	
        	// CHMOD Files
        	
        	$files = $vfolder->files($path);
        	if (!VWP::isWarning($files)) {
        		
        		foreach($files as $file) {
        			$fullpath = $path.'/'.$file;
        		    if (!$this->setPermissions($fullpath, $filemode, $foldermode)) {
                        $ret = false;
                    }        			
        		}
        	}
        	
        	// CHMOD Folders
        	
            $files = $vfolder->folders($path);
        	if (!VWP::isWarning($files)) {
        		
        		foreach($files as $file) {
        			$fullpath = $path.'/'.$file;
        		    if (!$this->setPermissions($fullpath, $filemode, $foldermode)) {
                        $ret = false;
                    }        			
        		}
        	}

            if (isset ($foldermode)) {
            	$r = $this->_client->chmod($path,$foldermode);
            	if (VWP::isWarning($r)) {
            		$ret = false;
            	} 
            }
        } else {
            if (isset ($filemode)) {
            	$r = $this->_client->chmod($path,$filemode);
            	if (VWP::isWarning($r)) {
            		$ret = false;
            	}                
            }
        } // if
        return $ret;
    }
    
    /**
     * Get the permissions of the file/folder at a give path
     *     
     * @param string $path	The path of a file/folder
     * @return string|object Returns warning as this driver has no way of testing for file permissions
     * @access public
     */

    function getPermissions($path) {    	
        return VWP::raiseWarning('Feature not supported!',get_class($this),null,false);    	
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
        return $path;
    } 

    /**
     * Test if path is absolute
     *
     * @todo Implement isAbsolute()
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
  
        $path = $this->_fs->path()->clean($path);
        return substr($path,0,1) == DS;
    }    

    /**
     * Combine Paths
     * 
     * Usage: $this->_fs->path()->combine($path1,$path2,...);
     *      
     * @param string $path1 Path 1
     * @return string Combined path
     */
           
    function combine($path1) 
    {
        $ret = $path1;
  
        $paths = func_get_args();
        foreach($paths as $path) {
            if ($this->_fs->path()->isAbsolute($path)) {
                $ret = $path;
            } else {
                $ret .= DS.$path;
            }
        }
        return $this->_fs->path()->clean($ret);
    }
 
    /**
     * Method to determine if script owns the path
     *
     * @static
     * @param string $path	Path to check ownership
     * @return boolean Returns false as this is a remote filesystem
     */
  
    function isOwner($path) 
    {
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
    	$vfile =& $this->_fs->file();
    	
        settype($paths, 'array'); //force to array

        // start looping through the path set
        foreach ($paths as $path) {
            // get the path to the file
            $fullname = $this->clean($path.DS.$file);
   
            if ($vfile->exists($fullname)) {
                return $fullname;
            }
        }

        // could not find the file in the set of paths
        return false;
    }

    /**
     * Class Constructor
     *      
     * @param unknown_type $filesystem
     */
    
    function __construct(&$filesystem) 
    {
        parent::__construct($filesystem);
        $this->_client =& $this->_fs->client();	
    }
         
    // end class VFTPPath
} 
