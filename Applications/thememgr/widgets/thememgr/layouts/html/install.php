<?php 

/**
 * Theme Manager - Default Install Form 
 *  
 * @package    VWP.Thememgr
 * @subpackage Widgets.ThemeMgr
 * @author Ralph Ritoch <rritoch@gmail.com> 
 * @copyright (c) Ralph Ritoch - All rights reserved
 * @link http://www.vnetpublishing.com
 */

?>
<?php echo $this->tabs; ?>

<?php
 
 $hbar = '<tr class="hbar">
   <td class="vbar"></td>
   <td colspan="3" class="hbar"></td>
   <td class="vbar"></td>
  </tr>';

?>

<form action="" method="post" id="theme_install_control">
<table id="theme_install" class="control_panel">
 <thead>  
  <tr class="controls">
   <td colspan="5">
    <ul>
     <li class="submit"><div class="btn"><span><input type="submit" value="Go" id="theme_install_go1" /></span></div></li>
     <li><a href="#theme_install_go1"><span><input type="radio" name="task" value="" /> Close</span></a></li>     
    </ul>
    
   </td>
  </tr>
  <tr class="title">
   <td class="vbar"><div class="clr_b"></div></td>
   <td colspan="3" class="title"><h2>Theme Installer</h2></td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>
  <?php echo $hbar; ?>
 </thead>
 <tbody>
 </tbody>
 <tfoot>
  <tr class="controls">
   <td colspan="5">
    <ul>
     <li class="submit"><div class="btn"><span><input type="submit" id="theme_install_go2" value="Go" /></span></div></li>
     <li><a href="#theme_install_go2"><span><input type="radio" name="task" value="" /> Close</span></a></li>
    </ul>
   </td>   
  </tr>
 </tfoot>
</table>
</form> 


<br />

<?php
 
 $hbar = '<tr class="hbar">
   <td class="vbar"></td>
   <td colspan="3" class="hbar"></td>
   <td class="vbar"></td>
  </tr>';

?>

<form action="" method="post" id="local_package_install_control">
<table id="local_package_install" class="control_panel">
 <thead>  
  <tr class="controls">
   <td colspan="5">
    <ul>
     <li class="submit"><div class="btn"><span><input type="submit" value="Go" id="local_package_install_go1" /></span></div></li>     
     <li><a href="#local_package_install_go1"><span><input type="radio" name="task" value="install_from_package" /> Install</span></a></li>     
    </ul>
    
   </td>
  </tr>
  <tr class="title">
   <td class="vbar"><div class="clr_b"></div></td>
   <td colspan="3" class="title"><p>Install from local package</p></td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>
  <?php echo $hbar; ?>
 </thead>
 <tbody>
 <tr class="data">
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="label">Package File</td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="data"><input type="text" name="package" /></td>
   <td class="vbar"><div class="clr_b"></div></td>
 </tbody>
 <tfoot>
  <tr class="controls">
   <td colspan="5">
    <ul>
     <li class="submit"><div class="btn"><span><input type="submit" id="local_package_install_go2" value="Go" /></span></div></li>     
     <li><a href="#local_package_install_go2"><span><input type="radio" name="task" value="install_from_package" /> Install</span></a></li>     
    </ul>
    
   </td>   
  </tr>
 </tfoot>
</table>
</form> 
<br />

<?php
 
 $hbar = '<tr class="hbar">
   <td class="vbar"></td>
   <td colspan="3" class="hbar"></td>
   <td class="vbar"></td>
  </tr>';

?>

<form action="" method="post" id="local_folder_install_control">
<table id="local_folder_install" class="control_panel">
 <thead>  
  <tr class="controls">
   <td colspan="5">
    <ul>
     <li class="submit"><div class="btn"><span><input type="submit" value="Go" id="local_folder_install_go1" /></span></div></li>     
     <li><a href="#local_folder_install_go1"><span><input type="radio" name="task" value="install_from_folder" /> Install</span></a></li>     
    </ul>
    
   </td>
  </tr>
  <tr class="title">
   <td class="vbar"><div class="clr_b"></div></td>
   <td colspan="3" class="title"><p>Install from local folder</p></td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>
  <?php echo $hbar; ?>
 </thead>
 <tbody>
 <tr class="data">
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="label">Folder</td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="data"><input type="text" name="folder" /></td>
   <td class="vbar"><div class="clr_b"></div></td>
 </tbody>
 <tfoot>
  <tr class="controls">
   <td colspan="5">
    <ul>
     <li class="submit"><div class="btn"><span><input type="submit" id="local_folder_install_go2" value="Go" /></span></div></li>     
     <li><a href="#local_folder_install_go2"><span><input type="radio" name="task" value="install_from_folder" /> Install</span></a></li>     
    </ul>
    
   </td>   
  </tr>
 </tfoot>
</table>
</form>

<br />
<?php echo $this->tabs_foot; ?>