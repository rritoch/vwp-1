<?php

VWP::RequireLibrary('vwp.ui.widget.params');

class Content_WidgetParams_Article extends VWidgetParams {
 
 static $_articles;
 
 var $title = "Content Article";
 
 var $article = null;
 var $fmt = null;
 
 function getArticles() {
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
 
 function getDefinitions() {
  $alist = $this->getArticles();
  
  $def = array();
  $def["article"] = array(
   "label"=>"Article",
   "type"=>"select",
   "values"=>$alist
  );

  return $def;
 }
 
 function __construct() {
  parent::__construct();
  $this->addPath('models',dirname(dirname(dirname(__FILE__))).DS.'models');
 }

} // end class