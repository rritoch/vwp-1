<?php

/**
 * Virtual Web Platform - PHP Parser
 *  
 * This file provides a PHP Parser
 * 
 * @package VWP
 * @subpackage Libraries.System
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License    
 */

/** 
 * Require Filesystem Support
 */

VWP::RequireLibrary('vwp.filesystem');

/**
 * Require Document Support
 */

VWP::RequireLibrary('vwp.documents.document');

/**
 * Require XML Document Support
 */

VDocument::RequireDocumentType('xml');

/**
 * Virtual Web Platform - PHP Parser
 *  
 * This class provides a PHP Parser
 * 
 * @package VWP
 * @subpackage Libraries.System
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License    
 */

class VParserPHP extends VObject 
{

	/**
	 * Source Filename
	 * 
	 * @var string $_filename Filename
	 * @access private
	 */
	
    protected $_filename;
    
    /**
     * PHP Source
     * 
     * @var string $_source Source code
     * @access private
     */
    
    protected $_source;
    
    /**
     * XML DOM Document
     * 
     * @var object $_doc DOM Document
     * @access private 
     */
    
    protected $_doc;
    
    /**
     * Class block stack
     * 
     * @var array $_cache_class_block Class block stack
     * @access private
     */
    
    protected $_cache_class_block = array();
 
    /**
     * Token name buffer
     * 
     * @var array $_token_names
     * @access public
     */
    
    static $_token_names = array();
 
    /**
     * Clear cache
     * 
     * @access private     
     */
    
    protected function _clearCache() 
    {
        $this->_cache_class_block = array();
    }
 
    /**
     * Set statement type
     * 
     * @param object $elem Element node
     * @param string $type Type
     * @access private
     */
    
    protected function _setStatementType($elem,$type) 
    {
 
        $doc = $elem->ownerDocument;  
        $p = $elem;
  
        while($p->nodeName != 'statement') {  
            if ($doc->documentElement->isSameNode($p->parentNode)) {
                return false;
            }
            $p = $p->parentNode;  
        }
 
        $p->setAttribute('type',$type);
    }

    /**
     * Set statement name
     * 
     * @param object $elem Element node
     * @param string $name Name
     * @access private
     */    
    
    protected function _setStatementName($elem,$name) 
    {
 
        $doc = $elem->ownerDocument;
  
        $p = $elem;
  
        while($p->nodeName != 'statement') {  
            if ($doc->documentElement->isSameNode($p->parentNode)) {
                return false;
            }
            $p = $p->parentNode;  
        }
 
        $p->setAttribute('name',$name);
    }
 
    /**
     * Parse PHP Source
     * 
     * @access public
     */
    
