<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/mod/geolocation/models/functions.php';

$request = $_SERVER['REQUEST_URI'];
$show_map = get_input('show_map', 0);
$map_api = get_plugin_setting('google_api', 'geolocation');
$is_location = false;

$type = $entities[0]->getType();
$subtype = $entities[0]->getSubtype();

$url = parse_url($request);
$query = $url['query'];
$path = explode('&', $query);
$url_params = array();
foreach ($path as $part) {
	$v = explode('=', $part);
	$url_params[$v[0]] = $v[1];
}

$url_params['entity_type'] = $type;
if ($type != 'user' && $type != 'group') {
	$url_params['entity_subtype'] = $subtype;
}
$url_params['search_type'] = 'entities';
$url_params['show_map'] = 1;

array_walk($url_params, 'params_to_url');
$adv_query = implode('&', $url_params);

//echo '<pre>'; print_r($request); echo '</pre>';

foreach ($entities as $entity) {
	
	$lg = $entity->getLongitude();
	$lt = $entity->getLatitude();
	
	if (!$lg || !$lt) {
		continue;
	} else {
		$is_location = true;
	}
	
}
if ($is_location) {
	if ($show_map) {
		if (empty($GLOBALS['search_results'])) {
			$GLOBALS['search_results'] = array();
		}
		$GLOBALS['search_results'] = array_merge($GLOBALS['search_results'], $entities);
		//echo elgg_view('geolocation/search_map', array('entities' => $entities, 'map_api' => $map_api));
	} else {
		
		echo elgg_view('geolocation/search_points_listing', array('entities' => $entities, 'prefix' => $type));
		
		?>
		<div class="search_listing">
		<!--<a href="?<?= $adv_query?>">view on a map</a>-->
		<a href="#map2" onclick="show_map(<?=$type?>_markers);return false;">view on a map</a>
		</div>
		<?
	}
}

?>
