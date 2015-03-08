<?php

/**
 * Virtual Web Platform - CSV Response Document
 *  
 * This file provides the default API for
 * CSV Response Documents.   
 * 
 * @package VWP
 * @subpackage Libraries.Documents  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

/**
 * Require Response Document Support
 */

VWP::RequireLibrary('vwp.documents.document');

/**
 * Require URI Support
 */

VWP::RequireLibrary('vwp.uri');

/**
 * Require Path Support
 */

VWP::RequireLibrary('vwp.filesystem.path'); 

/**
 * Require Session Support
 */
 
VWP::RequireLibrary('vwp.session');
 
/**
 * Require Theme Targets
 */

VWP::RequireLibrary('vwp.themes.targets');

/**
 * Require Theme Parameters
 */

VWP::RequireLibrary('vwp.themes.params');

/**
 * Virtual Web Platform - CSV Response Document
 *  
 * This class provides the default API for
 * CSV Response Documents.   
 * 
 * @package VWP
 * @subpackage Libraries.Documents  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */
 
class CSVDocument extends VDocument 
{

    /**
     * Default escape function
     *
     * @var string
     * @access public
     */
  
    public $_escape = array('CSVDocument','csvencode');

    /**
     * HTTP Headers
     * 
     * @var array $_headers HTTP response headers
     * @access private  
     */
     
    protected $_headers = array();
 
    /**
     * @var object $_templateDriver Template driver
     * @access private  
     */
     
    protected $_templateDriver;
 
    /**
     * @var string $page_title Page title
     * @access public
     */
     
    public $page_title;

    /**
     * @var string $document_title Document title
     * @access public
     */
   
    public $document_title;

    /**
     * @var string $site_name Site Name
     * @access public
     */
     
    public $site_name;
 
    /**
     * @var string $theme_path Theme path
     * @access public
     */ 
 
    public $theme_path;

    /**
     * Mime Type
     * 
     * @var $_mime_type Mime type
     * @access public
     */
       
    public $_mime_type = "text/csv";
 
    /**
     * Character Set
     * 
     * @var $_charset Charset
     * @access public
     */
  
    public $_charset = "utf-8";
 
 
    /**
     * Target
     * 
     * @var string $target
     */
    
    public $target = null;
    
    
    /**
     * CSV Data
     */
    
    public $_csv_data = null;
    
    /**
     * CSV Filename
     * 
     * @var string CSV Filename
     */
    
    public $_csv_filename = 'data.csv';
       
    /**
     * Record Separator
     *  
     * @var string $record_sep 
     */
    
    static $record_sep = "\r\n";
    
    /**
     * Field separator
     * 
     * @var string $field_sep
     */
    
    static $field_sep = ",";

    
    /**
     * Excel Specific Formatting
     * 
     * @var boolean $excel_preserve_space
     */
    
    static $excel_preserve_space = false;
    
    /**
     * Get Target
     * 
     * @return string target
     */
    
    function getTarget() 
    {
    	return $this->target;
    }     
    
    /**
     * Get Field Delimiter
     * 
     * @return string Field Delimiter
     * @access public
     */
    
    public static function getFieldDelimter() 
    {
    	return self::$field_sep;
    }

    /**
     * Get record Separator
     * 
     *  @return string Record Separator
     *  @access public
     */
    
    public static function getRecordSeparator() 
    {
    	return self::$record_sep;
    }
    
    /**
     * Get output filename
     * 
     * @return string output Filename
     * @access public
     */
    
    function getOutputFilename() 
    {
    	return $this->_csv_filename;
    }    
    
    /**
     * Set output filename
     * 
     * @param string $destFilename
     * @access public
     */
    
    function setOutputFilename($destFilename) {
    	$this->_csv_filename = $destFilename;
    }
    
    /**
     * Get record Separator
     * 
     *  @return string Record Separator
     *  @access public
     */
    
    public static function setRecordSeparator() 
    {
    	return self::$record_sep;
    }
       
    /**
     * Load CSV From File
     * 
     * @param string $filename Filename
     * @return boolean|obect True on success, error or warning otherwise
     * @access public
     */
    
    function loadCSVFile($filename) 
    {
        $vfile =& v()->filesystem()->file();
        $data = $vfile->read($filename);    
        if (VWP::isWarning($data)) {
            return $data;
        }
        return $this->loadCSV($data);
    }
 
    /**     
     * Load CSV from source
     *      
     * @param string $data CSV Data
     * @return boolean|object True on success, error or warning otherwise
     * @access public
     */
    
    function loadCSV($data) 
    {
        $this->_csv_data = $data;
    }

 
    /**
     * Parse XML attributes
     * 
     * @static
     * @param string $str Source string
     * @return array Attributes
     * @access public
     */
  
