<?php
	/**
	*  System Tag Cloud view for TGS dashboard
	*
	*/
	
	
	/** 
	* Function to compare tag objects 
	* based on the 'tag' property for use
	* with usort();
	*/
	function compare_tag_objects($a, $b) {
		$a = strtolower($a->tag);
		$b = strtolower($b->tag);  
		return strcmp($a, $b);
	}
	
	$mintagcount = get_plugin_setting('mintagcount','tgsdashboard');	
	$site_tags = array('value' => get_tags($mintagcount,10000, "tags"));
	
	// Sort tags alphabetically 
	usort($site_tags['value'], "compare_tag_objects");	
?>


<div class="sidebarBox">
<h3><?php echo elgg_echo('tgsdashboard:tagcloud:title') ?></h3>

<?php echo elgg_view("output/tagcloud", $site_tags); ?>

<div class="clearfloat"></div>
</div>