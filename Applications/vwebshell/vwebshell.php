<?php

/**
 * VWP - VWebShell Entry Point 
 *  
 *  
 * @package    VWP.VWebShell
 * @subpackage Base
 * @author Ralph Ritoch <rritoch@gmail.com> 
 * @copyright (c) Ralph Ritoch - All rights reserved
 * @link http://www.vnetpublishing.com 
 * @todo Documentation and Licensing 
 */

VWP::RequireLibrary('vwp.sys.types.shell');

/**
 * VWP - VWebShell Entry Point 
 *  
 *  
 * @package    VWP.VWebShell
 * @subpackage Base
 * @author Ralph Ritoch <rritoch@gmail.com> 
 * @copyright (c) Ralph Ritoch - All rights reserved
 * @link http://www.vnetpublishing.com 
 * @todo Documentation and Licensing 
 */

class VWebShellApplication extends VShell {
        
    private $_environment = array();

    private $_cur_screen = null;
    
    
    function &user() {
        $u =& VUser::getCurrent();
        return $u;
    }
    
    function getScreen() {
        return $this->_cur_screen;
    }
    
	/**
	 * Get Shell Environment
	 * 
	 * Note: If method is not provided complete shell environment is returned
	 * 
	 * @return array Shell Environment
	 * @access public
	 */
    
    public function &getEnv($method = null) 
    {                
        if (count($this->_environment) < 1) {
            array_push($this->_environment,array());
        }
        $e = count($this->_environment) - 1;
        
        if ($method === null) {
            return $this->_environment[$e];
        }
        
        if (!isset($this->_environment[$e][$method])) {
        	$this->_environment[$e][$method] = null;
        }
        
        return $this->_environment[$e][$method]; 
    }
    
    public function getAll($method = 'any') {
        $env =& $this->getEnv();
        
        if (isset($env[$method])) {
            return $env[$method];
        }
        return array();
    }


    public function setVar($name,$value = null,$method = 'any') {
        $env =& $this->getEnv();
        if (!isset($env[$method])) {
            $env[$method] = array();
        }
        if (!isset($env['any'])) {
            $env['any'] = array();
        }
        
        $env[$method][$name] = $value;
        $env['any'][$name] = $value;
        return $value;
    }

    public function getVar($name,$default = null,$method = 'any') {
        $env =& $this->getEnv();        
        if (isset($env[$method][$name])) {
            return $env[$method][$name];
        }
        return $default;
    }


    /**
     * Get an environment Variable as a command
     * 
     * @param string $vname Variable name
     * @param mixed $default Default value
     * @param string Method name, defaults to any
     * @return string Command
     * @access public      
     */
  
  
    public function getCmd($vname,$default = false, $method = 'any') 
    {
        $val = self::getVar($vname,$default,$method);

        if (is_array($val)) {
            $val = array_shift($val);
        } 
           
        if (!is_string($val)) {
            return $default;
        }
   
        $words = explode(" ",$val);
        return $words[0];
    }
    

    /**
     * Get an environment Variable as a word
     * 
     * @param string $vname Variable name
     * @param mixed $default Default value
     * @param string Method name, defaults to any
     * @return string Word value
     * @access public      
     */
      
    public function getWord($vname,$default = null, $method = 'any') 
    {
        $val = self::getVar($vname,$default,$method);
        if (is_array($val)) {
            $val = array_shift($val);
        }   
        if (!is_string($val)) {
            return $default;
        }         
        $words = explode(" ",$val);
        return $words[0];
    }
    

     /**
      * Get list of selected checkboxes
      * 
      * @param string|array Checkboxes
      * @param string Method  
      * @return array Selected Checkboxes
      */         

     public function getChecked($checkboxList,$method = 'any') 
     {
        if (!is_array($checkboxList)) {  
            if (!is_string($checkboxList)) {
                return array();
            }
            $checkboxList = array($checkboxList);
        }
              
        $checked = array();
  
        foreach($checkboxList as $ck) {
            $data = self::getVar($ck,null,$method);

            if (!empty($data)) {
                if (is_array($data)) {
                    $current = array_keys($data);     
                } else {
                    $current = array($ck);
                }
    
                $checked = array_merge($checked,$current);
            }
        }
        return $checked;  
    }

