<?php
/*******************************************************************************
 * Build a plugin skeleton
 * 
 * 
 * @package Elgg Developer Tools
 * @author Cash Costello
 ******************************************************************************/

	require_once dirname(dirname(__FILE__)) . '/includes/PluginBuilder.php';

	global $CONFIG;
	
	admin_gatekeeper();
	action_gatekeeper();

	// get form inputs
	$params = $_REQUEST['params'];
		
	// cache inputs in case something doesn't validate or something goes wrong in the creation process
	$_SESSION['build_plugin'] = $params;
		
	// at minimum we need plugin name
	if (!$params['plugin_name'])
	{
		register_error(elgg_echo('elgg_dev_tools:error:nopluginname'));
		forward('pg/elgg_dev_tools/builder/?tab=builder');
	}
	$params['plugin_name'] = trim($params['plugin_name']);
	
	// add author name to params array
	$user = get_loggedin_user();
	$params['author'] = $user->name;
	
	$base_dir = $CONFIG->pluginspath;
	$template_dir = $CONFIG->pluginspath . "elgg_dev_tools/templates/";
	
	// create the builder object and create the plugin - the object handles errors
	$builder = new PluginBuilder();
	$builder->build($base_dir, $template_dir, $params);
		
	// clear cached inputs
	unset($_SESSION['build_plugin']);
		
	system_message(sprintf(elgg_echo("elgg_dev_tools:message:successfulbuild"), $plugin_name));
	forward($CONFIG->wwwroot . 'pg/admin/plugins/');
?>