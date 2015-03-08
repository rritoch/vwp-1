<?php

/**
 * Content Category Widget Parameters
 *  
 * @package VWP.Content
 * @subpackage Widgets.Category
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
 * Content Category Widget Parameters
 *  
 * @package VWP.Content
 * @subpackage Widgets.Category
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch 2011 - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License
 */

class Content_WidgetParams_Category extends VWidgetParams 
{

	/**
	 * Categories
	 * 
	 * @var array $_categories
	 * @access private
	 */
	
    static $_categories;
 
    /**
     * Category Title
     * 
     * @var string $title Title
     * @access public
     */
    
    public $title = "Content Category";
 
    /**
     * Category Id
     * 
     * @var integer $category Category Id
     * @access public
     */
    
    public $category = null;
    
    /**
     * Category Format
     * 
     * @var string Format
     * @access public
     */
    
    public $fmt = null;
 
    /**
     * Get Categories
     * 
     * @return array Categories
     * @access public
     */
    
    function getCategories() 
    {
        if (!isset(self::$_categories)) {
            $categories =& $this->getModel('categories');
            self::$_categories = array();
            if (VWP::isWarning($categories)) {
                $categories->ethrow();
                return self::$_categories;
            }
            
            $catlist = $categories->getAll();
      
            foreach($catlist as $cat) {
                self::$_categories[$cat["id"]] = $cat["name"];
            }
        }
        return self::$_categories;
    }
 
    /**
     * Get Parameter Definitions
     *
     * @return array Parameter definitions
     * @access public
     */
    
    function getDefinitions() 
    {
        $cats = $this->getCategories();
  
        $def = array();
        $def["category"] = array(
            "label"=>"Category",
            "type"=>"select",
            "values"=>$cats
          );
          
        $def["fmt"] = array(
           "label"=>"Format",
           "type"=>"select",
           "values"=>array("default"=>"Default","blog"=>"Blog")  
          );
        return $def;
    }
 
    /**
     * Class constructor
     * 
     * @access public
     */
    
    function __construct() 
    {
        parent::__construct();
        $this->addPath('models',dirname(dirname(dirname(__FILE__))).DS.'models');
    }

    // end class Content_WidgetParams_Category
}
 