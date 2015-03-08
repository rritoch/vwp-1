<?php

/**
 * VWP Registry Library
 * 
 * This library contains classes to interface with a
 * virtual system registry.
 *   
 * @package VWP
 * @subpackage Libraries.System
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License    
 * @todo Test HKEY_CLASSES_ROOT
 * @todo Implement Registry Security
 * @todo Implement reserved API functions
 * @todo Implement remaining registry storage types
 */ 

/**
 * Require Registry Key Support
 */

VWP::RequireLibrary('vwp.sys.registry.key');

/**
 * Require Registry LOCAL_MACHINE key Support
 */

VWP::RequireLibrary('vwp.sys.registry.local_machine');

/**
 * Require Registry CLASSES_ROOT key Support
 */

VWP::RequireLibrary('vwp.sys.registry.classes_root');

/**
 * Require Registry USERS key Support
 */

VWP::RequireLibrary('vwp.sys.registry.users');

/**
 * Require Registry CURRENT_CONFIG key Support
 */

VWP::RequireLibrary('vwp.sys.registry.current_config');

/**
 * Require Registry CURRENT_USER key Support
 */

VWP::RequireLibrary('vwp.sys.registry.current_user');


/*
 * Design Notes
 *
 * HKEY_CLASSES_ROOT is alias of HKEY_LOCAL_MACHINE/Software/Classes
 * 
 * HKEY_USERS\(sid)_Classes - User specific file extensions 
 * HKEY_USERS\(sid)\SOFTWARE\Classes -  
 * HKEY_CURRENT_USER is alias linking to HKEY_USERS/.DEFAULT + HKEY_USERS/(sid) 
 */