    function _parse() 
    {
        $nl = "\n";
        $doc = new DomDocument;
        $doc->loadXML('<' . '?xml version="1.0" encoding="utf-8" ?' . '>'.$nl.'<xphp></xphp>');
  
        $rootNode = $doc->documentElement;
        $parent = $rootNode;
  
        $tokens = token_get_all($this->_source);
        $level = 1;
  
        // Make first statement
  
        $space = $nl . str_repeat(" ",$level);
        $s = $doc->createTextNode($space);
        $elem = $doc->createElement('statement');  
        $parent->appendChild($s);
        $parent->appendChild($elem);
        $parent = $elem;
        $level++;
  
  
        $classNodes = array();
        $waitName = false;
  
        foreach($tokens as $token) {
            $type = $token[0];
          
            if (!isset(self::$_token_names[$type])) {
                if (is_numeric($type)) {
                    $tname = token_name($type);
                } else {
                    $tname = "op";
                }
                if (empty($tname)) {
                    $tname= "NoType";
                }
                self::$_token_names[$type] = $tname;
            }

            $name = self::$_token_names[$type];
            $space = $nl . str_repeat(" ",$level);
      
            $noadd = false;
            if ($name == "op") {
                if ($type == "{") {
                    //  Start Block
     
                    // Start New Block Tag
                    $noadd = true;
                    $elem = $doc->createElement('block');
                    $s = $doc->createTextNode($space);
                    $parent->appendChild($s);
                    $parent->appendChild($elem);
                    $parent = $elem;
                    $level++;
     
                    // Start New Statement
                    $space = $nl . str_repeat(" ",$level);
                    $s = $doc->createTextNode($space);
                    $elem = $doc->createElement('statement');  
                    $parent->appendChild($s);
                    $parent->appendChild($elem);
                    $parent = $elem;
                    $level++;
                    $noadd = true;
          
                } elseif ($type == "}") {
                    // End Block
     
                    $noadd = true;
                    if ($parent->isSameNode($rootNode) || $parent->nodeName != 'statement') {
                        VWP::raiseWarning('Parse Error!',get_class($this));
                    } else {
            
                        if ($parent->childNodes->length < 1) {       
                            // Remove Statement Tag   
          
                            $s = $parent;       
                            $parent = $parent->parentNode;
                            $level--;
                            $parent->removeChild($s);
                 
                        } else {
                            // End Statement Tag
                 
                            $parent = $parent->parentNode;
                            $level--;
                        }
      
                        // End Block Tag    
                        $parent = $parent->parentNode;
                        $level--;
                    }
          
                    // Collapse Classes
                    $cn = count($classNodes);
                    if ($cn > 0) {
                        $cn--;                   
                        if ($parent->isSameNode($classNodes[$cn])) {
      
                            // Collapse Class
       
                            array_pop($classNodes);
       
       
                            //    End T_CLASS tag
                            $parent = $parent->parentNode;
                            $level--;
            
                        }
                    }
     
                    if ($parent->childNodes->length < 1) {       
                        // Remove Statement Tag   
          
                        $s = $parent;       
                        $parent = $parent->parentNode;
                        $level--;
                        $parent->removeChild($s);
                 
                    } else {
                        // End Statement Tag
                 
                        $parent = $parent->parentNode;
                        $level--;
                    }
                               
                    // Start New Statement Tag
                    $space = $nl . str_repeat(" ",$level);
                    $s = $doc->createTextNode($space);
                    $elem = $doc->createElement('statement');  
                    $parent->appendChild($s);
                    $parent->appendChild($elem);
                    $parent = $elem;
                    $level++;
          
                } elseif ($type == ";") {
    
                    // End Statement
                    $space = $nl . str_repeat(" ",$level);
                    $s = $doc->createTextNode($space);
                    $elem = $doc->createElement($name,XMLDocument::xmlentities($type));
                    $parent->appendChild($s);
                    $parent->appendChild($elem);          
                    $parent = $parent->parentNode;
                    $level--;
     
                    // Start New Statement
                    $space = $nl . str_repeat(" ",$level);
                    $s = $doc->createTextNode($space);
                    $elem = $doc->createElement('statement');  
                    $parent->appendChild($s);
                    $parent->appendChild($elem);
                    $parent = $elem;
                    $level++;
                    $noadd = true;    
                } elseif ($type == "(") {
        
                    // Start Group
     
                    $noadd = true;          
                    $s = $doc->createTextNode($space);
                    $elem = $doc->createElement('group');
                    $parent->appendChild($s);
                    $parent->appendChild($elem);
                    $parent = $elem;
                    $level++;
     
                    // Start New Statement
                    $space = $nl . str_repeat(" ",$level);
                    $s = $doc->createTextNode($space);
                    $statement = $doc->createElement('statement');  
                    $parent->appendChild($s);
                    $parent->appendChild($statement);
                    $parent = $statement;
                    $level++;
         
                } elseif ($type == ")") {
                    // End Group
                    $noadd = true;
     
                    // End Statement Tag
                    $parent = $parent->parentNode;
                    $level--;

                    // End Group Tag     
                    $parent = $parent->parentNode;
                    $level--;     
                } else {
                    $elem = $doc->createElement($name,XMLDocument::xmlentities($type));
                }
            } else if ($name == 'T_CLASS') {
                if (count($token) > 1) {
                    $celem = $doc->createElement($name,XMLDocument::xmlentities($token[1]));
                } else {
                    $celem = $doc->createElement($name);
                }   
   
                $elem = $doc->createElement('class');
                $space = $nl . str_repeat(" ",$level + 1);
                $s = $doc->createTextNode($space);
                $elem->appendChild($s); 
                $elem->appendChild($celem);    
            } else {
                if (count($token) > 1) {
                    $elem = $doc->createElement($name,XMLDocument::xmlentities($token[1]));
                } else {
                    $elem = $doc->createElement($name);
                }
            }
            if (!$noadd) {
                $s = $doc->createTextNode($space);
                $parent->appendChild($s);
                $parent->appendChild($elem);
                if ($name == "T_CLASS") {     
                    $this->_setStatementType($parent,'class');
                    $waitName = true;
                    $parent = $elem;
                    array_push($classNodes,$elem);
                    $level++;
                } elseif ($name == "T_FUNCTION") {
                    $this->_setStatementType($parent,'function');
                    $waitName = true;    
                } elseif ($waitName && $name == "T_STRING") {
                    $this->_setStatementName($parent,$token[1]);
                    $waitName = false;
                }
            }    
        }
        $level--;
        $space = $nl . str_repeat(" ",$level);
        $s = $doc->createTextNode($space);
        $parent->appendChild($s);
        $this->_doc = $doc;     
    }

