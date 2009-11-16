<?php

ini_set("display_errors", "1");
ini_set("display_startup_errors", "1");
ini_set('error_reporting', E_ALL);
ini_set('pcre.backtrack_limit', 10000000);

require_once (dirname(dirname(__FILE__)) . "/models/Http.php");
require_once (dirname(dirname(__FILE__)) . "/models/Google_OpenID.php");

global $CONFIG;

$home_url = $CONFIG->wwwroot;

$google = new Google_OpenID();

$google->set_home_url($home_url);
$google->set_return_url($home_url . 'action/googleappslogin/return');

//$google->set_start_url("https://www.google.com/accounts/o8/id");
$google->set_start_url('https://www.google.com/accounts/o8/site-xrds?ns=2&hd=flatsoft.com');

$url = $google->get_authorization_url();

forward($url);

exit;
?>