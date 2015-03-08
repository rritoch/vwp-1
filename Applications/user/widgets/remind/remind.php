<?php 



class User_Widget_Remind extends VWidget {
	
		
	function recover_username($tpl = null) 
	{
		$shellob =& v()->shell();
		$user =& $this->getModel('user');
		$email = $shellob->getVar('email',null);		
		$user_list = $user->findUsersByEmail($email);
		
		$settings = $user->getConfig();		
		if (VWP::isWarning($settings)) {
			$settings->ethrow();
			return $this->display($tpl);
		}			
		
		
		$cfg = new VConfig;
		
		$site_name = $cfg->site_name;
		$this->assignRef('site_name',$site_name); 

		$from = array($settings['email_from_name'],$settings['email_address']);
		
		$route =& VRoute::getInstance();
		
	
		
		if (VWP::isWarning($user_list)) {
			$user_list->ethrow();
		} else {					
		    if (count($user_list) < 1) {
			    VWP::raiseWarning('There are no users with the provided email address!',__CLASS__);
		    } else {
		    	                     						    	
		    	$user_accounts = array();		    			    			    	
		    	foreach($user_list as $username) {
		    	
		    		$u = VUser::getUserByUsername($username);

		    		if (!VWP::isWarning($u)) {
		    	    	$code = $user->generateConfirmationCode($u->getProperties());
		        		$u->setMeta('auth_code',$code,'user:reset');
		    	        $result = $u->save();
	    		        if (VWP::isWarning($result)) {
    				        $result->ethrow();
			            } else {		    				    				    		
		    		        $acct = $u->getProperties();		    		        		    	
			                $acct['change_password_url'] = 'index.php?app=user&widget=reset&userid='.urlencode($u->username).'&aid=' . urlencode($code);
			                $acct['change_password_url'] = $route->encode($acct['change_password_url']);
			                $user_accounts[] = $acct;		    				    		
		    	        }
		    		}		    	
		    	}
		    	$this->assignRef('user_accounts',$user_accounts);

		    	if (count($user_accounts) > 0) {
		    	    // Store original output format	
				    $save_format = $this->getFormat(); 
				
				
                    // Setup Email Templates
           			    
	    		    $username = $user_accounts[0]['username'];
	    		    $this->assignRef('username',$username);
	    		    
	    		    $this->assignRef('name',$name);
			        $name = $user_accounts[0]['name'];			    
			        			        			    			    
			        $this->email = new stdClass;
				    $this->email->from = $from;
				    $this->email->to = array($name,$email);
                    $this->setFormat('html');
                    $this->email->subject = 'Account recovery';  
                    $this->setLayout('account_info_text');
                    ob_start();
                    parent::display($tpl);
                    $this->email->text = ob_get_contents();
                    ob_end_clean();   
                    $this->setLayout('account_info_html');
                    ob_start();
                    parent::display($tpl);
                    $this->email->html = ob_get_contents();
                    ob_end_clean();   
                
                    $this->setLayout('default');
                    $this->setFormat($save_format);
                
	    	        $result = $user->sendReminder($this->email);
		            if (VWP::isWarning($result)) {
		        	    $result->ethrow();
		            } else {
    		        	VWP::addNotice('Account information sent!');
		            }		    	
		        } else {
                    VWP::raiseWarning('There are no users with the provided email address!',__CLASS__);
		        }
		    }
		}
		return $this->display($tpl);
	}
	
	function recover_password($tpl = null) 
	{
		$shellob =& v()->shell();
		$route =& VRoute::getInstance();									
		$user =& $this->getModel('user');
		$username = $shellob->getVar('username');
		$u =& VUser::getUserByUsername($username);

		$settings = $user->getConfig();		
		if (VWP::isWarning($settings)) {
			$settings->ethrow();
			return $this->display($tpl);
		}
		
		$cfg = new VConfig;
		
		$site_name = $cfg->site_name;
		$this->assignRef('site_name',$site_name); 

		$from = array($settings['email_from_name'],$settings['email_address']);
						
		if (VWP::isWarning($u)) {
			$u->ethrow();
		} else {
			$code = $user->generateConfirmationCode($u->getProperties());
			$u->setMeta('auth_code',$code,'user:reset');
			$result = $u->save();
			if (VWP::isWarning($result)) {
				$result->ethrow();
			} else {
				
			    // Store original output format	
				$save_format = $this->getFormat();

				// Setup Email Templates
				
			    $username = $u->username;
			    $this->assignRef('username',$username);
			    
			    $name = $u->name;					
				$this->assignRef('name',$name);
				
			    $change_password_url = 'index.php?app=user&widget=reset&userid='.urlencode($username).'&aid=' . urlencode($code);
			    $change_password_url = $route->encode($change_password_url);
			    $this->assignRef('change_password_url',$change_password_url);				
			    
			    $this->email = new stdClass;
				$this->email->from = $from;
				$this->email->to = array($u->name,$u->email);
                $this->setFormat('html');
                $this->email->subject = 'Password recovery';  
                $this->setLayout('password_reset_text');
                ob_start();
                parent::display($tpl);
                $this->email->text = ob_get_contents();
                ob_end_clean();   
                $this->setLayout('password_reset_html');
                ob_start();
                parent::display($tpl);
                $this->email->html = ob_get_contents();
                ob_end_clean();   
                
                $this->setLayout('default');
                $this->setFormat($save_format);
                
		        $result = $user->sendReminder($this->email);
		        if (VWP::isWarning($result)) {
		        	$result->ethrow();
		        } else {
		        	VWP::addNotice('Account information sent!');
		        }
			}
		}
		return $this->display($tpl);
	}
	
	function display($tpl = null) 
	{	
		$shellob =& v()->shell();
		
		$username = $shellob->getVar('username','');
		$this->assignRef('username',$username);
		
		$email = $shellob->getVar('email','');
		$this->assignRef('email',$email);
		
		$screen = $shellob->getScreen();
		$this->assignRef('screen',$screen);
			
		parent::display($tpl);
	}
	
}
