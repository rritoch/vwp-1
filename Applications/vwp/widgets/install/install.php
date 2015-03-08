<?php

/**
 * VWP - Install Widget 
 *  
 * @package    VWP
 * @subpackage Widgets
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @link http://www.vnetpublishing.com VNetPublishing.Com 
 * @copyright (c) Ralph Ritoch - All rights reserved
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

// No direct access

class_exists( 'VWP' ) or die( 'Restricted access' );

/**
 * Require widget support
 */

VWP::RequireLibrary('vwp.ui.widget');

/**
 * VWP - Install Widget 
 *  
 * @package    VWP
 * @subpackage Widgets
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @link http://www.vnetpublishing.com VNetPublishing.Com 
 * @copyright (c) Ralph Ritoch - All rights reserved
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License
 */

class VWP_Widget_Install extends VWidget 
{

	/**
	 * Display install documentation
	 * 
	 * @param mixed $tpl Optional
	 * @access public
	 */
	
    function display($tpl = null) 
    {
        $shellob =& VWP::getShell();
  
        // Initialize tabs
        
        $current_widget = 'install';
        $shellob->setVar('current_widget',$current_widget);
 
        $menu = $this->getWidget("tabs");
    
        if (!VWP::isWarning($menu)) {
            $menu = $menu->build();   
        }
   
        $menu_foot = $this->getWidget("tabs");
    
        if (!VWP::isWarning($menu_foot)) {
            $menu_foot = $menu_foot->build('foot');   
        }
  
        if (VWP::isWarning($menu)) {
            $menu->ethrow();
            $menu = null;
        }

        if (VWP::isWarning($menu_foot)) {
            $menu_foot->ethrow();
            $menu_foot = null;
        }  

        $this->assignRef('menu',$menu);
        $this->assignRef('menu_foot',$menu_foot); 
 
        // Initialize form
        
        $install_processed = false;
        $this->assignRef('install_processed',$install_processed);

        // Display form
        
        $screen = $shellob->getScreen();
        $this->assignRef('screen',$screen);        
        parent::display($tpl);
    }

	/**
	 * Install module from package
	 * 
	 * @param mixed $tpl Optional
	 * @access public
	 */
        
    function install_from_package($tpl = null) 
    {
        $shellob =& VWP::getShell();
  
        // Initialize tabs
        
        $current_widget = 'install';
        $shellob->setVar('current_widget',$current_widget);
 
        $menu = $this->getWidget("tabs");
    
        if (!VWP::isWarning($menu)) {
            $menu = $menu->build();   
        }
   
        $menu_foot = $this->getWidget("tabs");
    
        if (!VWP::isWarning($menu_foot)) {
            $menu_foot = $menu_foot->build('foot');   
        }
  
        if (VWP::isWarning($menu)) {
            $menu->ethrow();
            $menu = null;
        }

        if (VWP::isWarning($menu_foot)) {
            $menu_foot->ethrow();
            $menu_foot = null;
        }  

        $this->assignRef('menu',$menu);
        $this->assignRef('menu_foot',$menu_foot); 

        // Install module
        
        $install = $this->getModel('install');

        $install_processed = true;
        $success = false;
        $complete = true;
  
        $pkg = $shellob->getVar('package',false);
        if ($pkg === false) {
            VWP::raiseError('Missing package in request!',get_class($this).":install_from_package");
        } else {
            $result = $install->doInstallPackage($pkg);
            if (VWP::isWarning($result)) {
                $result->ethrow();
            } else {
                $success = $result["success"];
                $complete = $result["complete"];
            }  
        }
  
        if ($success) {
            VWP::addNotice("Install Successful");
        }
  
        $this->assignRef('install_processed',$install_processed);
        $this->assignRef('success',$success);
        $this->assignRef('complete',$complete);
        
        // Display result
        
        $screen = $shellob->getScreen();
        $this->assignRef('screen',$screen);          
        parent::display($tpl);
    }

    
	/**
	 * Install module from upload
	 * 
	 * @param mixed $tpl Optional
	 * @access public
	 */
        
