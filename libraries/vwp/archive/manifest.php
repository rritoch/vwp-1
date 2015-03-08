<?php

/**
 * VWP Manifest 
 * 
 * This file contains the VWP Manifest interface  
 *     
 * @package    VWP
 * @subpackage Libraries.Archive
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License 
 */

// Restricted access
class_exists("VWP") or die();

/**
 * Require XML Document Support
 */
 
VWP::RequireLibrary("vwp.documents.xml"); 

/**
 * VWP Manifest 
 * 
 * This class provides the VWP Manifest interface  
 *     
 * @package    VWP
 * @subpackage Libraries.Archive
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License   
 */
 
class VManifest extends VObject 
{
 
    /**
     * @var object $_vfile Filesystem connection
     * @access private
     */
        
    protected $_vfile;
  
    /**
     * @var string $_filename Source file
     * @access private
     */
        
    public $_filename;
 
    /**
     * @var object $_src_doc Source document
     * @access private
     */
       
    public $_src_doc;
 
    /**
     * @var object $_error Last error
     * @access private
     */
       
    public $_error;
 
    /**
     * @var string $_app Application ID
     * @access public
     */
         
    public $_app;

    /**
     * @var string $_theme_type Theme Type
     * @access public
     */
         
    public $_theme_type;
 
    /**
     * @var string $_theme_id Theme Type
     * @access public
     */
         
    public $_theme_id;
 
    /**
     * @var string $name Application name
     * @access public
     */
       
    public $name;
 
    /**
     * @var string $author Author
     * @access public
     */
       
    public $author;
 
    /**
     * @var string $author_link Author URL
     * @access public
     */
       
    public $author_link;
 
    /**
     * @var string Copyright notice
     * @access public
     */
       
    public $copyright;
 
    /**
     * @var string Version
     * @access public
     */
       
    public $version;
 
    /**
     * @var string Version status
     * @access public
     */
       
    public $version_status;
 
    /**
     * @var string Version start date
     * @access public
     */
       
    public $version_start_date;
 
    /**
     * @var string Version release date
     * @access public
     */
       
    public $version_release_date;
 
    /**
     * @var string Version build
     * @access public
     */
       
    public $version_build;
 
    /**
     * @var array $folders Application folders indexed by version
     * @access public
     */
       
    public $folders = array();
 
    /**
     * @var array $install Install settings
     * @access public
     */
       
    public $install;
 
    /**
     * Get an instance of a manfiest object
     * 
     * @return object Manfiest object
     * @access public         
     */     
 
    public static function &getInstance() 
    {
        static $m_list = array();   
        $idx = count($m_list);
        $m_list[] = new VManifest;
        return $m_list[$idx];
    }
 
 
    /**
     * Add a new file
     * 
     * @param string $type System type
     * @param string $filename Filename
     * @param string $version Version
     * @access public
     */
  
    function addFile($type,$filename,$version) 
    {
        if (!isset($this->folders[$version])) {
            $this->folders[$version] = array();;
        }  
        if (!isset($this->folders[$version][$type])) {
            $this->folders[$version][$type] = array();
        } 
        array_push($this->folders[$version][$type],$filename); 
    }
   
    /**
     * Set application ID
     * 
     * @param string $appName Application ID
     * @access public      
     */
       
    function setApplication($appName) 
    {
        $this->_app = $appName;
        $this->_theme_type = null;
        $this->_theme_id = null;
    }

    /**
     * Set Theme
     * 
     * @param string $themeType Theme Type
     * @param string $themeId Theme ID
     * @access public      
     */
       
    function setTheme($themeType,$themeId) 
    {
        $this->_app = null;
        $this->_theme_type = $themeType;
        $this->_theme_id = $themeId;
    }
 

    /**
     * Get folder list
     * 
     * @param string $version Version
     * @return array Folders
     * @access public   
     */
                
    function getFolders($version = null) 
    {
        if ($version === null) {
            $version = $this->version;
        }
        return $this->folders[$version];
    }
 
    /**
     * Get list of manifest text info fields
     * 
     * @return array Manifest fields
     * @access private   
     */
             
    function _getFields() 
    {
        return array( "name",
                      "author",
                      "author_link",
                      "copyright",
                      "version",
                      "version_status",
                      "version_start_date",
                      "version_release_date",
                      "version_build",
                      );
 
    }
 
