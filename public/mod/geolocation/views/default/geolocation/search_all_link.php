<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/mod/geolocation/models/functions.php';

$request = $_SERVER['REQUEST_URI'];
$show_map = get_input('show_map', 0);
$map_api = get_plugin_setting('google_api', 'geolocation');

//$type = $entities[0]->getType();
//$subtype = $entities[0]->getSubtype();

$url = parse_url($request);
$query = $url['query'];
$path = explode('&', $query);
$url_params = array();
foreach ($path as $part) {
	$v = explode('=', $part);
	$url_params[$v[0]] = $v[1];
}


unset($url_params['entity_type']);
unset($url_params['entity_subtype']);
unset($url_params['search_type']);
$url_params['show_map'] = 1;

array_walk($url_params, 'params_to_url');
$adv_query = implode('&', $url_params);
/*
?>
<ul>
<li><a href="?<?= $adv_query?>">All results on a map</a></li>
</ul>

<?
*/
?>
