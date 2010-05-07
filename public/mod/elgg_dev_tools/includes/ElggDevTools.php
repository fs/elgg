<?php
/*******************************************************************************
 * Elgg Developer Tools Main Logic
 * 
 * This page holds the Elgg Developer Tools Main Logic class
 * 
 * @package Elgg Developer Tools
 * @author Aaron Saray (102degrees.com)
 * @author Cash Costello
 ******************************************************************************/

/**
 * Elgg Developer Tools Main Logic 
 * 
 * 
 */
class ElggDevTools 
{
	/**
	 * Launcher executes functionality on plugin init  
	 * 
	 * Launcher is responsible for querying the settings and running anything
	 * that is demanded to run initially.
	 */
	public static function launcher()
	{
		$displayerrors = get_plugin_setting('displayerrors', 'elgg_developer_tools');
		if ($displayerrors) ini_set('display_errors', 1);
		        
		/** include firePHP if need be **/
		$firephp = get_plugin_setting('enablefirephp', 'elgg_developer_tools');
		if ($firephp) {
			require_once dirname(__FILE__) . '/firephp/FirePHP.class.php';
			require_once dirname(__FILE__) . '/firephp/fb.php';
		}
		else {
			require_once dirname(__FILE__) . '/FirePHPDisabled.php';
		}
		
		$timing = get_plugin_setting('timing', 'elgg_developer_tools');
		if ($timing)
			register_elgg_event_handler('shutdown', 'system', 'elgg_dev_tools_shutdown_hook');
		
		$showviews = get_plugin_setting('showviews', 'elgg_developer_tools');
		if ($showviews)
			register_plugin_hook('display', 'view', 'elgg_dev_tools_outline_views'); 
	}
}
?>