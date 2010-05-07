<?php

	/**
	 * Elgg river dashboard plugin
	 * 
	 * @package ElggRiverDash
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Curverider Ltd <info@elgg.com>
	 * @copyright Curverider Ltd 2008-2010
	 * @link http://elgg.org/
	 */

		function tgsdashboard_init() {
		
			global $CONFIG;
			
			// Register and optionally replace the dashboard
			if (get_plugin_setting('useasdashboard', 'tgsdashboard') == 'yes') {
				register_page_handler('dashboard','tgsdashboard_page_handler');
			} else {
				// Activity main menu
				if (isloggedin())
				{
					add_menu(elgg_echo('activity'), $CONFIG->wwwroot . "mod/tgsdashboard/");
				}
			}
		
			// Page handler
			register_page_handler('tgsdashboard','tgsdashboard_page_handler');
			
			elgg_extend_view('css','tgsdashboard/css');
			
			add_widget_type('river_widget',elgg_echo('river:widget:title'), elgg_echo('river:widget:description'));
			
		}
		
		/**
		 * Page handler for riverdash
		 *
		 * @param unknown_type $page
		 */
		function tgsdashboard_page_handler($page)
		{
			global $CONFIG;
			
			include(dirname(__FILE__) . "/index.php");
			return true;
		}
		
		function tgsdashboard_dashboard() {
			
			include(dirname(__FILE__) . '/index.php');
			
		}

		register_elgg_event_handler('init','system','tgsdashboard_init');
		
	// Register actions
		global $CONFIG;
		register_action("tgsdashboard/add",false,$CONFIG->pluginspath . "tgsdashboard/actions/add.php");
		register_action("tgsdashboard/delete",false,$CONFIG->pluginspath . "tgsdashboard/actions/delete.php");


?>