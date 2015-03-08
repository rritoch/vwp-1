<?php

VWP::RequireLibrary('vwp.uri');

class Search_Widget_Box extends VWidget
{

	function display($tpl = null) {

		$route =& VRoute::getInstance();
		$search_url = 'index.php?app=search';
		
		$search_url = $route->encode($search_url);
				
		$parts = explode('?',$search_url);
		
		if (count($parts) > 1) {
			$search_url = $parts[0];
			$extra = VURI::parseQuery($parts[1]);
		} else {
			$extra = array();
		}
				
		$this->assignRef('search_url',$search_url);
		$this->assignRef('extra',$extra);
		parent::display($tpl);
	}
}