    public static function parseAttributes($str) 
    {
  
        $s = strlen($str);
  
        if (substr($str,$s - 1,1) == "/") {
            $s = $s - 1;
        }
  
        $m = 0;
  
        $achars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890_";
        $space = " \r\n\t";
        $attribs = array();
        $quote = null;
  
        for($p = 0; $p < $s; $p++) {
            $c = substr($str,$p,1);
   
            switch($m) {
    
                case 1: // waiting for end of key
                    if (strpos($space,$c) !== false) {
                        $attribs[$key] = null;
                        $key = null;
                        $val = null;       
                        $m = 0;
                    } else if ($c == "=") {
                        $m = 3;
                        $val = '';
                    } elseif ($c == "'" || $c == "\"") {      
                        $quote = $c;
                        $m = 2;
                    } else {
                        $key .= $c;
                    }         
                   break;
    
                case 2: // waiting for key endquote
                    if ($c == $quote) {
                        $m = 1;
                    } else {
                        $key .= $c;
                    }
                    break;
                case 3: // waiting for end of value
                    if (strpos($space,$c) !== false) {
                        $attribs[$key] = $val;
                        $key = null;
                        $val = null;
                        $m = 0;
                    } elseif ($c == "'" || $c == "\"") {
                        $quote = $c;
                        $m = 4;
                    } else {
                        $val .= $c;
                    }
                    break;
                case 4: // waiting for value endquote
                    if ($c == $quote) {
                        $m = 3;
                    } else {
                        $val .= $c;
                    }
                    break;
                default: // waiting for key
                    if (strpos($achars,$c) !== false) {
                        $key = $c;
                        $val = null;       
                        $m = 1;
                    }
                    break;  
            }
        }
  
        if ($key !== null) {    
            $attribs[$key] = $val;    
        }
     
        return $attribs;
    }

    /**
     * Process template driver call
     *
     * @param array $match Match data   
     * @access private
     */
 
    public function _templateDriverCall($match) 
    {     
        $txt = $match[1];
        $s = explode(" ",$txt);
        $cmd = array_shift($s);
        if (substr($cmd,0,1) == "_") {
            return '';
        }
        $attribs = self::parseAttributes(implode(" ",$s));
        return $this->_templateDriver->$cmd($attribs);
    }

    /**
     * Parse driver tags
     * 
     * @param string $source Source document
     * @return string Result document
     * @access private
     */
  
    function _parseDriverTags($source) 
    {
        $t = $source;
        $match = "#<vdriver:(.*?)>#";   
        while(preg_match($match,$t)) {    
            $result = preg_replace_callback($match,array($this,'_templateDriverCall'),$t);
            if ($result == null) {
                $this->setError("XMLDocument::_parseDriverTags ended with errors!");     
                return $t;
            }
            $t = $result;        
        }
        return $t;
    }
 
    /**
     * Parse document tags
     * 
     * @param string $data Source document
     * @return string Result document
     * @access private
     */
 
    function _parseDocumentTags($data) 
    {
      
        $user =& VUser::getCurrent();
        $shellob =& $user->getShell();
  
        $replace = array();
        $matches = array();	 

        if(preg_match_all('#<vdoc:target\ (.*?)' . '>#i', $data, $matches)) {
     
            $matches[0] = array_reverse($matches[0]);
            $matches[1] = array_reverse($matches[1]);
            $count = count($matches[1]);
            $sys =& VWP::getInstance();
   
            for($i = 0; $i < $count; $i++) {
                $attribs = self::parseAttributes( $matches[1][$i] );
        
                if (isset($attribs["name"])) {
                    $target =& VThemeTarget::_getTarget($attribs["name"]);
                    if (VWP::isWarning($target)) {
                        $target->ethrow();
                        $result = '';
                    } else {
                    	$this->target = $attribs['name'];     
                        ob_start();
                        $target->runTask('display');
                        $buffer = ob_get_contents();   
                        ob_end_clean();
                        $this->target = null;
                    }          
                } else {
                    $buffer = '';
                }
                $replace[$i] = $buffer; 
            }
   
            $data = str_replace($matches[0], $replace, $data); 
        }

        $replace = array();
        $matches = array();
   
        if(preg_match_all('#<vdoc:include\ (.*?)' . '>#i', $data, $matches)) {
            $matches[0] = array_reverse($matches[0]);
            $matches[1] = array_reverse($matches[1]);			
      
            $count = count($matches[1]);

            for($i = 0; $i < $count; $i++) {
                $attribs = self::parseAttributes( $matches[1][$i] );
    
                if (isset($attribs["app"])) {
                    $cmd = $attribs["app"];
                    if (isset($attribs['widget'])) {
                        $cmd .= '.' . $attribs['widget'];
                    }
                
                    $bcfg = $attribs;
                    $bcfg['parent'] = get_class($this);
                    $screenId = $this->createScreenBuffer($attribs);

                    $stdio = new VStdio;
                    $stdio->setOutBuffer($this,$screenId);

                    $env = array('get'=>$attribs,'any'=>$attribs);
        
                    $rscreenId = VEnv::getVar('screen',null,'post');

                    $papp = VEnv::getVar('app');
                      
                    if (
                        ($papp == $attribs['app']) && 
                        (($rscreenId === null) || ($rscreenId == $stdio->getScreenId()))
                       ) {            
                        $env["post"] = VEnv::getChannel('post');
                        foreach($env["post"] as $k=>$v) {
                            $env['any'][$k] = $v;
                        }                                                
                    }
                                                
                    $result = $shellob->execute($cmd,$env,$stdio);
                    if (VWP::isWarning($result)) {
                        $result->ethrow();
                    }
        
                    $buffer = $this->getBuffer($screenId);
        
                } else {                        
                    $screenId = $this->createScreenBuffer($attribs);
                    $buffer = $this->getBuffer($screenId);        
                }
            
                //  $buffer = str_replace('<' . '?','&lt;?',$buffer);
                //   $buffer = str_replace('?' . '>','?&gt;',$buffer);
    
                $replace[$i] = $buffer; 
            }
            $data = str_replace($matches[0], $replace, $data);
        }

        return $data;
    }

