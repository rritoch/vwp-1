<?php

/**
 * Content Article Widget Parameters
 *  
 * @package VWP.Content
 * @subpackage Widgets.Article
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch 2011 - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License
 */

/**
 * Require Widget Parameter Support
 */

VWP::RequireLibrary('vwp.ui.widget.params');

/**
 * Content Article Widget Parameters
 *  
 * @package VWP.Content
 * @subpackage Widgets.Article
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch 2011 - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License
 */

class Content_WidgetParams_Article extends VWidgetParams 
{
 
	/**
	 * Articles
	 * 
	 * @var array $_articles
	 * @access private
	 */
	
    static $_articles;

    /**
     * Display Title
     * 
     * @var integer Display Title
     * @access public
     */
    
    public $display_title = 1;
    
    /**
     * Article Id
     * 
     * @var integer Article
     * @access public
     */
    
    public $article = null;

    /**
     * Get Articles
     * 
     * @return array Articles
     * @access public
     */
    
    function getArticles() 
    {
        if (!isset(self::$_articles)) {
            $articles =& $this->getModel('articles');
            self::$_articles = array();
            if (VWP::isWarning($articles)) {
                $articles->ethrow();
                return self::$_articles;
            }
            $alist = $articles->getAll();
      
            foreach($alist as $article) {
                self::$_articles[$article["id"]] = $article["title"];
            }
        }
        return self::$_articles;
    }
 
    /**
     * Get Parameter Definitions
     * 
     * @return array Parameter Definitions
     * @access public
     */ 
    
    function getDefinitions() 
    {
        $alist = $this->getArticles();
  
        $def = array();
        $def["article"] = array(
            "label"=>"Article",
            "type"=>"select",
            "values"=>$alist
        );
        
        $def["display_title"] = array(
            "label"=>"Display Title",
            "type"=>"select",
            "values"=>array(
               '1'=>'Yes',
               '0'=>'No',
            )          
        );
          
       return $def;
    }
 
    /**
     * Class Constructor
     * 
     * @access public
     */
    
    function __construct() 
    {
        parent::__construct();
        $this->addPath('models',dirname(dirname(dirname(__FILE__))).DS.'models');
    }

    // end class Content_WidgetParams_Article
}
 