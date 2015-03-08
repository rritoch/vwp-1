<?php

/**
 * SMTP Client
 *     
 * @package VWP
 * @subpackage Libraries.Networking.Clients  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

/**
 * Require SMTP Protocol
 */

VWP::RequireLibrary('vwp.net.protocols.smtp');

/**
 * Require Mime Support
 */

VWP::RequireLibrary('vwp.sys.mime');

/**
 * SMTP Client
 *     
 * @package VWP
 * @subpackage Libraries.Networking.Clients  
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */


class VSMTPClient extends VSMTP 
{
	
    protected $_mime;
	

    /**
     * Email Subject
     *   
     * @var string $subject Email subject
     * @access private   
     */

    var $subject;

    /**
     * Rejected email
     *   
     * @var string $rejected_email Rejected email
     * @access private   
     */

    var $rejected_email;

    /**
     * BCC addresses
     *   
     * @var array $bcc BCC addresses
     * @access private   
     */

    var $bcc;

    /**
     * To address
     *   
     * @var string $to To address
     * @access private   
     */

    var $to;

    /**
     * Email headers
     *   
     * @var array $named_headers Email headers
     * @access private   
     */

    var $named_headers;

    /**
     * HTML Message
     *   
     * @var string $html_message HTML message
     * @access private   
     */
 
    var $html_message;

    /**
     * HTML Charset
     *   
     * @var string $html_charset HTML charset
     * @access private   
     */

    var $html_charset;

    /**
     * Text message
     *   
     * @var string $text_message Text message
     * @access private   
     */

    var $text_message;

    /**
     * Text Charset
     *   
     * @var string $text_charset Text charset
     * @access private   
     */

    var $text_charset;

    /**
     * File attachments
     *   
     * @var array $attachments File attachments
     * @access private   
     */


    var $attachments; 

    /**
     * Clear all buffers
     * 
     * @access public
     */       
  
    function smtp_flush() 
    {             
        $this->rejected_email = null;
        $this->_smtp_msg = null;
        $this->bcc = array();
        $this->named_headers = array();
        $this->rejected_email = null;
        $this->html_message = null;
        $this->html_charset = "iso-8859-1";  
        $this->text_message = null;
        $this->text_charset = "iso-8859-1";
        $this->attachments = array();
    }
   
    /**
     * Set text message
     * 
     * @param string $msg Text message
     * @access public
     */
           
    function set_text_message($msg) {
        $this->text_message = $msg;
    }

    /**
     * Set HTML message
     * 
     * @param string $msg HTML message
     * @access public
     */

    function set_html_message($msg) {
        $this->html_message = $msg;
    }

 
    /**
     * Get the mimetype of a filename
     * 
     * @param string $filename Filename  
     * @return string Mime type  
     * @access public    
     */   
 
    function query_files_mimetype($filename) 
    {    	        	
    	$mtype = $this->_mime->mimetypeByFilename($filename);
    	
    	if (empty($mtype)) {
    		$mtype = 'application/octet-stream'; 
    	}
        return $mtype;  
    }   
 
    /**
     * Add a file attachment
     * 
     * @param string $source File source
     * @param string $filename File name
     * @param string $mimetype (optional) Mime type  
     * @access public      
     */
        
    function add_attachment($source,$filename,$mimetype = false) 
    {
        $fileinfo = array();
        if ($mimetype) {
            $fileinfo["mime-type"] = $mimetype;
        }
        $fileinfo["file"] = $source;
        $fileinfo["filename"] = $filename;
        array_push($this->attachments,$fileinfo);
    }

    /**
     * Get encoded Message
     * 
     * @return string Encoded message
     * @access public      
     */   

