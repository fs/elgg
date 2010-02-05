<?php

#ini_set("display_errors", "1");
#ini_set("display_startup_errors", "1");
#ini_set('error_reporting', E_ALL);
#ini_set('pcre.backtrack_limit', 10000000);

require_once (dirname(dirname(__FILE__)) . "/models/Http.php");
require_once (dirname(dirname(__FILE__)) . "/models/oauth.php");
require_once (dirname(dirname(__FILE__)) . "/models/Google_OpenID.php");

global $CONFIG;
if (empty($CONFIG->input['openid_ns'])){
	$CONFIG->input = array_merge($CONFIG->input, $_POST);
}
$google = Google_OpenID::create_from_response($CONFIG->input);
$google->set_home_url($googleapps_domain);
//$response = $google->get_response();
//$request_token = $response[''];

//echo '<pre>';print_r($response);exit;
if (!$google->is_authorized()) {
	register_error(sprintf(elgg_echo('googleappslogin:googleappserror'), 'No authorised'));
	forward();
} else {
	
	$email = $google->get_email();
	$firstname = $google->get_firstname();
	$lastname = $google->get_lastname();
	
	//echo "user is authorized\n<br>";
	
	$do_login = false;
	$duplicate_account = false;
	
	if (empty($email)) {
		register_error(sprintf(elgg_echo('googleappslogin:googleappserror'), 'No data'));
		forward();
	}
	
	$entities = get_user_by_email($email);
	//$entities = elgg_get_entities(array('email' => 'shotman0@rambler.ru'));
	//echo '<pre>';print_r($email . '<br><br>');
	//print_r($entities);exit;
    if (!$entities) {
		
		$username = $email;
		
		if(get_user_by_username($username)) {
			// oops, try adding a "_googleapps" to the end
			//$username .= '_googleapps';
			
			if(get_user_by_username($username)) {
				$duplicate_account = true;
				register_error(sprintf(elgg_echo("googleappslogin:account_duplicate"), $username));
			}
		}
		
		if (!$duplicate_account) {
			
			$firstname = $google->get_firstname();
			$lastname = $google->get_lastname();
			
			$user = new ElggUser();
			$user->email = $email;
			$user->name = (!empty($firstname) || !empty($firstname)) ? ($google->get_firstname() . ' ' . $google->get_lastname()) : $email;
			$user->access_id = 2;
			$user->subtype = 'googleapps';
			$user->username = $username;
			// $user->googleapps_screen_name = $email;
			// $user->googleapps_controlled_profile = 'yes';
			
			if ($user->save()) {
				$new_account = true;
				$do_login = true;
				
				// need to keep track of subtype because getSubtype does not work
				// for newly created users in Elgg 1.5
				$subtype = 'googleapps';
			} else {
				register_error(elgg_echo("googleappslogin:account_create"));
			}
		}
	} elseif ($entities[0]->active == 'no') {
		// this is an inactive account
		register_error(elgg_echo("googleappslogin:inactive"));
	} else {
		$user = $entities[0];
		
		$subtype = $user->getSubtype();
		if ($subtype == 'googleapps') {
			
			// account is active, check to see if this user has been banned
			if (isset($user->banned) && $user->banned == 'yes') { // this needs to change.
				register_error(elgg_echo("googleappslogin:banned"));
			} else {
				$do_login = true;
				$new_account = false;
			}
		} else {
			register_error(sprintf(elgg_echo('googleappslogin:googleappserror'), 'Sorry, but username ' . $user->username . ' already exists.'));
		}
		
    }
	
	if ($do_login) {
		$rememberme = get_input('remember',0) ? true : false;
		
		login($user, $rememberme);
		/*if ($user->googleapps_controlled_profile != 'no') {
			die('This is ' . $user->googleapps_controlled_profile);
		}*/
		//echo '<pre>';print_r($user->getSubtype());exit;
		if (($subtype == 'googleapps') && ($user->googleapps_controlled_profile != 'no')) {
			// update from GoogleApps
			$user->email = $email;
			$user->name = (!empty($firstname) || !empty($firstname)) ? ($google->get_firstname() . ' ' . $google->get_lastname()) : $email;
			$user->save();
		}
		
		/*
		if ($new_account) {
			thewire_save_post($twitterInfo->status['text'], ACCESS_PUBLIC, 0, 'twitter');
		}
		*/
	}
}

forward();
exit;
?>