/**
 * VWP Registry Library
 * 
 * This is Registry API Interface class.
 *   
 * @package VWP
 * @subpackage Libraries.System
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License    
 */ 
 
 class Registry extends VObject 
 {
    
     /**
      * Get instance of HKEY_CLASSES_ROOT Registry Key
      *             
      * @return object HKEY_CLASSES_ROOT Registry Key
      * @access public   
      */
              
     public static function &ClassesRoot() 
     {
         static $hkey_classes_root;   
         if (!isset($hkey_classes_root)) {
             $hkey_classes_root = new HKEY_CLASSES_ROOT();
         }
         return $hkey_classes_root;
     } 

     /**
      * Get instance of HKEY_CURRENT_USER Registry Key
      *      
      *       
      * @return object HKEY_CURRENT_USER Registry Key
      * @access public   
      */
  
     function CurrentUser() 
     {  
         static $hkey_current_user;   
         if (!isset($hkey_current_user)) {
             $hkey_current_user = new HKEY_CURRENT_USER();
         }
         return $hkey_current_user;    
     }

     /**
      * Get instance of HKEY_LOCAL_MACHINE Registry Key
      *       
      * @return object HKEY_LOCAL_MACHINE Registry Key
      * @access public   
      */

     public static function &LocalMachine() 
     {
         static $hkey_local_machine;   
         if (!isset($hkey_local_machine)) {
             $hkey_local_machine = new HKEY_LOCAL_MACHINE();
         }
         return $hkey_local_machine;       
     }
    
     /**
      * Retrieves the current size of the registry and the maximum size that the registry is allowed to attain on the system.
      * 
      * @todo Implement GetSystemRegistryQuota()
      * @return integer Error code, currently returns ERROR_UNSUPPORTED         
      * @access public          
      */
        
     function GetSystemRegistryQuota() 
     {
         return ERROR_UNSUPPORTED;  
     }
  
     /**
      * Closes a handle to the specified registry key.
      * 
      * @param object $hKey A handle to the open key to be closed.
      * @return integer Returns error code or ERROR_SUCCESS if successful
      * @access public            
      */
        	
     public static function RegCloseKey($hkey) 
     {
         $hkey->Close();
         return ERROR_SUCCESS;
     }
  
     /**
      * Establishes a connection to a predefined registry handle on another computer.
      *
      * Reserved for future use!
      *
      * @todo Implement RegConnectRegistry()
      * @return integer Error code, currently returns ERROR_UNSUPPORTED         
      * @access public          
      */    
  	
     function RegConnectRegistry() 
     {
         return ERROR_UNSUPPORTED;
     }
  
     /**
      * Copies the specified registry key, along with its values and subkeys, to the specified destination key.
      *    
      * Reserved for future use!
      *
      * @todo Implement RegCopyTree()
      * @return integer Error code, currently returns ERROR_UNSUPPORTED         
      * @access public          
      */    

     function RegCopyTree() 
     {
         return ERROR_UNSUPPORTED;
     }
  
     /**
      * Creates the specified registry key.
      * 
      * If the key already exists, the function opens it. Note that key names are not case sensitive.
      * 
      * Possible Values for $dwOptions
      *    
      * REG_OPTION_BACKUP_RESTORE 0x00000004L
      * 
      * If this flag is set, the function ignores the samDesired parameter and attempts to open the key with the access required to backup or restore the key. If the calling thread has the SE_BACKUP_NAME privilege enabled, the key is opened with the ACCESS_SYSTEM_SECURITY and KEY_READ access rights. If the calling thread has the SE_RESTORE_NAME privilege enabled, the key is opened with the ACCESS_SYSTEM_SECURITY and KEY_WRITE access rights. If both privileges are enabled, the key has the combined access rights for both privileges. For more information, see Running with Special Privileges.
      * 
      * REG_OPTION_CREATE_LINK 0x00000002L
      * 
      * This key is a symbolic link. The target path is assigned to the L"SymbolicLinkValue" value of the key. The target path must be an absolute registry path.
      * 
      * Registry symbolic links should be used only when absolutely necessary for application compatibility.
      * 
      * REG_OPTION_NON_VOLATILE 0x00000000L
      * 
      * This key is not volatile; this is the default. The information is stored in a file and is preserved when the system is restarted. The RegSaveKey function saves keys that are not volatile.
      * 
      * REG_OPTION_VOLATILE 0x00000001L
      *
      * All keys created by the function are volatile. The information is stored in memory and is not preserved when the corresponding registry hive is unloaded. For HKEY_LOCAL_MACHINE, this occurs when the system is shut down. For registry keys loaded by the RegLoadKey function, this occurs when the corresponding RegUnLoadKey is performed. The RegSaveKey function does not save volatile keys. This flag is ignored for keys that already exist.       
      *
      * Return values for $lpdwDisposition
      *    
      * REG_CREATED_NEW_KEY 0x00000001L The key did not exist and was created.
      * REG_OPENED_EXISTING_KEY 0x00000002L The key existed and was simply opened without being changed.
      *           
      * @param object $hKey A handle to an open registry key. The calling process must have KEY_CREATE_SUB_KEY access to the key
      * @param string $lpSubKey  The name of a subkey that this function opens or creates. The subkey specified must be a subkey of the key identified by the hKey parameter; it can be up to 32 levels deep in the registry tree.
      * @param integer $Reserved This parameter is reserved and must be zero
      * @param string $lpClass The user-defined class type of this key. This parameter may be ignored. This parameter can be NULL
      * @param integer $dwOptions
      * @param mixed $samDesired A mask that specifies the access rights for the key.
      * @param mixed $lpSecurityAttributes
      * @param object $phkResult A pointer to a variable that receives a handle to the opened or created key. If the key is not one of the predefined registry keys, call the RegCloseKey function after you have finished using the handle.
      * @param integer $lpdwDisposition  
      * @return integer Returns ERROR_SUCCESS on success or error code otherwise                                      
      */
        	
     public static function RegCreateKeyEx(
                      $hKey,
                      $lpSubKey,
                      $Reserved,
                      $lpClass,
                      $dwOptions,
                      $samDesired,
                      $lpSecurityAttributes,
                      &$phkResult, 
                      &$lpdwDisposition) {
    
        $phkResult = $hKey->getKey($lpSubKey);
        if ($phkResult !== false) {
            $lpdwDisposition = ERROR_SUCCESS;
            return ERROR_SUCCESS;
        }
        $phkResult = $hKey->createKey($lpSubKey);
        if ($phkResult !== false) {
            $lpdwDisposition = ERROR_SUCCESS;
            return ERROR_SUCCESS;
        }
        $lpdwDisposition = ERROR_FAILED;
        return VWP::raiseError("Unable to create registry key $lpsubKey.","Registry::RegCreateKeyEx",$lpdwDisposition,false);   
    }
  
    /**
     * Create a transacted registry key
     *    
     * Reserved for future use!
     *
     * @todo Implement RegCreateKeyTransacted()
     * @return integer Error code, currently returns ERROR_UNSUPPORTED         
     * @access public          
     */  
           	
    function RegCreateKeyTransacted() 
    {
        return ERROR_UNSUPPORTED;
    }  
     
    /**
     * Deletes a subkey and its values.
     * 	   
     * Reserved for future use!
     *
     * @todo Implement RegDeleteKey()
     * @return integer Error code, currently returns ERROR_UNSUPPORTED         
     * @access public          
     */  
          
    function RegDeleteKey() 
    {
        return ERROR_UNSUPPORTED;
    }

    /**
     * Deletes a subkey and its values from the specified platform-specific view of the registry.
     *    
     * Reserved for future use!
     *
     * @todo Implement RegDeleteKeyEx()
     * @return integer Error code, currently returns ERROR_UNSUPPORTED         
     * @access public          
     */  
          
    function RegDeleteKeyEx() 
    {
        return ERROR_UNSUPPORTED;
    }

    /**
     * Deletes a subkey and its values from the specified platform-specific view of the registry as a transacted operation.
     * 	   
     * Reserved for future use!
     *
     * @todo Implement RegDeleteKeyTransacted()
     * @return integer Error code, currently returns ERROR_UNSUPPORTED         
     * @access public          
     */  
          	
    function RegDeleteKeyTransacted() 
    {
        return ERROR_UNSUPPORTED;
    }
  
    /**
     * Removes the specified value from the specified registry key and subkey.
     * 	   
     * Reserved for future use!
     *
     * @todo Implement RegDeleteKeyValue()
     * @return integer Error code, currently returns ERROR_UNSUPPORTED         
     * @access public          
     */  
        
    function RegDeleteKeyValue() 
    {
        return ERROR_UNSUPPORTED;
    }
  
    /**
     * Deletes the subkeys and values of the specified key recursively.
     * 	   
     * Reserved for future use!
     *  
     * @todo Implement RegDeleteTree()
     * @return integer Error code, currently returns ERROR_UNSUPPORTED         
     * @access public          
     */  
        
    function RegDeleteTree() 
    {
        return ERROR_UNSUPPORTED;
    }
  
    /**
     * Removes a named value from the specified registry key.
     * 	   
     * Reserved for future use!
     *
     * @todo Implement RegDeleteValue()
     * @return integer Error code, currently returns ERROR_UNSUPPORTED         
     * @access public          
     */  
        
    function RegDeleteValue() 
    {
        return ERROR_UNSUPPORTED;
    }
  
    /**
     * Disables handle caching for the predefined registry handle for HKEY_CURRENT_USER for the current process.
     * 	   
     * Reserved for future use!
     *
     * @todo Implement RegDisablePredefinedCache()
     * @return integer Error code, currently returns ERROR_UNSUPPORTED         
     * @access public          
     */  
        
    function RegDisablePredefinedCache() 
    {
        return ERROR_UNSUPPORTED;
    }
  

    /**
     * Disables handle caching for all predefined registry handles for the current process.
     *    
     * Reserved for future use!
     *
     * @todo Implement RegDisablePredefinedCacheEx
     * @return integer Error code, currently returns ERROR_UNSUPPORTED         
     * @access public          
     */  
          
    function RegDisablePredefinedCacheEx() 
    {
        return ERROR_UNSUPPORTED;
    }
  
    /**
     * Disables registry reflection for the specified key.
     * 	   
     * Reserved for future use!
     *
     * @todo Implement RegDisableReflectionKey()
     * @return integer Error code, currently returns ERROR_UNSUPPORTED         
     * @access public          
     */  
        
    function RegDisableReflectionKey() 
    {
        return ERROR_UNSUPPORTED;  
    }
  
    /**
     * Enables registry reflection for the specified disabled key.
     * 	   
     * Reserved for future use!
     *
     * @todo Implement RegEnableReflectionKey()
     * @return integer Error code, currently returns ERROR_UNSUPPORTED         
     * @access public          
     */  
        
    function RegEnableReflectionKey() 
    {
        return ERROR_UNSUPPORTED;
    }
  
    /**
     * Enumerates the subkeys of the specified open registry key.
     *
     * @param object $hKey Registry Key
     * @param integer $dwIndex Key index
     * @param string $lpName 
     * @param string $lpClass The user-defined class type of this key. This parameter may be ignored. This parameter can be NULL
     * @param mixed $lpReserved Reserved
     * @param string $lpClass Not implemented
     * @param string $lpcClass Not implemented
     * @param mixed $lpftLastWriteTime Not implemented
     *             
     * @return integer Error code, currently returns ERROR_UNSUPPORTED         
     * @access public          
     */  
        
    public static function RegEnumKeyEx($hKey,$dwIndex,&$lpName,&$lpcName,$lpReserved,&$lpClass,&$lpcClass,&$lpftLastWriteTime) 
    {
   
        if (!method_exists($hKey,'listKeys')) {
            return VWP::raiseWarning('Invalid registry key provided!','Registry:RegOpenKeyEx',ERROR_FAILED,false);
        } 
        $keys = $hKey->listKeys();

        if (count($keys) > $dwIndex) {
            if (strlen($keys[$dwIndex]) > $lpcName) {
                $lpName = null;
                $lpcName = 0;
                $lpdwDisposition = ERROR_FAILED;
                return VWP::raiseError("Insufficient buffer space to hold registry key name.","Registry::RegEnumKeyEx",$lpdwDisposition,false);     
            }
            $lpName = $keys[$dwIndex];
            $lpcName = strlen($lpName);
            return ERROR_SUCCESS;    
        }

        $lpName = null;
        $lpcName = 0;          
        $lpdwDisposition = ERROR_NO_MORE_ITEMS;
        return VWP::raiseWarning("No more registry items.","Registry::RegEnumKeyEx",$lpdwDisposition,false);      
    }
  
    /**
     * Enumerates the values for the specified open registry key.
     * 	   
     * @param object $hKey Registry Key
     * @param integer $dwIndex Value index   
     * @param string $lpValueName Value name
     * @param integer $lpcchValueName length of value name
     * @param integer $lpReserved Reserved must be zero
     * @param integer $lpType Value Type
     * @param mixed $lpData Value data
     * @param integer $lpcbData Value data length 
     * @return Error code or ERROR_SUCCESS on success
     * @access public         
     */     
  
    public static function RegEnumValue(  
             $hKey,
             $dwIndex,
             &$lpValueName,
             &$lpcchValueName,
             $lpReserved,
             &$lpType,
             &$lpData,
             &$lpcbData) {
  
        $info = $hKey->getValue($dwIndex);
   
        if ($info === false) {    
            $lpdwDisposition = ERROR_NO_MORE_ITEMS;
            return VWP::raiseError("No more registry items.","Registry::RegEnumValue",$lpdwDisposition,false);
        }
   
        if ($lpcchValueName > 0) {
            if (strlen($info["name"]) > $lpcchValueName) {
                $lpdwDisposition = ERROR_FAILED;
                return VWP::raiseWarning("Insufficient buffer space to hold registry value name.","Registry::RegEnumValue",$lpdwDisposition,false);
            }   
        }
   
        $lpValueName = $info["name"];
        $lpcchValueName = strlen($info["name"]);
  
        $types = explode('&',REG_TYPES);
        $lpType = 0;
        for($ptr = 0; $ptr < count($types); $ptr++) {
            if ($info["type"] == $types[$ptr]) {
                $lpType = $ptr;
            }
        } 
      
        $lpData = substr($info["data"],0,$lpcbData);   
      
        if (strlen($info["data"]) > $lpcbData) {    
            $lpdwDisposition = ERROR_MORE_DATA;
            return VWP::raiseWarning("Insufficient buffer space to read all data.","Registry::RegEnumValue",$lpdwDisposition,false);
        }
   
        $lpcbData = strlen($lpData);
        return ERROR_SUCCESS;
    }

    /**
     * Writes all attributes of the specified open registry key into the registry.
     *    
     * Reserved for future use!
     *        
     * @todo Implement RegFlushKey()        
     * @return integer Error code, currently returns ERROR_UNSUPPORTED         
     * @access public          
     */
          
    function RegFlushKey() 
    {
        return ERROR_UNSUPPORTED;
    }
  
    /**
     * Retrieves a copy of the security descriptor protecting the specified open registry key.
     *    
     * Reserved for future use!
     *
     * @todo Implement RegGetKeySecurity()
     * @return integer Error code, currently returns ERROR_UNSUPPORTED         
     * @access public          
     */
        
    function RegGetKeySecurity() 
    {
        return ERROR_UNSUPPORTED;
    }
  
    /**
     * Retrieves the type and data for the specified registry value.
     *    
     * Reserved for future use!
     *
     * @todo Implement RegGetValue()
     * @return integer Error code, currently returns ERROR_UNSUPPORTED         
     * @access public          
     */
          
    function RegGetValue() 
    {
        return ERROR_UNSUPPORTED;
    }
  
    /**
     * Creates a subkey under HKEY_USERS or HKEY_LOCAL_MACHINE and stores registration information from a specified file into that subkey.
     * 
     * Reserved for future use!
     * 
     * @todo Implement RegLoadKey()             
     * @return integer Error code, currently returns ERROR_UNSUPPORTED         
     * @access public          
     */
          
    function RegLoadKey() 
    {
        return ERROR_UNSUPPORTED;
    }
  
    /**
     * Loads the specified string from the specified key and subkey.
     * 	   
     * Reserved for future use!
     * 
     * @todo Implement RegLoadMUIString()
     * @return integer Error code, currently returns ERROR_UNSUPPORTED         
     * @access public          
     */
        
    function RegLoadMUIString() 
    {
        return ERROR_UNSUPPORTED;
    }

    /**
     * Notifies the caller about changes to the attributes or contents of a specified registry key.
     *    
     * Reserved for future use!
     *
     * @todo Implement RegNotifyChangeKeyValue()
     * @return integer Error code, currently returns ERROR_UNSUPPORTED         
     * @access public          
     */
          
    function RegNotifyChangeKeyValue() 
    {
        return ERROR_UNSUPPORTED;
    }

    /**
     * Retrieves a handle to the HKEY_CURRENT_USER key for the user the current thread is impersonating.
     *    
     * Reserved for future use!
     *
     * @todo Implement RegOpenCurrentUser()
     * @return integer Error code, currently returns ERROR_UNSUPPORTED         
     * @access public          
     */
          
    function RegOpenCurrentUser() 
    {
        return ERROR_UNSUPPORTED;
    }
  
    /**
     * Opens the specified registry key.
     * 
     * @param object $hKey - A handle to an open registry key.
     * @param string $lpSubKey - The name of the registry subkey to be opened,
     * @param integer $ulOptions - This parameter is reserved and must be zero.
     * @param mixed $samDesired - A mask that specifies the desired access rights to the key
     * @param phkResult - A pointer to a variable that receives a handle to the opened key.
     * @return integer Error code on failure or ERROR_SUCCESS on success
     * @access public            
     */
      	
    public static function RegOpenKeyEx($hKey,
                        $lpSubKey,
                        $ulOptions,
                        $samDesired,
                        &$phkResult) 
    {
        if (!method_exists($hKey,'getKey')) {
            return VWP::raiseWarning('Invalid registry key provided!','Registry:RegOpenKeyEx',ERROR_FAILED,false);
        }                        	
        $phkResult = $hKey->getKey($lpSubKey);
   
        if ($phkResult === false) {  
            $lpdwDisposition = ERROR_FAILED;
            return VWP::raiseWarning("Unable to open registry key ($lpSubKey).","Registry::RegOpenKeyEx",$lpdwDisposition,false);     
        }
        return ERROR_SUCCESS;                        
    }

    /**
     * Opens the specified registry key and associates it with a transaction.
     * 	   
     * Reserved for future use!
     *
     * @todo Implement RegOpenKeyTransacted()
     * @return integer Error code, currently returns ERROR_UNSUPPORTED         
     * @access public          
     */
          
    function RegOpenKeyTransacted() 
    {
        return ERROR_UNSUPPORTED;
    }

    /**
     * Retrieves a handle to the HKEY_CLASSES_ROOT key for the specified user.
     *    
     * Reserved for future use!
     * 
     * @todo Implement RegOpenUserClassesRoot()
     * @return integer Error code, currently returns ERROR_UNSUPPORTED         
     * @access public          
     */
          
    function RegOpenUserClassesRoot() 
    {
        return ERROR_UNSUPPORTED;
    }
  
    /**
     * Maps a predefined registry key to a specified registry key.
     *    
     * Reserved for future use!
     * 
     * @todo Implement RegOverridePredefKey()
     * @return integer Error code, currently returns ERROR_UNSUPPORTED         
     * @access public          
     */
          
    function RegOverridePredefKey() 
    {
        return ERROR_UNSUPPORTED;
    }

    /**
     * Retrieves information about the specified registry key.
     *    
     * Reserved for future use!
     *
     * @todo Implement RegQueryInfoKey()
     * @return integer Error code, currently returns ERROR_UNSUPPORTED         
     * @access public          
     */
          
    function RegQueryInfoKey() 
    {
        return ERROR_UNSUPPORTED;
    }
  
    /**
     * Retrieves the type and data for a list of value names associated with an open registry key.
     * 	   
     * Reserved for future use!
     * 
     * @todo Implement RegQueryMultipleValues()
     * @return integer Error code, currently returns ERROR_UNSUPPORTED         
     * @access public          
     */
        
    function RegQueryMultipleValues() 
    {
        return ERROR_UNSUPPORTED;
    }

    /**
     * Determines whether reflection has been disabled or enabled for the specified key.
     * 	   
     * Reserved for future use!
     *
     * @todo Implement RegQueryReflectionKey()
     * @return integer Error code, currently returns ERROR_UNSUPPORTED         
     * @access public          
     */
          
    function RegQueryReflectionKey() 
    {
        return ERROR_UNSUPPORTED;  
    }
  
    /**
     * Retrieves the type and data for a specified value name associated with an open registry key.
     *    
     * Reserved for future use!
     *   
     * @todo Impelement RegQueryValueEx()       
     * @return integer Error code, currently returns ERROR_UNSUPPORTED         
     * @access public          
     */
        
    function RegQueryValueEx() 
    {
        return ERROR_UNSUPPORTED;
    }

    /**
     * Replaces the file backing a registry key and all its subkeys with another file.
     *    
     * Reserved for future use!
     * 
     * @todo Implement RegReplaceKey()
     * @return integer Error code, currently returns ERROR_UNSUPPORTED         
     * @access public          
     */
          
    function RegReplaceKey() 
    {
        return ERROR_UNSUPPORTED;
    }

    /**
     * Reads the registry information in a specified file and copies it over the specified key.
     * 	   
     * Reserved for future use!
     *
     * @todo Implement RegRestoreKey()
     * @return integer Error code, currently returns ERROR_UNSUPPORTED         
     * @access public          
     */
          
    function RegRestoreKey() 
    {
        return ERROR_UNSUPPORTED;
    }

    /**
     * Saves the specified key and all of its subkeys and values to a new file.
     *    
     * Reserved for future use!
     * 
     * @todo Implement RegSaveKey()
     * @return integer Error code, currently returns ERROR_UNSUPPORTED         
     * @access public          
     */
          
    function RegSaveKey() 
    {
        return ERROR_UNSUPPORTED;
    }
  
    /**
     * Saves the specified key and all of its subkeys and values to a new file. You can specify the format for the saved key or hive.
     *    
     * Reserved for future use!
     *
     * @todo Implement RegSaveKeyEx()        
     * @return integer Error code, currently returns ERROR_UNSUPPORTED         
     * @access public          
     */
        
    function RegSaveKeyEx() 
    {
        return ERROR_UNSUPPORTED;
    }
  
    /**
     * Sets the data for the specified value in the specified registry key and subkey.
     *    
     * Reserved for future use!
     *
     * @todo Implement RegSetKeyValue()
     * @return integer Error code, currently returns ERROR_UNSUPPORTED         
     * @access public          
     */
        
    function RegSetKeyValue() 
    {
        return ERROR_UNSUPPORTED;
    }

    /**
     * Sets the security of an open registry key.
     *    
     * Reserved for future use!
     *
     * @todo Implement RegSetKeySecurity()
     * @return integer Error code, currently returns ERROR_UNSUPPORTED         
     * @access public          
     */
          
    function RegSetKeySecurity() 
    {
        return ERROR_UNSUPPORTED;
    }

    /**
     * Sets the data and type of a specified value under a registry key.
     *    
     * @param object $hKey Registry Key
     * @param string $lpValueName Value Name
     * @param integer $Reserved Reserved must be zero
     * @param integer $dwType Value data type
     * @param mixed $lpData Value data
     * @param integer $cbData Length of data
     * @return integer Error code on failure or ERROR_SUCCESS on success   
     */
          
    public static function RegSetValueEx($hKey,
                         $lpValueName,
                         $Reserved,
                         $dwType,
                         $lpData,
                         $cbData) {
        $types = explode('&',REG_TYPES);
        $maxtype = count($types) - 1;
        if (($dwType > $maxtype) || ($dwType < 0)) { 
            $dtype = 'REG_NONE';
        } else {
            $dtype = $types[$dwType];
        }      
        if ($hKey->setValue($lpValueName,$lpData,$dtype)) {
            return ERROR_SUCCESS;
        }
   
        $lpdwDisposition = ERROR_FAILED;
        return VWP::raiseError("Unable to set registry key value ($lpValueName).","Registry::RegSetValueEx",$lpdwDisposition,false);   
    }
  
    /**
     * Unloads the specified registry key and its subkeys from the registry.
     * 	   
     * Reserved for future use!
     * 
     * @todo Implement RegUnLoadKey()        
     * @return integer Error code, currently returns ERROR_UNSUPPORTED         
     * @access public          
     */
        
    function RegUnLoadKey() {
        return ERROR_UNSUPPORTED;
    } 
  
    /**
     * Initialize registry defines
     * 
     * Initializes defines such as Value data types
     * note: This function should be called before any registry keys are accessed
     * 
     * @access private         
     */           
  
    public static function init_defines() 
    {
   
        $reg_types = array(
            'REG_NONE',   // - No defined value type.
            'REG_BINARY', // - Binary data in any form.
            'REG_DWORD', // - A 32-bit number.
            'REG_DWORD_LITTLE_ENDIAN', // - A 32-bit number in little-endian format.  Windows is designed to run on little-endian computer architectures. Therefore, this value is defined as REG_DWORD in the Windows header files.
            'REG_DWORD_BIG_ENDIAN', // -A 32-bit number in big-endian format.  Some UNIX systems support big-endian architectures.
            'REG_EXPAND_SZ', // - A null-terminated string that contains unexpanded references to environment variables (for example, "%PATH%"). It will be a Unicode or ANSI string depending on whether you use the Unicode or ANSI functions. To expand the environment variable references, use the ExpandEnvironmentStrings function.
            'REG_LINK', // - A null-terminated Unicode string that contains the target path of a symbolic link that was created by calling the RegCreateKeyEx function with REG_OPTION_CREATE_LINK.
            'REG_MULTI_SZ',// A sequence of null-terminated strings, terminated by an empty string (\0).
            'REG_QWORD', // - A 64-bit number.
            'REG_QWORD_LITTLE_ENDIAN', // - A 64-bit number in little-endian format. Windows is designed to run on little-endian computer architectures. Therefore, this value is defined as REG_QWORD in the Windows header files.
            'REG_SZ' // -A null-terminated string. This will be either a Unicode or an ANSI string, depending on whether you use the Unicode or ANSI functions.
        );
   
        define('REG_TYPES',implode('&',$reg_types));    
  
        for($ptr = 0; $ptr < count($reg_types); $ptr++) {
            define($reg_types[$ptr],$ptr);
        }
   
        $vreg_local_machine = VWP::getVarPath('vwp').DS.'registry'.DS.'hkey_local_machine.php';
        $vfolder =& VFilesystem::local()->folder();
        $vfolder->create(dirname($vreg_local_machine));   
        define('VREG_LOCAL_MACHINE',$vreg_local_machine);  
    }
    
    // end class Registry 
} 


// Initialize Registry
Registry::init_defines();
 