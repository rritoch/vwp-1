<?php


class User_Widget_Confirm extends VWidget 
{
	
	public function resend_confirmation($tpl = null) 
	{
		$shellob =& v()->shell();
		$username = $shellob->getVar('userid','');
		$user =& $this->getModel('user');
		
		$u = VUser::getUserByUsername($username);
		if (VWP::isWarning($u)) {
			$u->ethrow();
		} else {
		    if (empty($u->email_verification)) {
		    	VWP::addNotice('Email address has already been confirmed!');
		    } else {
		    		    	
                $username = $username;                      
                $this->assignRef('username',$username);
           
                $settings = $user->getConfig();                      
                $this->assignRef('settings',$settings);

                $confirmation_code = $u->email_verification;                                      
                $this->assignRef('confirmation_code',$confirmation_code);
           
                $route = VRoute::getInstance();
           
                $confirmation_link = 'index.php?app=user&widget=confirm&userid='.urlencode($username).'&code=' . urlencode($confirmation_code);
                $confirmation_link = $route->encode($confirmation_link);           
                $this->assignRef('confirmation_link',$confirmation_link);
                      
                $site_name = $user->getSiteName();
                $this->assignRef('site_name',$site_name);
           
                $site_url = 'index.php';
                $site_url = $route->encode($site_url);
           
                $this->assignRef('site_url',$site_url);
           
                // Store original output format
           
                $save_format = $this->getFormat();
   
                // Setup Email Templates
                      
                $from = array($settings['email_from_name'],$settings['email_address']);
                $to = array($u->name,$u->email);   
                $this->setFormat('html');
   
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
                
                $result = $user->resendConfirmationEmail($email_confirm);
                if (VWP::isWarning($result)) {
                	$result->ethrow();
                } else {
                	VWP::addNotice('Confirmation email resent!');
                }
		    }			
		}
		
		$this->setLayout('default');
		
		return $this->display($tpl);
	}
	
	public function confirm($tpl = null) 
	{
		
		$shellob =& v()->shell();
		
		$user =& $this->getModel('user');
		
		$confirmation_code = $shellob->getVar('code','');		
		$this->assignRef('confirmation_code',$confirmation_code);
		
		$username = $shellob->getVar('userid','');
		$this->assignRef('username',$username);	
		
		$result = $user->confirmEmail($username,$confirmation_code);
		
		if (VWP::isWarning($result)) {
			$result->ethrow();
		} else {
			VWP::addNotice('Email address confirmed!');			
			$route =& VRoute::getInstance();			
			$url = 'index.php?app=user&widget=confirm&userid='.urlencode($username);
			$url = $route->encode($url);
			$this->setRedirect($url);
		}
		
		
		$screen = $shellob->getScreen();
		
		$this->assignRef('screen',$screen);
		
		parent::display($tpl);		
		
	}
	
	public function display($tpl = null) 
	{
		
		
		$shellob =& v()->shell();
		
		$confirmation_code = $shellob->getVar('code','');		
		$this->assignRef('confirmation_code',$confirmation_code);
		
		$username = $shellob->getVar('userid','');
		$this->assignRef('username',$username);
		
		$screen = $shellob->getScreen();
		
		$this->assignRef('screen',$screen);
		
		parent::display($tpl);
	}
	
}