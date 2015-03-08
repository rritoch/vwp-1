<?php

VWP::RequireLibrary('vwp.ui.widget');

class Content_Widget_Catmgr extends VWidget {

 /**
  * Delete Categories
  *
  * @param mixed $tpl Optional
  * @access public
  */
 function delete_categories($tpl = null) {
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
   VWP::raiseWarning("No categories selected",get_class($this));   
  } else {
   $categories = $this->getModel('categories');   
   if (!VWP::isWarning($categories)) {
    $categories->deleteCategories($selected);
    VWP::addNotice("Categories deleted!");
   }
  }
  $this->display($tpl);
  }

 /**
  * Save changes to a category
  *
  * @param mixed $tpl Optional
  * @access public
  */
 function save_category($tpl = null) {
  $shellob =& VWP::getShell();
  
  $category = $this->getModel('category');
  if (VWP::isWarning($category)) {
   $category->ethrow();
   $category = array();
  } else {
   $is_new = $shellob->getVar('is_new',false);
   $cat = $shellob->getVar('cat');
      
   if ($is_new) {
    $category->load();
   } else {    
    $category->load($cat["id"]);
   }
         
   $category->bind($cat);
      
   $result = $category->save();
   if (VWP::isWarning($result)) {
    $result->ethrow();
   } else {
    VWP::addNotice("Category saved!");
   }
  }
  $category = $category->getProperties();
  
  $this->assignRef('category',$category);
     
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
  * Refresh Category Settings
  *
  * @param mixed $tpl Optional
  * @access public
  */
 function refresh($tpl = null) {
  $shellob =& VWP::getShell();
  
  $is_new = $shellob->getVar('is_new',1);
  if ($is_new) return $this->new_category($tpl);
  $cat = $shellob->getVar('cat');
  $id = $cat["id"];
  $id = array($id=>"on");
  $shellob->setVar("id",$id);
  $this->edit_category($tpl);
  }

 /**
  * Display new category form
  * 
  * @param mixed $tpl Optional
  * @access public
  */
 function new_category($tpl = null) {

 	$shellob =& VWP::getShell();
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

  // Setup new category
  
  $category = $this->getModel('category');
  if (VWP::isWarning($category)) {
   $category->ethrow();
   $category = array();
  } else {
   $category->load();
   $category = $category->getProperties();
  }
  
  $this->assignRef('category',$category);

  // Display layout
  $screen = $shellob->getScreen();
  $this->assignRef('screen',$screen);  
  
  $this->setLayout('edit');      
  parent::display($tpl);
  }


 /**
  * Display Edit Category Form
  *
  * @param mixed $tpl Optional
  * @access public
  */
 function edit_category($tpl = null) {

  $shellob =& VWP::getShell();
  
  // Set category
  
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
   VWP::raiseWarning("No category selected",get_class($this));
   return $this->display($tpl);
  }
  
  $id = $selected[0];

  // Setup category
  
  $category = $this->getModel('category');
  if (VWP::isWarning($category)) {
   $category->ethrow();
   $category = array();
  } else {
   $result = $category->load($id);
   if (VWP::isWarning($result)) {
    $result->ethrow();
    return $this->display($tpl);
   }
   
   $category = $category->getProperties();
  }
  
  $this->assignRef('category',$category);

  
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
  * Display Category List
  *
  * @param mixed $tpl Optional
  * @access public
  */ 
  function display($tpl = null) {
    $shellob =& VWP::getShell();

  // Get Categories
  
  $categories = $this->getModel('categories');
  $category_list = array();    
  if (VWP::isWarning($categories)) {
   $categories->ethrow();
  } else {
   $category_list = $categories->getAll();
  }  
  $this->assignRef('category_list',$category_list);
  
  $screen = $shellob->getScreen();
  $this->assignRef('screen',$screen);
  
  parent::display($tpl);
  } 
 
}