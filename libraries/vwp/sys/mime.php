<?php

/**
 * Virtual Web Platform - Mime support
 *  
 * This file provides Multipurpose Internet Mail Extensions (MIME) support        
 * 
 * @package VWP
 * @subpackage Libraries.System  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License   
 */

/**
 * Virtual Web Platform - Mime support
 *  
 * This class provides Multipurpose Internet Mail Extensions (MIME) support        
 * 
 * @package VWP
 * @subpackage Libraries.System  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License   
 */


class VMime extends VObject 
{
	
	 /**
	  * Mime Type File Extension Map
	  * 
	  * @var array Mime types indexed by file extension
	  */
	
     public $file_extention_map = array( 
         "ez" => "application/andrew-inset", 
         "hqx" => "application/mac-binhex40", 
         "cpt" => "application/mac-compactpro", 
         "doc" => "application/msword", 
         "bin" => "application/octet-stream", 
         "dms" => "application/octet-stream", 
         "lha" => "application/octet-stream", 
         "lzh" => "application/octet-stream", 
         "exe" => "application/octet-stream", 
         "class" => "application/octet-stream", 
         "so" => "application/octet-stream", 
         "dll" => "application/octet-stream", 
         "oda" => "application/oda", 
         "pdf" => "application/pdf", 
         "ai" => "application/postscript", 
         "eps" => "application/postscript", 
         "ps" => "application/postscript", 
         "smi" => "application/smil", 
         "smil" => "application/smil", 
         "wbxml" => "application/vnd.wap.wbxml", 
         "wmlc" => "application/vnd.wap.wmlc", 
         "wmlsc" => "application/vnd.wap.wmlscriptc", 
         "bcpio" => "application/x-bcpio", 
         "vcd" => "application/x-cdlink", 
         "pgn" => "application/x-chess-pgn", 
         "cpio" => "application/x-cpio", 
         "csh" => "application/x-csh", 
         "dcr" => "application/x-director", 
         "dir" => "application/x-director", 
         "dxr" => "application/x-director", 
         "dvi" => "application/x-dvi", 
         "spl" => "application/x-futuresplash", 
         "gtar" => "application/x-gtar", 
         "hdf" => "application/x-hdf", 
         "js" => "application/x-javascript", 
         "skp" => "application/x-koan", 
         "skd" => "application/x-koan", 
         "skt" => "application/x-koan", 
         "skm" => "application/x-koan", 
         "latex" => "application/x-latex", 
         "nc" => "application/x-netcdf", 
         "cdf" => "application/x-netcdf", 
         "sh" => "application/x-sh", 
         "shar" => "application/x-shar", 
         "swf" => "application/x-shockwave-flash", 
         "sit" => "application/x-stuffit", 
         "sv4cpio" => "application/x-sv4cpio", 
         "sv4crc" => "application/x-sv4crc", 
         "tar" => "application/x-tar", 
         "tcl" => "application/x-tcl", 
         "tex" => "application/x-tex", 
         "texinfo" => "application/x-texinfo", 
         "texi" => "application/x-texinfo", 
         "t" => "application/x-troff", 
         "tr" => "application/x-troff", 
         "roff" => "application/x-troff", 
         "man" => "application/x-troff-man", 
         "me" => "application/x-troff-me", 
         "ms" => "application/x-troff-ms", 
         "ustar" => "application/x-ustar", 
         "src" => "application/x-wais-source", 
         "xhtml" => "application/xhtml+xml", 
         "xht" => "application/xhtml+xml", 
         "zip" => "application/zip", 
         "au" => "audio/basic", 
         "snd" => "audio/basic", 
         "mid" => "audio/midi", 
         "midi" => "audio/midi", 
         "kar" => "audio/midi", 
         "mpga" => "audio/mpeg", 
         "mp2" => "audio/mpeg", 
         "mp3" => "audio/mpeg", 
         "aif" => "audio/x-aiff", 
         "aiff" => "audio/x-aiff", 
         "aifc" => "audio/x-aiff", 
         "m3u" => "audio/x-mpegurl", 
         "ram" => "audio/x-pn-realaudio", 
         "rm" => "audio/x-pn-realaudio", 
         "rpm" => "audio/x-pn-realaudio-plugin", 
         "ra" => "audio/x-realaudio", 
         "wav" => "audio/x-wav", 
         "pdb" => "chemical/x-pdb", 
         "xyz" => "chemical/x-xyz", 
         "bmp" => "image/bmp", 
         "gif" => "image/gif", 
         "ief" => "image/ief", 
         "jpeg" => "image/jpeg", 
         "jpg" => "image/jpeg", 
         "jpe" => "image/jpeg", 
         "png" => "image/png", 
         "tiff" => "image/tiff", 
         "tif" => "image/tif", 
         "djvu" => "image/vnd.djvu", 
         "djv" => "image/vnd.djvu", 
         "wbmp" => "image/vnd.wap.wbmp", 
         "ras" => "image/x-cmu-raster", 
         "pnm" => "image/x-portable-anymap", 
         "pbm" => "image/x-portable-bitmap", 
         "pgm" => "image/x-portable-graymap", 
         "ppm" => "image/x-portable-pixmap", 
         "rgb" => "image/x-rgb", 
         "xbm" => "image/x-xbitmap", 
         "xpm" => "image/x-xpixmap", 
         "xwd" => "image/x-windowdump", 
         "igs" => "model/iges", 
         "iges" => "model/iges", 
         "msh" => "model/mesh", 
         "mesh" => "model/mesh", 
         "silo" => "model/mesh", 
         "wrl" => "model/vrml", 
         "vrml" => "model/vrml", 
         "css" => "text/css", 
         "html" => "text/html", 
         "htm" => "text/html", 
         "asc" => "text/plain", 
         "txt" => "text/plain", 
         "rtx" => "text/richtext", 
         "rtf" => "text/rtf", 
         "sgml" => "text/sgml", 
         "sgm" => "text/sgml", 
         "tsv" => "text/tab-seperated-values", 
         "wml" => "text/vnd.wap.wml", 
         "wmls" => "text/vnd.wap.wmlscript", 
         "etx" => "text/x-setext", 
         "xml" => "text/xml", 
         "xsl" => "text/xml", 
         "mpeg" => "video/mpeg", 
         "mpg" => "video/mpeg", 
         "mpe" => "video/mpeg", 
         "qt" => "video/quicktime", 
         "mov" => "video/quicktime", 
         "mxu" => "video/vnd.mpegurl", 
         "avi" => "video/x-msvideo", 
         "movie" => "video/x-sgi-movie", 
         "ice" => "x-conference-xcooltalk" 
      ); 
	
      
    /**
     * Get bitcount in message
     * 
     * @param string $msg Message  
     * @return integer Number of bits used in message
     * @access public  
     */       

