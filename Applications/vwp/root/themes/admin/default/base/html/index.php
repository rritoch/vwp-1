<?php
/**
 * Overall page template
 */ 
 
VWP::RequireLibrary('vwp.uri');
 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>        
 <head>                
  <title><?php echo $this->title; ?></title>                
  <link rel="stylesheet" href="<?php echo htmlentities($this->theme_path); ?>/css/layout.css" />                 
  <link rel="stylesheet" href="<?php echo htmlentities($this->theme_path); ?>/css/colors.css" />                 
  <link rel="stylesheet" href="<?php echo htmlentities($this->theme_path); ?>/css/fonts.css" />                 
  <style type="text/css"><!--
   div.titlebar {	
   	background: url('<?php echo htmlentities($this->theme_path); ?>/images/titlebar/titlebar1.png');
   	background-repeat: repeat-x;
   	height: 38px;	
   }
   #header div.titlebar h2 {
    background: url('<?php echo htmlentities($this->theme_path); ?>/images/titlebar/logo.png');
    background-repeat: no-repeat;
   }
   div.titlebar h2 {
    height: 23px;
    width: 255px;
   }
   #topmenu {
    background: url('<?php echo htmlentities($this->theme_path); ?>/images/topmenu/tmenu_left.png');  
    background-repeat: repeat-y; 
   }
   #topmenu div.bg {
    background: url('<?php echo htmlentities($this->theme_path); ?>/images/topmenu/tmenu_bg.png');  
    background-repeat: repeat; 
   }
   #topmenu div.bg ul li a:link,
   #topmenu div.bg ul li a:visited,
   #topmenu div.bg ul li a:active,
   #topmenu div.bg ul li a:hover {
    display: block;
    background: url('<?php echo htmlentities($this->theme_path); ?>/images/topmenu/tmenu_btn_left.png');  
    background-repeat: no-repeat;
    background-position: top left;
   }
   #topmenu div.bg ul li a:link span.linktext,
   #topmenu div.bg ul li a:visited span.linktext,
   #topmenu div.bg ul li a:active span.linktext,
   #topmenu div.bg ul li a:hover span.linktext {
    display: block;
    background: url('<?php echo htmlentities($this->theme_path); ?>/images/topmenu/tmenu_btn_right.png');  
    background-repeat: no-repeat;
    background-position: top right;
   }
   #console_frames td.panel {
    background: url('<?php echo htmlentities($this->theme_path); ?>/images/layout/metal_bg.png');
    background-repeat: repeat-y;
   }
   #console_frames td.panel_bar,
   #console_frames td.vbar div.vbar {
    background: url('<?php echo htmlentities($this->theme_path); ?>/images/layout/vbar8px.png');
    background-repeat: repeat-y;
    background-position: top right;                                                
   }
   
   
   #console_frames tr.panelh_bar div.bar,
   #console_wrapper, .layout_bar {
    background: url('<?php echo htmlentities($this->theme_path); ?>/images/layout/hbar11px.png');
    background-repeat: repeat-x; 
   }
   
   #console_wrapper {
    background-position: bottom left;
   }
   
   table.control_panel tr.controls td {
    background: url('<?php echo htmlentities($this->theme_path); ?>/images/controlpanel/ctrl_btnhbar23px.png');
    background-repeat: repeat-x;                                                 
   }
   
   table.control_panel td.hbar {
    background: url('<?php echo htmlentities($this->theme_path); ?>/images/controlpanel/ctrl_hbar6px.png');
    background-repeat: repeat-x;                                                 
   }
   
   table.control_panel td.vbar {
    background: url('<?php echo htmlentities($this->theme_path); ?>/images/controlpanel/ctrl_vbar5px.png');
    background-repeat: repeat-y;                                                 
   }

   table.control_panel tr.controls a:link,
   table.control_panel tr.controls a:visited,
   table.control_panel tr.controls a:active,
   table.control_panel tr.controls a:hover,
   table.control_panel tr.controls div.btn {
    background: url('<?php echo htmlentities($this->theme_path); ?>/images/controlpanel/ctrl_btn_left.png');
    background-repeat: no-repeat;                                                
   }                                                

   table.control_panel tr.controls a:link span,
   table.control_panel tr.controls a:visited span,
   table.control_panel tr.controls a:active span,
   table.control_panel tr.controls a:hover span,
   table.control_panel tr.controls div.btn span {
    display: block;
    background: url('<?php echo htmlentities($this->theme_path); ?>/images/controlpanel/ctrl_btn_right.png');
    background-repeat: no-repeat;
    background-position: top right; 
   }
   
   table.control_panel tr.col_titles td.header {
    background: url('<?php echo htmlentities($this->theme_path); ?>/images/controlpanel/ctrl_hbar19px.png');
    background-repeat: repeat;                                                 
   }
   
   table.control_panel a.up:link span,
   table.control_panel a.up:visited span,
   table.control_panel a.up:active span,
   table.control_panel a.up:hover span {
    background: url('<?php echo htmlentities($this->theme_path); ?>/images/controlpanel/ctrl_btnup.png');   
    background-repeat: no-repeat;       
   }
   
   table.control_panel a.down:link span,
   table.control_panel a.down:visited span,
   table.control_panel a.down:active span,
   table.control_panel a.down:hover span {
    background: url('<?php echo htmlentities($this->theme_path); ?>/images/controlpanel/ctrl_btndown.png');   
    background-repeat: no-repeat;      
   }
   

   div.tab_top,
   div.tab_bottom {
    background: url('<?php echo htmlentities($this->theme_path); ?>/images/tabset/tab_hbar6px.png');
    background-repeat: repeat-x;
   }
   
   div.tab_top {
    background-position: top left;
   }
   
   div.tab_bottom {
    background-position: bottom left;
   }
   
   div.tab_left, div.tab_right {
    background: url('<?php echo htmlentities($this->theme_path); ?>/images/tabset/tab_vbar5px.png');
    background-repeat: repeat-y;
   }
   
   div.tab_left {
    background-position: top left;
   }
   
   div.tab_right {
    background-position: top right;
   }
   
   ul.tabset a:link,
   ul.tabset a:visited,
   ul.tabset a:active,
   ul.tabset a:hover {
    display: block;
    background: url('<?php echo htmlentities($this->theme_path); ?>/images/tabset/tab_left.png');
    background-repeat: no-repeat;
    background-position: top left;                                                
   }
   
   ul.tabset a:link span,
   ul.tabset a:visited span,
   ul.tabset a:active span,
   ul.tabset a:hover span {
    display: block;
    background: url('<?php echo htmlentities($this->theme_path); ?>/images/tabset/tab_right.png');
    background-repeat: no-repeat;
    background-position: top right;                                                
   }
   
   ul.tabset li.selected a:link,
   ul.tabset li.selected a:visited,
   ul.tabset li.selected a:active,
   ul.tabset li.selected a:hover {
    display: block;
    background: url('<?php echo htmlentities($this->theme_path); ?>/images/tabset/tab_left_sel.png');
    background-repeat: no-repeat;
    background-position: top left;                                                
   }
   
   ul.tabset li.selected a:link span,
   ul.tabset li.selected a:visited span,
   ul.tabset li.selected a:active span,
   ul.tabset li.selected a:hover span {
    display: block;
    background: url('<?php echo htmlentities($this->theme_path); ?>/images/tabset/tab_right_sel.png');
    background-repeat: no-repeat;
    background-position: top right;                                                
   }
   
                                                                                                                                                 
   --></style>
 <script type="text/javascript" src="<?php echo htmlentities($this->theme_path); ?>/js/theme.js"></script>                        
 </head>        
  <body>                
   <div id="bg">                        
    <div id="wrapper">                                  
     <!-- Header -->                                    
     <div id="header">                                             
      <!-- Title Bar -->                                              
      <div class="titlebar" id="topleft">   
       <h1><?php echo htmlentities($this->site_name); ?></h1>   
       <h2 title="Virtual Web Platform - &copy; Ralph Ritoch 2010"><span>Virtual Web Platform - &copy; Ralph Ritoch 2010</span></h2>                                           
      </div>                                              
      <!-- Top Menu -->                                             
      <div id="topmenu"><div class="clr_b"></div>                                                    
       <div class="bg">                                                        
        <div class="clr_b"></div>
        <vdoc:include app="menu" widget="hmenu" name="admin_topmenu" />                                                                                                                            
        <div class="clr_b"></div>                                                
       </div>                                                   
       <div class="clr_b"></div>                                        
      </div>                                       
     </div>                                    
     <!-- Console -->                                   
     <div id="console_wrapper">                                          
      <div id="console">                                                    
       <table id="console_frames">                                                           
         <tbody>                                                                    
          <tr class="status" valign="top">                                                                             
           <td class="panel" valign="top">
             <div id="sys_status">                                                                                            
             <!-- Left Status -->
              <p>Administrator Control Panel</p>
             <!-- End Left Status -->      
             </div>
           </td>                                                                             
           <td class="panel_bar">                                                                                
            <div class="clr_b"></div></td>                                                                                       
           <td class="info">
            <div id="sys_messages">                                                                                          
             <!-- Right Status -->                                                                                 
             <?php if (count($this->errors)> 0) { ?>                                                                                 
             <div id="error_win">                                                                                        
              <div>                                                                                                
               <div>                                                                                                        
                <div>                                                                                                                  
                 <?php foreach($this->errors as $errmsg) { ?>                                                                                                                  
                 <fieldset class="err">                                                                                                                          
                  <legend>Error                                                                                                                         
                  </legend>                                                                                                                             
                  <?php echo htmlentities($errmsg); ?><br />                                                                                                                             
                 </fieldset>                                                                                                                          
                 <?php } ?>                                                                                                                         
                 <p>We are sorry for the inconvienience. We will repair the problem shortly.                                                                                                                         
                 </p>                                                                                                        
                </div>                                                                                                
               </div>                                                                                        
              </div>                                                                                
             </div>                                                                                
             <?php } ?>                                                                                  
             <?php if (count($this->warnings)> 0) { ?>                                                                                 
             <div id="warning_win">                                                                                        
                     <div>                                                                                                
                             <div>                                                                                                        
                                     <div>                                                                                                                  
                                             <?php foreach($this->warnings as $errmsg) { ?>                                                                                                                  
                                             <fieldset class="warn">                                                                                                                          
                                                     <legend>Warning:                                                                                                                         
                                                     </legend>                                                                                                                             
                                                     <?php echo htmlentities($errmsg); ?><br />                                                                                                                             
                                                     </fieldset>                                                                                                                          
                                                     <?php } ?>                                                                                                         
                                     </div>                                                                                                
                             </div>                                                                                        
                     </div>                                                                                
             </div>                                                                                
             <?php } ?>                                                                                     
             <?php if (count($this->notices)> 0) { ?>                                                                                 
             <div id="notice_win">                                                                                        
                     <div>                                                                                                
                             <div>                                                                                                        
                                     <div>                                                                                                                  
                                             <?php foreach($this->notices as $errmsg) { ?>                                                                                                                  
                                             <fieldset>                                                                                                                          
                                                     <legend>Notice:                                                                                                                         
                                                     </legend>                                                                                                                             
                                                     <?php echo htmlentities($errmsg); ?><br />                                                                                                                             
                                                     </fieldset>                                                                                                                          
                                                     <?php } ?>                                                                                                         
                                     </div>                                                                                                
                             </div>                                                                                        
                     </div>                                                                                
             </div>                                                                                
             <?php } ?>                                                                                
             <?php if (count($this->debug_notices)> 0) { ?>                                                                                
             <div id="debug_win">                                                                                        
              <div>                                                                                                
               <div>                                                                                                        
                <div>                                                                                                                   
                 <?php foreach($this->debug_notices as $errmsg) { ?>                                                                                                                  
                 <fieldset>                                                                                                                          
                         <legend>DEBUG:                                                                                                                         
                         </legend>                                                                                                                             
                         <?php echo htmlentities($errmsg); ?><br />                                                                                                                             
                 </fieldset>                                                                                                                          
                 <?php } ?>                                                                                                         
                </div>                                                                                                
               </div>                                                                                        
              </div>                                                                                
             </div>                                                                                  
             <?php } ?>
            </div>                                                                    
            <!-- End Right Status -->           
           </td>                                                                    
          </tr>          
          <tr class="panelh_bar">
           <td class="panel"><div class="bar"></div></td>
           <td class="panel_bar"><div class="clr_b"></div></td>
           <td class="info"><div class="bar"></div></td>
          </tr>                                                                              
          <tr class="terminal">                                                                             
           <td class="panel">
            <div id="left_panel">                                                                                     
             <!-- Begin Left Frame -->                                                                                  
             <table>                                                                                           
              <tr>                                                                                                
               <td id="leftframe" valign="top">                
                 <div class="login">
                  <vdoc:include app="user" widget="user" />
                  <vdoc:target name="left_column" />                  
                 </div>  
                </td>
               <td></td>                                                                                                        
              </tr>                                                                                  
             </table>                                                                                     
             <!-- End Left Frame -->
            </div>          
           </td>                                                                                  
          <td class="panel_bar">                                                                                
           <div class="clr_b"></div>
          </td>                                                                                      
          <td class="info">                                                                                           
           <!-- Begin Content -->                                                                                       
           <div id="content">                                                                                              
            <div class="content_body">
             <vdoc:target name="content_top" />                                                                                                      
             <vdoc:include alias="content" />
             <vdoc:target name="content_bottom" />                                                                                              
            </div>
            <div class="clr_b"></div>                                                                                             
           </div>
           <hr class="layout_bar" />
           <div id="footer">
                         <div>                                                                                                        
                                 <div>                                                                                                                
                                         <div>                                                                                                                                  
                                                 <p>Powered by the Virtual Web Platform - Copyright &copy; Ralph Ritoch 2010 - ALL RIGHTS RESERVED                                                                                                                         
                                                 </p>                                                                                                                      
                                         </div>                                                                                                        
                                 </div>                                                                                                
                         </div>                                                                                        
                 </div>                                                                                                                                                                           
         <!-- End Content -->          </td>                                                                    
                                                                </tr>                                                           
                                                        </tbody>                                                  
                                                </table>                                              
                                        </div>
                                        <div class="clr_b"></div>                                  
                                </div>
                                <div class="clr_b"></div>                            
                        </div>                        
                        <!-- end Wrapper -->                          
                        <div class="clr_b"></div>                
                </div>                
                <!-- end BG -->

 <script type="text/javascript" src="<?php echo htmlentities($this->theme_path); ?>/js/theme_footer.js"></script>                
 <?php  
 ?>        
 </body>
</html>