    /**
     * Get manifest information
     * 
     * @return array Manifest Info
     * @access public
     */
             
    function getInfo() 
    {
        $info = array("_app"=>$this->_app,
                "_theme_id"=>$this->_theme_id,
                "_theme_type"=>$this->_theme_type);
        $fields = $this->_getFields();
        foreach($fields as $fld) {
            $info[$fld] = $this->$fld;   
        }
        $info["install"] = $this->install;
        return $info;
    }
 
    /**
     * Clean manifest object
     * 
     * @access public 
     */
            
    function clean() 
    {
        $fields = $this->_getFields();
        foreach($fields as $fld) {
            $this->$fld = null;
        }
        $this->folders = array();
        $this->_src_doc = null;
        $this->_error = null;
        $this->_filename = null;   
    }
 
    /**
     * Parse manifest folder branch
     * 
     * @param object $folder Document folder element
     * @return array File list
     * @access private
     */
           
    function _parseFolder($folder) 
    {
        $files = array();
        for($ptr = 0; $ptr < $folder->childNodes->length; $ptr++) {
            $elem = $folder->childNodes->item($ptr);
            if ($elem->nodeName == "file") {
                array_push($files,$elem->nodeValue);
            }   
        }
        return $files;
    }
 
    /**
     * Create a folder element
     * 
     * @param string $name Subsystem name
     * @param array $data File list
     * @param string $version Version
     * @access private
     */
               
    function _createFolder($name,$data,$version = null) 
    {
  
        if ($version === null) {
            $version = $this->version;
        }
        $folder = $this->_src_doc->createElement("folder");
        $folder->setAttribute("name",$name);
        $folder->setAttribute("version",$version);
        foreach($data as $filename) {
            $space = $this->_src_doc->createTextNode("\n  ");
            $file = $this->_src_doc->createElement("file",XMLDocument::xmlentities($filename));
            $folder->appendChild($space);
            $folder->appendChild($file);
        }
        $space = $this->_src_doc->createTextNode("\n ");
        $folder->appendChild($space);
        return $folder;
    }
 
    /**
     * Load a manifest file
     * 
     * @param string $filename Filename
     * @return true|object True on success, error or warning otherwise
     */
             
    function load($filename = null) 
    {
 
        // Clean Object
  
        if (empty($filename)) {
            $filename = $this->_filename;
        }   
        $this->clean();
  
        // Initialize
  
        $this->_filename = $filename;   
        $this->_src_doc = new DomDocument;
  
        // Load Document
  
        $data = $this->_vfile->read($filename);
  
        if (VWP::isWarning($data)) {
            $this->_error = $data;
            return $this->_error;
        }
  
        $result = @ $this->_src_doc->loadXML($data);   
        if (!$result) {
            $this->_error = VWP::raiseError("Cannot parse manifest!","VManifest",null,false);
            return $this->_error;       
        }
  
        // Initialize Fields
  
        $fields = $this->_getFields();
        $this->install = array();
  
        $this->_app = $this->_src_doc->documentElement->getAttribute('app');
        $this->_theme_type = $this->_src_doc->documentElement->getAttribute('theme_type');
        $this->_theme_id = $this->_src_doc->documentElement->getAttribute('theme_id');
  
        for($ptr = 0; $ptr < $this->_src_doc->documentElement->childNodes->length; $ptr++) {
            $elem = $this->_src_doc->documentElement->childNodes->item($ptr);
            $key = $elem->nodeName;
            if (in_array($key,$fields)) {
                $this->$key = $elem->nodeValue;
            }
            if ($key == "folder") {
                $folder_name = $elem->getAttribute("name");
                $folder_version = $elem->getAttribute("version");
                if (strlen($folder_version) < 1) {
                    $folder_version = $this->version; 
                }
                if (!isset($this->folders[$folder_version])) {
                    $this->folders[$folder_version] = array();
                }
                $this->folders[$folder_version][$folder_name] = $this->_parseFolder($elem);     
            }
            if ($key == "install") {
                for($i=0;$i < $elem->childNodes->length; $i++) {
                    $ie = $elem->childNodes->item($i);
                    $iname = $ie->nodeName;
                    $ival = $ie->nodeValue;
                    if ($iname !== "#text") {      
                        $this->install[$iname] = $ival;
                    }           
                }
            }
        }       
        return true;   
    }

