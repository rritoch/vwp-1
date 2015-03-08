<?php

/**
 * VWP - Default install form layout 
 *  
 * @package    VWP
 * @subpackage Layouts.Install
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @link http://www.vnetpublishing.com VNetPublishing.Com 
 * @copyright (c) Ralph Ritoch - All rights reserved
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

?>
<?php echo $this->menu; ?>
<h3>Application Installer</h3>
<br />
<?php

 $hbar = '<tr class="hbar">
   <td class="vbar"></td>
   <td colspan="3" class="hbar"></td>
   <td class="vbar"></td>
  </tr>';

?>
<form action="" method="post" id="install_from_package">

<table class="control_panel" id="install_from_package_control">
 <thead>
  <tr class="controls">
   <td colspan="5">
    <ul>
     <li class="submit"><div class="btn"><span><input type="submit" value="Go" id="install_from_package_go1" /></span></div></li>
     <li><a href="#install_from_package_go1"><span><input type="radio" name="task" value="install_from_package" /> Install</span></a></li>
    </ul>    
   </td>
  </tr>  
  <tr class="title">
   <td class="vbar"><div class="clr_b"></div></td>
   <td colspan="3" class="title"><h2>Install from package on server</h2></td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>
  <?php echo $hbar; ?> 
 </thead>
 <tbody>
  <tr class="data">
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="label">Package Filename</td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="data"><input type="text" size="60" name="package"></td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>
  <?php echo $hbar; ?> 
 </tbody>
 <tfoot>
  <tr class="controls">
   <td colspan="7">
    <ul>
     <li class="submit"><div class="btn"><span><input type="submit" value="Go" id="install_from_package_go2" /></span></div></li>
     <li><a href="#install_from_package_go2"><span><input type="radio" name="task" value="install_from_package" /> Install</span></a></li>
    </ul>    
   </td>
  </tr>  
 </tfoot>
</table>
<input type="hidden" name="app" value="vwp" />
<input type="hidden" name="widget" value="install" />
<input type="hidden" name="screen" value="<?php echo htmlentities($this->screen); ?>" />
</form>

<br />

<?php

 $hbar = '<tr class="hbar">
   <td class="vbar"></td>
   <td colspan="3" class="hbar"></td>
   <td class="vbar"></td>
  </tr>';

?>
<form action="" method="post" id="install_from_folder">

<table class="control_panel" id="install_from_folder_control">
 <thead>
  <tr class="controls">
   <td colspan="5">
    <ul>
     <li class="submit"><div class="btn"><span><input type="submit" value="Go" id="install_from_folder_go1" /></span></div></li>
     <li><a href="#install_from_folder_go1"><span><input type="radio" name="task" value="install_from_folder" /> Install</span></a></li>
    </ul>    
   </td>
  </tr>  
  <tr class="title">
   <td class="vbar"><div class="clr_b"></div></td>
   <td colspan="3" class="title"><h2>Install from server folder</h2></td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>
  <?php echo $hbar; ?> 
 </thead>
 <tbody>
  <tr class="data">
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="label">Folder Path</td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="data"><input type="text" size="60" name="folder"></td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>
  <?php echo $hbar; ?> 
 </tbody>
 <tfoot>
  <tr class="controls">
   <td colspan="7">
    <ul>
     <li class="submit"><div class="btn"><span><input type="submit" value="Go" id="install_from_folder_go2" /></span></div></li>
     <li><a href="#install_from_folder_go2"><span><input type="radio" name="task" value="install_from_folder" /> Install</span></a></li>
    </ul>    
   </td>
  </tr>  
 </tfoot>
</table>
<input type="hidden" name="app" value="vwp" />
<input type="hidden" name="widget" value="install" />
<input type="hidden" name="screen" value="<?php echo htmlentities($this->screen); ?>" />
</form>
<br />


<?php

 $hbar = '<tr class="hbar">
   <td class="vbar"></td>
   <td colspan="3" class="hbar"></td>
   <td class="vbar"></td>
  </tr>';

?>
<form action="" method="post" enctype="multipart/form-data" id="install_from_upload">

<table class="control_panel" id="install_from_upload_control">
 <thead>
  <tr class="controls">
   <td colspan="5">
    <ul>
     <li class="submit"><div class="btn"><span><input type="submit" value="Go" id="install_from_upload_go1" /></span></div></li>
     <li><a href="#install_from_upload_go1"><span><input type="radio" name="task" value="install_from_upload" /> Install</span></a></li>
    </ul>    
   </td>
  </tr>  
  <tr class="title">
   <td class="vbar"><div class="clr_b"></div></td>
   <td colspan="3" class="title"><h2>Upload and Install</h2></td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>
  <?php echo $hbar; ?> 
 </thead>
 <tbody>
  <tr class="data">
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="label">Select Package</td>
   <td class="vbar"><div class="clr_b"></div></td>
   <td class="data"><input type="file" size="20" name="pkg"></td>
   <td class="vbar"><div class="clr_b"></div></td>
  </tr>
  <?php echo $hbar; ?> 
 </tbody>
 <tfoot>
  <tr class="controls">
   <td colspan="7">
    <ul>
     <li class="submit"><div class="btn"><span><input type="submit" value="Go" id="install_from_upload_go2" /></span></div></li>
     <li><a href="#install_from_upload_go2"><span><input type="radio" name="task" value="install_from_upload" /> Install</span></a></li>
    </ul>    
   </td>
  </tr>  
 </tfoot>
</table>
<input type="hidden" name="app" value="vwp" />
<input type="hidden" name="widget" value="install" />
<input type="hidden" name="screen" value="<?php echo htmlentities($this->screen); ?>" />
</form>
<br />

<?php echo $this->menu_foot; ?>