<?php

/**
 * New User Registration Widget 
 *  
 * @package    VWP.User
 * @subpackage Widgets
 * @author Ralph Ritoch <rritoch@gmail.com> 
 * @copyright (c) Ralph Ritoch - All rights reserved
 * @link http://www.vnetpublishing.com 
 */

VWP::RequireLibrary("vwp.ui.widget");

/**
 * New User Widget 
 *  
 * @package    VWP.User
 * @subpackage Widgets
 * @author Ralph Ritoch <rritoch@gmail.com> 
 * @copyright (c) Ralph Ritoch - All rights reserved
 * @link http://www.vnetpublishing.com 
 */

class User_Widget_New extends VWidget 
{

    /**
     * Register new user
     * 
     * @param mixed $tpl Optional  
     * @access public    
     */
       
    function register($tpl = null) 
    {
 	
        $shellob =& VWP::getShell();
  
        $user = $this->getModel("user");
  
        $credentials = $shellob->getVar("register");
        $this->assignRef('credentials',$credentials);
  
        if ($credentials["password"] == $credentials["confirm_password"]) {

           // Process Registration Request
           
           $userinfo = $credentials;
           unset($userinfo["confirm_password"]);

           
           $username = $userinfo['username'];           
           $this->assignRef('username',$username);
           
           $settings = $user->getConfig();
                      
           $this->assignRef('settings',$settings);

           $confirmation_code = $user->generateConfirmationCode($userinfo);
           
           $userinfo['email_verification'] = $confirmation_code;
           
           $this->assignRef('confirmation_code',$confirmation_code);
           
           $route = VRoute::getInstance();
           
           $confirmation_link = 'index.php?app=user&widget=confirm&userid='.urlencode($username).'&code=' . urlencode($confirmation_code);
           $confirmation_link = $route->encode($confirmation_link);           
           $this->assignRef('confirmation_link',$confirmation_link);
           
           $confirmation_link_short = 'index.php?app=user&widget=confirm&userid='.urlencode($username);
           $confirmation_link_short = $route->encode($confirmation_link_short);           
           $this->assignRef('confirmation_link_short',$confirmation_link_short);
           
           $site_name = $user->getSiteName();
           $this->assignRef('site_name',$site_name);
           
           $site_url = 'index.php';
           $site_url = $route->encode($site_url);
           
           $this->assignRef('site_url',$site_url);
           
           // Store original output format
           
           $save_format = $this->getFormat();
   
           // Setup Email Templates
                      
           $from = array($settings['email_from_name'],$settings['email_address']);
           $to = array($credentials['name'],$credentials['email']);   
           $this->setFormat('html');
   
           // Welcome Email
   
           $this->email = new stdClass;   
           $this->email->subject = 'Welcome!';  
           $this->email->from = $from;
           $this->email->to = $to; 
           $this->setLayout('welcome_text');
           ob_start();
           parent::display($tpl);
           $this->email->text = ob_get_contents();
           ob_end_clean();   
           $this->setLayout('welcome_html');
           ob_start();
           parent::display($tpl);
           $this->email->html = ob_get_contents();
           ob_end_clean();   
           $email_welcome = $this->email;
   
           // Confirm Email
      
           $this->email = new stdClass;   
           $this->email->subject = 'Please confirm your email address';
           $this->email->from = $from;
           $this->email->to = $to;              
           $this->setLayout('confirm_text');
           ob_start();
           parent::display($tpl);
           $this->email->text = ob_get_contents();
           ob_end_clean();   
           $this->setLayout('confirm_html');
           ob_start();
           parent::display($tpl);
           $this->email->html = ob_get_contents();
           ob_end_clean();   
           $email_confirm = $this->email;

           // Restore original output format
           
           $this->setFormat($save_format);
           
           // Register
           
           $result = $user->register($userinfo,$email_welcome,$email_confirm);
           if (VWP::isWarning($result)) {
           	   // Failed
               $result->ethrow();
               $register = $credentials;
               $this->setLayout('default');
           } else {
           	   // Success
               VWP::addNotice("Account created!");
               $register = array();
               $this->setLayout('success');
           }  
       } else {
       	   $this->setLayout('default');
       	   // Password mismatch error
       	   
           unset($credentials["password"]);
           unset($credentials["confirm_password"]);
           $register = $credentials;
           VWP::raiseWarning("Passwords do not match, please confirm your password!");
       }

       // Display the widget
       
       $user->cleanRegisterVars($register);
       $this->assignRef('register',$register);

       $screen = $shellob->getScreen();
       $this->assignRef('screen',$screen);
    
       parent::display();
 }

 /**
  * Display new user registration widget
  * 
  * @param mixed $tpl Optional  
  * @access public    
  */

 function display($tpl = null) {
  $shellob =& VWP::getShell();
   
  $register = array();
  $user = $this->getModel("user");
  $user->cleanRegisterVars($register);
  $this->assignRef('register',$register);
  
  $screen = $shellob->getScreen();
  $this->assignRef('screen',$screen);
  
  parent::display();
 }

} // end class