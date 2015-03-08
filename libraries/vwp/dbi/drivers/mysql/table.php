<?php

/**
 * Virtual Web Platform - MySQL Table
 *  
 * This file provides the driver for
 * MySQL Table Access.   
 * 
 * @package VWP
 * @subpackage Libraries.DBI.Drivers.MySQL
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License   
 */

/**
 * Require MySQL database support
 */

VWP::RequireLibrary('vwp.dbi.drivers.mysql');

/**
 * Require paging support
 */

VWP::RequireLibrary('vwp.dbi.paging');

/**
 * Require filter support
 */

VWP::RequireLibrary('vwp.dbi.filter');


/**
 * Virtual Web Platform - MySQL Table
 *  
 * This class provides the driver for
 * MySQL Table Access.   
 * 
 * @package VWP
 * @subpackage Libraries.DBI.Drivers.MySQL
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License 
 */

class VMysqlTable extends VMysqlDatabase 
{
 
	public $maxcachesize = 1024;
	
    /**
     * @var object Paging
     * @access public
     */
       
    public $paging = null;
 
    /**
     * @var string $tableName
     * @access public
     */
       
    public $tableName = null;
 
    /**
     * @var array Columns
     * @access public
     */
       
    public $columns = null;
 
    /**
     * @var array Rows
     * @access public
     */
       
    protected $rows = array();
 
    /**
     * @var string $primary_key Primary key column
     * @access public
     */
       
    public $primary_key = null;

    /**
     * Class destructor
     * 
     * @access public
     */
         
    function __destruct() 
    {
        // do nothing!
    } 


    /**
     * Create a filter
     * 
     * @return VDatabaseFilter Filter object
     * @access public
     */
  
    function createFilter() 
    {
        return new VDatabaseFilter;
    }

    /**
     * Get Matches
     * 
     * @param VDatabaseFilter $filter Filter
     * @return array|object Matched column identifiers on success, error or warning on failure
     * @access public        
     */
 
    function getMatches($filter) 
    {
 
        // Init Settings
  
        $values = $filter->m_values;
        $match_types = $filter->m_operators;
        $connector = $filter->m_connector;
        $ordering = $filter->s_keys;
        $offset = $filter->m_offset + 0;
        $maxresults = $filter->m_maxresults;
  
        // Validate Request
  
        if (!isset($this->columns)) {
        	$this->loadColumns();
        }

        // Short Circuit
  
        if ($maxresults !== null) {
            $maxresults = $maxresults + 0;
            if ($maxresults < 1) {
                return array();
            }
        }
  
        // Set Pointer
  
        if (isset($this->primary_key)) {
        	$sql_key = $this->nameQuote($this->primary_key);        	
        } else {
            $sql_key = "*";
        }
        
        $sql_table = $this->nameQuote($this->tableName);
  
        // Set Filter
  
        $mlist = array();  
        foreach($values as $key=>$val) {
            $t = isset($match_types[$key]) ? $match_types[$key] : '=';
   
            if (($t == '=') && ($val === null)) {
                $sql = $this->nameQuote($key) . " IS NULL";
                
            } elseif ($t == "IGLOB") {
            	$val = str_replace("\\\\","\\",$val);            	
            	$val = str_replace("\\*",'*',$val);
            	$val = str_replace("\\?",'?',$val);
            	$val = str_replace("*","%",$val);
            	$val = str_replace("?","_",$val);
            	$sql = $this->nameQuote($key) . " LIKE " . $this->quote($val);
            	             	            	
            } elseif ($t == "NOT IGLOB") {
            	$val = str_replace("\\\\","\\",$val);            	
            	$val = str_replace("\\*",'*',$val);
            	$val = str_replace("\\?",'?',$val);
            	$val = str_replace("*","%",$val);
            	$val = str_replace("?","_",$val);
            	$sql = $this->nameQuote($key) . " NOT LIKE " . $this->quote($val);            	            		
            } else {
                $sql = $this->nameQuote($key) . " " . $t . " " . $this->quote($val);
            }
            array_push($mlist,$sql);
        }
  
        $sql_where = '';
        if (count($mlist) > 0) {
            $sql_where = " WHERE " . implode(" ".$connector." ",$mlist);  
        }
    
        // Set Ordering
  
        $order_parts = array();
        foreach($ordering as $field=>$dir) {
            if ($dir < 0) {
                array_push($order_parts, $this->nameQuote($field) . ' DESC');
            } else {
                array_push($order_parts, $this->nameQuote($field) . ' ASC');
            }
        }
  
        $sql_order = '';
        if (count($order_parts) > 0) {
            $sql_order = ' ORDER BY ' . implode(',',$order_parts);
        }  
  
        // Set Limits
  
        $sql_limit = '';
        if ($maxresults !== null) {      
            $sql_limit = ' LIMIT ' . $offset . ',' . $maxresults;
            $offset = 0;
        }
  
        // Send Query
     
        $query = "SELECT "
                 .$sql_key
                 ." FROM "
                 .$sql_table
                 ." "
                 .$sql_where
                 .$sql_order
                 .$sql_limit;  

        //VWP::raiseWarning($query);
        
        $this->setQuery($query);
  
        $r = $this->query();
        if (VWP::isWarning($r)) {
            return $r;
        }
  
        $result = array();
        $r = $this->loadAssocList();
        
        if (isset($this->primary_key)) {
            foreach($r as $data) {
                if ($offset > 0) {
                    $offset--;
                } else {
                    array_push($result,$data[$this->primary_key]);
                }
            }
        } else {
        	
            $coltypes = array();
            foreach($this->columns as $cinfo) {
            	$coltypes[$cinfo['name']] = $cinfo['_Type'];
            }        	
        	
            foreach($r as $data) {
            	foreach($data as $key=>$val) {
            		if ($coltypes[$key] == "timestamp") {
            			$t = strtotime($val);
            			$data[$key] = new VTime;
            			$data[$key]->setPHPTime($t);
            		}
            	}
                if ($offset > 0) {
                    $offset--;
                } else {
                    array_push($result,$data);
                }
            }        	
        }
        return $result;    
    }
 
