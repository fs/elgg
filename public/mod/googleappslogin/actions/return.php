<?php

#ini_set("display_errors", "1");
#ini_set("display_startup_errors", "1");
#ini_set('error_reporting', E_ALL);
#ini_set('pcre.backtrack_limit', 10000000);

require_once (dirname(dirname(__FILE__)) . "/models/Http.php");
require_once (dirname(dirname(__FILE__)) . "/models/Google_OpenID.php");

global $CONFIG;

$google = Google_OpenID::create_from_response($CONFIG->input);
$google->set_home_url($googleapps_domain);

if (!$google->is_authorized()) {
	//register_error(sprintf(elgg_echo('googleappslogin:googleappserror'), 'No authorised'));
    forward();
} else {
    $email = $google->get_email();
	//echo '<pre>';print_r($CONFIG->input);exit;
    //echo "user is authorized\n<br>";

    $do_login = false;
    $duplicate_account = false;
	
	if (empty($email)) {
		register_error(sprintf(elgg_echo('googleappslogin:googleappserror'), 'No data'));
		forward();
	} else {
		
	}

    $entities = get_entities_from_metadata('googleapps_screen_name', $email, 'user', 'googleapps');
    
    if (!$entities) {

        $entities = get_entities_from_metadata('googleapps_screen_name', $email, 'user');

        if (!$entities) {
            // this account does not exist, so create it
            // currently the username is just set to the Google name, but this may change
            // check to make sure that a non-Google account with the same user name
            // does not already exist (c) twitterlogin plugin

            $username = $email;

            if(get_user_by_username($username)) {
                // oops, try adding a "_googleapps" to the end
                $username .= '_googleapps';

                if(get_user_by_username($username)) {
                    $duplicate_account = true;
                    register_error(sprintf(elgg_echo("googleappslogin:account_duplicate"), $username));
                }
            }

            if (!$duplicate_account) {
                $user = new ElggUser();
                $user->email = $email;
                $user->name = $google->get_firstname() . ' ' . $google->get_lastname();
                $user->access_id = 2;
                $user->subtype = 'googleapps';
                $user->username = $username;
                $user->googleapps_screen_name = $email;
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
            }
        } else {
            // account is using a GoogleApps slave login, check to see if this user has been banned

            $user = $entities[0];

            if (isset($user->banned) && $user->banned == 'yes') { // this needs to change. // indian code
                register_error(elgg_echo("googleappslogin:banned"));
            } else {
                $do_login = true;
                $new_account = false;
                $subtype = 'elgg';
            }
        }

    } elseif ($entities[0]->active == 'no') {
        // this is an inactive account
	register_error(elgg_echo("googleappslogin:inactive"));
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
            // update from GoogleApps
            //$user->briefdescription = $twitterInfo->description;

            //$user->website = $twitterInfo->url;
            //$user->location = explode(',',urldecode($twitterInfo->location));
            //$user->twitter_icon_url_normal = $twitterInfo->profile_image_url;
            //$user->twitter_icon_url_mini = str_replace('_normal.jpg','_mini.jpg',$twitterInfo->profile_image_url);
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