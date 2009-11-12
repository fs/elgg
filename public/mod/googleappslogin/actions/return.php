<?php

// get model

require_once(dirname(dirname(__FILE__)) . "/models/EpiCurl.php");
require_once(dirname(dirname(__FILE__)) . "/models/EpiOAuth.php");
require_once(dirname(dirname(__FILE__)) . "/models/Epigoogleapps.php");
require_once(dirname(dirname(__FILE__)) . "/models/secret.php");

$googleappsObj = new Epigoogleapps($consumer_key, $consumer_secret);

$googleappsObj->setToken(get_input('oauth_token',''));
$token = $googleappsObj->getAccessToken();
$googleappsObj->setToken($token->oauth_token, $token->oauth_token_secret);
$googleappsInfo = $googleappsObj->get_accountVerify_credentials();
//print_r($googleappsInfo->response);
if ($googleappsInfo->response && $googleappsInfo->response['error']) {
    register_error(sprintf(elgg_echo('googleappslogin:googleappserror'),$googleappsInfo->response['error']));
} else {	
	// use an alias as the Elgg account username may or may not be the same as the googleapps account username
	$entities = get_entities_from_metadata('googleapps_screen_name', $googleappsInfo->screen_name, 'user', 'googleapps');
	$do_login = false;
	$duplicate_acccount = false;
	
	if (!$entities || $entities[0]->active == 'no') {
		if (!$entities) {
			$entities = get_entities_from_metadata('googleapps_screen_name', $googleappsInfo->screen_name, 'user');
			if (!$entities) {
				// this account does not exist, so create it
				// currently the username is just set to the googleapps name, but this may change
				
				// check to make sure that a non-googleapps account with the same user name
				// does not already exist
				$username = $googleappsInfo->screen_name;
				if(get_user_by_username($username)) {
					// oops, try adding a "_googleapps" to the end
					$username .= '_googleapps';
					if(get_user_by_username($username)) {
						$duplicate_account = true;			
						register_error(sprintf(elgg_echo("googleappslogin:account_duplicate"),$username));
					}
				}
				if (!$duplicate_account) {								    
			        $user = new ElggUser();
				    $user->email = '';
				    $user->name = $googleappsInfo->name;
				    $user->access_id = 2;
				    $user->subtype = 'googleapps';
				    $user->username = $username;
				    $user->googleapps_screen_name = $googleappsInfo->screen_name;
				    $user->googleapps_controlled_profile = 'yes';
				    			
				    if ($user->save()) {
				    	$new_account = true;
					    $do_login = true;
					    // need to keep track of subtype because getSubtype does not work
					    // for newly created users in Elgg 1.5
					    $subtype = 'googleapps';
				    } else {
			    	    register_error(elgg_echo("googleappslogin:account_create"));
				    }
				} else {
					
				}
			} else {
				$user = $entities[0];
				
				// account is using a googleapps slave login, check to see if this user has been banned
			
			    if (isset($user->banned) && $user->banned == 'yes') { // this needs to change.
			        register_error(elgg_echo("googleappslogin:banned"));
			    } else {
				    $do_login = true;
				    $new_account = false;
				    $subtype = 'elgg';
			    }				
			}
		} else {
			// this is an inactive account
			register_error(elgg_echo("googleappslogin:inactive"));
		}
		
	} else {		
		$user = $entities[0];
		// account is active, check to see if this user has been banned
	    if (isset($user->banned) && $user->banned == 'yes') { // this needs to change.
	        register_error(elgg_echo("googleappslogin:banned"));
	    } else {
		    $do_login = true;
		    $new_account = false;
		    $subtype = 'googleapps';
	    }		    
	}
	
	if ($do_login) {				
		$rememberme = get_input('remember',0);
		if (!empty($rememberme)) {
			login($user,true);
		} else {
			login($user);
		}
		
		if (($subtype == 'googleapps') && ($user->googleapps_controlled_profile != 'no')) {
			// update from googleapps
			$user->briefdescription = $googleappsInfo->description;
		    $user->website = $googleappsInfo->url;
		    $user->location = explode(',',urldecode($googleappsInfo->location));
		    $user->googleapps_icon_url_normal = $googleappsInfo->profile_image_url;
		    $user->googleapps_icon_url_mini = str_replace('_normal.jpg','_mini.jpg',$googleappsInfo->profile_image_url);
		}
		
		if ($new_account) {
			thewire_save_post($googleappsInfo->status['text'], ACCESS_PUBLIC, 0, 'googleapps');
		}
	}
}

forward();

exit;

?>