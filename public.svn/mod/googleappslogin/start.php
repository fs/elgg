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
		
		$googleappslogin_url = elgg_validate_action_url('https://' . $_SERVER['HTTP_HOST'] . '/action/googleappslogin/login');
		$googleappsconnect_url = elgg_validate_action_url('https://' . $_SERVER['HTTP_HOST'] . '/action/googleappslogin/connect');
		$googleappsdisconnect_url = elgg_validate_action_url('http://' . $_SERVER['HTTP_HOST'] . '/action/googleappslogin/disconnect');
		$oauth_update_url = elgg_validate_action_url('https://' . $_SERVER['HTTP_HOST'] . '/action/googleappslogin/oauth_update');
		
		$GLOBALS['googleappslogin_url'] = $googleappslogin_url;
		$GLOBALS['googleappsconnect_url'] = $googleappsconnect_url;
		$GLOBALS['googleappsdisconnect_url'] = $googleappsdisconnect_url;
		$GLOBALS['oauth_update_url'] = $oauth_update_url;
		$GLOBALS['oauth_update_interval'] = get_plugin_setting('oauth_update_interval', 'googleappslogin');
		
		//$body = elgg_view("blogs/list", array('googleappslogin_url' => $googleappslogin_url));
		//elgg_extend_view("account/forms/login", $body);
		elgg_extend_view('account/forms/login', 'googleappslogin/login');
		elgg_extend_view('messages/list', 'googleappslogin/scripts');
		// Extend system CSS with our own styles
		elgg_extend_view('css','googleappslogin/css');
		elgg_extend_view('elgg_topbar/extend','googleappslogin/new_mail');
		register_plugin_hook('usersettings:save','user','googleappslogin_user_settings_save');
		/*
		if (!$_COOKIE['elgg_logout'] && $_COOKIE['elgg_redirect'] < 1) {
			setcookie('elgg_redirect', '1');
			//header('Location: /action/googleappslogin/login');
		}
		*/
	}
	
	function googleappslogin_pagesetup() {
		
		// make profile edit links invisible for googleapps accounts
		// that do not have googleapps control explicitly turned off
		if ((get_context() == 'profile') 
			&& ($page_owner_entity = page_owner_entity()) 
			&& ($page_owner_entity->google == 1)
			&& ($page_owner_entity->googleapps_controlled_profile != 'no')
		) {
			elgg_extend_view('metatags','googleappslogin/hide_profile_embed');
		}
		
		extend_elgg_settings_page('googleappslogin/settings/usersettings', 'usersettings/user');
	}
	
	function googleappslogin_logout() {
		
		//setcookie('elgg_redirect', '0');
		//setcookie('elgg_logout', mktime());
	}
	
	function googleappslogin_login() {
		
		$user = $_SESSION['user'];
		if (!empty($_SESSION['logged_with_openid']) && !empty($user) && 
			($user->googleapps_sync_email != 'no' || $user->googleapps_sync_sites != 'no')) {
			googleappslogin_get_oauth_data();
		}
	}
	
	function googleappslogin_can_edit($hook_name, $entity_type, $return_value, $parameters) {
         
		$entity = $parameters['entity'];
		$context = get_context();
		if ($context == 'googleappslogin' && $entity->google == 1) {
			// should be able to do anything with googleapps user data
			return true;
		}
	return null;
	}
	
	function googleappslogin_icon_url($hook_name,$entity_type, $return_value, $parameters) {
		
		$entity = $parameters['entity'];
		if (($entity->google == 1)) {
			if (($parameters['size'] == 'tiny') || ($parameters['size'] == 'topbar')) {
				return $entity->googleapps_icon_url_mini;
			} else {
				return $entity->googleapps_icon_url_normal;
			}
		}
	}
	
	function googleappslogin_user_settings_save() {
		
		gatekeeper();
		
		$googleapps_controlled_profile = strip_tags(get_input('googleapps_controlled_profile'));
		$googleapps_sync_email = strip_tags(get_input('googleapps_sync_email'));
		$googleapps_sync_sites = strip_tags(get_input('googleapps_sync_sites'));
		$googleapps_sites_settings = $_POST['googleapps_sites_settings'];
		
		$user_id = get_input('guid');
		$user = "";
		$error = false;
		$synchronize = false;
		
		if (!$user_id) {
			$user = $_SESSION['user'];
		} else {
			$user = get_entity($user_id);
		}
		$subtype = $user->getSubtype();
		
		if ($user->google == 1) {
			
			if ($googleapps_controlled_profile == 'no' && empty($user->password)) {
				register_error(sprintf(elgg_echo('googleappslogin:googleappserror'), 'Please provide your password before you stop synchronizing with googleapps.'));
				forward($_SERVER['HTTP_REFERER']);
			}
			
			if (elgg_strlen($googleapps_controlled_profile) > 50) {
				register_error(elgg_echo('admin:configuration:fail'));
				forward($_SERVER['HTTP_REFERER']);
			}
			
			if (($user) && ($user->canEdit())) {
				if ($googleapps_controlled_profile != $user->googleapps_controlled_profile) {
					$user->googleapps_controlled_profile = $googleapps_controlled_profile;
					if (!$user->save()) {
						$error = true;
					}
				}
				
				if (!empty($googleapps_sites_settings)) {
					$site_list = unserialize($user->site_list);
					foreach ($googleapps_sites_settings as $title => $access) {
						$site_list[$title] = $access;
					}
					$user->site_list = serialize($site_list);
					$user->save();
				}
				
				if ($googleapps_sync_email != $user->googleapps_sync_email) {
					$user->googleapps_sync_email = $googleapps_sync_email;
					if (!$user->save()) {
						$error = true;
					} else {
						
						if ($user->googleapps_sync_email == 'yes') {
							$synchronize = true;
						}
						
					}
				}
				
				if ($googleapps_sync_sites != $user->googleapps_sync_sites) {
					$user->googleapps_sync_sites = $googleapps_sync_sites;
					if (!$user->save()) {
						$error = true;
					} else {
						
						if ($user->googleapps_sync_sites == 'yes') {
							$synchronize = true;
						}
						
					}
				}
				
				if ($synchronize) {
					$_SESSION['oauth_connect'] = 1;
					$googleappslogin_return = elgg_validate_action_url('https://' . $_SERVER['HTTP_HOST'] . '/action/googleappslogin/return');
					forward($googleappslogin_return);
				}
				
			} else {
				$error = true;
			}
			
			if (!$error) {
				system_message(elgg_echo('admin:configuration:success'));
			} else {
				register_error(elgg_echo('admin:configuration:fail'));
			}
		}
	}
	
	require_once 'models/functions.php';
	
	register_elgg_event_handler('init','system','googleappslogin_init');
	register_elgg_event_handler('pagesetup','system','googleappslogin_pagesetup');
	//register_elgg_event_handler('logout', 'user', 'googleappslogin_logout');
	register_elgg_event_handler('login', 'user', 'googleappslogin_login');
	
	// TODO: remove this permissions hook if it turns out not to be necessary
	
	register_plugin_hook('permissions_check','user','googleappslogin_can_edit');
	register_plugin_hook('entity:icon:url','user','googleappslogin_icon_url');
	
	// Register actions
	
	register_action('googleappslogin/oauth_update', true, $CONFIG->pluginspath . 'googleappslogin/actions/oauth_update.php');
	register_action('googleappslogin/login', true, $CONFIG->pluginspath . 'googleappslogin/actions/login.php');
	register_action('googleappslogin/connect', true, $CONFIG->pluginspath . 'googleappslogin/actions/connect.php');
	register_action('googleappslogin/disconnect', true, $CONFIG->pluginspath . 'googleappslogin/actions/disconnect.php');
	register_action('googleappslogin/return', true, $CONFIG->pluginspath . 'googleappslogin/actions/return.php');
	register_action('googleappslogin/return_with_connect', true, $CONFIG->pluginspath . 'googleappslogin/actions/return_with_connect.php');
?>