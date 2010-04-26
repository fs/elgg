<?php

ini_set("display_errors", "1");
ini_set("display_startup_errors", "1");
ini_set('error_reporting', E_ALL);
ini_set('pcre.backtrack_limit', 10000000);

require_once (dirname(dirname(__FILE__)) . "/models/Http.php");
require_once (dirname(dirname(__FILE__)) . "/models/Google_OpenID.php");
require_once (dirname(dirname(__FILE__)) . "/models/secret.php");
require_once (dirname(dirname(__FILE__)) . "/models/OAuth.php");
require_once (dirname(dirname(__FILE__)) . "/models/client.inc");

global $CONFIG;

$home_url = $CONFIG->wwwroot;

$user = page_owner_entity();

if (!$user) {    	
	$user = $_SESSION['user'];
}
$subtype = $user->getSubtype();

if ($user->google) {
	
	if (empty($user->password)) {
		register_error(sprintf(elgg_echo('googleappslogin:googleappserror'), 'Please provide your password before you disconnect profile from googleapps.'));
		forward($_SERVER['HTTP_REFERER']);
	}
	
	$user->sync = '0';
	$user->subtype = '';
	$user->connect = 0;
	$user->googleapps_controlled_profile = 'no';
	$user->google = 0;
	$user->access_token = '';
	$user->token_secret = '';
	$user->save();
	
	unset($_SESSION['access_token']);
	unset($_SESSION['access_secret']);
	unset($_SESSION['logged_with_openid']);
	unset($_SESSION['oauth_connect']);
	
	system_message('Your profile has been successfully disconnected from googleapps.');
}

forward('mod/googleappslogin');

exit;

