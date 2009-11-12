<?php

$body = '';

$login_key = get_plugin_setting('login_key', 'googleappslogin');
$login_secret = get_plugin_setting('login_secret', 'googleappslogin');

$body .= "<p><b>" . elgg_echo('googleappslogin:title') . "</b></p>";
$body .= '<br />';
$body .= elgg_echo('googleappslogin:details');
$body .= '<br />';
$body .= elgg_echo('googleappslogin:key') . "<br />";
$body .= elgg_view('input/text',array('internalname'=>'params[login_key]','value'=>$login_key));
$body .= elgg_echo('googleappslogin:secret') . "<br />";
$body .= elgg_view('input/text',array('internalname'=>'params[login_secret]','value'=>$login_secret));

echo $body;

?>