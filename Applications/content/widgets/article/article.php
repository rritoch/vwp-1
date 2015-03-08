<?php

/**
 * Content Article Widget
 *  
 * @package VWP.Content
 * @subpackage Widgets
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch 2011 - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License
 */

/**
 * Require Widget Support
 */

VWP::RequireLibrary('vwp.ui.widget');

/**
 * Content Article Widget
 *  
 * @package VWP.Content
 * @subpackage Widgets
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @copyright (c) Ralph Ritoch 2011 - All Rights Reserved
 * @link http://www.vnetpublishing.com VNetPublishing.Com
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License
 */


class Content_Widget_Article extends VWidget 
{

    /**
     * Display Article
     * 
     * @param mixed $tpl Optional
     * @access public
     */
   
    function display($tpl = null) 
    {
        $shellob =& VWP::getShell();
        $ref = $shellob->getVar('ref');
  
        $params =& $this->getParams();  
        if (!empty($ref)) {
            $params->loadRef($ref);
        }
  
        $article_id = $shellob->getVar('article');
        
        $display_title = $shellob->getVar('display_title',1) ? true : false;
        if (empty($article_id)) {
            $article_id = $params->article;
            $display_title = $params->display_title ? true : false;
        }
  
        $article = $this->getModel('article');
        if (empty($article_id)) {
            $found = false;
            $article = null;   
            $this->setLayout('404');
        } else {
            $result = $article->load($article_id);
            if (VWP::isWarning($result)) {            	
                $found = false;
                $article = null;    
                $this->setLayout('404');
            } else {
                $found = true;
                $article = $article->getProperties();
                
                if ($article["published"] != "1") {
                    $found = false;
                    $this->setLayout('404');                	
                } else {                       
                    $article["content"] = $this->noPHP($article["content"]);
                }                
                
                
            }
        }

        v()->document()->set('page_title',$article['title']);
        v()->document()->setMetaData('keywords',$article['keywords']);
        v()->document()->setMetaData('description',$article['description']);
        
        $this->assignRef('found',$found);
        $this->assignRef('article',$article);
        $this->assignRef('display_title',$display_title);
           
        parent::display();
    }
    
    // end class Content_Widget_Article
} 
