<?php
session_start();

require_once (dirname(dirname(__FILE__)) . '/models/Http.php');
require_once (dirname(dirname(__FILE__)) . '/models/Google_OpenID.php');
require_once (dirname(dirname(__FILE__)) . '/models/OAuth.php');
require_once (dirname(dirname(__FILE__)) . '/models/client.inc');

global $CONFIG;
if (empty($CONFIG->input['openid_ns'])){
	$CONFIG->input = array_merge($CONFIG->input, $_POST);
}
//print_r($GLOBALS);
$user = $_SESSION['user'];

$CONSUMER_KEY = get_plugin_setting('googleapps_domain', 'googleappslogin');
$CONSUMER_SECRET = get_plugin_setting('login_secret', 'googleappslogin');

$oauth_sync_email = get_plugin_setting('oauth_sync_email', 'googleappslogin');
$oauth_sync_sites = get_plugin_setting('oauth_sync_sites', 'googleappslogin');

$oauth_verifier = $CONFIG->input['oauth_verifier'];

$client = new OAuth_Client($CONSUMER_KEY, $CONSUMER_SECRET, SIG_METHOD_HMAC);

//print_r($_SESSION);

//exit;
if (!$client->authorized() && !empty($user) && ($oauth_sync_email != 'no' || $oauth_sync_sites != 'no')) {
	
	if (empty($oauth_verifier)) {
		
		$result = $client->oauth_authorize();
		header('Location: ' . $result);
		exit;
		
	} else {
		
		$token = $client->oauth_fetch_access_token($oauth_verifier, $_SESSION['request_key'], $_SESSION['request_secret']);
		
		$_SESSION['access_token'] = $token->key;
		$_SESSION['access_secret'] = $token->secret;
		
		$user->access_token = $token->key;
		$user->token_secret = $token->secret;
		$user->save();
		
		googleapps_fetch_oauth_data($client);
		
	}
	
} else {
	/*
	if ($user) {
		echo '<pre>';
		print_r($user);
		echo '</pre>';
		exit;
	}
	*/
	
}

if (!empty($_SESSION['oauth_connect'])) {
	unset($_SESSION['oauth_connect']);
	forward('mod/googleappslogin');
}

$google = Google_OpenID::create_from_response($CONFIG->input);

$google->set_home_url($googleapps_domain);
$response = $google->get_response();

if (!empty($response)) {
	$request_token = !empty($response['openid_ext2_request_token']) ? $response['openid_ext2_request_token'] : '';
} else {
	//register_error(sprintf(elgg_echo('googleappslogin:googleappserror'), 'Bad response'));
	forward();
}

if (!$google->is_authorized()) {
	register_error(sprintf(elgg_echo('googleappslogin:googleappserror'), 'No authorized'));
	forward();
} else {
	
	$email = $google->get_email();
	$firstname = $google->get_firstname();
	$lastname = $google->get_lastname();
	$_SESSION['logged_with_openid'] = 1;
	//echo "user is authorized\n<br>";
	
	$do_login = false;
	$duplicate_account = false;
	
	if (empty($email)) {
		register_error(sprintf(elgg_echo('googleappslogin:googleappserror'), 'No email'));
		forward();
	}
	
	$entities = get_user_by_email($email);
	//$entities = elgg_get_entities(array('email' => 'shotman0@rambler.ru'));
	//echo '<pre>';print_r($email . '<br><br>');
	//print_r($entities);exit;
    if (!$entities) {
		
		$username = $email;



    $username = preg_replace("/\@[a-zA-Z\.0-9\-]+$/", "", $username);
    
    if(get_user_by_username($username)) {
        $username = preg_replace("/\@([a-zA-Z\.0-9\-]+)/", ".$1", $email);
    }


			
			if(get_user_by_username($username)) {
				$duplicate_account = true;
				register_error(sprintf(elgg_echo("googleappslogin:account_duplicate"), $username));
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
			
			$user->google = 1;
			$user->sync = 1;
			$user->googleapps_controlled_profile = 'yes';
			
			if ($user->save()) {
				$new_account = true;
				$do_login = true;
				
				// need to keep track of subtype because getSubtype does not work
				// for newly created users in Elgg 1.5
				$subtype = 'googleapps';
				$user->google = 1;
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
		//print_r($subtype);exit;
		if ($user->google == 1 || $subtype == 'googleapps') {
		//if ($subtype == 'googleapps') {
			
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
		$rememberme = true;

                $user_sync_settings = unserialize($user->sync_settings);
                

		if (($user->google || $subtype == 'googleapps') && ($user->googleapps_controlled_profile != 'no') && $user_sync_settings['sync_name']!==0) {
			// update from GoogleApps
			$user->email = $email;
			$user->name = (!empty($firstname) || !empty($lastname)) ? ($firstname . ' ' . $lastname) : $email;
			/*
			echo '<pre>FIRSTNAME: ';print_r($firstname);echo '</pre>';
			echo '<pre>LASTNAME: ';print_r($lastname);echo '</pre>';
			echo '<pre>RESULT: ';print_r($user->name);echo '</pre>';
			exit;
			*/
			$user->save();
		}

		login($user, $rememberme);
		if (isset($_SESSION['last_forward_from']) && $_SESSION['last_forward_from']) {
			$forward_url = $_SESSION['last_forward_from'];
			unset($_SESSION['last_forward_from']);
			forward($forward_url);
		}
	}
}

forward();
exit;
?>
