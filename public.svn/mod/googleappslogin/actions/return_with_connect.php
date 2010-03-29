<?php

#ini_set("display_errors", "1");
#ini_set("display_startup_errors", "1");
#ini_set('error_reporting', E_ALL);
#ini_set('pcre.backtrack_limit', 10000000);

require_once (dirname(dirname(__FILE__)) . "/models/Http.php");
require_once (dirname(dirname(__FILE__)) . "/models/OAuth.php");
require_once (dirname(dirname(__FILE__)) . "/models/Google_OpenID.php");

global $CONFIG;
if (empty($CONFIG->input['openid_ns'])){
	$CONFIG->input = array_merge($CONFIG->input, $_POST);
}

$google = Google_OpenID::create_from_response($CONFIG->input);
$google->set_home_url($googleapps_domain);

if (!$google->is_authorized()) {
	register_error(sprintf(elgg_echo('googleappslogin:googleappserror'), 'No authorised'));
	forward('pg/settings');
} else {
	
	if (!$user) {
		$user = $_SESSION['user'];
	}
	
	$email = $google->get_email();
	$firstname = $google->get_firstname();
	$lastname = $google->get_lastname();
	
	$entities = get_user_by_email($email);
	
	if (!empty($entities) && $entities[0]->username !== $user->username) {
		register_error(sprintf(elgg_echo('googleappslogin:googleappserror'), 'Sorry, but email ' . $email . ' is already exists and used by other user.'));
		forward('pg/settings');
	}
	$is_sync = $user->sync == '1';
	if ($is_sync) {
		
		if (empty($email)) {
			register_error(sprintf(elgg_echo('googleappslogin:googleappserror'), 'No data'));
			forward();
		}
		
		$user->email = $email;
		if (!empty($firstname) || !empty($lastname)) {
			$user->name = $firstname . (!empty($firstname) ? ' ' : '' ) . $lastname;
		}
		$user->subtype = 'googleapps';
		$user->google = 1;
		$user->connect = 1;
		$user->googleapps_controlled_profile = 'yes';
		$user->googleapps_sync_email = 'yes';
		$user->googleapps_sync_sites = 'yes';
		$user->save();
		
		$_SESSION['oauth_connect'] = 1;
		$googleappslogin_return = elgg_validate_action_url('https://' . $_SERVER['HTTP_HOST'] . '/action/googleappslogin/return');
		forward($googleappslogin_return);
		
	} else {
		register_error(sprintf(elgg_echo('googleappslogin:googleappserror'), 'This user is not ready for synchronization.'));
		forward('pg/settings');
	}
	
}

forward('pg/settings');
exit;
?>