    /**
     * Encode string to CSV data
     * 
     * @param string $txt Source text
     * @return string Encoded text
     * @access public
     */
              
    public static function csvencode($txt) 
    {
    	$str = $txt;
        $str = str_replace("\"","\"\"",$str);
        return $str;  
    }

    /**
     * Encode CSV data to CSV Field
     * 
     * @param string $txt Source text
     * @return string Encoded text
     * @access public
     */    
    
    public static function csvencodefield($csvenc_text) 
    {
    	if (
    	     is_numeric($csvenc_text) &&
    	     (substr($csvenc_text,0,1) != '0' || !self::$excel_preserve_space)
    	
    	   ) {
    		return $csvenc_text;
    	}
    	
    	
    	$str = $csvenc_text;
    	
    	if (self::$excel_preserve_space) {
    		if (
    		    (substr($str,0,1) == "0") || // leading zero
    		    (substr($str,0,1) == " ") || // leading spaces
    		    (false !== strpos($str,"\"")) || // quote
    		    (false !== strpos($str,self::$field_sep)) || // field sep
    		    (false !== strpos($str,self::$record_sep)) // record sep
    		   ) {   
    		    $str = '="' . $csvenc_text . '"';
    		}
    	} else {
    	    if (
    		    (false !== strpos($str,"\"")) || // quote
    		    (false !== strpos($str,self::$field_sep)) || // field sep
    		    (false !== strpos($str,self::$record_sep)) // record sep
    		   ) {   
    		    $str = '="' . $csvenc_text . '"';
    		}    		
    		
    	}
    	return $str;    	    	
    }
    
    /**
     * Set HTTP response header
     * 
     * @param string $string Header
     * @param boolean $replace Replace headers
     * @access public
     */  
 
    function header($string,$replace = true) 
    {
        $args = func_get_args();
        while(count($args) > 3) {
            array_pop($args);
        }   
        array_push($this->_headers,$args);
    }
 
    /**
     * Send HTTP headers
     * 
     * @access public
     */
     
    function sendHeaders() 
    {
        foreach($this->_headers as $hinfo) {
            if (count($hinfo) > 2) {
                header($hinfo[0],$hinfo[1],$hinfo[2]);
            } elseif (count($hinfo) > 1) {
                header($hinfo[0],$hinfo[1]);
            } else {
                header($hinfo[0]);
            }
        }
        $this->_headers = array();
    }

    /**
     * Get theme Template
     * 
     * @param string Theme Path  
     * @return string Path to template    
     */
  
    function getThemeTemplateFile($themePath) 
    {
        return $themePath.DS.'template.csv.php'; 
    }

    /**
     * Register Default Variables
     * 
     * @access public
     */
     
    function registerDefaults() 
    {
        parent::registerDefaults();
        $cfg = VWP::getConfig();
        if (isset($cfg->site_name)) {
            $this->site_name = $cfg->site_name;
        } else {
            $this->site_name = '';
        }
  
        $themeId = VWP::getTheme();
        $themeType = VWP::getThemeType();
  
        $params = VThemeParams::getInstance($themeType,$themeId);
        if (VWP::isWarning($params)) {
  	         $this->theme_params = array();
        } else {
  	         $this->theme_params = $params->getProperties();
        }
  
    }
       
