<?php

/**
 * VWP - Default application list layout
 * 
 * @package    VWP
 * @subpackage Layouts.AppMgr
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @link http://www.vnetpublishing.com VNetPublishing.Com 
 * @copyright (c) Ralph Ritoch - All rights reserved
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License   
 */

?>
<?php echo $this->menu; ?>
<?php

 $hbar = '<tr class="hbar">
   <td class="vbar"></td>
   <td colspan="13" class="hbar"></td>
   <td class="vbar"></td>
  </tr>';

?>
<form action="" method="post" id="applications">

<table class="control_panel" id="applications_control">
 <thead>
  <tr class="controls">
   <td colspan="15">
    <ul>
     <li class="submit"><div class="btn"><span><input type="submit" value="Go" id="application_go1" /></span></div></li>
     <li><a href="#applications_go1"><span><input type="radio" name="task" value="uninstall_app" /> Uninstall</span></a></li>
    </ul>    
   </td>
  </tr>  
  <tr class="title">
   <td class="vbar"><div class="clr_b"></div></td>
   <td colspan="13" class="title"><h2>Installed Applications</h2></td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>
  <?php echo $hbar; ?> 
  <tr class="col_titles">
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="header" id="eselect"><input type="checkbox" name="ckall" onClick="javascript:columnToggleCheckAll('eselect');" /></td>  
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="header">ID</td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="header">Name</td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="header">Version</td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="header">Release Date</td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="header">Author</td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="header">Web Site</td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>
  <?php echo $hbar; ?>       
 </thead>
 <tbody>
<?php if (count($this->application_list) > 0) { 
  foreach($this->application_list as $app) { ?>

  <tr class="data">
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="data ctr"><input type="checkbox" name="ck[<?php echo htmlentities($app["id"]); ?>]" /></td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="data"><?php echo htmlentities($app["id"]); ?></td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="data"><?php echo htmlentities($app["name"]); ?></td>

   <td class="vbar"><div class="clr_b"></div></td>
   <td class="data"><?php echo htmlentities($app["version"]); ?></td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="data"><?php echo htmlentities($app["version_release_date"]); ?></td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="data"><?php echo htmlentities($app["author"]); ?></td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="data"><?php echo htmlentities($app["author_link"]); ?></td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>
  <?php echo $hbar; ?> 
<?php  }
 } else { ?> 
<tr class="data">
 <td class="vbar"><div class="clr_b"></div></td>
 <td class="data" colspan="13">No Applications Installed</td>
 <td class="vbar"><div class="clr_b"></div></td>
</tr>
<?php echo $hbar; ?>
<?php } ?>
 </tbody>
 <tfoot>
  <tr class="controls">
   <td colspan="15">
    <ul>
     <li class="submit"><div class="btn"><span><input type="submit" value="Go" id="application_go2" /></span></div></li>
     <li><a href="#application_go2"><span><input type="radio" name="task" value="uninstall_app" /> Uninstall</span></a></li>
    </ul>    
   </td>
  </tr>  
 </tfoot>
</table>
<input type="hidden" name="app" value="vwp" />
<input type="hidden" name="widget" value="appmgr" />
<input type="hidden" name="eid" value="" />
<input type="hidden" name="arg1" value="" />
</form>

<?php echo $this->menu_foot; ?>