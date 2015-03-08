<?php 
  

?>
<script type="text/javascript" src="<?php echo $this->script_url; ?>"></script>
<script type="text/javascript">
	// Default skin
	tinyMCE.init({
		// General options
		mode : "exact",
		base : "<?php echo $this->base_url; ?>",
		elements : "<?php echo htmlentities($this->editor["name"]); ?>",
		
		plugins : "pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,inlinepopups,autosave",

		// Theme options
		theme : "advanced",
<?php $ctr = 1; foreach($this->editor["theme_advanced_buttons"] as $btngroup) { ?>
		theme_advanced_buttons<?php echo $ctr; ?> : "<?php echo is_array($btngroup) ? htmlentities(implode(",",$btngroup)) : htmlentities($btngroup); ?>",
<?php $ctr++; } ?>
		theme_advanced_toolbar_location : "<?php echo htmlentities($this->editor["theme_advanced_toolbar_location"]); ?>",
		theme_advanced_toolbar_align : "<?php echo htmlentities($this->editor["theme_advanced_toolbar_align"]); ?>",
		theme_advanced_statusbar_location : "<?php echo htmlentities($this->editor["theme_advanced_statusbar_location"]); ?>",
		theme_advanced_resizing : <?php echo $this->editor["theme_advanced_resizing"] ? 'true' : 'false'; ?>,

<?php if (isset($this->timymce_cfg['css'])) { ?>				
		// Example content CSS (should be your site CSS)
				
		content_css : "<?php echo $this->tinymce_cfg['css']; ?>",
		
<?php } ?>
		// Drop lists for link/image/media/template dialogs
		template_external_list_url : "<?php echo $this->editor["template_external_list_url"]; ?>",
		external_link_list_url : "<?php echo $this->editor["external_link_list_url"]; ?>",
		external_image_list_url : "<?php echo $this->editor["external_image_list_url"]; ?>",
		media_external_list_url : "<?php echo $this->editor["media_external_list_url"]; ?>",

		// Replace values for the template plugin
		template_replace_values : {
			username : "Some User",
			staffid : "991234"
		}
	});
	
</script>

<textarea id="<?php echo $this->editor["name"]; ?>" name="<?php echo $this->editor["name"]; ?>" rows="15" cols="80" style="width: <?php echo $this->width; ?>"><?php echo htmlentities($this->editor["value"]); ?></textarea>	