    /**
     * Get table properties
     * 
     * @return array Table properties
     * @access public
     */
         
    function getProperties($public = true) 
    {
        $result = parent::getProperties($public);
    
        if (is_object($this->paging)) {
            $result["paging"] = $this->paging->getProperties();
        }
    
        if (is_array($this->rows)) {
            unset($result["rows"]);
            $result["rows"] = array();   
            foreach($this->rows as $key=>$val) {
                if (is_object($val)) {          
                    $result["rows"][$key] = get_object_vars($val);           
                } else {
                    $result["rows"][$key] = $val;
                }     
            }
        }  
        return $result; 
    }

    /**
     * Set table properties
     * 
     * @param array $properties Properties
     * @return true on success  
     * @access public
     */
 
    function setProperties( $properties ) 
    {
        if (isset($properties["paging"])) {
            if (is_array($properties["paging"])) {
                $old = $properties["paging"];
                $properties["paging"] = new VDatabasePaging;
                $properties["paging"]->setProperties($old);
            }
        }
        return parent::setProperties($properties);
    } 
 
    /**
     * Check if table has column
     * 
     * @param string $name Column name
     * @return boolean True if column exists
     */
    
    function hasColumn($name) 
    {
        if (empty($this->columns)) $this->loadColumns();
        $cols = $this->columns;
        foreach($cols as $c) {
        	if ($c['name'] == $name) {
        		return true;
        	}
        }
        return false;	
    }
    
    /**
     * Insert a column
     *
     * <pre>
     * 
     *  Options:
     *  (boolean) required - False if NULL allowed
     *  (integer) size - Size
     *  
     * </pre> 
     * 
     * @param string $columnName Column Name
     * @param string $type ANSI-SQL Data type
     * @param array $options column options
     * @param string $before Column name to insert before 
     * @access private
     */
     

