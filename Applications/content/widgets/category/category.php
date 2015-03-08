<?php

/**
 * Content Category Widget
 *  
 * @package    VWP.Content
 * @subpackage Widgets
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch 2011 - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License
 */

VWP::RequireLibrary('vwp.ui.widget');

/**
 * Content Manager Widget
 *  
 * @package    VWP.Content
 * @subpackage Widgets
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch 2011 - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License
 */

class Content_Widget_Category extends VWidget 
{

    /**
     * Display a category
     *
     * @param mixed $tpl Optional
     * @access public
     */
	
    function display($tpl = null) 
    {
        $shellob =& VWP::getShell();   
        $ref = $shellob->getVar('ref');  
        $params =& $this->getParams();
  
        if (VWP::isWarning($params)) {
            $params->ethrow();
            return $params;
        }
    
        if (!empty($ref)) {
            $params->loadRef($ref);
        }
  
        $category_id = $shellob->getVar('category');  
        if (empty($category_id)) {
            $category_id = $params->category; 
        }

        $fmt = $shellob->getVar('fmt');
        if (empty($fmt)) {
            $fmt = $params->fmt;
        }
  
        if (empty($fmt)) {   
            $this->setLayout('default'); 
        } else {   
            $this->setLayout($fmt);
        }
        
        if (!empty($category_id)) {
            $category = $this->getModel('category');
            if (VWP::isWarning($category)) {
                $category->ethrow();
                return $category;
            }
            $result = $category->load($category_id);
            if (VWP::isWarning($result)) {
                $category_id = null;
            }  
        }
  
        if (empty($category_id)) {   
            $this->setLayout('404');
        } else {
   
            // Get category list
   
            $categories = $this->getModel('categories');
            if (VWP::isWarning($categories)) {
                $categories->ethrow();
                return $categories;
            }
            $category_list = $categories->getAll();
            $this->assignRef('category_list',$category_list);
   
            // Get category info
   
            $catinfo = array();
            foreach($category_list as $cat) {
                $catinfo[$cat["id"]] = $cat;
            }
    
            // Get articles
      
            $articles = array();
            $raw_articles = $category->getArticles();
            $raw_articles_len = count($raw_articles);            
            for($ptr = 0; $ptr < $raw_articles_len; $ptr++) {
                $a = $raw_articles[$ptr];                
                if ($a["published"] == "1") {
                	$a["content"] = $this->noPHP($raw_articles[$ptr]["content"]);
                    $articles[] = $a;
                }
            }
            
            unset($raw_articles);
            unset($raw_articles_len);
            
            $this->assignRef('articles',$articles);
   
            // Get sub-categories
   
            $sub_categories = $category->getSubCategories();
            $this->assignRef('sub_categories',$sub_categories);
   
            // Get child articles
   
            $child_articles = array();
            $raw_child_articles = $category->getChildArticles();   
            $raw_child_articles_len = count($raw_child_articles);
            
            for($ptr = 0; $ptr < $raw_child_articles_len; $ptr++) {
            	
            	$a = $raw_child_articles[$ptr];
                if ($a["published"] == "1") {
                	$a["content"] = $this->noPHP($raw_child_articles[$ptr]["content"]);
                    $child_articles[] = $a;
                }            	                
            }
   
            unset($raw_child_articles);
            unset($raw_child_articles_len);
            
            $this->assignRef('child_articles',$child_articles);
      
            // Get current category
   
            $category = $category->getProperties();
            
            v()->document()->set('page_title',$category['name']);
            v()->document()->setMetaData('keywords',$category['keywords']);
            v()->document()->setMetaData('description',$category['description']);
            
            $this->assignRef('category',$category);
            
        }
   
        parent::display();
    }
    
    // end class Content_Widget_Category
}
 