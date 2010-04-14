<?php

//$show_map = get_input('show_map', 0);
$show_map = 1;

$lt = 0;
$lg = 0;

foreach ($vars['entities'] as $entity) {
	$lt = $entity->getLatitude();
	$lg = $entity->getLongitude();
	
	if ($lg && $lg) {
		break;
	}
}

if ($show_map) {
	
?><script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?= $GLOBALS['google_api'] ?>" 
		type="text/javascript"></script>
<script type="text/javascript">

	var map = null;
	var mapType = null;
	var bounds = null;
	var all_markers = Array();
	
	function show_map(points) {
		
		if (points == 'all') {
			
			var all_markers_array = new Array();
			
			for (i in all_markers) {
				all_markers_array = all_markers_array.concat(all_markers[i]);
			}
			
			show_map(all_markers_array);
			
			return;
		}
		
		if (GBrowserIsCompatible()) {
			
			$('#layout_map').show();
			$.facebox($('#layout_map'));
			
			map.clearOverlays();
			
			bounds = new GLatLngBounds();
			
			for (i in points) {
				map.addOverlay(points[i]);
				bounds.extend(points[i].getLatLng());
				//GEvent.trigger(points[i], "click");
			}
			
			var center = bounds.getCenter();
			var zoom   = map.getBoundsZoomLevel(bounds);
			map.setCenter(center, zoom);
			
		}
	}
	
	jQuery(function() {
		
		map = new google.maps.Map2(document.getElementById("map"));
		map.setUIToDefault();
		$('#layout_map').hide();
		
	});

</script>
<div style="clear:both;"></div>

<div id="layout_map">
	<div id="content_area_user_title"><h2>Locations on map</h2></div>
	<div id="map" style="left:30px;">
		<div style="padding: 1em; color: gray">Loading...</div>
	</div>
</div>

<?php
}
?>
