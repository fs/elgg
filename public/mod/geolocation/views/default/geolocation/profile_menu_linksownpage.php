<?php if (get_context() == "profile"): ?>
<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?= $GLOBALS['google_api'] ?>" type="text/javascript"></script>
<p class="user_menu_item">
  <a href="javascript:show_map_and_marker('<?php echo elgg_echo("geolocation:current_location"); ?>', new GLatLng('<?php echo $vars['entity']->current_latitude; ?>', '<?php echo $vars['entity']->current_longitude; ?>'));"><?php echo elgg_echo("geolocation:current_location"); ?></a>
</p>
<p class="user_menu_item">
  <a href="javascript:show_map_and_marker('<?php echo elgg_echo("geolocation:home_location"); ?>', new GLatLng('<?php echo $vars['entity']->home_latitude; ?>', '<?php echo $vars['entity']->home_longitude; ?>'));"><?php echo elgg_echo("geolocation:home_location"); ?></a>
</p>
<div id="layout_map">
	<div id="content_area_user_title"><h2>Locations on map</h2></div>
	<div id="map">
		<div style="padding: 1em; color: gray">Loading...</div>
	</div>
</div>
<script type="text/javascript">
  var map = new google.maps.Map2(document.getElementById('map'));
  $('#layout_map').hide();
  function show_map_and_marker(type, latlng) {
    $('#layout_map div h2').html(type.substr(0, 1).toUpperCase() + type.substr(1) + ' on a map');
		map.clearOverlays();
		map.setCenter(latlng, 13);
		map.addOverlay(new GMarker(latlng));
		$('#layout_map').show();
		$.facebox($('#layout_map'));		
	}
</script>
<?php endif; ?>