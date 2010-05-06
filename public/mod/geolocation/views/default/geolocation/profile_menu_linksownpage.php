<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?= $GLOBALS['google_api'] ?>" type="text/javascript"></script>
<p>
	<a href="javascript:show_map_and_marker('map_current', new GLatLng('<?php echo $vars['entity']->current_latitude; ?>', '<?php echo $vars['entity']->current_longitude; ?>'));"><?php echo elgg_echo("geolocation:current_location"); ?></a>
	<div id="map_current"></div>
</p>
<p>
	<a href="javascript:show_map_and_marker('map_home', new GLatLng('<?php echo $vars['entity']->home_latitude; ?>', '<?php echo $vars['entity']->home_longitude; ?>'));"><?php echo elgg_echo("geolocation:home_location"); ?></a>
	<div id="map_home"></div>
</p>
<script type="text/javascript">
	function show_map_and_marker(map_id, latlng) {
		map = new google.maps.Map2(document.getElementById(map_id));
		map.setCenter(latlng, 13);
		map.addOverlay(new GMarker(latlng));
	}
</script>