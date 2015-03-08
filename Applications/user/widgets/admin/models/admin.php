<?php
 
/**
 * User Model 
 *  
 * @package    VWP.User
 * @subpackage Widgets
 * @author Ralph Ritoch <rritoch@gmail.com> 
 * @copyright (c) Ralph Ritoch - All rights reserved
 * @link http://www.vnetpublishing.com 
 */

/**
 *  Require Model Support
 */

VWP::RequireLibrary("vwp.model");

/**
 *  Require Registry Support
 */

VWP::RequireLibrary('vwp.sys.registry');

/**
 *  Require Event Support
 */

VWP::RequireLibrary('vwp.sys.events');

/**
 * User Model 
 *  
 * @package    VWP.User
 * @subpackage Widgets
 * @author Ralph Ritoch <rritoch@gmail.com> 
 * @copyright (c) Ralph Ritoch - All rights reserved
 * @link http://www.vnetpublishing.com 
 */

class User_Admin_Model_Admin extends VModel 
{


	function deleteUsers($userlist) 
	{
		
		$ret = true;
		foreach($userlist as $username) {
            $user =& VUser::getUserByUsername($username);
            if (!VWP::isWarning($user)) { 
                $result = $user->delete();
                if (VWP::isWarning($result)) {
            	    $ret = VWP::raiseWarning('Unable to remove some users!',__CLASS__,null,false);
                }
            }
            	        		
		}
		return $ret;
	}
	
    function registerUser($userinfo) 
    {
        unset($userinfo["confirm_password"]);
        $result = VEvent::dispatch_event('user','register',$userinfo);
      
        $lastError = VWP::raiseWarning("No user databases available!",get_class($this),null,false);
      
        foreach($result["trace"] as $r) {
            if (!VWP::isWarning($r["result"]) && $r["result"]) {
                return true;
            } else {
   	            if ($r["result"]) {
                    $lastError = $r["result"];
   	            }
            }
        }    
        return $lastError;
    }

    function saveUser($userinfo) {
        unset($userinfo["confirm_password"]);
        $result = VEvent::dispatch_event('user','save',$userinfo);
  
        $lastError = VWP::raiseWarning("No user databases available!",get_class($this),null,false);
        foreach($result["trace"] as $r) {
            if (!VWP::isWarning($r["result"])) {
                return true;
            } else {
                $lastError = $r["result"];
            }
        }    
        return $lastError;
    }
 
    function getUser($username) 
    {
        $credentials = array("username"=>$username);
        $result = VEvent::dispatch_event('user','find',$credentials);  
    
        $result = $result["result"];
  
        if (VWP::isWarning($result)) {
            return $result;
        }
  
        if (!is_array($result)) {
            return VWP::raiseWarning("User not found!",get_class($this),null,false);
        }
  
        if (!isset($result["name"])) {
            $result["name"] = '';
        }
        return $result;
    }
 
    function getAdminUserlist() 
    {
        global $users;
  
        $src = VPATH_BASE.DS.'etc'.DS.'passwd.php';
        if (!$this->_vfile->exists($src)) {
            return array();
        }
  
        if (isset($users)) {
            $old_users = $users;
        }
  
        require($src);
        $list = array();
        foreach($users as $id=>$info) {
            if (!isset($info["name"])) {
                $info["name"] = '';
            }
            array_push($list,$info);
        }
  
        if (isset($old_users)) {
            $users = $old_users;
        }
  
        return $list; 
    }
 
    function getUserUserlist() 
    {
        $config = $this->getConfig();
  
        if (VWP::isWarning($config)) {
            return array(); // fudge
        }
  
        $db =& $this->_dbi->getDatabase($config["user_database"]);
        if (VWP::isWarning($db)) {
            return array(); // fudge
        }
  
        // Check Table
  
        $tbl_prefix = $config["table_prefix"];
  
        $table_name = $tbl_prefix . 'users';
        $table = & $db->getTable($table_name);
  
        if (VWP::isError($table)) {
            return array(); // double fudge
        }
  
        $table->loadData();
        $users = array();
        foreach($table->rows as $row) {
            $u = $row->getProperties();
            $user = array();
            foreach($u["fields"] as $id=>$i) {
                $user[$id] = $i["value"];
            }   
            array_push($users,$user);
        }  
        return $users;
    }
 
