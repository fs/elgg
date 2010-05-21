<?php
/*******************************************************************************
 * Main Admin page with tabs
 * 
 * 
 * 
 * @package Elgg Developer Tools
 * @author Cash Costello
 ******************************************************************************/

	global $CONFIG;
	
	$tab = $vars['tab'];
	if (!$tab)
		$tab = 'settings';
		
	$settingsselect = 'class="edt_tab_nav"'; 
	$builderselect = 'class="edt_tab_nav"';
	$inspectselect = 'class="edt_tab_nav"';
	switch($tab) {
		case 'builder':
			$builderselect = 'class="selected edt_tab_nav"';
			break;
		case 'inspect':
			$inspectselect = 'class="selected edt_tab_nav"';
			break;
			case 'settings':
		default:
			$settingsselect = 'class="selected edt_tab_nav"';
			break;
	}
	
?>
<div class="contentWrapper">
	<div id="elgg_horizontal_tabbed_nav">
		<ul>
			<li id="edt_settings_nav" <?php echo $settingsselect; ?>><a href="javascript:edtSwitchTab('edt_settings')"><?php echo elgg_echo('elgg_dev_tools:settings'); ?></a></li>
			<li id="edt_builder_nav" <?php echo $builderselect; ?>><a href="javascript:edtSwitchTab('edt_builder')"><?php echo elgg_echo('elgg_dev_tools:builder'); ?></a></li>
			<li id="edt_inspect_nav" <?php echo $inspectselect; ?>><a href="javascript:edtSwitchTab('edt_inspect')"><?php echo elgg_echo('elgg_dev_tools:inspect'); ?></a></li>
		</ul>
	</div>
	<br />

	<div id="edt_settings_tab" class="elgg_dev_tools_tab">
<?php 
	echo elgg_view("elgg_dev_tools/settings");
?>
	</div>
	<div id="edt_builder_tab" class="elgg_dev_tools_tab">
<?php 
	echo elgg_view("elgg_dev_tools/builder");
?>
	</div>
	<div id="edt_inspect_tab" class="elgg_dev_tools_tab">
<?php 
	echo elgg_view("elgg_dev_tools/inspect");
?>
	</div>
	
</div>
<script type="text/javascript">
$(document).ready(function() {
	$("#edt_<?php echo $tab; ?>_tab").show();
});

function edtSwitchTab(tab_id)
{
	var nav_name = "#" + tab_id + "_nav";
	var tab_name = "#" + tab_id + "_tab";
	
	$(".elgg_dev_tools_tab").hide();
	$(tab_name).show();
	$(".edt_tab_nav").removeClass("selected");
	$(nav_name).addClass("selected");
}
</script>