    /**
     * Save manifest
     * 
     * @param string $filename Filename
     * @return true|object True on success, error or warning otherwise  
     * @access public
     */
           
    function save($filename = null) 
    {
 
        if (empty($filename)) {
            $filename = $this->_filename;
        }
          
        // Include Manifest INFO
  
        $info = $this->getInfo();

        // Init Doc if one not built
   
        if (empty($this->_src_doc)) {   
            $nl = "\r\n";
            $this->_src_doc = new DomDocument;
   
            if (empty($info['_app'])) {
                // Theme
    
   	            $txt =  '<theme_manifest ';
                $txt .= 'xmlns="http://standards.vnetpublishing.com/schemas/vwp/2010/10/ThemeManifest" ';
                $txt .= 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ';
                $txt .= 'xsi:schemaLocation="http://standards.vnetpublishing.com/schemas/vwp/2010/10/ThemeManifest http://standards.vnetpublishing.com/schemas/vwp/2010/10/ThemeManifest/" ';
                $txt .= 'theme_type="" '; 
                $txt .= 'theme_id=""></theme_manifest>';
            } else {
   	 
            	 // Application
   	 
                $txt = '<application_manifest '; 
                $txt .= 'xmlns="http://standards.vnetpublishing.com/schemas/vwp/2010/10/ApplicationManifest" ';
                $txt .= 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ';
                $txt .= 'xsi:schemaLocation="http://standards.vnetpublishing.com/schemas/vwp/2010/10/ApplicationManifest http://standards.vnetpublishing.com/schemas/vwp/2010/10/ApplicationManifest/" ';              
                $txt .= 'app=""></application_manifest>';
            }
   
            $this->_src_doc->loadXML($txt);
        }  
  
  
        for($ptr = 0; $ptr < $this->_src_doc->documentElement->childNodes->length; $ptr++) {
            $elem = $this->_src_doc->documentElement->childNodes->item($ptr);
            $key = $elem->nodeName;
            if (isset($info[$key])) {
                if (is_array($info[$key])) {
                    $new_item = $this->_src_doc->createElement($key);
                    foreach($info[$key] as $sk=>$sv) {
                        $new_item->appendChild($this->_src_doc->createElement($sk,XMLDocument::xmlentities($sv)));             
                    }
                    $this->_src_doc->documentElement->removeChild($elem);
                    $this->_src_doc->documentElement->appendChild($new_item);      
                } else {
                    $elem->nodeValue = XMLDocument::xmlentities($info[$key]);
                }
                unset($info[$key]);     
            }
            if ($key == "folder") {
                $this->_src_doc->documentElement->removeChild($elem);
            }
        }
  
        if (empty($info['_app'])) {
  	         $this->_src_doc->documentElement->setAttribute('theme_id',$info["_theme_id"]);
  	         $this->_src_doc->documentElement->setAttribute('theme_type',$info["_theme_type"]);
        } else {
             $this->_src_doc->documentElement->setAttribute('app',$info["_app"]);
        }
           
        foreach($info as $key=>$val) {
            if ($val === null) {
                $val = '';	
            }
            if (is_string($val) && (substr($key,0,1) != "_")) {
                $space = $this->_src_doc->createTextNode("\n ");
                $elem = $this->_src_doc->createElement($key,XMLDocument::xmlentities($val));
                $this->_src_doc->documentElement->appendChild($space);
                $this->_src_doc->documentElement->appendChild($elem);
            }   
        }
  
        // Include manifest Folders
  
        foreach($this->folders as $version=>$folderlist) {
            foreach($folderlist as $name=>$data) {
                $space = $this->_src_doc->createTextNode("\n ");
                $elem = $this->_createFolder($name,$data,$version);
                $this->_src_doc->documentElement->appendChild($space);
                $this->_src_doc->documentElement->appendChild($elem);
            }
        }
  
        $data = $this->_src_doc->saveXML();
  
        return $this->_vfile->write($filename,$data);       
    }
 
    /**
     * Get Theme ID
     * 
     * @return string Theme ID
     * @access public
     */
 
    function getThemeId() 
    {
 	    return $this->_theme_id;
    }
 
    /**
     * Class constructor
     *   
     * @access public
     */ 
      
    function __construct() 
    {
        parent::__construct();
        $this->_vfile =& v()->filesystem()->file();  
    }
    
    // end class VManifest
}
 