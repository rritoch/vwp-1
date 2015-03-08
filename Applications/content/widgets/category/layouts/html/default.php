<?php 

/**
 * VWP Content Manager - Default Category Layout
 * 
 * @package VWP.Content
 * @subpackage Layouts.Category.HTML
 * @author Ralph Ritoch <rritoch@gmail.com>
 * @link http://www.vnetpublishing.com VNetPublishing.Com 
 * @copyright (c) Ralph Ritoch 2011 - All rights reserved
 * @license http://www.vnetpublishing.com/Legal/Licenses/2010/10/vnetlpl.txt VNETLPL Limited Public License  
 */

?>
<h4><?php echo $this->category["name"]; ?></h4>
<?php foreach ($this->articles as $article) { ?>
<div class="article">
<h5 class="title"><?php echo htmlentities($article["title"]); ?></h5>
<?php echo $article["content"]; ?>
</div>
<?php } 

foreach($this->sub_categories as $cat) { ?>
<p><a href="index.php?app=content&widget=category&category=<?php echo htmlentities($cat["id"]); ?>"><?php echo htmlentities($cat["name"]); ?></a></p>
<?php 
 }
?>