    function insertColumn($columnName,$type,$options,$before) 
    {

    	$timeTypes = array('date','time','timestamp');
    	$required = isset($options['required']) ? $options['required'] : false;
    	
    	$size = isset($options['size']) ? $options['size'] : null;
    	
    	if (empty($this->columns)) $this->loadColumns();
    	
    	$sqlnull = $required ? ' NOT NULL ' :  ' NULL ';
    	$sqlautoinc = '';
    	
	    $default = isset($options['default']) ? $options['default'] : null;
    		
	    $sqldefault = '';
   		    
	    if (
  		        ($default !== null) &&    		            		        
   		        (
   		          (!in_array($type,$timeTypes)) ||
   		          ($default !== 'NOW')
   		        )
   		       ) {    		        
   		        $sqldefault = ' DEFAULT ' . $this->quote($default) . ' ';
       }

       if ($default == 'NOW' && in_array($type,$timeTypes)) {
           $sqldefault = ' DEFAULT CURRENT_TIMESTAMP ';	
       }    	
  	
    	
    	$columns = array();
    	
    	switch($type) {    			
   				case "integer":
   					$columns[] = $this->nameQuote($columnName) . ' INT ' . $sqlnull . $sqldefault . $sqlautoinc; 
   					break;
   			    case "double_precision":    				
   					$columns[] = $this->nameQuote($columnName) . ' DOUBLE ' . $sqlnull . $sqldefault; 
   					break;    			    	
   				case "character_varying":
   					
   					$size = $size === null ? 1 : $size + 0;
   					if ($size > 255) {
   						$columns[] = $this->nameQuote($columnName) . ' TEXT ' . $sqlnull . $sqldefault;
   					} else {
                           $columns[] = $this->nameQuote($columnName) . ' VARCHAR('.$size.') ' . $sqlnull . $sqldefault;
   					}
                       break;
                   case "bit":
   					
   					$size = $size === null ? 1 : $size + 0;
   					if ($size > 1) {
   						$columns[] = $this->nameQuote($columnName) . ' BIT(' . $size . ') ' . $sqlnull . $sqldefault;
   					} else {
                           $columns[] = $this->nameQuote($columnName) . ' TINYINT(1) ' . $sqlnull . $sqldefault;
   					}
                       break;
                   case "timestamp":
                   	$columns[] = $this->nameQuote($columnName) . ' TIMESTAMP ' . $sqlnull . $sqldefault . $sqlautoinc;
                   	break;                    	    					
   				case "smallint":    				
   				case "bit_varying":
   				case "date":
   				case "time":    				
   				case "decimal":
   				case "real":    				
   				case "float":
   				case "character":    				
   				case "national_character":		
                   case "national_character_varying":
                   case "interval":    					
   				default:
   			        $e = VWP::raiseWarning("Column '$columnName' has an unknown type '".$baseType->type."'.",__CLASS__,null,false);
   			        return $e;    					
   			}
    		

    		$cl = $this->columns;	
   			if (empty($before)) {
    		    $index = count($cl) - 1;
    		    if ($index < 0) {
    		    	$suffix = ' FIRST ';
    		    } else {
    		        $suffix = ' AFTER ' . $this->nameQuote($cl[$index]['name']);
    		    }		
   			} else {
   				
   				$index = null;
   				for($idx=0;$idx < count($cl);$idx++) {
   					if ($cl[$idx]['name'] == $before) {
   						$index = $idx;
   					}
   				}
                if ($index === null) {
                	$e = VWP::raiseWarning('Column '.$columnName . ' not found',__CLASS__,null,false);
                	return $e;
                }

                if ($index < 1) {
                	$suffix = ' FIRST ';
                } else {                
   			        $suffix =  ' AFTER ' . $this->nameQuote($cl[$index - 1]['name']);
                }
   			}

   		$sql_table = $this->nameQuote($this->tableName);
   		
   		$query = 'ALTER TABLE ' . $sql_table . ' ADD ' . $columns[0]. $suffix;
        $this->setQuery($query);
    	
    	$result = $this->query();
        if (VWP::isWarning($result)) {
    	    return $result;
    	}
    	$this->loadColumns();
    	return true;    			
    }
    	
    
 
