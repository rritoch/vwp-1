<?php

/**
 * VWP Search Library
 * 
 * This library provides search support.
 *   
 * @package VWP
 * @subpackage Libraries.System
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch 2011 - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License    
 */ 

/**
 * VWP Search Library
 * 
 * This class provides search support.
 *   
 * @package VWP
 * @subpackage Libraries.System
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch 2011 - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License    
 */


class VSearch extends VObject 
{
	/**
	 * Search String 
	 * 
	 * @var string $query Query
	 * @access public
	 */
		
	protected $query;
	
	/**
	 * Search Options
	 * 
	 * @var array $options Options
	 * @access public
	 */
	
	protected $options;
	
	/**
	 * Location 
	 * 
	 * @var mixed $location Location
	 * @access public
	 */
	
	protected $location;
	
	/**
	 * Filters
	 * 
	 * @var array $filters Filters
	 * @access public
	 */
	
	protected $filters = array();
	
	/**
	 * Results
	 * 
	 * @var array $results Results
	 * @access public
	 */
	
	protected $results;
	
	/**
	 * Minimum score
	 * 
	 * @var float $min_score Score
	 * @access public
	 */
	protected $min_score = 0;

	/**
	 * Scouts
	 * 
	 * @var array $scouts Scouts
	 * @access public
	 */
	
	protected $scouts = array();
	
	/**
	 * Result Count
	 * 
	 * @var integer $count Count
	 * @access public
	 */
	
	protected $count;
	
	/**
	 * Max Title Length
	 * 
	 * @static
	 * @var integer $max_title_length Length
	 * @access protected
	 */
	
	static $max_title_length = 70;

	/**
	 * Max URL Length
	 * 
	 * @static
	 * @var integer $max_url_length Length
	 * @access protected
	 */
	
	static $max_url_length = 70;	

	/**
	 * Max Description Length
	 * 
	 * @static
	 * @var integer $max_description_length Length
	 * @access protected
	 */	

	static $max_description_length = 155;
	
	
	/**
	 * Max Results
	 * 
	 * @static
	 * @var integer $max_results Max results
	 * @access protected
	 */		
	
	static $max_results = 1000;

	/**
	 * Max New Age
	 * 
	 * @static
	 * @var integer $max_new_age Max new age
	 * @access protected
	 */	
	
	static $default_max_new_age = 1209600;
	
	/**
	 * Get Results
	 * 
	 * @return array Results
	 * @access public
	 */
	
	public function getResults() 
	{
	    $results = array();

	    foreach($this->results as $r) {
	    	if ($r !== null) {
	    		$results[] = $r;
	    	}
	    }
	    return $results;
	}
	
	/**
	 * Get Maximum Distance
	 * 
	 * Reserved for future use
	 * 
	 * @todo Implement VSearch:getMaxDistance
	 * @return mixed Distance
	 * @access public
	 */
	
	public function getMaxDistance() 
	{
		return null;
	}
	
	/**
	 * Add Filter
	 * 
	 * @param object $filter Search filter
	 * @access public
	 */
	
	public function addFilter($filter) 
	{
		$this->filters[] = $filter;
	}

	/**
	 * Calculate Distance
	 *
	 * Reserved for future use
	 * 
	 * @param mixed $l1 Location 1
	 * @param mixed $l2 Location 2
	 * @return mixed distance
	 * @access public
	 */
	
	public function calculateDistance($l1,$l2) 
	{
		return null;
	}
	
	/**
	 * Get Maximum New Age
	 * 
	 * @return integer Age in seconds
	 * @access public
	 */
	
	public function getMaxNewAge() 
	{		
		if (isset($this->options['max_new_age'])) {
			return abs($this->options['max_new_age']);
		}
		return self::$default_max_new_age;
	}
	
	/**
	 * Calculate score
	 * 
	 * @param array $result Result
	 * @return float Score
	 * @access public
	 */
	
	public function calculateScore($result) 
	{
		
        // factor relevance
		if (isset($result['relevance'])) {
			$relevance =  $result['relevance'] + (float)10.0 / (float)110.0; 
		} else {
			$relevance = (float)10.0 / (float)110;
		}		
				
		// factor distance
		if (isset($result['distance'])) {
		   $max_distance = $this->getMaxDistance();
		   
		   $tmp = abs($result['distance']);
		   $tmp = $tmp > $max_distance ? $max_distance : $tmp;
		   $base = $max_distance * (float)2.0;
		   $distance = ($base - $tmp) / (float)$base;	
		} else {
			$distance = (float)0.5;
		}
		
		// factor newness
		if (isset($result['age'])) {
			$max_new_age = $this->getMaxNewAge();
			$adjage = $result['age'] > $max_new_age ? $max_new_age : $result['age'];			
			$base = $max_new_age * (float)2.0;			
			$newness = ($base - $adjage) / (float)$base;			
		} else {
			$newness = (float)0.5;
		}

		// factor importance		
		$importance = (float)90.0;
		
		if (isset($result['friendliness'])) {
		    $importance += 	$result['friendliness'];
		}
		
		if (isset($result['authority'])) {
			$importance += $result['authority'];
		}
		
		if (isset($result['popularity'])) {
			$importance += $result['popularity'];
		}		
	
		$importance = $importance / (float)120.0;
	
		$score = $relevance * $newness * $importance * $distance;
		return $score;
	}
	