    /**
     * Locate class node
     *
     * @param string $className
     * @return object Class Node
     * @access private
     */
    
    protected function _getClassNode($className) 
    {

        $classes = $this->_doc->getElementsByTagName('class');  
        $classNode = null;  
  
        for($p = 0; $p < $classes->length; $p++) {   
            $c = $classes->item($p);
            for($i = 0; $i < $c->childNodes->length; $i++) {   
                if ($c->childNodes->item($i)->nodeName == "T_STRING") {
                    if (strtolower($c->childNodes->item($i)->nodeValue) == strtolower($className)) {
                        $classNode = $c;     
                    } 
                }    
            }   
        } 
 
        if ($classNode === null) {   
            return VWP::raiseWarning('Class \''.$className.'\' not found',get_class($this),null,false);
        } 
 
        return $classNode;
    }
 
    /**
     * Locate class Block Node
     *      
     * @param string $className Class Name
     * @access private     
     */
    
    protected function _getClassBlockNode($className) 
    {
  
        if (!isset($this->_cache_class_block[$className])) {
            // Locate Class
    
            $classNode = $this->_getClassNode($className);  
            if (VWP::isWarning($classNode)) {
                $this->_cache_class_block[$className] = $classNode;
            } else {    
                $this->_cache_class_block[$className] = VWP::raiseWarning('Class block not found',get_class($this),null,false);    
                $len = $classNode->childNodes->length;  
                for($p = 0; $p < $len; $p++) {   
                    $node = $classNode->childNodes->item($p);      
                    if (strtolower($node->nodeName) == 'block') {
                        $this->_cache_class_block[$className] = $node;
                        $p = $len;
                    }    
                }   
            }
        }
        return $this->_cache_class_block[$className];
    }
 
    /**
     * Get Comments
     * 
     * @param object $node Node
     * @access public
     */
    
    function getComments($node = null) 
    {
        $txt = '';
        if ($node === null) {
            $node = $this->_doc->documentElement; 
        }
  
        for($p = 0; $p < $node->childNodes->length; $p++) {
            $c = $node->childNodes->item($p);
            if ($c->nodeName == 'T_DOC_COMMENT' || $c->nodeName == 'T_COMMENT') {
                $txt .= $c->nodeValue;
            } elseif (substr($c->nodeName,0,1) !== "#") {
                $txt .= $this->getComments($c);
            }
  
        }
  
        return $txt;
    }
 
    /**
     * Get file description
     * 
     * @return string Description
     * @access public
     */
    
