<?php

ini_set("display_errors", "1");
ini_set("display_startup_errors", "1");
ini_set('error_reporting', E_ALL);
ini_set('pcre.backtrack_limit', 10000000);

require_once (dirname(dirname(__FILE__)) . '/models/functions.php');
global $CONFIG;
$result = googleappslogin_get_oauth_data(true);
echo $result;
exit;
?>
