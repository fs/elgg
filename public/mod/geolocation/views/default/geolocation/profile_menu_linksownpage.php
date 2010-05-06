<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?= $GLOBALS['google_api'] ?>" type="text/javascript"></script>
<p>
	<a href="javascript:show_map_and_marker('map', new GLatLng('<?php echo $vars['entity']->current_latitude; ?>', '<?php echo $vars['entity']->current_longitude; ?>'));"><?php echo elgg_echo("geolocation:current_location"); ?></a>
</p>
<p>
	<a href="javascript:show_map_and_marker('map', new GLatLng('<?php echo $vars['entity']->home_latitude; ?>', '<?php echo $vars['entity']->home_longitude; ?>'));"><?php echo elgg_echo("geolocation:home_location"); ?></a>
</p>
<div id="layout_map" class="map">
	<div id="content_area_user_title"><h2>Locations on map</h2></div>
	<div id="map" style="left:30px;">
		<div style="padding: 1em; color: gray">Loading...</div>
	</div>
</div>
<script type="text/javascript">
	function show_map_and_marker(map_id, latlng) {
		map = new google.maps.Map2(document.getElementById(map_id));
		map.clearOverlays();
		map.setCenter(latlng, 13);
		map.addOverlay(new GMarker(latlng));
		$('#layout_map').show();
		$.facebox($('#layout_map'));		
	}
</script>