    function getMessage() 
    {
        $ret = "";
  
        $multipart = false;

        if ($this->text_message === null && $this->html_message === null) {
            return $this->report_error("No Message Provided.");
        }
      
        if (
            (($this->text_message !== null) && ($this->html_message !== null)) ||
            (count($this->attachments) > 0)    
            ) {
            $multipart = true;
        }
  
        if (!$multipart) {
            $headers = "";
            foreach ($this->named_headers as $key=>$val) {
                $headers .= $val . "\r\n";
            }
            $headers .= "MIME-Version: 1.0\r\n";
         
            if ($this->html_message !== null) {
                // html message
                $headers .= "Content-Type: text/html; charset=" . $this->html_charset . "\r\n";
                if (($this->_mime->use_bits($this->html_message) < 8) && (!$this->_mime->has_line_overflow($this->html_message))) {
                    // 7bit html
                    $headers .= "Content-Transfer-Encoding: 7bit\r\n";
                    $data = $headers . "\r\n" . $this->html_message;
                } else {
                    // 8bit html
                    $headers .= "Content-Transfer-Encoding: quoted-printable\r\n";
                    $data = $headers . "\r\n";
                    $data .= $this->_mime->quoted_printable_encode($this->html_message); 
                }            
            } else { 
                // text message        
                $headers .= "Content-Type: text/plain; charset=" . $this->text_charset . "\r\n";
                if ($this->use_bits($this->text_message) < 8) {
                    // 7bit text
                    $headers .= "Content-Transfer-Encoding: 7bit\r\n";
                    $data = $headers . "\r\n" . $this->text_message;
                } else {
                    // 8bit text
                    $headers .= "Content-Transfer-Encoding: quoted-printable\r\n";
                    $data = $headers . "\r\n";
                    $data .= $this->quoted_printable_encode($this->text_message); 
                }        
            }
 	        $data = str_replace("\n.\r\n", "\n. \r\n", preg_replace("~(?<!\r)\n~is", "\r\n", $data) ); 	 
            $ret .= $data;
        } else {
            // MULTIPART
            
            // init            
            $mime_boundary = "<ECS>--==+X[".md5(time())."]";
    
            // headers       
            $headers = "";
    
            foreach ($this->named_headers as $key=>$val) {
                $headers .= $val . "\r\n";
            }
            $headers .= "MIME-Version: 1.0\r\n";
            if ($this->html_message !== null && $this->text_message !== null) {
                 $headers .= "Content-Type: multipart/alternative;\r\n\tboundary=\"".$mime_boundary."\"\r\n";
            } else { 
                 $headers .= "Content-Type: multipart/mixed;\r\n\tboundary=\"".$mime_boundary."\"\r\n";
            }
    
            $data = str_replace("\n.\r\n", "\n. \r\n", preg_replace("~(?<!\r)\n~is", "\r\n", $headers) );
     	  
            $ret .= $data;
            // text message
   
            if ($this->text_message !== null) {
                $headers = "\r\n--" . $mime_boundary . "\r\n";
                $headers .= "Content-Type: text/plain; charset=" . $this->text_charset . "\r\n";
                if (($this->_mime->use_bits($this->text_message) < 8) && (!$this->_mime->has_line_overflow($this->text_message))) {
                    // 7bit text
                    $headers .= "Content-Transfer-Encoding: 7bit\r\n";
                    $data = $headers . "\r\n" . $this->text_message;
                } else {
                    // 8bit text
                    $headers .= "Content-Transfer-Encoding: quoted-printable\r\n";
                    $data = $headers . "\r\n";
                    $data .= $this->quoted_printable_encode($this->text_message);       
                }   
 	            $data = str_replace("\n.\r\n", "\n. \r\n", preg_replace("~(?<!\r)\n~is", "\r\n", $data) );
 	            $ret .= $data;
            }

            if ($this->html_message !== null) {
                $headers = "\r\n--" . $mime_boundary . "\r\n";
                $headers .= "Content-Type: text/html; charset=" . $this->html_charset . "\r\n";
                if (($this->_mime->use_bits($this->html_message) < 8) && (!$this->_mime->has_line_overflow($this->html_message))) {
                    // 7bit text
                    $headers .= "Content-Transfer-Encoding: 7bit\r\n";
                    $data = $headers . "\r\n" . $this->html_message;
                } else {
                    // 8bit text
                    $headers .= "Content-Transfer-Encoding: quoted-printable\r\n";
                    $data = $headers . "\r\n";
                    $data .= $this->quoted_printable_encode($this->html_message); 
                }   
 	            $data = str_replace("\n.\r\n", "\n. \r\n", preg_replace("~(?<!\r)\n~is", "\r\n", $data) );
 	            $ret .= $data;
            }
    
            if (count($this->attachments) > 0) {    
                foreach ($this->attachments as $fileinfo) {
      
                    $filename = $fileinfo["filename"];
                    if (isset($fileinfo["mime-type"])) {
                        $content = $fileinfo["mime-type"];
                    } else {      
                        $content = $this->_mime->mimetypeByFilename($filename);
                    }          
                    $headers = "\r\n--" . $mime_boundary . "\r\n";                
                    $headers .= "Content-Type: $content;\r\n\tname=\"$filename\"\r\n";
                    $headers .= "Content-Transfer-Encoding: base64\r\n";
                    $headers .= "Content-Disposition: attachment;\r\n\tfilename=\"$filename\"\r\n";
                    $headers .= "\r\n";
                    $data = str_replace("\n.\r\n", "\n. \r\n", preg_replace("~(?<!\r)\n~is", "\r\n", $headers) );
 	                $ret .= $data; 	
                    $cache = '';
      
                    $data = $this->_mime->base64_encode(file_get_contents($fileinfo["file"]));
      
                    if ((strlen($data) > 1) &&
                         (substr($data,strlen($data) - 2,2) == "\r\n")
                       ) {
                        $data = substr($data,0,strlen($data) - 2);
                    }
      
                    $data = str_replace("\n.\r\n", "\n. \r\n", preg_replace("~(?<!\r)\n~is", "\r\n", $data) );
                    $ret .= $data;
                }
            }
    
            $data = "\r\n--" . $mime_boundary . "--\r\n\r\n";
            $data = str_replace("\n.\r\n", "\n. \r\n", preg_replace("~(?<!\r)\n~is", "\r\n", $data) );
            $ret .= $data;               
        } // end multipart
 
        return $ret;
    }
 
