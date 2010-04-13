<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/mod/geolocation/models/functions.php';

$request = $_SERVER['REQUEST_URI'];
$show_map = get_input('show_map', 0);
$map_api = get_plugin_setting('google_api', 'geolocation');
$selected = false;

$url = parse_url($request);

if (preg_match('/\/pg\/search/', $url['path'])) {
	?>
	<div>
		<h3>Search in a region</h3>
		<form name="search_region" id="search_region" onsubmit="location.href='/pg/search_region/' + escape(this.query.value) + '';return false;">
		
		<input type="text" name="query" id="query" style="width:100px;" value=""/>
			<input type="submit" id="query_submit" value="Search" />
		
		</form>
	</div>
	<?
}
?>