    /**
     * Update column settings
     *
     * @todo Database update column support
     * @param string $columnName
     * @param string $type Data type
     * @param unknown_type $options
     * @access private
     */
    
    function updateColumn($columnName,$type,$options) 
    {
 
    }
 
    /**
     * Set primary key
     * 
     * @todo Implement set primary key support     
     * @param string $columnName Column name
     * @param boolean $auto Auto increment
     * @access private
     */
    
    function setPrimaryKey($columnName,$auto) 
    {
 
    }

    /**
     * Get paging object
     *   
     * @return object Table paging
     * @access public
     */
         
    function &getPaging() 
    {
        if (!isset($this->paging)) {
            $this->paging = new VDatabasePaging;
        }
        return $this->paging; 
    }

    /**
     * set paging object
     *   
     * @param object Table paging
     * @access public
     */
  
    function setPaging(&$paging) 
    {
        $this->paging = & $paging;
    }

    /**
     * Import data from XML
     *
     * @param string $tableId Table ID     
     * @param string $schema Schema
     * @param string $schemaSource Source of schema
     * @param string $databaseName Database name
     * @return boolean|object True on success, error or warning otherwise 
     * @access public
     */
    
    function importXMLData($tableId, &$xmlData,$schema,$schemaSource,$databaseName = null) 
    {
    	
    	// Load Schema
    	
    	$doc = new DOMDocument();    	
    	VWP::noWarn();
    	$r = $doc->loadXML($schema);
    	VWP::noWarn(false);    	
    	if (!$r) {
    		$e = VWP::raiseWarning('Invalid Table Schema!',__CLASS__,null,false);
    		return $e;
    	}

    	$tableSchema = new VSchema($doc,$schemaSource);

    	// Locate Table Declaration
    	
    	$tableElement = $tableSchema->getGlobalElementDeclByName($tableId);
    	
    	if (VWP::isWarning($tableElement)) {
    		$e = VWP::raiseWarning('Table declaration not found!',__CLASS__,null,false);
    		return $e;
    	}  
    	    	
    	// Get Table Attributes
    	
        $autoinc = null;
    	$prikey = null;
    	
    	$attrs = $tableSchema->getElementDeclAttributes($tableElement);
    	if (VWP::isWarning($attrs)) {
    		return $attrs;
    	}
    	
    	$len = count($attrs);
    	for($idx=0;$idx < $len;$idx++) {
    		$node = $attrs[$idx];
    		$name = $node->getAttribute('name');
    		
    		switch($name) {
    			case "prikey":
    				$prikey = $node->getAttribute('default');
    				break;
    			case "auto_increment":
    				$autoinc = $node->getAttribute('default');
    				break;
    			default:
    			    break;    		
    		}
    	}
    	
    	// Get Table Columns
    	
    	$columns = array();
    	
    	$sequence =& $tableSchema->getElementDeclSequence($tableElement);
    	if (VWP::isWarning($sequence)) {
    		return $sequence;
    	}
    	    	
    	if ($sequence->getLength() != 1) {
    		$e = VWP::raiseWarning('Invalid column declaration pointer!',__CLASS__,null,false);
    		return $e;    		
    	}

    	$colDeclPtr = $sequence->getItem(0);

    	$colDeclName = '';

        if ($colDeclPtr->hasAttribute('ref')) {
    		$colDeclName = $colDeclPtr->getAttribute('ref'); 
    	}
    	    	
    	if ($colDeclPtr->hasAttribute('name')) {
    		$colDeclName = $colDeclPtr->getAttribute('name'); 
    	}
    	
    	$parts = explode(':',$colDeclName);
    	$colDeclLocalName = array_pop($parts);
    	$colDeclPrefix = implode(':',$parts);
    	if (empty($colDeclPrefix)) {
    		$colDeclPrefix = null;
    	}    	
    	$colDeclNamespace = $colDeclPtr->lookupNamespaceUri($colDeclPrefix);
    	    	
    	$colDecls = $tableSchema->getElementDeclAttributes($colDeclPtr);
    	
    	if (VWP::isWarning($colDecls)) {
    		return $colDecls;
    	}
    	
    	$timeTypes = array('date','time','timestamp');
    	    	    
    	$len = count($colDecls);
    	
    	$colids = array();
    	
    	for($idx=0;$idx < $len; $idx++) {
    		$colNode = $colDecls[$idx];
    		$colName = $colNode->getAttribute('name');
    		$columns[] = $this->nameQuote($colName);
    		$colids[] = $colName;
    	}
    	
    	// Load Data
    	
    	VWP::setTimeLimit(0);
    	$data = new DOMDocument;
    	
    	VWP::noWarn();
    	$r = $data->loadXML($xmlData);
    	VWP::noWarn(false);
    	
    	if (VWP::isWarning($r)) {
    	    $xmldata = VWP::raiseWarning('Invalid source data',__CLASS__,null,false);
    	    VWP::resetTimeLimit(); 
    		return $xmldata;
    	}
    	
    	$xmlData = true;
    	
    	$datarows = $data->getELementsByTagNameNS($colDeclNamespace,$colDeclLocalName);
    	
    	$len = $datarows->length;
    	    	
    	for($p=0;$p < $len; $p++) {
    		$item = $datarows->item($p);
    		$coldata = array();
    		foreach($colids as $cid) {
    			if ($item->hasAttribute($cid)) {
    				$coldata[] = $this->quote($item->getAttribute($cid));
    			} else {
    				$coldata[] = 'NULL';
    			}
    		}
    		$vals = implode(',',$coldata);    	
    		$values = ' VALUES(' . $vals . ')';
    	    $query = 'INSERT INTO '. $this->nameQuote($this->tableName) . '(' . implode(',',$columns) . ')' . $values;
    	    	
    	    $this->setQuery($query);
    	
    	    $result = $this->query();
    	    if (VWP::isWarning($result)) {
    	    	VWP::resetTimeLimit();
    		    return $result;    		    
    	    }    		    		    		
    	}
    	    	       	
        VWP::resetTimeLimit();
    	return true;
    }
    
    
    /**
     * Load table columns
     * 
     * @return true|object True on success, error or warning otherwise
     * @access public  
     */

