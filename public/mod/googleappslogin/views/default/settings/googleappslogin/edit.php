<?php

$body = '';

$googleapps_domain = get_plugin_setting('googleapps_domain', 'googleappslogin');
$login_key = get_plugin_setting('login_key', 'googleappslogin');
$login_secret = get_plugin_setting('login_secret', 'googleappslogin');
$private_key = get_plugin_setting('private_key', 'googleappslogin');

$body .= "<p><b>" . elgg_echo('googleappslogin:title') . "</b></p>";
$body .= '<br />';
$body .= elgg_echo('googleappslogin:details');
$body .= '<br />';

$body .= elgg_echo('googleappslogin:domain') . "<br />";
$body .= elgg_view('input/text',array('internalname'=>'params[googleapps_domain]','value'=>$googleapps_domain));

$body .= elgg_echo('googleappslogin:secret') . "<br />";
$body .= elgg_view('input/text',array('internalname'=>'params[login_secret]','value'=>$login_secret));
$body .= elgg_echo('googleappslogin:privatekey') . "<br />";
$body .= elgg_view('input/longtext',array('internalname'=>'params[private_key]','value'=>$private_key));

echo $body;

?>