    /**
     * Display document
     * 
     * @access public
     */
 
    function render() 
    {
 
        $this->registerDefaults();
        $this->sendHeaders();   
  
        $mode = VEnv::getWord('mode');
  
        if ($mode == 'raw') {
            $template = $this->getBuffer(null, null, null,'content');
        } else {
   
            // load template and driver
   
            $themeName = VWP::getTheme();      
            $themeType = VWP::getThemeType();
   
            $theme_path = VPATH_BASE.DS.'themes'.DS.$themeType.DS.$themeName;   
            $base = VPATH_BASE;
   
            if (substr($theme_path,0,strlen($base)) == $base) {
                $this->theme_path = VURI::base() . v()->filesystem()->path()->clean(substr($theme_path,strlen($base)),"/");
            } else {
                $this->theme_path = '';
            }
   
            $themeTemplate = $this->getThemeTemplateFile($theme_path);
            $themeDriver = $theme_path.DS.'driver.php';
      
            if (file_exists($themeDriver)) {       
                require_once($themeDriver);   
                $themeDriverClass = $themeName . "ThemeDriver";
                $this->_templateDriver = new $themeDriverClass();
            }
          
            $template = '';
   
            if (file_exists($themeTemplate)) {
                ob_start();
                require($themeTemplate);
                $template = ob_get_contents();
                ob_end_clean();
            } else {
                $template = '<html><head><title>Missing Template</title></head><body><h1>Missing Template</h1><p><b>Theme:</b> ' . htmlentities($themeType . ':' . $themeName) . '</p><p><b>Template:</b> template.html.php</p></body></html>';
            } 
        
            if ($template === false) $template = '';      

            if ($this->_templateDriver != null) {
                $template = $this->_parseDriverTags($template);   
            }      
            $template = $this->_parseDocumentTags($template);   

        }


        // convert xml tags to php
        $xmatch = '#' . '<\\?xml(.*?)\\?' . '>' . '#i';
        preg_match_all($xmatch,$template,$matches);   
        if (count($matches) > 1) {
            $xprefix = '<' . "?php echo '<' . '?xml'; ?" . '>'; 
            $xsuffix = '<' . "?php echo '?'; ?" . '>>';
            for($i=0;$i < count($matches[0]);$i++) {
                $template = str_replace($matches[0][$i],  $xprefix.$matches[1][$i].$xsuffix,$template);
            }
        }
  
        $redirect = VWP::getRedirectURL();
        $sess =& VSession::getInstance();
  
        if (empty($redirect)) {
            $sess->set('error_messages',array(),'messages');
            $sess->set('warnings',array(),'messages');               
            $sess->set('notices',array(),'messages');     
            $sess->set('debug',array(),'messages');     
            session_write_close();
            $this->registerDefaults();              
            //  eval('?' . '>' . $template . '<' . '?php ');
            eval('?' . '>' . $template /* . '<' . '?php ' */);
   
        } else {
            $error_messages = VWP::getErrorMessages();      
            $warnings = VWP::getWarnings();
            $notices = VWP::getNotices();
            $debug_notices = VWP::getDebugNotices();
      
            if (is_array($error_messages)) {
                $sess->set('error_messages',$error_messages,'messages');
            } else {
                $sess->set('error_messages',array(),'messages');
            }
   
            if (is_array($warnings)) {
                $sess->set('warnings',$warnings,'messages');
            } else {
                $sess->set('warnings',array(),'messages');
            }
   
            if (is_array($notices)) {
                $sess->set('notices',$notices,'messages');
            } else {               
                $sess->set('notices',array(),'messages');
            }
   
            if (is_array($debug_notices)) {
                $sess->set('debug',$debug_notices,'messages');
            } else {     
                $sess->set('debug',array(),'messages');
            }   
  
            session_write_close();
      
            header('Location: ' . $redirect); 
        }
               
    }
 
    /**
     * Class constructor
     * 
     * @access public
     */
         
    function __construct() 
    {
        parent::__construct();   
        
        $this->header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); 
        $this->header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        $this->header('Cache-Control: no-store, no-cache, must-revalidate'); 
        $this->header('Cache-Control: post-check=0, pre-check=0', false); 
        $this->header('Pragma: no-cache');
        
        if (!empty($this->_mime_type)) {
            $this->header('Content-Type: '.$this->_mime_type . ';charset=' . $this->_charset);            
        }
        
        if (!empty($this->_csv_filename)) {
            $this->header('Content-disposition: attachment;filename='.$this->_csv_filename);        	
        }              
    }
    
    // end class XMLDocument   
} 