    /**
     * Remove unwanted characters and symbols from a string
     * 
     * @param string $str Original string
     * @return string String with unwanted characters removed    
     * @access private  
     */
       
    function clean_string($str) 
    {
	    $str = preg_replace("~[ \r\n\t]~", " ", $str);
	    $str = preg_replace("~,,~", ",", $str);
        $str = preg_replace("~\#\[\]'\"\(\):;/\$!£%\^&\*\{\}~", "", $str);
	    return $str;
    } 
  
    /**
     * Sent email
     * 
     * @return true|false True on success or false otherwise
     * @access public    
     */     
 
    function send_message() 
    {
        if (!$this->_socket) {
            return false;
        }
  	 
	    $from = &$this->from;
	    $to = &$this->to;
        
	    // $subject = &$this->subject;


	    if($this->smtp_command("MAIL FROM: <". $from .">") != 250) {
		    return $this->report_error("Could not set who this mail is from.");
        }

	    $bcc = $this->bcc;
	    if ($bcc) {
	        array_unshift($bcc,$this->to);
	    } else {
	        $bcc = array($this->to);
        }
  
        $this->rejected_email = array();
        
	    foreach($bcc as $email) {
            if (preg_match("~[^ ]+\@[^ ]+~", $email)) {
                if($this->smtp_command("RCPT TO: <". $email .">") != 250) {
                    $this->rejected_email[] = $email;
                    return $this->report_error("Could not send email to: $email");
	            }
	        } else {
                $this->rejected_email[] = $email;
                return $this->report_error("Could not send email to: $email");   
            }
        }

        $data = $this->getMessage();
  
        if (VWP::isWarning($data)) {
        	return $data;
        }

        $result = $this->send_data($data);        
        if (VWP::isWarning($result)) {
            return $result;
        }
          
       return true;
   }
 
   
    /**
     * Get rejected email address
     *   
     * @return string Rejected email address
     * @access public    
     */     

    function query_rejected_email() 
    {
        return $this->rejected_email;
    }

    /**
     * Set BCC addresses
     * 
     * @param array $emails Email addresses
     * @access public    
     */
       
    function set_bcc($emails) 
    {
	    $this->bcc = $emails;
    }
 
    /**
     * Set who the email is from
     * 
     * @param string $email Email address
     * @param string $name Name
     */         

    function set_from($email, $name = false) 
    {

	    $email = $this->clean_string($email);
        $name = $this->clean_string($name);

        $this->from = $email;
    
	    $this->named_headers["return_path"] = "Return-Path: <{$email}>";

        if ($name) {	
 	        $this->named_headers["from"] = "From: \"{$name}\" <{$email}>";
	    } else {
	        $this->named_headers["from"] = "From: {$email}";
        }
  
	    //$this->_headers[] = "Message-Id: <". md5(uniqid(rand())) .".". preg_replace("~[^a-z0-9]~i", "", $name) ."@". $this->_smpt_server .">";
	    return true;
    } 
  
    /**
     * Set who the email will be sent to
     * 
     * @param string $email Email address
     * @param string|false $name Name
     * @access public       
     */     

    function set_to($email, $name = FALSE) 
    {	
	    $email = $this->clean_string($email);

	    if($name) {
            $name = $this->clean_string($name);
	        $to = "To: \"{$name}\" <{$email}>";
	    } else {
            $to = "To: {$email}";
        }

	    $this->to = $email;
        $this->named_headers["to"] = $to;
    }
 
    /**
     * Set message subject
     * 
     * @param string $subject Message subject
     * @access public
     */
           
    function set_subject($subject) 
    {
        $subject = $this->clean_string($subject);
        $this->subject = $subject;
        $this->named_headers["subject"] = "Subject: {$subject}";
    } 
 
    /**
     * Class Constructor
     *
     * @access public
     */
 
    function __construct() {
     	parent::__construct();
     	$this->_mime =& VMime::getInstance();     	  
        $this->smtp_flush();     	
    }          	
}