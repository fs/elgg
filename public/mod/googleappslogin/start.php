<?php

/**
 * Elgg googlelogin plugin
 *
 * @package GoogleAppsLogin
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Alexander Ulitin <alexander.ulitin@flatsoft.com>
 * @copyright FlatSourcing 2010
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

	global $CONFIG;

	$googleappslogin_url = elgg_add_action_tokens_to_url('https://' . $_SERVER['HTTP_HOST'] . '/action/googleappslogin/login');
	$googleappsconnect_url = elgg_add_action_tokens_to_url('https://' . $_SERVER['HTTP_HOST'] . '/action/googleappslogin/connect');
	$googleappsdisconnect_url = elgg_add_action_tokens_to_url('http://' . $_SERVER['HTTP_HOST'] . '/action/googleappslogin/disconnect');
	$oauth_update_url = elgg_add_action_tokens_to_url('https://' . $_SERVER['HTTP_HOST'] . '/action/googleappslogin/oauth_update');

	$GLOBALS['googleappslogin_url'] = $googleappslogin_url;
	$GLOBALS['googleappsconnect_url'] = $googleappsconnect_url;
	$GLOBALS['googleappsdisconnect_url'] = $googleappsdisconnect_url;
	$GLOBALS['oauth_update_url'] = $oauth_update_url;
	$GLOBALS['oauth_update_interval'] = get_plugin_setting('oauth_update_interval', 'googleappslogin');

	$oauth_sync_email = get_plugin_setting('oauth_sync_email', 'googleappslogin');
	$oauth_sync_sites = get_plugin_setting('oauth_sync_sites', 'googleappslogin');

	$domain = get_plugin_setting('googleapps_domain', 'googleappslogin');
	$GLOBALS['link_to_add_site'] = 'https://sites.google.com/a/' . $domain . '/sites/system/app/pages/meta/dashboard/create-new-site" target="_blank';

	//$body = elgg_view("blogs/list", array('googleappslogin_url' => $googleappslogin_url));
	//elgg_extend_view("account/forms/login", $body);
	elgg_extend_view('account/forms/login', 'googleappslogin/login');
	elgg_extend_view('messages/list', 'googleappslogin/scripts');

	// Extend system CSS with our own styles
	elgg_extend_view('css','googleappslogin/css');
	elgg_extend_view('elgg_topbar/extend','googleappslogin/new_mail');
	//register_plugin_hook('usersettings:save','user','googleappslogin_user_settings_save');
	register_entity_type('object','site_activity', 'Site activity');
	$user = $_SESSION['user'];
	if (!empty($user) &&
			$user->google &&
			$oauth_sync_sites != 'no') {
		// Set up pages
		add_menu(elgg_echo('googleappslogin:sites'), $CONFIG->wwwroot . 'pg/wikis/' . $_SESSION['user']->username);
		//elgg_extend_view('profile/menu/links','googleappslogin/menu');
		register_page_handler('wikis','googleappslogin_page_handler');
	}

	// Register widgets
	add_widget_type('google_docs', elgg_echo('googleappslogin:google_docs'),
			elgg_echo('googleappslogin:google_docs:description'));
}

function googleappslogin_pagesetup() {

	global $CONFIG;

	if (get_context() == "settings") {
		add_submenu_item(elgg_echo('googleappslogin:google_sites_settings'), $CONFIG->wwwroot . "mod/googleappslogin/");
	}

	if (get_context() == 'wikis') {
		add_submenu_item(elgg_echo('googleappslogin:sites:your'), $CONFIG->wwwroot . 'pg/wikis/' . $_SESSION['user']->username);
		add_submenu_item(elgg_echo('googleappslogin:sites:everyone'), $CONFIG->wwwroot . 'pg/wikis/all');
		add_submenu_item(elgg_echo('googleappslogin:site:add'), $GLOBALS['link_to_add_site']);
	}

	//extend_elgg_settings_page('googleappslogin/settings/usersettings', 'usersettings/user');
}

/**
 * googleappslogin page handler; allows the use of fancy URLs
 *
 * @param array $page From the page_handler function
 * @return true|false Depending on success
 */
function googleappslogin_page_handler($page) {

	// The second part dictates what we're doing
	if (isset($page[0])) {
		switch ($page[0]) {

			case "all" :

				$all = true;
				include(dirname(__FILE__) . '/wikis.php');
				return true;

				break;

			default:

				include(dirname(__FILE__) . '/wikis.php');
				return true;

				break;

		}
	} else {
		include(dirname(__FILE__) . '/wikis.php');
		return true;
	}

	return false;

}

function googleappslogin_logout() {

	//setcookie('elgg_redirect', '0');
	//setcookie('elgg_logout', mktime());
}

function googleappslogin_login() {

	$oauth_sync_email = get_plugin_setting('oauth_sync_email', 'googleappslogin');
	$oauth_sync_sites = get_plugin_setting('oauth_sync_sites', 'googleappslogin');

	$user = $_SESSION['user'];
	if (!empty($user) &&
			$user->google &&
			($oauth_sync_email != 'no' || $oauth_sync_sites != 'no' || $oauth_sync_docs != 'no')) {
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

	function update_acitivities_access($site_name, $access) {
		$entities = get_entities_from_metadata('site_name', $site_name, 'object');
		foreach ($entities as $entity) {
			$entity->access_id = $access == 2 ? 1 : ($access == 22 ? 2 : $access);
			$entity->save();
		}
	}

	// temporary!
	/*
		$entities = get_entities('user');
		foreach ($entities as $user) {
			$site_list = unserialize($user->site_list);
			foreach ($site_list as $title => $access) {
				$site_list[$title] = $access == 2 ? 1 : $access;
				update_acitivities_access($title, $access);
			}
			$user->site_list = serialize($site_list);
			$user->save();
		}
	*/
	// end temporary


	$googleapps_controlled_profile = strip_tags(get_input('googleapps_controlled_profile'));
	//$googleapps_sync_email = strip_tags(get_input('googleapps_sync_email'));
	//$googleapps_sync_sites = strip_tags(get_input('googleapps_sync_sites'));
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
				//$user->googleapps_controlled_profile = $googleapps_controlled_profile;
				if (!$user->save()) {
					$error = true;
				}
			}

			if (!empty($googleapps_sites_settings)) {
				$site_list = unserialize($user->site_list);
				foreach ($googleapps_sites_settings as $title => $access) {
					$site_list[$title] = $access;
					update_acitivities_access($title, $access);
				}
				$user->site_list = serialize($site_list);
				$user->save();
			}

			/*
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
					$googleappslogin_return = elgg_add_action_tokens_to_url('https://' . $_SERVER['HTTP_HOST'] . '/action/googleappslogin/return');
					forward($googleappslogin_return);
				}
			*/

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
register_action('googleappslogin/save', false, $CONFIG->pluginspath . 'googleappslogin/actions/save.php');

register_plugin_hook('cron', 'fiveminute', 'googleapps_cron_fetch_data');




