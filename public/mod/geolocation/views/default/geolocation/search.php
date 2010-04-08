<?php

$request = $_SERVER['REQUEST_URI'];
//$show_map = get_input('show_map', 0);
$map_api = get_plugin_setting('google_api', 'geolocation');

$url = parse_url($request);
$path = $url['path'];
$results = $GLOBALS['my_search_result'];
//print_r($results);exit;
if ($path == '/pg/search/' && !empty($results)) {
	?>
	<div style="margin-left:230px;">
	<?
		echo elgg_view('geolocation/search_map', array('entities' => $results));
	?>
	</div>
	<?
}

?>
