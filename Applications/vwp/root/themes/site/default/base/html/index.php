<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html>
 <head>
  <title><?php echo $this->page_title; ?></title>
  <link rel="stylesheet" href="<?php echo htmlentities($this->theme_path.'/css/default.css'); ?>" />
<?php foreach($this->scripts as $uri=>$type) { ?>
  <script type="<?php echo htmlentities($type); ?>" src="<?php echo htmlentities($uri); ?>"></script>      	  
<?php } ?>
 <style type="text/css">
 <!--
#bg {   
    background: url('<?php echo htmlentities($this->theme_path); ?>/images/vpnetbg.jpg');
    background-repeat: no-repeat;
    background-position: center top;
}  
 -->
 </style>
</head>
<body><div id="bg">
<div id="wrapper">

 <div id="header">
  <div id="topleft">
  <h1><?php echo htmlentities($this->site_name); ?></h1>
  </div>
  <div id="topright">
  <p>Virtual Web Platform.</p>
  </div>
 </div>
 <div id="menubar">
  <vdoc:include app="menu" widget="hmenu" name="topmenu" />
 </div>
<?php if (count($this->errors) > 0) { ?>
<div id="error_win"><div><div><div>

 <?php foreach($this->errors as $errmsg) { ?>
 <fieldset>
 <legend>Error</legend>  
 <?php echo htmlentities($errmsg); ?><br />  
 </fieldset>
 <?php } ?>

<p>We are sorry for the inconvienience. We will repair the problem shortly.</p>
</div></div></div>
</div>
<?php } ?>
 
 <div id="fullcontent">

<?php if (count($this->notices) > 0) { ?>
<div id="notice_win"><div><div><div>

 <?php foreach($this->notices as $errmsg) { ?>
 <fieldset>
 <legend>Notice:</legend>  
 <?php echo htmlentities($errmsg); ?><br />  
 </fieldset>
 <?php } ?>
</div></div></div>
</div>
<?php } ?>  
<?php if (count($this->warnings) > 0) { ?>
<div id="warning_win"><div><div><div>

 <?php foreach($this->warnings as $errmsg) { ?>
 <fieldset>
 <legend>Warning:</legend>  
 <?php echo htmlentities($errmsg); ?><br />  
 </fieldset>
 <?php } ?>
</div></div></div>
</div>
<?php } ?>  
<?php if (count($this->debug_notices) > 0) { ?>
<div id="debug_win"><div><div><div>

 <?php foreach($this->debug_notices as $errmsg) { ?>
 <fieldset>
 <legend>Debug Notice:</legend>  
 <?php echo htmlentities($errmsg); ?><br />  
 </fieldset>
 <?php } ?>
</div></div></div>
</div>
<?php } ?> 
 <div id="fullcontent">
 <table>
  <tr><td id="leftframe">
  <div id="left">
  <vdoc:include app="vwp" widget="validate" />
  <vdoc:include app="user" widget="user" />
   </div>
  </td><td>
 <div id="content">
 <vdoc:include alias="content" />
 </div>
 </td>
 </tr>
 </table>
 </div>
 <div id="footer"><div><div><div>  
  <p>Copyright &copy; Ralph Ritoch 2010</p>
  </div></div></div></div>
</div>
</div></body>
 </html>