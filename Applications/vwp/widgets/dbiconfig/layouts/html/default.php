<?php

/**
 * VWP - Database list layout
 * 
 * @package    VWP
 * @subpackage Layouts.DBIConfig
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @link http://www.vnetpublishing.com VNetPublishing.Com 
 * @copyright (c) Ralph Ritoch - All rights reserved
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License   
 */

?>
<?php echo $this->menu ?>
<?php

 $hbar = '<tr class="hbar">
   <td class="vbar"></td>
   <td colspan="11" class="hbar"></td>
   <td class="vbar"></td>
  </tr>';

?>
<form action="" method="post" id="databases">

<table class="control_panel" id="databases_control">
 <thead>
  <tr class="controls">
   <td colspan="13">
    <ul>
     <li class="submit"><div class="btn"><span><input type="submit" value="Go" id="databases_go1" /></span></div></li>
     <li><a href="#databases_go1"><span><input type="radio" name="task" value="create_db" /> New</span></a></li>
     <li><a href="#databases_go1"><span><input type="radio" name="task" value="edit_db" /> Edit</span></a></li>
    </ul>    
   </td>
  </tr>  
  <tr class="title">
   <td class="vbar"><div class="clr_b"></div></td>
   <td colspan="11" class="title"><h2>Database Configuration</h2></td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>
  <?php echo $hbar; ?> 
  <tr class="col_titles">
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="header" id="eselect"><input type="checkbox" name="ckall" onClick="javascript:columnToggleCheckAll('eselect');" /></td>  
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="header">Name</td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="header">Type</td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="header">Server</td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="header">User</td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="header">Database</td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>
  <?php echo $hbar; ?>       
 </thead>
 <tbody> 
 
<?php if (count($this->databases) > 0) { ?> 
<?php foreach($this->databases as $dbinfo) { ?>
 <tr>
  <tr class="data">
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="data ctr"><input type="checkbox" name="ck[<?php echo htmlentities($dbinfo["_id"]); ?>]" /></td>
   <td class="vbar"><div class="clr_b"></div></td> 
   <td class="data"><?php echo htmlentities($dbinfo["_id"]); ?></td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="data"><?php echo htmlentities($dbinfo["_type"]); ?></td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="data"><?php echo htmlentities($dbinfo["server"]); ?></td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="data"><?php echo htmlentities($dbinfo["username"]); ?></td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="data"><?php echo htmlentities($dbinfo["database"]); ?></td>
   <td class="vbar"><div class="clr_b"></div></td>
 </tr>
 <?php echo $hbar; ?>
<?php } ?> 
<?php } else { ?>
<tr class="data">
 <td class="vbar"><div class="clr_b"></div></td>
 <td class="data" colspan="11">No Databases</td>
 <td class="vbar"><div class="clr_b"></div></td>
</tr>
<?php echo $hbar; ?>
<?php } ?>
 </tbody>
 <tfoot>
  <tr class="controls">
   <td colspan="13">
    <ul>
     <li class="submit"><div class="btn"><span><input type="submit" value="Go" id="databases_go2" /></span></div></li>
     <li><a href="#databases_go2"><span><input type="radio" name="task" value="create_db" /> New</span></a></li>
     <li><a href="#databases_go2"><span><input type="radio" name="task" value="edit_db" /> Edit</span></a></li>
    </ul>    
   </td>
  </tr>
  </tfoot>  
</table>
<input type="hidden" name="app" value="vwp" />
<input type="hidden" name="widget" value="dbiconfig" />
<input type="hidden" name="screen" value="<?php echo htmlentities($this->screen); ?>" />
</form>
<?php echo $this->menu_foot ?>