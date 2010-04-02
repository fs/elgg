<?php

$request = $_SERVER['REQUEST_URI'];
$show_map = get_input('show_map', 0);

$url = parse_url($request);

$query = $url['query'];
$path = explode('&', $query);
$url_params = array();
foreach ($path as $part) {
	$v = explode('=', $part);
	$url_params[$v[0]] = $v[1];
}

$url_params['entity_subtype'] = 1;
$url_params['show_map'] = 1;
function to_url_param(&$item, $key){
	$item = $key . '=' . $item;
}
array_walk($url_params, 'to_url_param');
$adv_query = implode('&', $url_params);

if ($show_map) {
	
	elgg_view('geolocation/search_map', array('entities' => $entities));
	
} else {
	?>
	<a href="?<?= $adv_query?>">view on a map</a>
	<?
}

?>
