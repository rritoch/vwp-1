<?php
/**
 * Virtual Web Platform - PNG Response Document
 *  
 * This file provides the default API for
 * PNG Response Documents.   
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
 * Virtual Web Platform - PNG Response Document
 *  
 * This class provides the default API for
 * PNG Response Documents.   
 * 
 * @package VWP
 * @subpackage Libraries.Documents  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

class ImageDocument extends VDocument 
{
   /**
     * Default escape function
     *
     * @var string
     * @access public
     */
  
    public $_escape = array('XMLDocument','xmlentities');

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
       
    public $_mime_type = "text/xml";
 
    /**
     * Character Set
     * 
     * @var $_charset Charset
     * @access public
     */
  
    public $_charset = "utf-8";
 
    /**
     * XML Document
     * 
     * @var DOMDocument $_xml_doc XML Document
     * @access private
     */
    
    public $_xml_doc = null;
 
    /**
     * Target
     * 
     * @var string $target
     * @access private
     */
    
    public $target = null;
    
    /**
     * Get Target
     * 
     * @return string target
     * @access public
     */
    
    function getTarget() {
    	return $this->target;
    }    
    
    /**
     * Get Document Type
     */
        
    function getDocumentType() 
    {
    	return 'image';
    }
        
    /**
     * Load XML From File
     * 
     * @param string $filename Filename
     * @return boolean|obect True on success, error or warning otherwise
     * @access public
     */
    
    function loadXMLFile($filename) 
    {
        $vfile =& v()->filesystem()->file();
        $data = $vfile->read($filename);    
        if (VWP::isWarning($data)) {
            return $data;
        }
        return $this->loadXML($data);
    }
 
    /**     
     * Load XML from source
     * 
     * @param string $data XML Data
     * @return boolean|object True on success, error or warning otherwise
     * @access public
     */
    
    function loadXML($data) 
    {
        VWP::noWarn();
        $r = $this->_xml_doc->loadXML($data);
        VWP::noWarn(false);
  
        if (!$r) {
            $err = VWP::getLastError();
            return VWP::raiseWarning($err[1],get_class($this),null,false);
        } 
    }

    /**     
     * Bag Child Node Prefixes
     * 
     * @static
     * @param object $node Node
     * @param array $bag Bag
     * @access public
     */

    public static function bagChildPrefixes($node,$bag = array()) 
    {
        $xsiNS = 'http://www.w3.org/2001/XMLSchema-instance';
  
        // Process Element
     
        if (!empty($node->prefix)) {
            $bag[$node->prefix] = true;
        }

        // Process Attributes
  
        $aptr = 0;
        $attr = $node->attributes->item($aptr);
        while($attr !== null) {
            if (!empty($attr->prefix)) {
                $bag[$attr->prefix] = true;
            }   
            $aptr++;
            $attr = $node->attributes->item($aptr); 
        }  
  
        $xsi_type = $node->getAttributeNS($xsiNS,'type');
        if ($xsi_type !== null) {
            $tmp = explode(':',$xsi_type);
            if (count($tmp) > 1) {
                array_pop($tmp);
                $xsi_prefix = implode(':',$tmp);
                $bag[$xsi_prefix] = true;
            }
        }
  
        // Process Children

        for($p=0;$p < $node->childNodes->length; $p++) {
            $childNode = $node->childNodes->item($p);
            if ($childNode->nodeType == XML_ELEMENT_NODE) {
                $bag = self::bagChildPrefixes($childNode,$bag);
            }   
        }  
      
        return $bag;
    }
 

    /**
     * Add XMLNS attributes to selected node
     * 
     * @param object $node Node
     * @access public
     */
    
    public static function collectChildNodeNamespaces($node) 
    {
   
        $prefix_list = array_keys(self::bagChildPrefixes($node));
    
        foreach($prefix_list as $prefix) {
            $ns = $node->lookupNamespaceURI($prefix);
            if (!empty($ns)) {
                $node->setAttribute('xmlns:'.$prefix,$ns);
            }
        } 
    }
  
    /**     
     * Change namespace prefix
     * 
     * @param object $node
     * @param string $origPrefix Original prefix
     * @param string $newPrefix New prefix
     * @param string $ns Namespace
     * @access public
     */
 
    public static function changeNamespacePrefix($node,$origPrefix,$newPrefix,$ns) 
    {
      
        if (empty($origPrefix)) {
            $declaredNS = $node->getAttribute('xmlns');
        } else {
            $declaredNS = $node->getAttribute('xmlns:' . $origPrefix);
        }
  
        if (($declaredNS == null) || ($declaredNS == $ns)) {   
            if ($node->prefix == $origPrefix) {
                $change_auth = true;
            } else {
                $change_auth = false;
            }  
        } else {
            return $node;
        }
    
        if ($change_auth) {
   
            // Prepare Change
   
            $doc = $node->ownerDocument;   
            $tmp = explode(':',$node->nodeName);
            $name = array_pop($tmp);
   
            // Create New Node
   
            if (empty($newPrefix)) {
                $newNode = $doc->createElement($name);
            } else {
                $newNode = $doc->createElement($newPrefix.':'.$name);
            }
         
            // Move Attributes to New Node
            $ptr = 0;   
            $attr = $node->attributes->item($ptr);
            while($attr !== null) {
                $newNode->setAttribute($attr->nodeName,$attr->nodeValue);   
                $ptr++;
                $attr = $node->attributes->item($ptr);
            }
   
            // Move Children to New Node
   
            $childNodes = array();
            while($node->childNodes->length > 0) {
                array_push($childNodes,$node->removeChild($node->childNodes->item(0)));    
            }
            foreach($childNodes as $child) {
                $newNode->appendChild($child);
            }
   
            // Register New Node
   
            $node->parentNode->replaceChild($newNode,$node);
            $node = $newNode;          
        }
  
        $children = array();
  
        for($ptr=0;$ptr < $node->childNodes->length;$ptr++) {
            $child = $node->childNodes->item($ptr);
            if ($child->nodeType == XML_ELEMENT_NODE) {
                array_push($children,$child);
            }   
        }
  
        foreach($children as $child) {
            self::changeNamespacePrefix($child,$origPrefix,$newPrefix,$ns);
        }

        return $node;         
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
     * Get Element After 
     * 
     * @param object $node Node 
     * @return object $node Node
     * @access public
     */
    
	public static function getElementAfter($node) 
	{
		$sibling = $node->nextSibling;
		while($sibling !== null) {
			if ($sibling->nodeType == XML_ELEMENT_NODE) {
				return $sibling;
			}
			$sibling = $sibling->nextSibling;
		}
		return null;
	}
 
    /**
     * Encode string to XML data
     * 
     * @param string $txt Source text
     * @return string Encoded text
     * @access public
     */
              
    public static function xmlentities($txt) 
    {
        $str = $txt;
        $str = str_replace("&","&amp;",$str);
        $str = str_replace("<","&lt;",$str);
        //$str = str_replace(">","&gt;",$str);
        $str = str_replace("\"","&quot;",$str);
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
     * @access public    
     */
  
    function getThemeTemplateFile($themePath) 
    {
        return $themePath.DS.'template.xml.php'; 
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
  
        
        $bcfg = array('alias'=>'content');
        $screenId = $this->createScreenBuffer($bcfg);                
        $template = $this->getBuffer($screenId);
        
        $redirect = VWP::getRedirectURL();
        $sess =& VSession::getInstance();
          
        
        if (empty($redirect)) {
            $sess->set('error_messages',array(),'messages');
            $sess->set('warnings',array(),'messages');               
            $sess->set('notices',array(),'messages');     
            $sess->set('debug',array(),'messages');     
            session_write_close();
            $this->registerDefaults();                          
            ob_start();            
            echo $template;
            $data = ob_get_contents();
            ob_end_clean();

            header('Content-length: '.strlen($data));
            echo $data;                          
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
        $this->_xml_doc = new DomDocument;      
    }
    
    // end class ImageDocument 		
}
