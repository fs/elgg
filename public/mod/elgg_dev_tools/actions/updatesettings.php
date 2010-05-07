<?php
/*******************************************************************************
 * Update Elgg Dev Tools Setttings
 * 
 * This applies the changes from the Elgg admin form.  Normally, you would dev
 * with a bit more security for default settings - but I'm assuming you - the dev
 * kinda know what you're doing
 * 
 * @package Elgg Developer Tools
 * @author Aaron Saray (102degrees.com)
 * @author Cash Costello
 ******************************************************************************/


	admin_gatekeeper();
	action_gatekeeper();

	
	/** enable firephp **/
	set_plugin_setting('enablefirephp', get_input('enablefirephp'), 'elgg_developer_tools');
	
	/** display errors **/
	set_plugin_setting('displayerrors', get_input('displayerrors'), 'elgg_developer_tools');

	/** enable/disable simple cache **/
	if (get_input('usesimplecache')) {
		elgg_view_enable_simplecache();
	} else {
		elgg_view_disable_simplecache();
	}
	
	// enable/disable views cache
	if (get_input('useviewscache')) {
		elgg_enable_filepath_cache();
	} else {
		elgg_disable_filepath_cache();
	}
	
	/** enable/disable debug **/
	if (get_input('debug')) {
		set_config('debug', 1);
	}
	else {
		unset_config('debug');
	}
	
	/** timing **/
	set_plugin_setting('timing', get_input('timing'), 'elgg_developer_tools');
	
	/** timing **/
	set_plugin_setting('showviews', get_input('showviews'), 'elgg_developer_tools');
	
	
	/** set update message and redirect cuz we're done **/
	system_message(elgg_echo("elgg_dev_tools:message:successfulupdate"));
	forward($CONFIG->wwwroot . 'pg/elgg_dev_tools/');
?>