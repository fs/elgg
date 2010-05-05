<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?= $GLOBALS['google_api'] ?>" type="text/javascript"></script>
<h4>Home location:</h4>
<div id="map_home" style="width:200px;height:200px;overflow: hidden;"></div>
<h4>Current location:</h4>
<div id="map_current" style="width:200px;height:200px;overflow: hidden;"></div>
<script>
	function show_map_and_marker(map_id, latlng) {
		map = new google.maps.Map2(document.getElementById(map_id));		
		map.setCenter(latlng, 10);
		map.addOverlay(new GMarker(latlng));
	}	
	show_map_and_marker('map_home', new GLatLng('<?php echo $vars['entity']->home_latitude; ?>', '<?php echo $vars['entity']->home_longitude; ?>'));
	show_map_and_marker('map_current', new GLatLng('<?php echo $vars['entity']->current_latitude; ?>', '<?php echo $vars['entity']->current_longitude; ?>'));
</script>