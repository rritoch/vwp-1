<?php

class ContentSearch extends VObject 
{

	public $filter;
	
    public static function getConfig() 
    {
        $localMachine = & Registry::LocalMachine();
  
        $result = Registry::RegOpenKeyEx($localMachine,
                        "SOFTWARE\\VNetPublishing\\Content\\Config",
                        0,
                        0, //samDesired
                        $registryKey);
                         
        if (!VWP::isWarning($result)) {     
            $data = array();
            $idx = 0;
            $keylen = 255;
            $vallen = 255;
            $lptype = REG_SZ; 
            while (!VWP::isError($result = Registry::RegEnumValue(
                                     $registryKey,
                                     $idx++,
                                     $key,
                                     $keylen,
                                     0, // reserved
                                     $lpType,
                                     $val,
                                     $vallen)))  {
                if (!VWP::isWarning($result) || $result->errno = ERROR_MORE_DATA) {                                       
                    $data[$key] = $val;
                    $keylen = 255;
                    $vallen = 255;  
                }
            }  
            Registry::RegCloseKey($registryKey);
            Registry::RegCloseKey($localMachine);
            return $data;
        }
  
        Registry::RegCloseKey($localMachine);
        return $result;
 
    }
	
    function calculateRelevance($search,$content) 
    {
    	
    	$c1 = preg_replace('|[^a-zA-Z0-9]|',' ',$content);
    	$c1 = trim(preg_replace('!\s+!', ' ', $c1));
    	
    	$c2 = $c1;
    	    	    	
    	$tokens = $search->getStringTokens();
    	
    	foreach($tokens as $s) {
    		if ($s[0]) {
    		    $txt = $s[1];
    	        $txt = preg_replace('|[^a-zA-Z0-9]|',' ',$txt);
    	        $txt = trim(preg_replace('!\s+!', ' ', $txt));    	        
    	        $c2 = str_replace($txt,'*',$c2);
    		}
    	}
    	
    	$n1 = strlen($c1);
    	$n2 = strlen($c2);
    	
    	if ($n2 == 0) {
    		return 0;
    	}
    	
    	$c = (1 - ($n2/$n1)) * 100;
    	
    	$c = 100 - abs(5 - $c);
    	
    	if ($c < 0) {
    		$c = 0;
    	}
    	
    	if ($c > 100) {
    		$c = 100;
    	}
    	
    	return $c;    	    	
    }
	
	function search($search,$max_results) 
	{
		
		
		$cfg = self::getConfig();
	    
	    if (VWP::isWarning($cfg)) {
	    	return false;
	    }		
			    
	    $articles = $cfg["table_prefix"] . 'content_articles';
	    
	    $articlesTbl =& v()->dbi()->getTable($articles);

	    $results = $articlesTbl->getMatches($this->filter);
	    
	    $route =& VRoute::getInstance();
	    
		foreach($results as $r) {

            $row =& $articlesTbl->getRow($r);           			
			
            $title = $row->get('title');
            $description = $row->get('description');
            
            $authority = 10;            
            $friendliness = 10;
            $popularity = 10;
            
            $mime_type = "text/html";
            
            $age = 0;
            
            $c = $row->get('_created');
                        
            $relevance = $this->calculateRelevance($search,$row->get('content'));
            
            $url = 'index.php?app=content&widget=article&article='.$row->get('id');
            $url = $route->encode($url);
            
            $location = null;
			$search->addResult('',$url, $title, $description, $mime_type, $relevance, $popularity, $authority, $friendliness, $age,$location);
		}		
	}
	
	public static function scan($args) 
	{
	    $search = $args[0];

	    $cfg = self::getConfig();
	    
	    if (VWP::isWarning($cfg)) {
	    	return false;
	    }
	    	    
	    $terms = $search->getStringTokens();

	    if (count($terms) > 0) {
	    	
	        $scout = new ContentSearch;
	    
	        $articles = $cfg["table_prefix"] . 'content_articles';
	    
	        $articlesTbl =& v()->dbi()->getTable($articles);

	        $scout->filter = $articlesTbl->createFilter();
	    
	        $scout->filter->addCondition("published","=","1");
	    	
	    	
	        foreach($terms as $i) {
	    	    $text = $i[1];
	    	    $text = htmlentities($text);
	    	    $text = str_replace("\\","\\\\",$text);
	    	    $text = str_replace('*',"\\*",$text);
	    	    $text = str_replace('?',"\\?",$text);
	    	    $text = str_replace(' ',"*",$text);	    	
	    	    if ($i[0]) {
	    	        $scout->filter->addCondition("content","IGLOB",'*'.$text.'*');
	    	    } else {
	    		    $scout->filter->addCondition("content","NOT IGLOB",'*'.$text.'*');
	    	    }
	        }
	    	    
	        $results = $articlesTbl->getMatches($scout->filter);
	        
	        if (VWP::isWarning($results)) {
	        	$results->ethrow();
	        } else {	        	
	            $count = count($results);
	            unset($results);	    
	            $cb = array($scout,'search');	    	    	    
	            $search->registerResource($cb,$count);
	        }
	    }
	    return true;
	}
	
	// end class ContentSearch
}