	/**
	 * Add Search Result
	 * 
	 * @param string $sysKey Reserved
	 * @param string $url URL
	 * @param string $title Title
	 * @param string $description Description	 
	 * @param string $mime_type Mime type
	 * @param float $relevance Relevance 0 to 100
	 * @param float $popularity Popularity -10 to 10
	 * @param float $authority Authority -10 to 10
	 * @param float $friendliness Friendliness -10 to 10 
	 * @param integer $age Age in seconds
	 * @param object $location Location Reserved
	 * @access public
	 */
	
	public function addResult($sysKey,$url, $title, $description, $mime_type, $relevance, $popularity, $authority, $friendliness, $age,$location) 
	{
		$result = array();
		
		// Preprocess URL
		$result['url'] = (string)$url;
        if (empty($result['url'])) {
        	return false;
        }

        $result['display_url'] = $result['url'];
		if (strlen($result['display_url']) > self::$max_url_length) {
			$elipse = " ... ";
		    $prelen = self::$max_url_length / 2;
		    $postlen = 	self::$max_url_length - $prelen;
		    $prelen -= 2;
		    $postlen -= 3;
		    		    
		    $parts = array(
		        substr($result['display_url'],0,$prelen),
		        substr($result['display_url'],strlen($result['display_url']) - $postlen)
		    );
		    
		    $result['display_url'] = implode($elipse,$parts);
		    unset($parts);
		    unset($prelen);
		    unset($postlen);
		    unset($elipse);
		}        
        
		// Preprocess Title        
		$result['title'] = $title === null ? (string)$url : (string)$title;
		$result['display_title'] = $title;
		if (strlen($result['display_title']) > self::$max_title_length) {
			$elipse = " ... ";
		    $prelen = self::$max_title_length / 2;
		    $postlen = 	self::$max_title_length - $prelen;
		    $prelen -= 2;
		    $postlen -= 3;
		    		    
		    $parts = array(
		        substr($result['display_title'],0,$prelen),
		        substr($result['display_title'],strlen($result['display_title']) - $postlen)
		    );
		    
		    $result['display_title'] = implode($elipse,$parts);
		    unset($parts);
		    unset($prelen);
		    unset($postlen);
		    unset($elipse);
		}

	    // Preprocess Description        
		$result['description'] = $description === null ? '' : (string)$description;
		$result['display_description'] = $description;
		if (strlen($result['display_description']) > self::$max_description_length) {
			$elipse = " ...";
		    $prelen = self::$max_description_length - 4;		    		   		    
		    $result['display_description'] = substr($result['display_description'],0,$prelen) . $elipse;
		    unset($prelen);
		    unset($elipse);
		}		
		
		// Preprocess Relevance		
		$result['relevance'] = (float)$relevance;
		if ($result['relevance'] < 0) {
			$result['relevance'] = (float)0;
		} elseif ($result['relevance'] > 100) {
			$result['relevance'] = (float)100;
		}

		// Preprocess Popularity
		$result['popularity'] = (float)$popularity;
		if ($result['popularity'] > 10) {
			$result['popularity'] = (float)10;
		} elseif ($result['popularity'] < -10) {
			$result['popularity'] = (float)-10;
		}
		
		// Preprocess Authority
		$result['authority'] = (float)$authority;
		if ($result['authority'] > 10) {
			$result['authority'] = (float)10;
		} elseif ($result['authority'] < -10) {
			$result['authority'] = (float)-10;
		}		

		// Preprocess Friendliness
		
		
		$result['friendliness'] = (float)$friendliness;
		if ($result['friendliness'] > 10) {
			$result['friendliness'] = (float)10;
		} elseif ($result['friendliness'] < -10) {
			$result['friendliness'] = (float)-10;
		}		
		
		
		// Preprocess age
		$result['age'] = null;
		if ($age !== null) {
			$result['age'] = (integer)$age;
			if ($result['age'] < 1) {
				$result['age'] = null;
			}
		}
		
		// Preprocess location
		$result['distance'] = self::calculateDistance($this->location,$location);

		foreach($this->filters as $filter) {
			if ($filter->block($result)) {
				return false;
			}
		}
		
		$score = $this->calculateScore($result);
		
		$result['score'] = $score;
		
		$len = count($this->results);
		
		if ($score < $this->min_score) {
			return false;
		}
		
		for($idx=0;$idx<$len;$idx++) {
			$r = $this->results[$idx];
			if ($r === null) {
				if ($idx == $len - 1) {
					$this->min_score = $score;
				}
				$this->results[$idx] = $result;
				return true;
			}

			if ($score > $r['score']) {
				array_splice($this->results, $idx, 0, array($result));
				array_pop($this->results);
				
				if (is_array($this->results[$len - 1])) {
					$this->min_score = $this->results[$len - 1]['score'];
				}				
				return true;
			}			
		}
		
		return false;
	}
	
