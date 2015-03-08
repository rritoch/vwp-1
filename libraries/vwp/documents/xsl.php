<?php

/**
 * Virtual Web Platform - XSL Document support
 *  
 * This file provides the default API for
 * XSLT Documents.   
 * 
 * @package VWP
 * @subpackage Libraries.Documents  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License   
 */

/**
 * Require XML Support
 */

VWP::RequireLibrary('vwp.documents.xml');


/**
 * Virtual Web Platform - XSL Document support
 *  
 * This class provides the default API for
 * XSLT  Documents.   
 * 
 * @package VWP
 * @subpackage Libraries.Documents  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License   
 */

class XSLDocument extends XMLDocument 
{
	
	/**	 
	 * Mime type
	 * 
	 * @var string $_mime_type Mime type
	 * @access public
	 */
	    
    public $_mime_type = "text/xsl";
        
    /**
     * Parse document tags
     * 
     * @param string $data Source document
     * @return string Result document
     */
 
    function _parseDocumentTags($data) 
    {

    	$targetMap = array();
    	
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
                        $targetMap[$attribs['name']] = $target->_getScreens();
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
   
        if(preg_match_all('#<vdoc:apply_target\ (.*?)' . '>#i', $data, $matches)) {
            $matches[0] = array_reverse($matches[0]);
            $matches[1] = array_reverse($matches[1]);			
      
            $count = count($matches[1]);

            for($i = 0; $i < $count; $i++) {
                $attribs = self::parseAttributes( $matches[1][$i] );
                $buffer = '';
                
                if (isset($attribs['name'])) {
                	$applyId = $attribs['name'];
                	
                	if (isset($targetMap[$applyId])) {
                		$select = '';
                		if (isset($attribs['select'])) {
                			$select = ' select="' . XMLDocument::xmlentities($attribs['select']) . '"';
                		}

                		foreach($targetMap[$applyId] as $screenId) {
                            $buffer .= '<xsl:apply-templates mode="'.self::screen2Mode($screenId).'"'.$select.' />';
                		}		
                	}
                }    
                $replace[$i] = $buffer; 
            }
            $data = str_replace($matches[0], $replace, $data);
        }        
        
        
        $scrBuf = array();
        
        $replace = array();
        $matches = array();
   
        if(preg_match_all('#<vdoc:include\ (.*?)' . '>#i', $data, $matches)) {
            $matches[0] = array_reverse($matches[0]);
            $matches[1] = array_reverse($matches[1]);			
      
            $count = count($matches[1]);

            for($i = 0; $i < $count; $i++) {
                $attribs = self::parseAttributes( $matches[1][$i] );
                $applyID = null;
                
                if (isset($attribs['vdoc:id'])) {
                	$applyID = $attribs['vdoc:id'];
                	unset($attribs['vdoc:id']);
                }
                
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
                
                if (!empty($applyID)) {
                	$scrBuf[$applyID] = $screenId;
                }
            
                //  $buffer = str_replace('<' . '?','&lt;?',$buffer);
                //   $buffer = str_replace('?' . '>','?&gt;',$buffer);
    
                $replace[$i] = $buffer; 
            }
            $data = str_replace($matches[0], $replace, $data);
        }

        $replace = array();
        $matches = array();
   
        if(preg_match_all('#<vdoc:apply_include\ (.*?)' . '>#i', $data, $matches)) {
            $matches[0] = array_reverse($matches[0]);
            $matches[1] = array_reverse($matches[1]);			
      
            $count = count($matches[1]);

            for($i = 0; $i < $count; $i++) {
                $attribs = self::parseAttributes( $matches[1][$i] );
                $buffer = '';
                
                if (isset($attribs['id'])) {
                	$applyId = $attribs['id'];
                	if (isset($scrBuf[$applyId])) {
                		$select = '';
                		if (isset($attribs['select'])) {
                			$select = ' select="' . XMLDocument::xmlentities($attribs['select']) . '"';
                		} 
                        $buffer = '<xsl:apply-templates mode="'.self::screen2Mode($scrBuf[$applyId]).'"'.$select.' />';		
                	}
                }    
                $replace[$i] = $buffer; 
            }
            $data = str_replace($matches[0], $replace, $data);
        }        
        
        return $data;
    }
        
    /**
     * Get theme Template
     * 
     * @param string Theme Path  
     * @return string Path to template    
     */
  
    function getThemeTemplateFile($themePath) 
    {
        return $themePath.DS.'template.xsl.php'; 
    }    
        
    /**
	 * Encode string
	 * 
	 * @param string $str Original string
	 * @return string Encoded string
	 * @access public
	 */
        	
	public static function screen2Mode($str) {
		$ret = '';
		
		$baseChars = 'ABCDEFGHIJKLNOPQRSTUVWXY';
		
        foreach (str_split($str) as $c) 
        { 
            
		    $i = ord($c);
		    $base = strlen($baseChars);		
            $val = '';
        
           while ($i > 0) {
	          $r = $i %  $base;		   
		      $val = substr($baseChars,$r,1) . $val;
		      $i = ($i - $r) / $base;
		   }
		   
		   
		   if (strlen($val) < 1) {			
			   $val = substr($baseChars,0,1);
		   }
		   
		   if (strlen($val) < 2) {
		   	  $val .= 'Z'; 		   	  
		   }
		
		   $ret .= $val;
                                    
        }
		
        return 'M_' . $ret;
	}
	
    // end class XSLDocument
}
