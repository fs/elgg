<?php

ini_set("display_errors", "1");
ini_set("display_startup_errors", "1");
ini_set('error_reporting', E_ALL);
ini_set('pcre.backtrack_limit', 10000000);

require_once (dirname(dirname(__FILE__)) . "/models/Http.php");
require_once (dirname(dirname(__FILE__)) . "/models/Google_OpenID.php");
require_once (dirname(dirname(__FILE__)) . "/models/secret.php");

global $CONFIG;

$home_url = $CONFIG->wwwroot;

$user = page_owner_entity();
//echo '<pre>';print_r($user->googleapps_controlled_profile);exit;
if (!$user) {    	
	$user = $_SESSION['user'];
}
$subtype = $user->getSubtype();

if (!$user->google) {
	$user->sync = '1';
	$user->googleapps_controlled_profile = 'no';
	
	$user->save();
	
	$google = new Google_OpenID();
	$google->use_oauth();
	$google->set_home_url($home_url);
	$google->set_return_url(elgg_add_action_tokens_to_url($home_url . 'action/googleappslogin/return_with_connect', FALSE));
	
	if ($googleapps_domain) {
		$google->set_start_url('https://www.google.com/accounts/o8/site-xrds?ns=2&hd=' . $googleapps_domain);
	} else {
		$google->set_start_url("https://www.google.com/accounts/o8/id");
	}

	
	try {
		$url = $google->get_authorization_url();
		forward($url);
	} catch(Exception $e) {
		register_error(sprintf(elgg_echo("googleappslogin:wrongdomain"), $username));
		forward();
	}
} else {
	forward('mod/googleappslogin');
}

exit;
?>
