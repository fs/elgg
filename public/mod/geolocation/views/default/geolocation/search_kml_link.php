<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/mod/geolocation/models/functions.php';

$request = $_SERVER['REQUEST_URI'];

$url = parse_url($request);
$query = $url['query'];
$path = explode('&', $query);

if (preg_match('/\/pg\/search/', $url['path'])) {
	
	$url_params = array();
	foreach ($path as $part) {
		$v = explode('=', $part);
		$url_params[$v[0]] = $v[1];
	}
	
	$url_params['view'] = 'kml';
	
	array_walk($url_params, 'params_to_url');
	$adv_query = implode('&', $url_params);
	
	?>
	<ul>
	<li><a href="?<?= $adv_query?>">Export Results to Google Earth</a></li>
	</ul>
	
	<?
}
?>
