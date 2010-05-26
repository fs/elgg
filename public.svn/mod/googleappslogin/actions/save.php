<?php

gatekeeper();

global $SESSION;


function update_acitivities_access($site_name, $access) {
	$entities = get_entities_from_metadata('site_name', $site_name, 'object');
	foreach ($entities as $entity) {
		$entity->access_id = $access == 2 ? 1 : ($access == 22 ? 2 : $access);
		$entity->save();
	}
}

$googleapps_controlled_profile = strip_tags(get_input('googleapps_controlled_profile'));		
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

	} else {
		$error = true;
	}

	if (!$error) {
		system_message(elgg_echo('admin:configuration:success'));
	} else {
		register_error(elgg_echo('admin:configuration:fail'));
	}
}

forward($_SERVER['HTTP_REFERER']);