    function loadColumns() 
    {
        $query = "SHOW COLUMNS FROM " 
          . $this->nameQuote($this->tableName);
        $this->setQuery($query);
        $response = $this->query();
        if ($this->isError($response)) {
            return $response;
        }
  
        $response = $this->loadAssocList();
        $myColumns = array();
        $this->primary_key = null;
        foreach($response as $trow) {
            $row = array();
            foreach($trow as $key=>$val) {
                $row["_".$key] = $val;
            }
            $row["name"] = $row["_Field"];
            if ($row["_Default"] == "CURRENT_TIMESTAMP") {   
                $row["default"] = null;
            } else {
                $row["default"] = $row["_Default"];
            }
            if ($row["_Key"] == "PRI") {
                $this->primary_key = $row["name"];
            }
            array_push($myColumns,$row);
        }
  
        $this->columns = $myColumns;
        return true;
    }
 
    /**
     * Load table data
     * 
     * Note: This function must be called to make the current database rows available
     * 
     * @return true|object True on success, error or warning otherwise
     * @access public  
     */
   
    function loadData() 
    { 
        // These days are long gone!
        // Do not depricate - Someone else may need this        
        return true;
    }

    /**
     * Get a row from a table
     * 
     * @param mixed $key Row identifier (primary key value)
     * @param string $tableName Table name
     * @param string $databaseName Database name
     * @return object Row object on success, error or warning on failure
     */