    function install_from_upload($tpl = null) 
    {
        $shellob =& VWP::getShell();
  
        // Initialize tabs
        
        $current_widget = 'install';
        $shellob->setVar('current_widget',$current_widget);
 
        $menu = $this->getWidget("tabs");
    
        if (!VWP::isWarning($menu)) {
            $menu = $menu->build();   
        }
   
        $menu_foot = $this->getWidget("tabs");
    
        if (!VWP::isWarning($menu_foot)) {
            $menu_foot = $menu_foot->build('foot');   
        }
  
        if (VWP::isWarning($menu)) {
            $menu->ethrow();
            $menu = null;
        }

        if (VWP::isWarning($menu_foot)) {
            $menu_foot->ethrow();
            $menu_foot = null;
        }  

        $this->assignRef('menu',$menu);
        $this->assignRef('menu_foot',$menu_foot); 

        // Install module
        
        $install = $this->getModel('install');

        $install_processed = true;
        $success = false;
        $complete = true;
  
        $vfile =& v()->filesystem()->file();
        $pkg = $vfile->mktemp();

        
        
        
        if (VWP::isWarning($pkg)) {
            $pkg->ethrow();
        } else {
            $vfile->delete($pkg);
            $fn = VEnv::getUploadFilename('pkg');
            $ext = $vfile->getExt($fn);
            $pkg = $vfile->stripExt($pkg) . '.'.$ext;

            $result = VEnv::getUpload('pkg',$pkg);
            if (!VWP::isWarning($result)) {
                $result = $install->doInstallPackage($pkg);
            }
            if (VWP::isWarning($result)) {
                $result->ethrow();
            } else {
                $success = $result["success"];
                $complete = $result["complete"];
            }
            $vfile->delete($pkg);  
        }
  
        if ($success) {
            VWP::addNotice("Install Successful");
        }
  
        $this->assignRef('install_processed',$install_processed);
        $this->assignRef('success',$success);
        $this->assignRef('complete',$complete);
        
        // Display result
        
        $screen = $shellob->getScreen();
        $this->assignRef('screen',$screen);          
        parent::display($tpl);
    }
    
    
	/**
	 * Install module from folder
	 * 
	 * @param mixed $tpl Optional
	 * @access public
	 */
        
    function install_from_folder($tpl = null) 
    {
        $shellob =& VWP::getShell();
  
        // Initialize tabs
        
        $current_widget = 'install';
        $shellob->setVar('current_widget',$current_widget);
 
        $menu = $this->getWidget("tabs");
    
        if (!VWP::isWarning($menu)) {
            $menu = $menu->build();   
        }
   
        $menu_foot = $this->getWidget("tabs");
    
        if (!VWP::isWarning($menu_foot)) {
            $menu_foot = $menu_foot->build('foot');   
        }
  
        if (VWP::isWarning($menu)) {
            $menu->ethrow();
            $menu = null;
        }

        if (VWP::isWarning($menu_foot)) {
            $menu_foot->ethrow();
            $menu_foot = null;
        }  

        $this->assignRef('menu',$menu);
        $this->assignRef('menu_foot',$menu_foot); 
 
        // Install module
        
        $install = $this->getModel('install');
        $install_processed = true;
        $success = false;
        $complete = true;
  
        $folder = $shellob->getVar('folder',false);
        if ($folder === false) {
            VWP::raiseError('Missing folder in request!',get_class($this).":install_from_folder");
        } else {
            $result = $install->doInstall($folder);
            if (VWP::isWarning($result)) {
                $result->ethrow();
            } else {
                $success = $result["success"];
                $complete = $result["complete"];
            }    
        }

        if ($success) {
            VWP::addNotice("Install Successful");
        }
  
        $this->assignRef('install_processed',$install_processed);  
        $this->assignRef('success',$success);
        $this->assignRef('complete',$complete);
  
        // Display result
        
        $screen = $shellob->getScreen();
        $this->assignRef('screen',$screen);         
        parent::display($tpl);
    }
    
    // end class VWP_Widget_Install 
}
