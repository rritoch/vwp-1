<?php

/**
 * Article Model 
 *  
 * @package VWP.Content
 * @subpackage Models
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch 2011 - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License
 */

/**
 * Require Model Support
 */

VWP::RequireLibrary("vwp.model");

/**
 * Require Registry Support
 */

VWP::RequireLibrary('vwp.sys.registry');

/**
 * Article Model
 *  
 * @package    VWP.Content
 * @subpackage Models
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch 2011 - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License 
 */

class Content_Model_Article extends VModel 
{
 
	/**
	 * Article Id
	 * 
	 * @var integer $id Article Id
	 * @access public
	 */
	
    public $id = null;
    
    /**
     * Article Title
     * 
     * @var string $title Title
     * @access public
     */
    
    public $title = null;
    
    /**
     * Article Content
     * 
     * @var string $content Content
     * @access public
     */
    
    public $content = null;
    
    /**
     * Article Filename
     * 
     * @var string $filename Filename
     * @access public
     */
    
    public $filename = null;
    
    /**
     * Article Description
     * 
     * @var string $description Description
     * @access public
     */
    
    public $description = null;
    
    /**
     * Article Keywords
     * 
     * @var string $keywords Keywords
     * @access public
     */
    
    public $keywords = null;
    
    /**
     * Article Category
     * 
     * @var string $category Category
     * @access public
     */
    
    public $category = null;
    
    /**
     * Author Name
     * 
     * @var string $author_name Author Name
     * @access public
     */
    
    public $author_name = null;
    
    /**
     * Author Username
     * 
     * @var string $author_username Author Username
     * @access public
     */
        
    public $author_username = null;
    
    /**
     * Published
     * 
     * @var integer $published Published
     * @access public
     */
    
    public $published = null;
    
    /**
     * Order
     * 
     * @var integer $order Order
     * @access public
     */
    
    public $order = null;
    
    /**
     * Release Date
     * 
     * @var VTime $release_date Release Date
     * @access public
     */
    
    public $release_date = null;
 
    /**
     * Get Editor
     * 
     * @param array $config Configuration
     * @return string|object Editor HTML on success, error or warning otherwise
     * @access public
     */
    
    function getEditor($config = array()) 
    {
        $cfg = $this->getConfig();
        if (VWP::isWarning($cfg)) {
            return '<p>Content manager not configured</p>';
        }
  
        $default_editor = $cfg['default_editor'];

        $key = "TOOLS\\Editors\\" . $default_editor;
  
        $localMachine = & Registry::LocalMachine();
  
        $result = Registry::RegOpenKeyEx($localMachine,
                        $key,
                        0,
                        0, //samDesired
                        $registryKey);
                         
        if (VWP::isWarning($result)) {
            Registry::RegCloseKey($localMachine);
            return '<p>Default editor not registered!</p>';
        }
         
        $data = array();
        $idx = 0;
        $keylen = 255;
        $vallen = 255;
        $lptype = REG_SZ; 
        while (!VWP::isError($result = Registry::RegEnumValue(
                                    $registryKey,
                                    $idx++,
                                    $key,
                                    $keylen,
                                    0, // reserved
                                    $lpType,
                                    $val,
                                    $vallen)))  {
            if (!VWP::isWarning($result) || $result->errno = ERROR_MORE_DATA) {                                       
                $editor[$key] = $val;
                $keylen = 255;
                $vallen = 255;  
            }
        }
          
        Registry::RegCloseKey($registryKey);
        Registry::RegCloseKey($localMachine);
  
        $app = $editor['app'];
        $widget = $editor['widget'];
        $config["widget"] = $widget;
        $config["name"] = "article[content]";
        $config["value"] = $this->content;
  
        $appId = $app;
  
        if (!empty($widget)) {
            $appId .= '.' . $widget;
        }
        
        $args = $appId;
        $stdout = new VStdio;
  
        $shellob =& v()->shell();
  
        $env = array('any'=>$config);
                 
        $result = $shellob->execute($args,$env,$stdout);
      
        return $stdout->getOutBuffer();
    }
 
    /**
     * Load Article
     * 
     * @param integer $articleid Article Id
     * @return boolean|object True on success, error or warning otherwise
     * @access public
     */
    
    function load($articleid = null) 
    {
        $config = $this->getConfig();  
        $tbl_prefix = $config["table_prefix"];  
        $table_name = $tbl_prefix . 'content_articles'; 
        $table = & $this->_dbi->getTable($table_name);
  
        if (VWP::isWarning($table)) {
            return $table;
        }
  
        $row = $table->getRow($articleid);

        if (VWP::isWarning($row)) {
            return $row;
        }
    
        $data = $row->getProperties();
        $fields = array();
        foreach($data["fields"] as $key=>$val) {
            $fields[$key] = $val["value"];
        }
        $this->setProperties($fields);
  
        return true;
    }

    /**
     * Save Article
     * 
     * @return boolean True on success, error or warning otherwise
     * @access public
     */
    
    function save() 
    {
        $config = $this->getConfig(); 
    
        if (VWP::isWarning($config)) {
            return $config;
        }

        if (empty($this->category)) {
  	        $this->category = null;
        }
        $tbl_prefix = $config["table_prefix"];  
        $table_name = $tbl_prefix . 'content_articles'; 
        $table = & $this->_dbi->getTable($table_name);
  
        if (VWP::isWarning($table)) {
            return $table;
        }
  
        $row = $table->getRow($this->id);

        if (VWP::isWarning($row)) {
            return $row;
        }
    
        $data = $this->getProperties();    
  
        foreach($data as $key=>$val) {
            $row->set($key,$val);
        }
    
        $result = $row->save();
        
        if (VWP::isWarning($result)) {
            return $result;
        }
  
        $data = $row->getProperties();
        $fields = array();
        
        foreach($data["fields"] as $key=>$val) {
            $fields[$key] = $val["value"];
        }
        
        $this->setProperties($fields);  
  
        return true; 
    }
  
    /**
     * Get Configuration Key
     * 
     * @access public
     */
    
    function getConfigKey() 
    {
        return "SOFTWARE\\VNetPublishing\\Content\\Config";
    }
 
    /**
     * Get configuration settings
     * 
     * @return array|object Configuration settings on success, error or warning otherwise
     * @access public
     */       
 
    function getConfig() 
    {
        $localMachine = & Registry::LocalMachine();
  
        $result = Registry::RegOpenKeyEx($localMachine,
                        self::getConfigKey(),
                        0,
                        0, //samDesired
                        $registryKey);
                         
        if (!VWP::isWarning($result)) {     
            $data = array();
            $idx = 0;
            $keylen = 255;
            $vallen = 255;
            $lptype = REG_SZ; 
            while (!VWP::isError($result = Registry::RegEnumValue(
                                     $registryKey,
                                     $idx++,
                                     $key,
                                     $keylen,
                                     0, // reserved
                                     $lpType,
                                     $val,
                                     $vallen)))  {
                if (!VWP::isWarning($result) || $result->errno = ERROR_MORE_DATA) {                                       
                    $data[$key] = $val;
                    $keylen = 255;
                    $vallen = 255;  
                } 
            }  
            Registry::RegCloseKey($registryKey);
            Registry::RegCloseKey($localMachine);
            return $data;
        }
          
        Registry::RegCloseKey($localMachine);
        return $result; 
    }
    
    // end class Content_Model_Article 
}
 