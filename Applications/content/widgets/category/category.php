<?php

VWP::RequireLibrary('vwp.ui.widget');

class Content_Widget_Category extends VWidget {

 /**
  * Display a category
  *
  * @param mixed $tpl Optional
  * @access public
  */
 function display($tpl = null) {
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
      
   $articles = $category->getArticles();
   for($ptr = 0; $ptr < count($articles); $ptr++) {
    $articles[$ptr]["content"] = $this->noPHP($articles[$ptr]["content"]);
   }
   $this->assignRef('articles',$articles);
   
   // Get sub-categories
   
   $sub_categories = $category->getSubCategories();
   $this->assignRef('sub_categories',$sub_categories);
   
   // Get child articles
   
   $child_articles = $category->getChildArticles();
   
   for($ptr = 0; $ptr < count($child_articles); $ptr++) {
    $child_articles[$ptr]["content"] = $this->noPHP($child_articles[$ptr]["content"]);
   }
   
   $this->assignRef('child_articles',$child_articles);
      
   // Get current category
   
   $category = $category->getProperties();
   $this->assignRef('category',$category);
  }
   
  parent::display();
  }

} // end class