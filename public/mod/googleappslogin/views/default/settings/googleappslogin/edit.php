<?php

$body = '';

$options = array(elgg_echo('googleappslogin:settings:yes') => 'yes',
				 elgg_echo('googleappslogin:settings:no') => 'no'
);

$googleapps_domain = get_plugin_setting('googleapps_domain', 'googleappslogin');
$login_key = get_plugin_setting('login_key', 'googleappslogin');
$login_secret = get_plugin_setting('login_secret', 'googleappslogin');
$private_key = get_plugin_setting('private_key', 'googleappslogin');
$oauth_update_interval = get_plugin_setting('oauth_update_interval', 'googleappslogin');

$oauth_sync_email = get_plugin_setting('oauth_sync_email', 'googleappslogin');
$oauth_sync_sites = get_plugin_setting('oauth_sync_sites', 'googleappslogin');
$oauth_sync_docs = get_plugin_setting('oauth_sync_docs', 'googleappslogin');

$body .= "<p><b>" . elgg_echo('googleappslogin:title') . "</b></p>";
$body .= '<br />';
$body .= elgg_echo('googleappslogin:details');
$body .= '<br />';

$body .= elgg_echo('googleappslogin:domain') . "<br />";
$body .= elgg_view('input/text', array('internalname' => 'params[googleapps_domain]', 'value' => $googleapps_domain));

$body .= elgg_echo('googleappslogin:secret') . "<br />";
$body .= elgg_view('input/text', array('internalname' => 'params[login_secret]', 'value' => $login_secret));

$body .= elgg_echo('googleappslogin:oauth_update_interval') . "<br />";
$body .= elgg_view('input/text', array('internalname' => 'params[oauth_update_interval]', 'value' => $oauth_update_interval));

//$logged_user = $_SESSION['user'];

//if ($logged_user->admin == 1) {
	
	if (!$oauth_sync_email) {
		$oauth_sync_email = 'yes';
	}
	if (!$oauth_sync_sites) {
		$oauth_sync_sites = 'yes';
	}
	if (!$oauth_sync_docs) {
		$oauth_sync_docs = 'yes';
	}

	$body .= elgg_echo('googleappslogin:googleapps_user_settings_sync_email') . "<br />";
	$body .= elgg_view('input/radio', array('internalname' => 'params[oauth_sync_email]', 'options' => $options, 'value' => $oauth_sync_email));
	
	$body .= elgg_echo('googleappslogin:googleapps_user_settings_sync_sites') . "<br />";
	$body .= elgg_view('input/radio', array('internalname' => 'params[oauth_sync_sites]', 'options' => $options, 'value' => $oauth_sync_sites));

	$body .= elgg_echo('googleappslogin:googleapps_user_settings_sync_docs') . "<br />";
	$body .= elgg_view('input/radio', array('internalname' => 'params[oauth_sync_docs]', 'options' => $options, 'value' => $oauth_sync_docs));
	
//}

//$body .= elgg_echo('googleappslogin:privatekey') . "<br />";
//$body .= elgg_view('input/longtext',array('internalname'=>'params[private_key]','value'=>$private_key));

echo $body;

?>
