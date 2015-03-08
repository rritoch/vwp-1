<?php

VWP::RequireLibrary('vwp.sys.search');

class Search_Model_Search extends VModel 
{
	
		
	function query($q,$options) 
	{
		$offset = isset($options['offset']) ? $options['offset'] : 0;
		$results_per_page = isset($options['results_per_page']) ? $options['results_per_page'] : 20; 
	    $search =& VSearch::query($q,$options,null);	    
		$total = $search->count;
	    $results = $search->getResults();	    			    
	    $count = count($results);	    	    
	    $results = array_slice($results,$offset,$results_per_page);	   	    	    
	    $ret = array(
	       'count' => $count,
	       'total' => $total,
	       'results' => $results	    
	    );	    
	    return $ret;
	}
	
	// end class Search_Model_Search
}
