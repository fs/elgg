<?php

	$english = array(	
	
		'item:user:googleapps' => "googleapps users",	
		'googleappslogin:title' => "Sign in with Google Apps - settings",
		'googleappslogin:details' => "To use Sign in with Google Apps, you have to enter your Google Apps (hosted) domain in the field below. If you want to provide futher integration with Google you need to register your site with Google Apps and obtain consumer key and secret. Your site should be registered with a high level of security (with a X.509 certificate). You also should enter your private RSA key in the text field below.",

		'googleappslogin:domain' => "Google Apps (hosted) domain name without www, http://, etc (example: flatsoft.com)",
		'googleappslogin:secret' => "Secret (to use with OAuth)",
		'googleappslogin:privatekey' => "Private RSA key (to use with OAuth)",

		'googleappslogin:account_create' => "Error: Unable to create your account. "
			."Please contact the site administrator or try again later.",
		'googleappslogin:inactive' => "Error: cannot activate your Elgg account.",
		'googleappslogin:banned' => "Error: cannot log you in. "
			."Please contact the site administrator or try again later.",
		'googleappslogin:googleappserror' => "Error: googleapps returned the following error message: %s",
		'googleappslogin:account_duplicate' => "Error: a non-googleapps account with "
			."the same username (%s) already exists on this site.",
		'googleappslogin:wrongdomain' => "Error: can't resolve OpenID entrypoint for your GoogleApps (hosted) domain or domain is not google-hosted.",
		'googleappslogin:settings:yes' => "yes",
		'googleappslogin:settings:no' => "no",
		'googleappslogin:googleapps_user_settings_title' => "googleapps profile",
		'googleappslogin:googleapps_user_settings_description' => "Let googleapps control my profile when I login into Elgg. "
			."If you set this to \"no\", you will be able to edit your Elgg profile and it will no longer be synchronised with googleapps.",
		'googleappslogin:googleapps_user_settings:save:ok' => "Your googleapps profile preference has been saved.",
		'googleappslogin:googleapps_login_settings:save:ok' => "Your googleapps screen name has been saved.",
		'googleappslogin:googleapps_login_title' => "googleapps login",
		'googleappslogin:googleapps_login_description' => "If you want to login to Elgg using googleapps, enter your googleapps screen name here.",
	);
					
	add_translation("en",$english);

?>