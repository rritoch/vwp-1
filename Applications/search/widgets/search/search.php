<?php

VWP::RequireLibrary('vwp.uri');

class Search_Widget_Search extends VWidget
{

	function display($tpl = null) 
	{
		$shellob =& v()->shell();
		
		$query = $shellob->getVar('q',null);
		
		$results_per_page = $shellob->getVar('l',20);

		if (empty($results_per_page)) {
			$results_per_page = 20;
		}
		
		if ($results_per_page < 1) {
			$results_per_page = 1;
		}
		
		if ($results_per_page > 100) {
			$results_per_page = 100;
		}
				
		$this->assignRef('results_per_page',$results_per_page);
				
		$processed = $query !== null;
		
		if ($processed) {
			$search = $this->getModel('search');
			$options = array();
			
			$offset = $shellob->getVar('o',0);
			
			if (empty($offset) || $offset < 1) {
				$offset = 0;
			}
			
			
			$options['offset'] = $offset;
			$options['results_per_page'] = $results_per_page; 
			
			$r = $search->query($query,$options);
			
			$results = $r['results'];
			$total = $r['total'];
			
			if ($r['count'] > $results_per_page) {
				
				$route =& VRoute::getInstance();
				
			    $opt = array();			
			    $opt['widget'] = 'search';
			    $opt['app'] = 'search';
			    $opt['q'] = $query;
			    $opt['l'] = $results_per_page;
			    
			    $base = 'index.php?' . VURI::createQuery($opt);
			    			    
			    
			    // Previous
			    
			    if ($offset > 0) {
			    	$o = $offset - $results_per_page;
			    	if ($o > 0) {
			    	    $url = $base.'&o='.$o;
			    	} else {
			    		$url = $base;
			    	}
			    	
			    	$previous = array(
			    	    "title"=>"previous",
			    	    "url"=> $route->encode($url),
			    	);
			    } else {
			    	$previous = null;
			    }
			    
			    $curpage = floor($offset / $results_per_page) + 1;
			    
			    $paging = array();
			    
			    // add current
			    
			    $paging[] = array(
			        "title"=> $curpage,
			        "url"=> null,   
			    );
			    
			    // add before
			    
			    $ptr = $curpage - 1;
			    
			    while($ptr > 0 && count($paging) < 6) {
			    	
			    	$o = ($ptr - 1) * $results_per_page;
			    	 
			    	if ($o > 0) {
			    	    $url = $base.'&o='.$o;	
			    	} else {
			    		$url = $base;
			    	} 
			    	    
			    	$n = array(
			    	    "title"=>$ptr,
			    	    "url" => $route->encode($url)
			    	 );
			    	array_unshift($paging,$n);
			    	$ptr--;
			    }
			    
			    // add after
			    
			    $ptr = $curpage + 1;
			    
			    $max_p = ($r['count'] / $results_per_page);
			    
			    if ($r['count'] % $results_per_page > 0) {
			    	$max_p++;
			    }
			      
			    while((count($paging) < 15) && $ptr <= $max_p) {

			    	$o = ($ptr - 1) * $results_per_page;
			    	 			    	
			    	$url = $base.'&o='.$o;	
			    	$n = array(
			    	    "title"=>$ptr,
			    	    "url" => $route->encode($url)
			    	 );

			    	array_push($paging,$n);
			    	$ptr++;			    	 			    	
			    }
			    			    			    
			    // Next

				if ($offset + $results_per_page < $r['count']) {
			    	$o = $offset + $results_per_page;			    	
			    	$url = $base.'&o='.$o;			    	
			    	$next = array(
			    	    "title"=>"next",
			    	    "url"=> $route->encode($url),
			    	);
			    } else {
			    	$next = null;
			    }

			} else {
				$paging = array();
				$previous = null;
				$next = null;
			}
			
			$this->assignRef('total',$total);
			$this->assignRef('next',$next);
			$this->assignRef('previous',$previous);
			$this->assignRef('paging',$paging);
			$this->assignRef('results',$results);
								
		} else {
			$query = '';
			$offset = 0;
		}
		
		
		$this->assignRef('offset',$offset);
		$this->assignRef('query',$query);
		$this->assignRef('processed',$processed);

		parent::display($tpl);
	}
}