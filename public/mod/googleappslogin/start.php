<?php

        /**
	 * Elgg googlelogin plugin
	 * 
	 * @package ElggGoogleLogin
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Andrey Pesoshin <andrey.pesoshin@flatsoft.com>
	 * @copyright FlatSoft 2009
	 * @link http://elgg.org/
	 */
	 
	 global $CONFIG;
	 
	/**
	 * googleappslogin initialisation
	 *
	 * These parameters are required for the event API, but we won't use them:
	 * 
	 * @param unknown_type $event
	 * @param unknown_type $object_type
	 * @param unknown_type $object
	 */
	function googleappslogin_init() {
		
		extend_view("account/forms/login", "googleappslogin/login");
		// Extend system CSS with our own styles
		extend_view('css','googleappslogin/css');
		register_plugin_hook('usersettings:save','user','googleappslogin_user_settings_save');
		if (!$_COOKIE['elgg_logout'] && $_COOKIE['elgg_redirect'] < 1) {
			setcookie('elgg_redirect', '1');
			//header('Location: /action/googleappslogin/login');
		}
	}
	
	function googleappslogin_pagesetup() {
		// make profile edit links invisible for googleapps accounts
		// that do not have googleapps control explicitly turned off
		if ((get_context() == 'profile') 
			&& ($page_owner_entity = page_owner_entity()) 
			&& ($page_owner_entity->getSubtype() == "googleapps")
			&& ($page_owner_entity->googleapps_controlled_profile != 'no')
		) {
			extend_view('metatags','googleappslogin/hide_profile_embed');
		}
		
		extend_elgg_settings_page('googleappslogin/settings/usersettings', 'usersettings/user');
	}
	
	function googleappslogin_logout() {
		setcookie('elgg_redirect', '0');
		setcookie('elgg_logout', mktime());
	}
	
	register_elgg_event_handler('init','system','googleappslogin_init');
	register_elgg_event_handler('pagesetup','system','googleappslogin_pagesetup');
	register_elgg_event_handler('logout', 'user', 'googleappslogin_logout');
	
	// TODO: remove this permissions hook if it turns out not to be necessary
	
	function googleappslogin_can_edit($hook_name, $entity_type, $return_value, $parameters) {
         
		$entity = $parameters['entity'];
		$context = get_context();
		if ($context == 'googleappslogin' && $entity->getSubtype() == "googleapps") {
			// should be able to do anything with googleapps user data
			return true;
		}
	return null;
	}
	
	function googleappslogin_icon_url($hook_name,$entity_type, $return_value, $parameters) {
		$entity = $parameters['entity'];
		if (($entity->getSubtype() == "googleapps") && ($entity->googleapps_controlled_profile != 'no')) {
			if (($parameters['size'] == 'tiny') || ($parameters['size'] == 'topbar')) {
				return $entity->googleapps_icon_url_mini;
			} else {
				return $entity->googleapps_icon_url_normal;
			}
		}
	}
	
	function googleappslogin_user_settings_save() {    	
		gatekeeper();
		
		$user = page_owner_entity();
		if (!$user) {    	
			$user = $_SESSION['user'];
		}
		
		$subtype = $user->getSubtype();
		
		if ($subtype == 'googleapps') {
			
			$googleapps_controlled_profile = get_input('googleapps_controlled_profile','yes');
			
			if ((!$user->googleapps_controlled_profile && ($googleapps_controlled_profile == 'no'))
				|| ($user->googleapps_controlled_profile && ($user->googleapps_controlled_profile != $googleapps_controlled_profile))
			) {    	
				$user->googleapps_controlled_profile = $googleapps_controlled_profile;	    
				system_message(elgg_echo('googleappslogin:googleapps_user_settings:save:ok'));
			}
		} else if (!$subtype) {
			
			// currently on users with no subtype (regular Elgg users) are allowed a
			// slave googleapps login
			$googleapps_screen_name = get_input('googleapps_screen_name');
			if ($googleapps_screen_name != $user->googleapps_screen_name) {
				$user->googleapps_screen_name = $googleapps_screen_name;
				system_message(elgg_echo('googleappslogin:googleapps_login_settings:save:ok'));
			}
		}
	}
	
	register_plugin_hook('permissions_check','user','googleappslogin_can_edit');
	register_plugin_hook('entity:icon:url','user','googleappslogin_icon_url');
	
	// Register actions
    global $CONFIG;
	
	register_action("googleappslogin/login", true, $CONFIG->pluginspath . "googleappslogin/actions/login.php");
	register_action("googleappslogin/return", true, $CONFIG->pluginspath . "googleappslogin/actions/return.php");
?>