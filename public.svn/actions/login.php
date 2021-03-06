<?php
/**
 * Elgg login action
 *
 * @package Elgg
 * @subpackage Core
 * @author Curverider Ltd
 * @link http://elgg.org/
 */

// Get username and password
$username = get_input('username');
$password = get_input("password");
$persistent = get_input("persistent", false);

// If all is present and correct, try to log in
$result = false;
if (!empty($username) && !empty($password)) {
	// check first if this is an email address and do a login
	// email addies will be validated
	if (strpos($username, '@') !== FALSE && ($user=get_user_by_email($username))) {
		$username = $user[0]->username;
	}

	if ($user = authenticate($username, $password)) {
		$result = login($user, $persistent);
	}
}

// Set the system_message as appropriate
if ($result) {
	system_message(elgg_echo('loginok'));
	if (isset($_SESSION['last_forward_from']) && $_SESSION['last_forward_from']) {
		$forward_url = $_SESSION['last_forward_from'];
		unset($_SESSION['last_forward_from']);
		forward($forward_url);
	} else {
		if (get_input('returntoreferer')) {
			forward($_SERVER['HTTP_REFERER']);
		} else {
			// forward to index for front page overrides.
			// index will forward to dashboard if appropriate.
			forward('index.php');
		}
	}
} else {
	$error_msg = elgg_echo('loginerror');
	// figure out why the login failed
	if (!empty($username) && !empty($password)) {
		// See if it exists and is disabled
		$access_status = access_get_show_hidden_status();
		access_show_hidden_entities(true);
		if (($user = get_user_by_username($username)) && !$user->validated) {
			// give plugins a chance to respond
			if (!trigger_plugin_hook('unvalidated_login_attempt','user',array('entity'=>$user))) {
				// if plugins have not registered an action, the default action is to
				// trigger the validation event again and assume that the validation
				// event will display an appropriate message
				trigger_elgg_event('validate', 'user', $user);
			}
		} else {
			register_error(elgg_echo('loginerror'));
		}
		access_show_hidden_entities($access_status);
	} else {
		register_error(elgg_echo('loginerror'));
	}
}