    function getFileDescription() 
    {
        $comments = $this->getComments();
        $desc = '';
        $dstart = strpos($comments,'/**');
        if ($dstart !== false) {
            $dstart += 2;
            $comments = substr($comments,$dstart);
   
            $dend = strpos($comments,'*/');
            if ($dend !== false) {
                $comments = substr($comments,0,$dend);
                $lines = explode("\n",$comments);
                $nlines = array();
                foreach($lines as $ln) {
                    $ln = trim($ln);
                    if (substr($ln,0,1) == "*") {
                        $ln = trim(substr($ln,1));
                    }
                    $nlines[] = $ln;
                }
                $desc = implode("\n",$nlines);
            }
        }
        
        return $desc;
    }
 
    /**
     * Decode Document Node
     * 
     * @param object $node Node
     * @access private
     */
 
    protected function _decode($node) 
    {
 
        $txt = '';
        $type = $node->nodeName;
  
        $textNodes = array(
         "op",
         "T_ABSTRACT",
         "T_AND_EQUAL",
         "T_ARRAY",
         "T_ARRAY_CAST",
         "T_AS",
         "T_BAD_CHARACTER",
         "T_BOOLEAN_AND",
         "T_BOOLEAN_OR",
         "T_BOOL_CAST",
         "T_BREAK",
         "T_CASE",
         "T_CATCH",
         "T_CHARACTER",
         "T_CLASS",
         "T_CLASS_C",
         "T_CLONE",
         "T_CLOSE_TAG",
         "T_COMMENT",
         "T_CONCAT_EQUAL",
         "T_CONST",
         "T_CONSTANT_ENCAPSED_STRING",
         "T_CONTINUE",
         "T_CURLY_OPEN",
         "T_DEC",
         "T_DECLARE",
         "T_DEFAULT",
         "T_DIR",
         "T_DIV_EQUAL",
         "T_DNUMBER",
         "T_DOC_COMMENT",
         "T_DO",
         "T_DOLLAR_OPEN_CURLY_BRACES",
         "T_DOUBLE_ARROW",
         "T_DOUBLE_CAST",
         "T_DOUBLE_COLON",
         "T_ECHO",
         "T_ELSE",
         "T_ELSEIF",
         "T_EMPTY",
         "T_ENCAPSED_AND_WHITESPACE",
         "T_ENDDECLARE",
         "T_ENDFOR",
         "T_ENDFOREACH",
         "T_ENDIF",
         "T_ENDSWITCH",
         "T_ENDWHILE",
         "T_END_HEREDOC",
         "T_EVAL",
         "T_EXIT",
         "T_EXTENDS",
         "T_FILE",
         "T_FINAL",
         "T_FOR",
         "T_FOREACH",
         "T_FUNCTION",
         "T_FUNC_C",
         "T_GLOBAL",
         "T_GOTO",
         "T_HALT_COMPILER",
         "T_IF",
         "T_IMPLEMENTS",
         "T_INC",
         "T_INCLUDE",
         "T_INCLUDE_ONCE",
         "T_INLINE_HTML",
         "T_INSTANCEOF",
         "T_INT_CAST",
         "T_INTERFACE",
         "T_ISSET",
         "T_IS_EQUAL",
         "T_IS_GREATER_OR_EQUAL",
         "T_IS_IDENTICAL",
         "T_IS_NOT_EQUAL",
         "T_IS_NOT_IDENTICAL",
         "T_IS_SMALLER_OR_EQUAL",
         "T_LINE",
         "T_LIST",
         "T_LNUMBER",
         "T_LOGICAL_AND",
         "T_LOGICAL_OR",
         "T_LOGICAL_XOR",
         "T_METHOD_C",
         "T_MINUS_EQUAL",
         "T_ML_COMMENT",
         "T_MOD_EQUAL",
         "T_MUL_EQUAL",
         "T_NAMESPACE",
         "T_NS_C",
         "T_NS_SEPARATOR",
         "T_NEW",
         "T_NUM_STRING",
         "T_OBJECT_CAST",
         "T_OBJECT_OPERATOR",
         "T_OLD_FUNCTION",
         "T_OPEN_TAG",
         "T_OPEN_TAG_WITH_ECHO",
         "T_OR_EQUAL",
         "T_PAAMAYIM_NEKUDOTAYIM",
         "T_PLUS_EQUAL",
         "T_PRINT",
         "T_PRIVATE",
         "T_PUBLIC",
         "T_PROTECTED",
         "T_REQUIRE",
         "T_REQUIRE_ONCE",
         "T_RETURN",
         "T_SL",
         "T_SL_EQUAL",
         "T_SR",
         "T_SR_EQUAL",
         "T_START_HEREDOC",
         "T_STATIC",
         "T_STRING",
         "T_STRING_CAST",
         "T_STRING_VARNAME",
         "T_SWITCH",
         "T_THROW",
         "T_TRY",
         "T_UNSET",
         "T_UNSET_CAST",
         "T_USE",
         "T_VAR",
         "T_VARIABLE",
         "T_WHILE",
         "T_WHITESPACE", 	 
         "T_XOR_EQUAL",    
        );
  
  
        if (in_array($type,$textNodes)) {
            return $node->nodeValue;
        }
  
        switch($type) {
            case "group":
                $txt .= "(";
                break;
            case "block":
                $txt .= "{";
                break;
            default:
                break;
        }
   
        for($p =0; $p < $node->childNodes->length;$p++) {
            $c = $node->childNodes->item($p);
   
            switch($c->nodeType) {
   
                case XML_ELEMENT_NODE:
                    $txt .= $this->_decode($c);
                    break;
                case XML_TEXT_NODE:
                    break; 
                default:
                    VWP::raiseWarning('Unsupported node type: ' . $c->nodeType,get_class($this));
                    break;   
            }
            //if ($c->nodeType == XML_ELEMENT_NODE) {    
            //}   
        }

        switch($type) {
            case "group":
                $txt .= ")";
                break;
            case "block":
                $txt .= "}";
                break;   
            default:    
                break;
        }
 
        return $txt;
    }

