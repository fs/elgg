<?php

// get model

require_once(dirname(dirname(__FILE__)) . "/models/EpiCurl.php");
require_once(dirname(dirname(__FILE__)) . "/models/EpiOAuth.php");
require_once(dirname(dirname(__FILE__)) . "/models/EpiGoogleApps.php");
require_once(dirname(dirname(__FILE__)) . "/models/secret.php");

$googleObj = new EpiGoogleApps($consumer_key, $consumer_secret);

$au = $googleObj->getAuthorizationUrl();
forward($au);

exit;
?>