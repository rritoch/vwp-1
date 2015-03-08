<?php 
/**
 * VWP - Default validate layout
 * 
 * @package    VWP
 * @subpackage Layouts.Validate
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @link http://www.vnetpublishing.com VNetPublishing.Com 
 * @copyright (c) Ralph Ritoch - All rights reserved
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License   
 */

?>
<?php if (!$this->validated) { ?>
<h4>Validation</h4>
<p><b>Invalid Configuration!</b></p>
<p>[<a href="<?php echo $this->escape($this->configUrl); ?>">configure</a>]</p>
<?php } ?>