    /**
     * Get arguments from node
     * 
     * @param object $node Node
     * @return array Arguments
     * @access protected
     */
    
    protected function _getArgs($node) 
    {
        $args = array();
  
        // Seek Statement
  
        $statement = null;
        for($i = 0;$i < $node->childNodes->length;$i++) {
            $c = $node->childNodes->item($i);
            if ($c->nodeName == "statement") {
                $statement = $c;
                $i = $node->childNodes->length;
            }  
        }
  
        if ($statement != null) {
            // Seek Arguments
   
            $curArg = '';  
            for($i = 0;$i < $statement->childNodes->length;$i++) {
                $c = $statement->childNodes->item($i);
                if (($c->nodeName == "op") && ($c->nodeValue == ",")) {
                    array_push($args,$curArg);
                    $curArg = '';
                } else {
                    if ($c->nodeType == XML_ELEMENT_NODE) {
                        $curArg .= $this->_decode($c);
                    }
                }   
            }
 
            if (strlen($curArg) > 0) {
                array_push($args,$curArg);
            }
  
        }
  
        return $args;
    }
 
    /**
     * Get statement description
     * @param object $statementNode Node
     * @return string Description
     * @access private
     */
    
    protected function _getStatementDescription($statementNode) 
    {
 
        $len = $statementNode->childNodes->length;
  
        $desc = '';
  
        for($i = 0; $i < $len; $i++) {
            $node = $statementNode->childNodes->item($i);
            if ($node->nodeType == XML_ELEMENT_NODE) {        
                if (in_array($node->nodeName, array("T_DOC_COMMENT","T_COMMENT","T_WHITESPACE"))) {
                    $desc .= $this->_decode($node);
                } else {
                    $i = $len;      
                }
            }   
        }
        return $desc;
    }
 
    /**
     * Get Function
     * 
     * @param string $name Function name
     * @param string $className Class name
     * @return array Function INFO
     * @access public
     */
    