    function getUsers() {
    
        $ladmin = array();
  
        foreach($this->getAdminUserlist() as $user) {
            $user["_source"] = "File";
            $ladmin[$user["username"]] = $user;
        }
  
        $luser = array();

        foreach($this->GetUserUserlist() as $user) {
            $user["_source"] = "Database";
            $luser[$user["username"]] = $user;
        }
    
        $m = array_merge($luser,$ladmin);
  
        $users = array();
        foreach($m as $key=>$val) {
            array_push($users,$val);
        }
        return $users;   
    }
 
    function getDBList() 
    {
        $dblist = array(""=>"(none)");
        $l = $this->_dbi->listDatabases();      
        if (VWP::isWarning($l)) {
            $l->ethrow();
            return $dblist;
        }
        foreach($l as $d) {
            $dblist[$d] = $d;
        }
        return $dblist;
    }

    function getConfigKey() {
        return "SOFTWARE\\VNetPublishing\\User\\Config";
    }
 
    /**
     * Get configuration settings
     * 
     * @return array|object Configuration settings on success, error or warning otherwise
     */       
 
    function getConfig() {
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

    /**
     * Check configuration settings
     */
     
    function checkConfig() 
    {
        $config = $this->getConfig();
        if (VWP::isWarning($config)) {
            return $config;
        }
        if (empty($config["user_database"])) {
            return true;
        }
  
        // Check Database
  
        $db =& $this->_dbi->getDatabase($config["user_database"]);
        if (VWP::isWarning($db)) {
            return $db;
        }
  
        // Check Table
  
        $tbl_prefix = $config["table_prefix"];
  
        $table_name = $tbl_prefix . 'users';
        $table = & $db->getTable($table_name);
  
        if (VWP::isError($table)) {  
            $type = strtolower($db->getDBType());
   
            switch($type) {
   
                case "mysql":
    
                    $query = 'CREATE TABLE IF NOT EXISTS '
                             .$db->nameQuote($table_name)
                             .' (
                               `id` int(11) NOT NULL AUTO_INCREMENT,                               `username` varbinary(128) NOT NULL,
                               `email` varbinary(128) NOT NULL,
                               `email_verification` varbinary(128),
                               `password` text NOT NULL,
                               `name` text NOT NULL,
                               `meta` BLOB,
                               `_admin` tinyint(1) UNSIGNED NOT NULL DEFAULT \'0\',
                               `_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                               PRIMARY KEY (`id`)
                              ) ENGINE=MyISAM DEFAULT CHARSET=latin1';
                    $db->setQuery($query);
                    $result = $db->query();
                    $db->unloadTables();                  
                    break;
                default:
                    return VWP::raiseWarning("Unable to create tables due to unsupported database type ($type)!",get_class($this),null,false);   
            }
        } else {
            $result = true;
        }      
        return $result;
    }
  
    /**
     * Save configuration settings
     * 
     * @param array $settings Configuration settings
     * @return true|object True on success, error or warning otherwise
     * @access public  
     */    
  
    function saveConfig($settings) 
    {
        $localMachine = & Registry::LocalMachine();  
  
        $result = Registry::RegCreateKeyEx($localMachine,
                              self::getConfigKey(),
                              0,
                              '',
                              0,
                              0,
                              0,
                              $registryKey,
                              $result); 
                              
        if (!VWP::isWarning($result)) {
            $result = true;
            foreach($settings as $key=>$val) {
                $sresult= Registry::RegSetValueEx($registryKey,
                           $key,
                           0, // reserved 
                           REG_SZ, // string
                           $val,
                           strlen($val)); 
                if (VWP::isWarning($sresult)) {
                    $result = $sresult;                            
                }  
            }
   
            Registry::RegCloseKey($registryKey);
            Registry::RegCloseKey($localMachine);
            if ($result === true) {
                $result = $this->checkConfig();
            }
            return $result;
        }
  
        Registry::RegCloseKey($localMachine);
        return $result; 
    }

    // end class User_Admin_Model_Admin
    
}
 