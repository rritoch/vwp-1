<?php


class User_Widget_Reset extends VWidget 
{
	
	function save_password($tpl = null) 
	{
		$shellob =& v()->shell();
		$user =& $this->getModel('user');
		$code = $shellob->getVar('aid','');
		$username = $shellob->getVar('userid','');
		
		$expired = false;
		
		if (empty($code) || empty($username)) {
			$expired = true;
			VWP::raiseWarning('Missing authorization tokens!',__CLASS__);
		}
		
		if (!$expired) {
			$u =& VUser::getUserByUsername($username);
			if (VWP::isWarning($u)) {
				$u->ethrow();
				$expired = true;
			} else {
				
				$match_code = $u->getMeta('auth_code','','user:reset');
				if (empty($match_code)) {
					$expired = true;
					VWP::raiseWarning('No pending reset request!',__CLASS__);
				} else {
					if ($code !== $match_code) {						
						VWP::raiseWarning('Invalid authorization code!',__CLASS__);
						$expired = true;
					}
				}
			}
		}
		
		if ($expired) {
             $this->setLayout('expired');	
		} else {
		
	    	$reset = $shellob->getVar('reset',array());
		
    		if ($reset['new_password'] == $reset['confirm_password']) {
			    $result = $user->resetPassword($username,$reset['new_password']);
			    if (VWP::isWarning($result)) {
                    $result->ethrow();			    	
			    } else {
			    	$this->setLayout('complete');
			    	VWP::addNotice('Password changed!');
			    }
		    } else {
			    VWP::raiseWarning('Passwords do not match!',__CLASS__);			    
		    } 
		}
		
		$screen = $shellob->getScreen();
		$this->assignRef('screen',$screen);
		
		parent::display($tpl);
	}
	
	function display($tpl = null) 
	{
		$shellob =& v()->shell();
		$user =& $this->getModel('user');
		$code = $shellob->getVar('aid','');
		$username = $shellob->getVar('userid','');
		
		$expired = false;
		
		if (empty($code) || empty($username)) {
			$expired = true;
			VWP::raiseWarning('Missing authorization tokens!',__CLASS__);
		}
		
		if (!$expired) {
			$u =& VUser::getUserByUsername($username);
			if (VWP::isWarning($u)) {
				$u->ethrow();
				$expired = true;
			} else {
				
				$match_code = $u->getMeta('auth_code','','user:reset');
				if (empty($match_code)) {
					$expired = true;
					VWP::raiseWarning('No pending reset request!',__CLASS__);
				} else {
					if ($code !== $match_code) {						
						VWP::raiseWarning('Invalid authorization code!',__CLASS__);
						$expired = true;
					}
				}
			}
		}
		
		if ($expired) {
			$this->setLayout('expired');
		}
		
		$screen = $shellob->getScreen();
		$this->assignRef('screen',$screen);		
		
		parent::display($tpl);
	}
}