    function use_bits($msg) 
    {
        $ret = 7;
        
        // short circuit
        if (strlen($msg) < 1) {
            return $ret;
        }
  
        // Check bytes in string
  
        $tmp = count_chars($msg, 3);

        if (ord($tmp[strlen($tmp)-1]) & 128) {
            return 8;
        }
  
        return $ret;
    }

    /**
     * Check if message has line overflow
     * 
     * @param string $msg Message
     * @param integer $maxlen Max line length
     * @access public
     */
    
    function has_line_overflow($msg,$maxlen = 76) 
    {
    	
        // Line Length Check
  
        $tmpa = explode($msg,"\n");
        foreach($tmpa as $line) {
            if (strlen($line) > $maxlen) {
                return true;
            }
        }    	
    	return false;
    }
      
    /**
     * Encode message as quoted_printable
     * 
     * @param string $str Message to encode
     * @return string Encoded message
     * @access public  
     */         
 
    function quoted_printable_encode($str) 
    {
        $ret = '';
        $line = '';
        
        $softnl = "=\r\n";
        
        // max line length: 76
                
        $len = strlen($str);
    
        for($i=0; $i<$len; $i++) {
            $char = $str[$i];
            if (ctype_print($char) && !ctype_punct($char) && ($char != " ")) {
                // pass-through
                
            	if (strlen($line) > 72) {
                    $ret .= $line . $softnl;
                    $line = '';
                }     
                $line .= $char;      
            } else {
                // encode
                
                if ($char == "\r") {
                    if (
                        (($i + 1) < $len) &&
                        ($str[$i + 1] == "\n")
                        ) {
                        // pass CRNL                        	
                        $i++;
                        $ret .= $line . "\r\n";
                        $line = '';           
                    } else {                    	
                        // Encode CR        
                        if (strlen($line) > 70) {
                            $ret .= $line . $softnl;
                            $line = '';
                        } 
                        $line .= sprintf('=%02X', ord($char));        
                    }
                } else {
                    if (($char == " ") || ($char == "\t")) {
                    	// Whitespace
                    	
                        if (strlen($line) > 69) {                 
                            // Wrap
                            $ret .= $line . $softnl;
                            $line = '';
                            $line .= sprintf('=%02X', ord($char));
                        } else {
                            if (
                                (($i + 1) == $len) || // eof
                                ( 
                                 (($i + 2) < $len) &&
                                 ($str[$i + 1] == "\r") &&
                                 ($str[$i + 2] == "\n")
                                )
                            ) { 
                                // End of line
              
                                $line .= sprintf('=%02X', ord($char));
                                $ret .= $line . "\r\n";
                                $line = '';
                                $i += 2;
                            } else {
                                $line .= $char;
                            }
                        }
                    } else {
                        // Encode
                        if (strlen($line) > 70) {
                            $ret .= $line . $softnl;
                            $line = '';
                        }
                        $line .= sprintf('=%02X', ord($char));
                    }
                }
            }
        }
    
        if (strlen($line) > 0) {
            $ret .= $line;
        }    
        return $ret;
    }

    /**
     * Encode data in base64 encoding
     * 
     * @param string $data Data
     * @param integer $maxlen Maximum line length
     * @access public
     */

    function base64_encode($data,$maxlen = 76) 
    {    
    	return chunk_split(base64_encode($data), $maxlen, "\r\n");
    }
	 
    /**
     * Get the mimetype of a filename
     * 
     * @param string $filename Filename  
     * @return string Mime type  
     * @access public    
     */   
 
    function mimetypeByFilename($filename) 
    {
    	$ret = null;
    	
        $ext = explode('.', $filename);        
        $ext = $ext[count($ext)-1];       
        if (isset($mimes[$ext])) { 
            $ret = $mimes[$ext];
        }        
        return $ret;
    }
    
    /**
     * Get Mime Driver
     * 
     * @return VMime Mime Driver
     */
    
    public static function &getInstance() {
    	static $md;
    	if (!isset($md)) {
    		$md = new VMime;
    	}
    	return $md;
    }
	    
	// end class VMime
}