    function getFunction($name,$className = null) 
    {
   
        // Locate Class
  
        $classBlock = $this->_getClassBlockNode($className);
        if (VWP::isWarning($classBlock)) {
            return $classBlock;
        }  
  
        $funcBlock = null;
  
        // Seek Function
  
        for($p = 0; $p < $classBlock->childNodes->length; $p++) {   
            $node = $classBlock->childNodes->item($p);   
            if (strtolower($node->nodeName) == 'statement') {
                $info = array();    
                $type = $node->getAttribute('type');
                if (empty($type)) {
                    $type = "statement";
                }
    
                $sname = $node->getAttribute('name');
    
                if (empty($sname)) {
                    $sname = null;
                }
                if (($type == "function") && ($name == $sname)) {
                    $funcBlock = $node;    
                }
            } 
        } 
  
        if ($funcBlock === null) {
            return VWP::raiseWarning('Function \'' . $name . '\' not found!',get_class($this),null,false);
        }

        $info = array();
        $info["name"] = $funcBlock->getAttribute('name');
        $info["description"] = '';
        $info["modifiers"] = array("access"=>'');
        $info["args"] = array();
        $info["body"] = '';
        $mode = 0;
  
        for($i = 0; $i < $funcBlock->childNodes->length; $i++) {
            $node = $funcBlock->childNodes->item($i);
            if ($node->nodeType == XML_ELEMENT_NODE) {
   
                // Get Description
    
                if ($mode == 0) {
                    if (in_array($node->nodeName, array("T_DOC_COMMENT","T_COMMENT","T_WHITESPACE"))) {
                        $info["description"] .= $this->_decode($node);
                    } else {      
                        $mode = 1;
                    }   
                }
    
                // Get Modifiers
    
                if ($mode == 1) {
                    if (in_array($node->nodeName, array("T_PUBLIC",
                                        "T_PRIVATE",
                                        "T_PROTECTED",
                                        "T_STATIC",
                                        "T_ABSTRACT",
                                        "T_FINAL",
                                        "T_WHITESPACE"))) {
                        $m = strtolower(substr($node->nodeName,2));
                        if ($m != "whitespace") {
                            if (in_array($m,array("public","private","protected"))) {
                                $info["modifiers"]["access"] = $m;
                            } else {
                                 $info["modifiers"][$m] = true;
                            }              
                        }                                        
                    } else {
                        $mode = 2;
                    }
                }
   
                // Get Function
    
                if ($mode == 2) {
                    if (!in_array($node->nodeName, array("T_FUNCTION",
                                         "T_WHITESPACE",
                                         "T_STRING"))) {
                         $mode = 3;                                  
                    }   
                }
    
                // Get Arguments
    
                if ($mode == 3) {
                     if ($node->nodeName == "group") {
                         $info["args"] = $this->_getArgs($node);
                         $mode = 4;
                     }   
                }
   
                // Get Body
    
                if ($mode == 4) {
                    if ($node->nodeName == "block") {
                        $info ["body"] = '';
                        for($p = 0; $p < $node->childNodes->length; $p++) {
                            $c = $node->childNodes->item($p);
                            if ($c->nodeType == XML_ELEMENT_NODE) {
                                $info["body"] .= $this->_decode($c);
                            }
                        }
                        $mode = 5;
                    }
                }
            }
        }
        
        return $info;    
    }

    /**
     * Check if function is valid
     *
     * @param array $info Function INFO
     * @access private
     */
    
    protected function _validFunction($info) 
    {
        /*
         [description] => string 
         [modifiers] => Array ( [access] => ) 
         [args] => Array ( [1] => $tpl = null [add] => )
         [body] => string
         [name] => string  
        */
        return true;
    }

    /**
     * Convert Function INFO into PHP
     * 
     * @param array $info Function info
     * @return string PHP Code
     * @access private
     */
    
