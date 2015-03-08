<?php

/**
 * VWP Content Manager - Default Article Layout
 * 
 * @package VWP.Content
 * @subpackage Layouts.Article.HTML
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @link http://www.vnetpublishing.com VNetPublishing.Com 
 * @copyright (c) Ralph Ritoch 2011 - All rights reserved
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

?>
<div class="article">
<?php if ($this->display_title) { ?>
<h4 class="title"><?php echo htmlentities($this->article["title"]); ?></h4>
<?php } ?>
<?php echo $this->article["content"]; ?>
</div>