	/**
	 * Generate a search
	 * 
	 * @param string $query Search query
	 * @param array $options Search options
	 * @param mixed $location Reserved
	 * @param array $filters Search filters
	 * @return VSearch Search
	 * @access public
	 */
	
	public static function &query($query,$options,$location,$filters = null) 
	{
		$qry = new VSearch($query,$options,$location);
		if (is_array($filters)) {
			foreach($filters as $filter) {
				$qry->addFilter($filter);
			}
		}
		return $qry;
	}

	/**
	 * Register Search Resource
	 *
	 * @param array $callback Callback method
	 * @param integer $results Number of results
	 * @access public
	 */
	
	function registerResource($callback,$results) 
	{
		if (is_array($callback) && 
		    count($callback) == 2 && 
		    is_object($callback[0]) &&
		    is_string($callback[1]) &&
		    $results > 0) {
		    $scout = array(
		        'cb' => $callback,		        
		        'count' => $results
		    );
		    $this->scouts[] = $scout;    	
		}
	}

	/**
	 * Scan
	 * 
	 * @access public
	 */
	
	function scan() 
	{
		$this->scouts = array();
		$args = array($this);
		$response = VEvent::dispatch_event("search","Scan",$args);				
	}
		
	/**
	 * Fetch
	 * 
	 * @access public
	 */
	
	
	function fetch() 
	{
		$scouts = $this->scouts;
		$this->results = array();
		
		for($idx = 0; $idx < self::$max_results;$idx++) {
			$this->results[] = null;
		}
		$count = 0;
		foreach($scouts as $scout) {
			$ob = $scout['cb'][0];
			$fn = $scout['cb'][1];
			if (method_exists($ob,$fn)) {			
			    $ob->$fn($this,self::$max_results);
			}
			$count += $scout['count'];
		}
		
		$this->count = $count;
	}

	/**
	 * Set Property
	 * 
	 * @param string $vname Property
	 * @param mixed $value Value
	 * @return mixed Value
	 * @access public
	 */
	
	function &set($vname,$value) 
	{
	    switch($vname) {
	    	case "query":
	    	case "options":
	        case "filters":
	        case "location":
	        case "results":
	        case "count":
	        case "scouts":
	    		$ret = null;
	    		break;	    	
	    	default:
	    		$ret = parent::set($vname,$value);
	    		break;
	    		
	    }
	    return $ret;	
	}	
	
	/**
	 * Get Property
	 * 
	 * @param string $vname Property
	 * @param mixed $default Default value
	 * @access public
	 */
	
	function &get($vname,$default = null) 
	{
	    switch($vname) {
	    	case "results":
	    		$ret = $this->getResults();
	    		break;
	    	default:
	    		$ret =& parent::get($vname,$default);
	    		break;	    		
	    }
	    return $ret;	
	}	
	
	/**
	 * Get String Tokens
	 * 
	 * @return array Tokens
	 * @access public
	 */
	
	function getStringTokens() {
		
		$segments = explode('"',$this->query);
		
		$quote = true;
		
		$len = count($segments);
		
		$tokens = array();
		
		$m = true;
		
		for($idx=0;$idx<$len;$idx++) {
		    $quote = $quote ? false : true;
		    		    
		    $str = trim(preg_replace('!\s+!', ' ', $segments[$idx]));
		    
		    
		    if ($quote) {
		    	if (strlen($str) > 0) {
		    	    $tokens[] = array($m,$str);
		    	}
		    } else {

		       if (substr($str,strlen($str)-1) == "-") {
		           $m = false;
		           $str = substr($str,strlen($str)-1);
		       } else {
		           $m = true;
		       }
               
		       $words = explode(" ",$str);
		       
		       
		       foreach($words as $w) {
		           if (substr($w,0,1) == "-") {
		               $m1 = false;
		               $w = substr($w,1);
		           } else {
		       	       $m1 = true;
		           }
		       
		           if (strlen($w) > 0) {
		               $tokens[] = array($m1,$w);
		           }		       
		       }
		    }    	
		}
	    return $tokens;	
	}
	
	/**
	 * Class Constructor
	 * 
	 * @param string $query Query
	 * @param array $options Options
	 * @param mixed $location Reserved
	 * @access public
	 */
	
	function __construct($query,$options,$location) 
	{
		$this->query = $query;
		$this->options = $options;
		$this->location = $location;
		
		$this->scan();
		$this->fetch();
	}
	
	// end class VSearch	
}
