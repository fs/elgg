<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/mod/geolocation/models/functions.php';

$request = $_SERVER['REQUEST_URI'];
$show_map = get_input('show_map', 0);
$map_api = get_plugin_setting('google_api', 'geolocation');
$selected = false;


$url = parse_url($request);
$query = $url['query'];
$path = explode('&', $query);

if (preg_match('/\/pg\/search/', $url['path'])) {
	
	$url_params = array();
	foreach ($path as $part) {
		$v = explode('=', $part);
		$url_params[$v[0]] = $v[1];
	}
	
	//print_r($url_params);
	
	?>
	<div>
		<h3>Search in a region</h3>
		<form name="search_region" id="search_region">
		<?php foreach ($url_params as $param => $value) : ?>
			<input type="hidden" name="<?= $param ?>" value="<?= $value ?>" />
		<?php endforeach; ?>
		<input type="text" name="region" id="query" style="width:100px;" value=""/>
			<input type="submit" id="query_submit" value="Search" />
		
		</form>
	</div>
	<?
}
?>
