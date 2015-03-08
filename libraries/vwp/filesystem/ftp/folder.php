<?php

/**
 * Virtual Web Platform - FTP Folder Support
 *  
 * This file provides FTP Folder Support   
 * 
 * @package VWP
 * @subpackage Libraries.Filesystem
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License   
 */

// Restrict access

class_exists('VWP') || die();

/**
 * Require folder support
 */

VWP::RequireLibrary('vwp.filesystem.local.folder');

/**
 * Virtual Web Platform - FTP Folder Support
 *  
 * This class provides FTP Folder Support   
 * 
 * @package VWP
 * @subpackage Libraries.Filesystem
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License   
 */

 
class VFTPFolder extends VFolder 
{
		
    /**
     * FTP Client
     * 
     * @var object $_client FTP Client
     * @access private
     */
           
    protected $_client;

    /**
     * FTP File
     * 
     * @var object $_vfile FTP File object
     * @access private  
     */
  
    protected $_vfile;
    
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
            $dir = $cfg->temp_dir;
        }
        
        $charlist = '0123456789abcdefghijklmnopqrstuvwxyz';
  
        if (!$this->exists($dir)) {
            $result = $this->create($dir);
            if (VWP::isWarning($result)) {
                return $result;
            }
        }
              
        $used = $this->folders($dir,'.');
        if (VWP::isWarning($used)) {
            return $used;
        }
     
        $base = strlen($charlist);
  
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
  
        $newpath = $dir.DS.$filename;
        $result = $this->create($newpath);
  
        if (VWP::isWarning($result)) {
            return $result;
        }  
        return $newpath;      
    }

    /**
     * Get instance of Folder object
     *
     * @param object Client
     * @return VFTPFolder Folder object
     * @access public      
     */
 
    public static function &getInstance(&$client) 
    {
 	    $fs =& Filesystem::getInstance($client,'ftp');
        $vfolder =& v()->filesystem($fs)->folder();
        return $vfolder;
    }

    /**
     * Get instance of File object
     * 
     * @return VFTPFile File object
     * @access public      
     */ 
  
    function &getFileInstance() 
    {
    
        if (!isset($this->_vfile)) {
            $this->_vfile =& $this->_fs->file();
        }
        return $this->_vfile;
    }
  
    /**
     * Parse file list
     *
     * @param string $list File list   
     * @access private
     */
         
    function _parseList($list) 
    {
        $re_date = '('
           . '\\w\\w\\w\\s+\\d+\\s+\\d+:\\d\\d'
           . '|'
           . '\\w\\w\\w\\s+\\d+\\s+\\d\\d\\d\\d'
           . ')';

        $re = '#^'
                  . '([\\-ld])' // type
                  . '([\\-r][\\-w][\\-xs][\\-r][\\-w][\\-xs][\\-r][\\-w][\\-xs])' // perms
                  . '\\s+'
                  . '(\\d+)' // filecode                   
                  . '\\s+'
                  . '(\\w+)' // owner
                  . '\\s+'
                  . '(\\w+)' // group
                  . '\\s+'
                  . '(\\d+)' // size
                  . '\\s+'                   
                  . $re_date // date
                  . '\\s+'                   
                  . '(.+)' // name
                  . '$#';
   
   
        $keys = array("_","type","permissions","code","owner","group","size","date");
        $key_len = count($keys);
        $dir = array();
           
        foreach(explode("\n",$list) as $line) {                        
            if (preg_match($re,$line,$matches)) {
                $ent = array();
                for($p = 1; $p < $key_len;$p++) {
                    $ent[$keys[$p]] = $matches[$p];
                }
                $rest = $matches[$key_len];
                $parts = explode("->",$rest);
                $ent["name"] = trim(array_shift($parts));
                if (count($parts) > 0) {
                    $ent["link"] = implode("->",$parts);
                }
                array_push($dir,$ent);  
            } 
        }
 
        return $dir;
    }


    /**
     * Create a folder -- and all necessary parent folders.
     *
     * @param string A path to create from the base path.
     * @param int Directory permissions to set for folders created.
     * @return boolean True if successful.
     * @access public
     */

    function create($path = '', $mode = 0755) 
    {
   
        $args = func_get_args();
   
        // Initialize variables
        static $nested = 0;

        // Check to make sure the path valid and clean
        $path = $this->_fs->path()->clean($path);

        // Check if parent dir exists
        $parent = dirname($path);
      
        if (!$this->exists($parent)) {
            // Prevent infinite loops!
            $nested++;

            if ($parent == $path) {
    
                $ret = $this->_client->mkd($path);
   
                if (VWP::isWarning($ret)) {
                    $nested--;
                    return VWP::raiseError(VText::_('Invalid path: ' . $path),get_class($this) . ':create',null,false );     
                }
            }
    
            if ($nested > 20) {
                $nested--;
                return VWP::raiseError(VText::_('Infinite loop detected'),get_class($this) . ':create',null,false);      
            }

            // Create the parent directory
            $cr = $this->create($parent, $mode);
            if (VWP::isError($cr)) {
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
   
        $ret = $this->_client->mkd($path);
   
        if (VWP::isWarning($ret)) {
            return $ret;
        }
  
        $ret = true;   
        $this->_client->chmod($path,$mode);    
        return $ret;
    }

    /**
     * Delete a folder.
     *
     * @param string The path to the folder to delete.
     * @return boolean True on success.
     * @access public	 
     */

    function delete($path) 
    {
  
        $vfile =& $this->getFileInstance();
	 
        // Sanity check
        if (!$path) {
            // Refuse to wipe entire system!
            return VWP::raiseWarning(VText::_('Attempt to delete base directory'),get_class($this) . ":delete",500,false);   
        }

        // Initialize variables
	
        // Check to make sure the path valid and clean
        $path = $this->_fs->path()->clean($path);

        // Is this really a folder?
        if (!$this->exists($path)) {
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
            $w = $this->delete($folder);
            if (VWP::isWarning($w)) {
                return $w;
            }   
        }
  
        $result = $this->_client->rmd($path);
        if (VWP::isWarning($result)) {
            return $result;
        }  
        return true;
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
      
        if ($sourceSystem === null) {
            $sourceSystem =& $this;
        }
      
        if ($path) {
            $src = $this->_fs->path()->clean($path . DS . $src);
            $dest = $this->_fs->path()->clean($path . DS . $dest);
        }

        // Eliminate trailing directory separators, if any
        $src = rtrim($src, DS);
        $dest = rtrim($dest, DS);

        if (!$sourceSystem->exists($src)) {
            return VWP::raiseError(VText::_('Cannot find source folder: ') . $src,get_class($this) . ":copy", -1, false);
        }

        if ((!$force) && $this->exists($dest)) {
            return VWP::raiseError(VText::_('Folder already exists'),get_class($this),-1,false);
        }

        // Make sure the destination exists
        $w = $this->create($dest);
  
        if (VWP::isWarning($w)) {
            return VWP::raiseError(VText::_('Unable to create target folder') . ': ' . $dest . ": " . $w->errmsg,get_class($this),-1,false);
        }
    
        if (!($dh = @opendir($src))) {
            return VWP::raiseError(VText::_('Unable to open source folder'),get_class($this),-1,false);
        }

        // Walk through the directory copying files and recursing into folders.
  
        $folder_list = $sourceSystem->folders($src);
        if (VWP::isWarning($folder_list)) {
            return $folder_list;
        }
  
        foreach($folder_list as $folder) {
            if ($folder != '.' && $folder != '..') {
                $sfid = $src.DS.$folder;
                $dfid = $dest.DS.$folder;
                $ret = $this->copy($sfid,$dfid,null,$force,$sourceSystem);
                if (VWP::isWarning($ret)) {
                    return $ret;
                }    
            }  
        }
  
        $sfile =& $sourceSystem->getFileInstance();
        $dfile =& $this->getFileInstance();
  
        $file_list = $sourceSystem->files($src);
        if (VWP::isWarning($file_list)) {
            return $folder_list;
        }
    
        foreach($file_list as $filename) {
            $sfid = $src.DS.$filename;
            $dfid = $dest.DS.$filename;
   
            // Read File from source   
            $data = $sfile->read($sfid);
            if (VWP::isWarning($data)) {
                 return $ret;
            }
   
            // Write file to destination
            $ret = $dfile->write($dfid,$data);
            if (VWP::isWarning($ret)) {
                return $ret;
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

        if (!$this->exists($src)) {
            return VWP::raiseWarning(VText::_('Cannot find source folder'),get_class($this).":move",null,false);
        }

        if ($this->exists($dest)) {
            return VWP::raiseWarning(VText::_('Folder already exists'),get_class($this).":move",null,false);
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
     * Check if folder exists
     *
     * @param string $path Folder path
     * @return boolean True if path is a folder
     * @access public
     */
   
    function exists($path) 
    {
 	  	
        // get PWD
   	 
        $response = $this->_client->pwd();
        if (!$this->_client->isWarning($response)) {    
            $path_info = $response->getPathInfo();        
            if (count($path_info) > 0) {
                $pwd = $this->_client->cleanFullPath($path_info[0]);     
            }
        }
   
        // CWD $path
 	 
        $reply = $this->_client->cwd($path);
       	 
        if ($this->_client->isWarning($reply)) {
            return false; 
        }     	 	 
	 
        if (isset($pwd)) {
            $this->_client->cwd($pwd);
        }   
        return true;
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
     */  
  
    function files($path, $filter = '.', $recurse = false, $fullpath = false, $exclude = array('.svn', 'CVS')) 
    {
        // Initialize variables
        $arr = array();

        // Is the path a folder?
        if (!$this->exists($path)) {
            return VWP::raiseWarning(VText::_('Path is not a folder') . ' Path: ' . $path,get_class($this) . ":folders",21,false);			
        }
    
        $reply = $this->_client->doList($path);
        if ($this->_client->isWarning($reply)) {
            return $reply;
        }
        
        $list = $reply->getData();
        
        $dhandle = $this->_parseList($list);  
   		
        foreach($dhandle as $fh) {
            $file = $fh["name"];
            if (
                 ($file != '.') && 
                 ($file != '..') && 
                 (!in_array($file, $exclude))
                ) {
                $dir = $path . '/' . $file;
                $isDir = ($fh["type"] == "d");
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
                    // Removes filtered directories
                    if (preg_match("/$filter/", $file)) {
                        if ($fullpath) {
                            $arr[] = $dir;
                        } else {
                            $arr[] = $file;
                        }
                    }        
                }
            }
        }
				
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

        // Is the path a folder?
        if (!$this->exists($path)) {
            return VWP::raiseWarning(VText::_('Path is not a folder'), 'Path: ' . $path,get_class($this) . ":folders",21,false);			
        }
    
        $reply = $this->_client->doList($path);
        if ($this->_client->isWarning($reply)) {
            return $reply;
        }
        
        $list = $reply->getData();
        
        $dhandle = $this->_parseList($list);  
   		
        foreach($dhandle as $fh) {
            $file = $fh["name"];
            if (
                 ($file != '.') && 
                 ($file != '..') && 
                 (!in_array($file, $exclude))
                ) {
                $dir = $path . '/' . $file;
                $isDir = ($fh["type"] == "d");
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
     
     // end class VFTPFolder    
} 
 