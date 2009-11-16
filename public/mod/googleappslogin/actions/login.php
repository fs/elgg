<?php

require_once (dirname(dirname(__FILE__)) . "/models/Http.php");
require_once (dirname(dirname(__FILE__)) . "/models/Google_OpenID.php");

$google = new Google_OpenID();

$google->set_home_url("localhost/elgg/");
$google->set_return_url("localhost/elgg/action/googleappslogin/return");

$google->set_start_url("https://www.google.com/accounts/o8/id");
//$google->set_start_url('https://www.google.com/accounts/o8/site-xrds?ns=2&hd=flatsoft.com');

$url = $google->get_authorization_url();

forward($url);

exit;
?>