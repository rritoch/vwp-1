<?php

ob_start();

$appID = 'user';

/** boolean True if a Windows based host */
define('VPATH_ISWIN', (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'));
/** boolean True if a Mac based host */
define('VPATH_ISMAC', (strtoupper(substr(PHP_OS, 0, 3)) === 'MAC'));


global $doc;

$doc = new DomDocument();
$doc->loadXML('<' . '?xml version="1.0" ?' . '>' . "\n" . '<pre_install_results></pre_install_results>');

function xmlentities($str) {
 $str = str_replace("&","&amp;",$str);
 $str = str_replace("<","&lt;",$str);
 $str = str_replace(">","&gt;",$str);
 $str = str_replace("\"","&quot;",$str);
 return $str;
}

function dispatchError($errno, $errstr, $errfile = null, $errline = null) {
 global $doc;
 
 switch($errno) {
   case E_WARNING:
     $etype = "E_WARNING";
     break;
    case E_NOTICE:     
     $etype = "E_NOTICE";
     break;  
    case E_USER_ERROR:
     $etype = "E_USER_ERROR";
     break;
    case E_RECOVERABLE_ERROR:
     $etype = "E_RECOVERABLE_ERROR";
     break;    
    case E_USER_WARNING:
     $etype = "E_USER_WARNING";
     break;
    case E_USER_NOTICE:
     $etype = "E_USER_NOTICE";
     break;
    case E_STRICT:
     $etype = "E_STRICT";
     break;
    default:
     $etype = "E_UNKNOWN";
     break;
 }

 $err = $doc->createElement('error');
 $err->setAttribute("type",$etype);

 $err->appendChild($doc->createElement('errno',xmlentities($errno)));
 $err->appendChild($doc->createElement('errstr',xmlentities($errstr)));
 $err->appendChild($doc->createElement('errfile',xmlentities($errfile)));
 $err->appendChild($doc->createElement('errline',xmlentities($errline)));
 $doc->documentElement->appendChild($err);
 return true; 
}

set_error_handler(  'dispatchError',E_ALL | E_STRICT);


function folder_create($path = '', $mode = 0755) {
     
   // Initialize variables
 static $nested = 0;
   
   // Check to make sure the path valid and clean
 $path = clean_path($path);

 // Check if parent dir exists
 $parent = dirname($path);
   
 if (!is_dir($parent)) {
    // Prevent infinite loops!
    $nested++;
    if (($nested > 20) || ($parent == $path)) {
     $nested--;
     return false;
    }

    // Create the parent directory
    
    if (!folder_create($parent,$mode)) {
     $nested--;
     return false;
    }
    // OK, parent directory has been created
    $nested--;
 }

 // Check if dir already exists
 if (is_dir($path)) {
  return true;
 }

 // Check for safe mode
     
 // We need to get and explode the open_basedir paths
 $obd = ini_get('open_basedir');

 // If open_basedir is set we need to get the open_basedir that the path is in
 if ($obd != null) {
  if (VPATH_ISWIN) {
   $obdSeparator = ";";
  } else {
   $obdSeparator = ":";
  }
   
  // Create the array of open_basedir paths
  $obdArray = explode($obdSeparator, $obd);
  $inBaseDir = false;
  // Iterate through open_basedir paths looking for a match
  foreach ($obdArray as $test) {
   $test = clean_path($test);
   if (strpos($path, $test) === 0) {
    $obdpath = $test;
    $inBaseDir = true;
    break;
   }
  }
   
  if ($inBaseDir == false) {
    // Return false because the path to be created is not in open_basedir
   return false;

  }
 }

 // First set umask
 $origmask = @umask(0);

 // Create the path
 if (!$ret = @mkdir($path, $mode)) {
  @umask($origmask);
  return false;
 }

 // Reset umask
 @umask($origmask);		
 return $ret;
}

function folder_copy($src, $dest) {
 
 // Eliminate trailing directory separators, if any
 $src = rtrim($src, DS);
 $dest = rtrim($dest, DS);

 if (!is_dir($src)) {
  trigger_error("Source folder $src not found!");
  return false;
 }

 // Make sure the destination exists
 
 if (! folder_create($dest)) {
  trigger_error("Unable to create folder $dest.");
  return false;
 }
 
 if (!($dh = @opendir($src))) {
  trigger_error("Unable to read source folder $src.");
  return false;
 }
 
 // Walk through the directory copying files and recursing into folders.

 while (($file = readdir($dh)) !== false) {
  $sfid = $src . DS . $file;
  $dfid = $dest . DS . $file;
  switch (filetype($sfid)) {
   case 'dir':
    if ($file != '.' && $file != '..') {
     $ret = folder_copy($sfid, $dfid);
     if ($ret !== true) {
      return $ret;
     }
    }
    break;
    
   case 'file':
    if (!@copy($sfid, $dfid)) {
     trigger_error("Unable to copy $sfid to $dfid !");
     return false;
    }
    break;
  }
 }		
 return true;
}

// Define Directory Separator

if (defined('DIRECTORY_SEPARATOR')) {
 define('DS',DIRECTORY_SEPARATOR); 
} else {
 define('DS','/');
}

function clean_path($path,$ds = DS) {
 $path = str_replace("/",$ds,$path);
 $path = str_replace("\\",$ds,$path);
 return $path;
}


// Start Code

$app_offset = '/base';

$cwd = clean_path(dirname(__FILE__),DS);
$sdir = clean_path(dirname(dirname(__FILE__)),DS);

$rqst = array();

foreach($_GET as $key=>$val) {
 if (get_magic_quotes_gpc()) {
  $rqst[$key] = stripslashes($val);
 } else {
  $rqst[$key] = $val;
 }
}

foreach($_POST as $key=>$val) {
 if (get_magic_quotes_gpc()) {
  $rqst[$key] = stripslashes($val);
 } else {
  $rqst[$key] = $val;
 }
}

if (isset($rqst["offset_path"])) {
 $path_offset = clean_path($rqst["offset_path"] . $app_offset,DS);
} else {
 die("Missing offset path!");
}

$split = explode($path_offset,$cwd);

array_pop($split);
 
$base_path = implode($path_offset,$split);  

if (strlen($base_path) < 1) { 
 die("Unable to parse base path!");
}

/*
$preserve = false;
if (file_exists($base_path.DS.'vwp_config.php')) {
 $preserve = $base_path.DS.'vwp_config.php.save';
 rename($base_path.DS.'vwp_config.php',$preserve);
}
*/

$c = array(); // Copy Results
// copy libraries
//$c[] = folder_copy($sdir.DS.'library',$base_path.DS.'libraries'.DS.$appID);

// copy base
$c[] = folder_copy($sdir.DS.'base',$base_path.DS.'Applications'.DS.$appID);

// copy root
//$c[] = folder_copy($sdir.DS.'base'.DS.'root',$base_path);


/*
if ($preserve !== false) {
 rename($preserve,$base_path.DS.'vwp_config.php');
}
*/

$status = "Success";

$ctr = 0;
foreach($c as $copyresult) {
 $ctr++;
 if (!$copyresult) {
  $status = "Failed Copy $ctr!";
 }
}

$stat = $doc->createElement('status',xmlentities($status));
$doc->documentElement->appendChild($stat);
$s_out = ob_get_contents();
$stat = $doc->createElement('stdout',xmlentities($s_out));
$doc->documentElement->appendChild($stat);
ob_end_clean();

header("content-type: text/xml;");
echo $doc->saveXML();