    protected function _funcToPHP($info) 
    {
 
        $nl = "\n";
  
        $flags = array();
        foreach($info["modifiers"] as $key=>$val) {
            if ($key !== "access") {
                array_push($flags,$key);
            }  
        }
      
        $raw_args = array();
  
        foreach($info["args"] as $key=>$val) {
            if ($key == 'add') {
                if (strlen(trim($val)) > 0) {
                    $nargs = explode(',',$val);     
                    $raw_args = array_merge($raw_args,$nargs);
                }
            } else {
                if (strlen(trim($val)) > 0) {
                    array_push($raw_args,$val);
                }
            }  
        }
    
        $args = implode(',',$raw_args);
  
        $access = '';
        if (!empty($info["modifiers"]["access"])) {
            $access = $info["modifiers"]["access"] . ' ';
        }
      
        $desc = $info["description"];
        $name = $info["name"];
        $body = $info["body"];
  
        $mods = $access . implode(" ",$flags);
    
        $code = '<' . '?php' . $nl
                .  $desc 
                .  $mods 
                . ' function ' 
                . $name 
                . '('
                . $args
                . ')'          
                . ' {' . $nl
                . $body
                . ' }';
    
        return $code;
    }
 
    /**
     * Create a PHP Function
     * 
     * @param array $info Function info
     * @return object Function node
     * @access private
     */
    
    protected function _makeFunction($info) 
    {
    
        $code = $this->_funcToPHP($info);  
        $p2 = new VParserPHP;
        $p2->setSource($code);
        $p2->_parse();
        $d2 = $p2->getDocument();
  
        // Get statement
  
        $statement = null;  
        $len = $d2->documentElement->childNodes->length; 
        for ($i = 0; $i < $len;$i++) {
            $c = $d2->documentElement->childNodes->item($i);
            if ($c->nodeName == 'statement') {
                $statement = $c;
                $i = $len;
            }  
        }
  
        if ($statement === null) {  
            return VWP::raiseWarning('Syntax Error!',get_class($this),null,false);
        }
  
        // Remove PHP START
  
        $start = null;
  
        $len = $statement->childNodes->length; 
        for ($i = 0; $i < $len;$i++) {
            $c = $statement->childNodes->item($i);
            if ($c->nodeName == 'T_OPEN_TAG') {
                $start = $c;
                $i = $len;
            }  
        }
  
        if ($start !== null) {
            $value = $start->nodeValue;
            if (strlen($value) > 5) {
                $n = $d2->createElement('T_WHITESPACE',substr($value,5));
                $statement->replaceChild($n,$start);
            } else {
                $statement->removeChild($start);
            }
        }
      
        $statement = $this->_doc->importNode($statement,true);
        return $statement;  
    }
 
    /**
     * Update a function
     * 
     * @param string $name Function name
     * @param array $info Function info
     * @param string $className Class name
     * @return boolean|object True on success, error or warning otherwise
     * @access public
     */
    
    function updateFunction($name,$info,$className = null) 
    {
    
        // Locate Class
  
        $classBlock = $this->_getClassBlockNode($className);
        if (VWP::isWarning($classBlock)) {
            return $classBlock;
        }  
    
        $funcBlock = null;
  
        // Seek Function
  
        for($p = 0; $p < $classBlock->childNodes->length; $p++) {   
            $node = $classBlock->childNodes->item($p);   
            if (strtolower($node->nodeName) == 'statement') {
        
                $type = $node->getAttribute('type');
                if (empty($type)) {
                    $type = "statement";
                }
    
                $sname = $node->getAttribute('name');
    
                if (empty($sname)) {
                    $sname = null;
                }
                if (($type == "function") && ($name == $sname)) {
                    $funcBlock = $node;    
                }
            } 
        } 
  
        if ($funcBlock === null) {
            return VWP::raiseWarning('Function \'' . $name . '\' not found!',get_class($this),null,false);
        }

        // Validate Function
  
        $valid = $this->_validFunction($info);
        if (VWP::isWarning($valid)) {
            return $valid;
        }
  
        // Make Function
  
        $statement = $this->_makeFunction($info);  
        if (VWP::isWarning($statement)) {
            return $statement;
        }

        // Replace Function
  
        $p = $funcBlock->parentNode;  
        $p->replaceChild($statement,$funcBlock);
  
        return true;
    }

