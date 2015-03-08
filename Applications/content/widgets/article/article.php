<?php

VWP::RequireLibrary('vwp.ui.widget');

class Content_Widget_Article extends VWidget {

 /**
  * Display Article
  * 
  * @param mixed $tpl Optional
  * @access public
  */
   
 function display($tpl = null) {
  $shellob =& VWP::getShell();
  $ref = $shellob->getVar('ref');
  
  $params =& $this->getParams();  
  if (!empty($ref)) {
   $params->loadRef($ref);
  }
  
  $article_id = $shellob->getVar('article');
  if (empty($article_id)) {
   $article_id = $params->article;
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
    $article["content"] = $this->noPHP($article["content"]);
   }
  }
  
  $this->assignRef('found',$found);
  $this->assignRef('article',$article);   
  parent::display();
  }

} // end class