    /**
     * Execute Application
     * 
     * @param string $application_name
     * @return string|error Application output on success      
     * @access private   
     */  
   
    public function execute($command, &$envp, &$stdio) 
    {                
                                        
        $argv = explode(' ',$command);
                
        $cmd = v()->filesystem()->path()->clean($argv[0]);
            
        if (strlen($cmd) < 1) {             
            return null;
        }                
         
        $old_screen = $this->_cur_screen;
        $this->_cur_screen = $stdio->getScreenId();
        
        $e = count($this->_environment);
                                
        $this->_environment[$e] = true;        
        $this->_environment[$e] =& $envp;         
                        
        $parts = explode(DS,$cmd);
        
        $appId = array_pop($parts);

        $mods = explode('.',$appId);
        $id = $mods[0];
                                                                        
        $applicationClassName = ucfirst($id) . 'Application';
        
        $vfile =& v()->filesystem()->file();
                 
        if (!class_exists($applicationClassName)) {
        
            // Load Class
            
            if (substr($cmd,0,1) == DS) {                                      
                $paths = array(DS);              
            } else {
                $paths = VEnv::getVar('path',array(),'shell');
            }
            
            $filename1 = $id.'.php';
            $filename2 = $id.DS.$id.'.php';            
                        
            $appFilename = null;
            $len = count($paths);
                                    
            for($i=0;$i < $len; $i++) {
                $path = $paths[$i];
                
                if ($vfile->exists($path.DS.$filename1)) {
                    $appFilename = $path.DS.$filename1;             
                    $i = $len;
                } elseif (($vfile->exists($path.DS.$filename2))) {
                    $appFilename = $path.DS.$filename2;
                    $i = $len;             
                }            
            }                                    
                        
            if (empty($appFilename)) {
                unset($this->_environment[$e]);
                $this->_environment[$e] = true;
                array_pop($this->_environment);
                $this->_cur_screen = $old_screen;
                return VWP::raiseError("File not found \"$cmd\"!","VWebShell",ERROR_FILENOTFOUND,false);
            } 

            /**
             * Require Application
             */
           
            require_once($appFilename);
        
            if (!class_exists($applicationClassName)) {
                unset($this->_environment[$e]);
                $this->_environment[$e] = true;
                array_pop($this->_environment);
                $this->_cur_screen = $old_screen;            
                return VWP::raiseError("Application class \"$applicationClassName\" missing!","VWebShell",null,false);
            }        
            self::$_nosave_application_paths[$applicationClassName] = dirname($appFilename);                                                                
        }
               

           
        self::$_nosave_current_app_path = self::$_nosave_application_paths[$applicationClassName];
       
        $iID = count(self::$_nosave_applications);
        
        $proc = VKernel::o()->createProcess($applicationClassName,$argv);
         
        self::$_nosave_applications[$iID] =& VKernel::o()->getProc($proc);
        
        if (is_object(self::$_nosave_applications[$iID]))  {
            $piID = null;
        
            if (isset(self::$_nosave_current) && self::$_nosave_current != null) {
                $piID = self::$_nosave_current; 
                self::$_nosave_applications[$iID]->_setpiID($piID);
            }
            
            self::$_nosave_current = $iID;
                        
            ob_start();    
                                
            $result = self::$_nosave_applications[$iID]->_execute($argv,$envp);
        
            self::$_nosave_current = $piID;
          
            $outbuffer = ob_get_contents();
        
            ob_end_clean();
            
            $writeOk = true;
            if (self::$_nosave_applications[$iID]->requireSecureLine()) {
                $writeOk = v()->getDocument()->isSecureLine();
            }
            
            if ($writeOk) {
                $stdio->write($outbuffer);
            } else {
                v()->getDocument()->divertUnsecureLine();
            }
            
        } else {
            $result = VWP::raiseWarning('Compile error!','VApplication',null,false);
        }
                
        $this->_environment[$e] = true;
        array_pop($this->_environment); 
        $this->_cur_screen = $old_screen;       
        return $result;         
    }
    
    
    function main($args,$env) {

    }

} 