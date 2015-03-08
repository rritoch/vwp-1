<?php


VWP::RequireLibrary('vwp.ui.widget');

class Tinymce_Widget_Editor extends VWidget {
  
 function display($tpl = null) {
  $shellob =& VWP::getShell();
      
  $tinymce = $this->getModel('tinymce');
  if (VWP::isWarning($tinymce)) {
   $tinymce->ethrow();
   $editor = null;
  } else {  
   $e1 =& $tinymce->open();
   $script_url = $e1->getScriptURL();
   $base_url = $e1->getExampleBaseURL();
   
   $tinymce_cfg = $shellob->getVar('tinymce_cfg',array());
   
   if (!isset($tinymce_cfg['name'])) {
    $t = $shellob->getVar('name');
    if (!empty($t)) {
     $tinymce_cfg['name'] = $t;
    }
   }
        
   if (!isset($tinymce_cfg['value'])) {
    $t = $shellob->getVar('value');    
    $tinymce_cfg['value'] = $t;    
   }
   
   $name = isset($tinymce_cfg['name']) ? $tinymce_cfg['name'] : 'ed1';
   $e1->setName($name);
      
   
   foreach($tinymce_cfg as $key=>$val) {
    $e1->set($key,$val);
   }

   $image_list_location = $shellob->getVar('image_list_url');         
   if (!empty($image_list_location)) {       
      $url = 'index.php?app=tinymce&widget=images&format=js&mode=raw&location='.urlencode($image_list_location);
      $url = VRoute::getInstance()->encode($url);
      $e1->set('external_image_list_url',$url); 
   }   
   
   
   $editor = $tinymce->getConfig($e1);
   

      
  }  
  
  $this->assignRef('base_url',$base_url);
  $this->assignRef('script_url',$script_url);
  $this->assignRef('editor',$editor);
  $this->assignRef('tinymce_cfg',$tinymce_cfg);
  
  parent::display();
 } 
 
} // end class