    /**
     * Add a function
     * 
     * @param string $name Function name
     * @param array $info Function INFO
     * @param string $className Class name
     * @return boolean|object True on success, error or warning otherwise
     * @access public
     */

    function addFunction($name,$info,$className = null) 
    {
        
        $find = $this->getFunction($name,$className);  
        if (!VWP::isWarning($find)) {
            return VWP::raiseWarning('Function \''.$name.'\' is already defined!',get_class($this),null,false);   
        }

        // Locate Class
  
        $classBlock = $this->_getClassBlockNode($className);
        if (VWP::isWarning($classBlock)) {
            return $classBlock;
        }  

        // Validate Function
  
        $valid = $this->_validFunction($info);
        if (VWP::isWarning($valid)) {
            return $valid;
        }
  
        // Make Function
  
        $statement = $this->_makeFunction($info);  
        if (VWP::isWarning($statement)) {
            return $statement;
        }

        // Replace Function
      
        $classBlock->appendChild($statement);
  
        return true;
    }


    /**
     * Get list of statements
     * 
     * @param string $className Class Name
     * @return array|object Statement list on success, error or warning otherwise
     * @access public
     */
 
    function getStatementList($className) 
    {

        // Locate Class
    
        $classBlock = $this->_getClassBlockNode($className);
        if (VWP::isWarning($classBlock)) {
            return $classBlock;
        }
  
        $statement_list = array();
  
  
        for($p = 0; $p < $classBlock->childNodes->length; $p++) {   
            $node = $classBlock->childNodes->item($p);   
            if (strtolower($node->nodeName) == 'statement') {
                $info = array();    
                $type = $node->getAttribute('type');
                if (empty($type)) {
                    $type = "statement";
                }
    
                $name = $node->getAttribute('name');
    
                if (empty($name)) {
                    $name = null;
                }
            
                $value = $this->_decode($node);    

                $info["type"] = $type;
                $info["value"] = $value;
                $info["name"] = $name;
                $info["description"] = $this->_getStatementDescription($node);
                array_push($statement_list,$info); 
            } 
        } 
    
        return $statement_list;  
    }
 
    /**
     * Get class list
     * 
     * @return array|object Class list on success, error or warning otherwise
     * @access public
     */

    function getClasses() 
    {
        $ret = array();
        $doc = $this->_doc;
        $classes = $doc->getElementsByTagName('class');
        for($p = 0; $p < $classes->length; $p++) {
            $cinfo = array();
            $c = $classes->item($p);
            $len = $c->childNodes->length;   
            for($i = 0; $i < $len; $i++) {    
                if ($c->childNodes->item($i)->nodeName == "T_STRING") {
                    $cinfo["name"] = $c->childNodes->item($i)->nodeValue;
                    $i = $len;
                }    
            }
            $ret[] = $cinfo;
        }
        return $ret;
    }
 
    /**
     * Get Parsed PHP DOM Document
     * 
     * @access public
     */
    
    function getDocument() 
    {
        return $this->_doc;
    }
 
    /**
     * Load PHP Source
     * 
     * @param string $filename
     * @return boolean|object True on success, error or warning otherwise
     * @access public
     */

    function load($filename) 
    {
        $vfile =& v()->filesystem()->file();
        $src = $vfile->read($filename);
        if (VWP::isWarning($src)) {
            return $src;
        }
  
        $this->setSource($src);
  
        $this->_filename = $filename;  
        return true;  
    }

    /**
     * Set PHP Source Code
     * 
     * @param string $src Source code
     * @return boolean|object True on success, error or warning otherwise
     * @access public
     */
    
    function setSource($src) 
    {
        $this->_clearCache();
  
        if (VWP::isWarning($src)) {
            return $src;
        }
        $this->_filename = null;
        $this->_source = $src;
        return true;  
    }

    /**
     * Get source code
     * 
     * @return string PHP Source
     * @access public
     */
    
    function getSource() 
    {
        $data = $this->_decode($this->_doc->documentElement);
        return $data;
    }

    // End class VParserPHP
}
