<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?= $GLOBALS['google_api'] ?>" type="text/javascript"></script>
<script type="text/javascript">
	function show_map_and_marker(map_id, latlng) {
		map = new google.maps.Map2(document.getElementById(map_id));
		map.setCenter(latlng, 13);
		map.addOverlay(new GMarker(latlng));

               var mapControl = new GMapTypeControl();
                map.addControl(mapControl);
                map.addControl(new GLargeMapControl());
	}
</script>
<?php if($vars['entity']->current_latitude && $vars['entity']->current_longitude): ?>
<div class="map_current">
	<h4>Current location</h4>
	<div id="map_current"></div>
</div>
<script type="text/javascript">
	show_map_and_marker('map_current', new GLatLng('<?php echo $vars['entity']->current_latitude; ?>', '<?php echo $vars['entity']->current_longitude; ?>'));
</script>
<?php endif; ?>
<?php if($vars['entity']->home_latitude && $vars['entity']->home_longitude): ?>
<div class="map_home">
	<h4>Home location</h4>
	<div id="map_home"></div>
</div>
<script type="text/javascript">
	show_map_and_marker('map_home', new GLatLng('<?php echo $vars['entity']->home_latitude; ?>', '<?php echo $vars['entity']->home_longitude; ?>'));
</script>
<?php endif; ?>