    function &getRow($key,$tableName = null,$databaseName = null) 
    {

    	if (!isset($this->primary_key)) {
    	    $key = VDBI_MultiFieldIdentity::encodeKey($key);    	        	        	    
    	}
    	
    	    	
        if ($this->columns === null) {
            $result = $this->loadColumns();
            if ($this->isError($result)) {
                return $result;
            }
        }
        
        // Handle new rows
  
        if ($key === null) {
               
            $row = new VMysqlRow;
            $row->bind($this);
            $row->rows = array();
            $row->_tables = null;
            $result = $row->load();
            if ($this->isError($result)) {
                return $result;
            }
            return $row;   
        }
        
        // Handle existing rows
  
        $myRows = $this->rows;
                        
        if (!isset($myRows[$key])) {

        	$max = $this->maxcachesize > 0 ? $this->maxcachesize : 1;
        	 
            while (count(array_keys($myRows)) >= $max) {
            	$old = array_keys($myRows);
            	$myRows[$old[0]] = null;
        	    unset($myRows[$old[0]]); 
            }        	
        	
            $myRows[$key] = new VMysqlRow;
            $myRows[$key]->bind($this);
            $myRows[$key]->rows = array();
            $myRows[$key]->_tables = null;             
            $result = $myRows[$key]->load($key);
                        
            if ($this->isError($result)) {                
                return $result;
            }
            $this->rows = $myRows;   
        }
                        
        return $myRows[$key];  
    }
 
    /**
     * List Columns
     * 
     * @return array Column list
     */
    
    function listColumns() {
    	if (!is_array($this->columns)) {
    		$this->loadColumns();
    	}
    	
    	$cols = array();
    	$myColumns = $this->columns;
    	foreach($myColumns as $row) {
    		$cols[] = $row['name'];
    	}
    	return $cols;    	
    }
    
    function &get($vname,$default = null) {
    	switch($vname) {    		
    		case "rows":
                $limit = '';
                if (is_object($this->paging)) {
                    switch($this->paging->getMode()) {
                        case "page":
                            $pagesize = $this->paging->getPageSize();
                            $pagenum = $this->paging->getPageNum();
                            if ($pagesize !== null) {
                                if ($pagesize < 1) {
                                    $limit = '';
                                } else {
                                    if ($pagenum < 1) {
                                        $pagenum = 1;
                                    }
                                    $length = (int) $pagesize;
                                    $start = ($pagenum - 1) * $length;
                                    $limit = " LIMIT $start,$length";       
                                }
                            }
                            break;
                        default:
                            $this->raiseWarning("Unrecognized paging mode!",1,true);
                            break;
                    }
                }
  
                if (!isset($this->columns)) {
                    $response = $this->loadColumns();
                    if ($this->isError($response)) {
                        return $response;
                    }                    
                }
  
                $query = "SELECT * FROM "
                   . $this->nameQuote($this->tableName)
                   . $limit;
  
                $this->setQuery($query);
                $response = $this->query();
                if ($this->isError($response)) {
                    return $response; 
                }
  
                $response = $this->loadAssocList();
  
                $myRows = array();
                foreach ($response as $row) {
                    $new_row = array("fields"=>array());
                    foreach($row as $name=>$value) {
                        $columninfo = array(
                                    "name"=>$name,
                                    "value"=>$value
                                   );    
                        $new_row["fields"][$name] = $columninfo;
                    }

                    if ($this->primary_key === null) {    
                        $row_ob = new MysqlRow;
                        $row_ob->bind($this);
                        $row_ob->rows = array();
                        $row_ob->_tables = null;
                        $row_ob->setProperties($new_row);
                        $row_ob->_new = false;
                        $row_ob->id = new VDBI_MultiFieldIdentity($row_ob);
                        $myRows[(string)$row_ob->id] =& $row_ob;                            
                    } else {
                        $myRows[$row[$this->primary_key]] = new VMysqlRow;
                        $myRows[$row[$this->primary_key]]->bind($this);
                        $myRows[$row[$this->primary_key]]->rows = array();
                        $myRows[$row[$this->primary_key]]->_tables = null;
                        $myRows[$row[$this->primary_key]]->setProperties($new_row);
                        $myRows[$row[$this->primary_key]]->_new = false;        
                    }         
                }
        
                $ret =& $myRows;
                break;    			
    		default:
    	        $ret =& parent::get($vname,$default);
    	}
        return $ret;
    }
    
    /**
     * Class constructor
     *
     * @param string $tableName Table name
     * @access public
     */
          
    function __construct($tableName = null) 
    {
        $this->_link = false;
        parent::__construct();
        $this->tableName = $tableName;  
    }
    
    // end class VMysqlTable
} 
