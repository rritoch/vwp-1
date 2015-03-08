<?php

VWP::RequireLibrary('vwp.ui.widget');

class Content_Widget_Articlemgr extends VWidget {

 /**
  * Delete Articles
  *
  * @param mixed $tpl Optional
  * @access public
  */
 function delete_articles($tpl = null) {
  $shellob =& VWP::getShell();
   
  // Set categories
  
  $is_new = false;
  $this->assignRef('is_new',$is_new);

  $tmp = $shellob->getVar('id');
  
  $selected = array();
  
  if (is_array($tmp)) {
   foreach($tmp as $key=>$val) {
    if (strtolower($val) == "on") {
     array_push($selected,$key);
    }
   }  
  }
  
  if (count($selected) < 1) {
   VWP::raiseWarning("No articles selected",get_class($this));   
  } else {
   $articles = $this->getModel('articles');   
   if (!VWP::isWarning($articles)) {
    $articles->deleteArticles($selected);
    VWP::addNotice("Categories deleted!");
   }
  }
  $this->display($tpl);
  }

 /**
  * Save changes to an article
  *
  * @param mixed $tpl Optional
  * @access public
  */
 function save_article($tpl = null) {
  $shellob =& v()->shell();
      
  $article = $this->getModel('article');
  if (VWP::isWarning($article)) {
   $article->ethrow();
   $article = array();
   $editor = '';
  } else {
   $is_new = $shellob->getVar('is_new',false);
   $data = $shellob->getVar('article');
      
   if ($is_new) {
    $article->load();
   } else {    
    $article->load($data["id"]);
   }
         
   $article->bind($data);
      
   $result = $article->save();
   if (VWP::isWarning($result)) {
    $result->ethrow();
   } else {
    VWP::addNotice("Article saved!");
   }
  }
  
  $editor = $article->getEditor();
  $article = $article->getProperties();
  
  $this->assignRef('article',$article);
  $this->assignRef('editor',$editor);
     
  // Get Categories
  
  $categories = $this->getModel('categories');
  $category_list = array();    
  if (VWP::isWarning($categories)) {
   $categories->ethrow();
  } else {
   $category_list = $categories->getAll();
  }  
  $this->assignRef('category_list',$category_list);
    
  $this->setLayout('edit');  
  $is_new = false;
  $this->assignRef('is_new',$is_new);
  
  $screen = $shellob->getScreen();
  $this->assignRef('screen',$screen);
  
  parent::display($tpl);
  }

 /**
  * Reset article form
  *
  * @param mixed $tpl Optional
  * @access public
  */
 function refresh($tpl = null) {
  $shellob =& VWP::getShell();
  
  $is_new = $shellob->getVar('is_new',1);
  if ($is_new) return $this->new_article($tpl);
  $data = $shellob->getVar('article');
  $id = $data["id"];
  $id = array($id=>"on");
  $shellob->setVar("id",$id);
  $this->edit_article($tpl);
  }

 /**
  * Display new article form
  *
  * @param mixed $tpl Optional
  * @access public
  */
 function new_article($tpl = null) {

 	$shellob =& v()->shell();
  // Set new category
  
  $is_new = true;
  $this->assignRef('is_new',$is_new);

  
  // Get Categories
  
  $categories = $this->getModel('categories');
  $category_list = array();    
  if (VWP::isWarning($categories)) {
   $categories->ethrow();
  } else {
   $category_list = $categories->getAll();
  }  
  $this->assignRef('category_list',$category_list);

  // Setup new article
  
  $article = $this->getModel('article');
  if (VWP::isWarning($article)) {
   $article->ethrow();
   $article = array();
   $editor = '';
  } else {
   $article->load();   
   $editor = $article->getEditor();
   $article = $article->getProperties();   
  }
  
  $this->assignRef('article',$article);
  $this->assignRef('editor',$editor);


  
  // Display layout
  $screen = $shellob->getScreen();
  $this->assignRef('screen',$screen);  
  
  $this->setLayout('edit');      
  parent::display($tpl);
  }


 /**
  * Display edit article form
  *
  * @param mixed $tpl Optional
  * @access public
  */
 function edit_article($tpl = null) {

  $shellob =& VWP::getShell();
  
  // Setup Article
  
  $is_new = false;
  $this->assignRef('is_new',$is_new);

  $tmp = $shellob->getVar('id');
  
  $selected = array();
  
  if (is_array($tmp)) {
   foreach($tmp as $key=>$val) {
    if (strtolower($val) == "on") {
     array_push($selected,$key);
    }
   }  
  }
  
  if (count($selected) < 1) {
   VWP::raiseWarning("No article selected",get_class($this));
   return $this->display($tpl);
  }
  
  $id = $selected[0];  
  
  $article = $this->getModel('article');
  if (VWP::isWarning($article)) {
   $article->ethrow();
   $article = array();
   $editor = '';
  } else {
   $result = $article->load($id);
   if (VWP::isWarning($result)) {
    $result->ethrow();
    return $this->display($tpl);
   }
   $editor = $article->getEditor();
   $article = $article->getProperties();
  }
  
  $this->assignRef('article',$article);
  $this->assignRef('editor',$editor);
  
  // Get Categories
  
  $categories = $this->getModel('categories');
  $category_list = array();    
  if (VWP::isWarning($categories)) {
   $categories->ethrow();
  } else {
   $category_list = $categories->getAll();
  }  
  $this->assignRef('category_list',$category_list);


  // Display layout
  
  $screen = $shellob->getScreen();
  $this->assignRef('screen',$screen);    
  
  $this->setLayout('edit');      
  parent::display($tpl);
  }

 
 /**
  * Display Article List
  *
  * @param mixed $tpl Optional
  * @access public
  */ 
  
  function display($tpl = null) {

  	 $shellob =& v()->shell();
  	 
  // Get Articles
  
  $articles = $this->getModel('articles');
  $article_list = array();    
  if (VWP::isWarning($articles)) {
   $articles->ethrow();
  } else {
   $article_list = $articles->getAll();
  }  
  $this->assignRef('article_list',$article_list);
  
  // Get Categories
  
  $categories = $this->getModel('categories');
  $category_list = array();    
  if (VWP::isWarning($categories)) {
   $categories->ethrow();
  } else {
   $category_list = $categories->getAll();
  }
  
  $categories = array();
  foreach($category_list as $cat) {
   $categories[$cat["id"]] = $cat["name"];
  }  
  $this->assignRef('category_list',$category_list);
  $this->assignRef('categories',$categories);
  
    $screen = $shellob->getScreen();
    $this->assignRef('screen',$screen);  
  
    parent::display($tpl);
  } 
 
}