<?php

class TinyMCE_Widget_Images extends VWidget 
{
		
	function display($tpl = null) 
	{
		$url = v()->shell()->getVar('location');
		
		if (empty($url)) {
			$image_list = array();
		} else {
		    $images = $this->getModel('images');
		    		    
		    $image_list = $images->getList($url);
		
		    if (VWP::isWarning($image_list)) {
			    $image_list->ethrow();
			    $image_list = array();
		    }
		}
		$mode = v()->shell()->getVar('mode');
		
		$this->assignRef('mode',$mode);
		$this->assignRef('image_list',$image_list);
		parent::display($tpl);
	}
	
	// end class Content_Widget_Images
}

