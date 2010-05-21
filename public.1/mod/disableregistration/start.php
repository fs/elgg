<?php
	/**
	 * DisableRegistration start.php
	 * 
	 * @package DisableRegistration
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Jeff Tilson
	 * @copyright THINK Global School 2010
	 * @link http://www.thinkglobalschool.com/
	 * 
	 */

	// Init
	function disableregistration_init()
	{
		global $CONFIG;

		// Disable registration
		$CONFIG->disable_registration = true; 

		register_plugin_hook('action', 'register', 'disable_register_hook');
		register_page_handler('register', 'disable_registration_page_handler');
		
		return true;
	}

	// Plugin hook to disable registration
	function disable_register_hook($hook, $type, $returnvalue, $params) {
		disable();
	}
	
	// Override for the registration page handler
	function disable_registration_page_handler() {
		disable();
	}
	
	// Displays error message and forwards
	function disable() {
		register_error("Registration Disabled");
		forward();
	}
	
	register_elgg_event_handler('init', 'system', 'disableregistration_init');
?>