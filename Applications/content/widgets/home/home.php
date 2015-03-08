<?php


// No direct access

class_exists( 'VWP' ) or die( 'Restricted access' );

VWP::RequireLibrary('vwp.ui.widget');
  
class Content_Widget_Home extends VWidget {
   
     /**
      * Display home page
      * 
      * @param mixed $tpl Optional
      * @access public
      */

    function display($tpl = null) 
    {
        $shellob =& VWP::getShell();
                    
        $admin =& $this->getModel("admin");
  
        if (VWP::isWarning($admin)) {
            $admin->ethrow();
            return $admin; 
        }
      
        $config = $admin->getConfig();
  
        if (VWP::isWarning($config)) {
            $config->ethrow();
            $config = array();
        }
  
        $required = $admin->getRequiredSettings();
        foreach($required as $k) {
            if ((!isset($config[$k])) || (empty($config[$k]))) {
                $config[$k] = '';
            }
        }  
             
        $doc = & VWP::getDocument();        
        $doc->setProperty("page_title",$config["home_title"]);
  
  
        $widgetOptions = array("app"=>"content");
  
        if ($config["home_page_type"] == "category") {
            $widgetName = "category";   
            $widgetOptions["category"] = $config["home_category"]; 
            if ($config["home_category_layout"] == "blog") {
                $widgetOptions["fmt"] = "blog";
            }   
        } else {
           $widgetName = "article";
           $widgetOptions["article"] = $config["home_article"];     
        } 
  
        $widgetOptions["widget"] = $widgetName;  
        $widget = $this->getWidget($widgetName);

  
        if (VWP::isWarning($widget)) {
           $widget->ethrow();
           $index = '';
        } else {            
            foreach($widgetOptions as $key=>$val) {
                $shellob->setVar($key,$val);
            }
            $index = $widget->build();            
        }
  
        $this->assignRef('index',$index);
      
        parent::display();
    }
 
    // End class
}
