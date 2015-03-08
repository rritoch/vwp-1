<?php

/**
 * VWP - Default event list layout
 * 
 * @package    VWP
 * @subpackage Layouts.EventMgr
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
   <td colspan="9" class="hbar"></td>
   <td class="vbar"></td>
  </tr>';

?>
<form action="" method="post" id="events">

<table class="control_panel" id="events_control">
 <thead>
  <tr class="controls">
   <td colspan="11">
    <ul>
     <li class="submit"><div class="btn"><span><input type="submit" value="Go" id="events_go1" /></span></div></li>
     <li><a href="#events_go1"><span><input type="radio" name="task" value="save_order" /> Save Order</span></a></li>     
     <li><a href="#events_go1"><span><input type="radio" name="task" value="disable_events" /> Disable</span></a></li>
     <li><a href="#events_go1"><span><input type="radio" name="task" value="enable_events" /> Enable</span></a></li>

    </ul>    
   </td>
  </tr>  
  <tr class="title">
   <td class="vbar"><div class="clr_b"></div></td>
   <td colspan="9" class="title"><h2>Events</h2></td>
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
   <td class="header">Ordering</td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="header">Active</td>
   <td class="vbar"><div class="clr_b"></div></td>

  </tr>
  <?php echo $hbar; ?>       
 </thead>
 <tbody>
<?php if (count($this->event_list) > 0) { 
  foreach($this->event_list as $event) { 
   $fldName = '_order[' .htmlentities($event["_id"]) . ']';
   
   $order = '<input class="order" type="text" onload="javascript:alert(\'here\')" size="3" name="'.$fldName.'" value="' . htmlentities($event["_order"]).'" />'
           . '<script type="text/javascript"><!-- 
              addMoveButtons(\'events\',\'move_event\',\'' . $event["_id"] . '\',\'' . $fldName . '\',\'eid\',\'arg1\');
              // --></script>';  
  
  ?>
  <tr class="data">
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="data ctr"><input type="checkbox" name="ck[<?php echo htmlentities($event["_id"]); ?>]" /></td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="data"><?php echo htmlentities($event["name"]); ?></td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="data"><?php echo htmlentities($event["type"]); ?></td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="data"><?php echo $order; ?></td>
   <td class="vbar"><div class="clr_b"></div></td>   
   <td class="data"><?php echo $event["_enabled"] ? "Enabled" : "Disabled" ; ?></td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>
  <?php echo $hbar; ?> 
<?php  }
 } else { ?> 
<tr class="data">
 <td class="vbar"><div class="clr_b"></div></td>
 <td class="data" colspan="9">No Events</td>
 <td class="vbar"><div class="clr_b"></div></td>
</tr>
<?php } ?>
 </tbody>
 <tfoot>
  <tr class="controls">
   <td colspan="11">
    <ul>
     <li class="submit"><div class="btn"><span><input type="submit" value="Go" id="events_go2" /></span></div></li>
     <li><a href="#events_go2"><span><input type="radio" name="task" value="enable_events" /> Enable</span></a></li>
     <li><a href="#events_go2"><span><input type="radio" name="task" value="disable_events" /> Disable</span></a></li>
     <li><a href="#events_go2"><span><input type="radio" name="task" value="save_order" /> Save Order</span></a></li>     
    </ul>    
   </td>
  </tr>  
 </tfoot>
</table>
<input type="hidden" name="app" value="vwp" />
<input type="hidden" name="widget" value="eventmgr" />
<input type="hidden" name="eid" value="" />
<input type="hidden" name="arg1" value="" />
<input type="hidden" name="screen" value="<?php echo htmlentities($this->screen); ?>" />
</form>
<?php echo $this->menu_foot ?>