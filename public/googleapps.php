<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

ini_set("display_errors", "1");
ini_set("display_startup_errors", "1");
ini_set('error_reporting', E_ALL);
ini_set('pcre.backtrack_limit', 10000000);

// get model

require_once "mod/googleappslogin/models/EpiCurl.php";
require_once "mod/googleappslogin/models/EpiOAuth.php";
require_once "mod/googleappslogin/models/EpiGoogleApps.php";
//require_once "mod/googleappslogin/models/secret.php";

$consumer_key = 'elgg.flatsourcing.com';
$consumer_secret = 'rSc2fNBOauGxgDU8hs3pXb8e';

$googleObj = new EpiGoogleApps($consumer_key, $consumer_secret);

var_dump($googleObj->getRequestToken());

//$au = $googleObj->getAuthorizationUrl();
//forward($au);

exit;


?>
