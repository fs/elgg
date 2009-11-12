<?php

// get model

require_once(dirname(dirname(__FILE__)) . "/models/EpiCurl.php");
require_once(dirname(dirname(__FILE__)) . "/models/EpiOAuth.php");
require_once(dirname(dirname(__FILE__)) . "/models/EpiTwitter.php");
require_once(dirname(dirname(__FILE__)) . "/models/secret.php");

$twitterObj = new EpiTwitter($consumer_key, $consumer_secret);
$au = $twitterObj->getAuthorizationUrl();
forward($au);

exit;
?>