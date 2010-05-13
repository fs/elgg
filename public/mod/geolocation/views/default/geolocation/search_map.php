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

	?>

<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?= $GLOBALS['google_api'] ?>" type="text/javascript"></script>
<script src="/mod/geolocation/js/markerclusterer.js" type="text/javascript"></script>
<script src="/pg/geolocation/data?types=all" type="text/javascript"></script>
<script type="text/javascript">
	var all_markers = Array();
	var map = null;
	var markers = [];
	var markerClusterer = null;
	function show_map(points) {
		var is_all = 0;
		markers = []
		if(points == 'all') {
			is_all = 1;
			points = data.marker
		}

        if(GBrowserIsCompatible()) {
			$('#layout_map').show();
			$.facebox($('#layout_map'));

			bounds = new GLatLngBounds();

			var icon = new GIcon(G_DEFAULT_ICON);
			icon.image = "http://chart.apis.google.com/chart?cht=mm&chs=24x32&chco=FFFFFF,008CFF,000000&ext=.png";
			for (i in points) {
				if(is_all == '1') {
					var latlng = new GLatLng(points[i].latitude, points[i].longitude);
					var marker = new GMarker(latlng);

					if(points[i].desc) {
						var fn = markerClick(points[i].desc, latlng);
						GEvent.addListener(marker, "click", fn);
					}
				} else {
					var marker = points[i];
				}
				bounds.extend(marker.getLatLng());

				markers.push(marker);
			}

			var center = bounds.getCenter();
			var zoom   = map.getBoundsZoomLevel(bounds);
			map.setCenter(center, zoom);
			map.addControl(new GLargeMapControl());
			map.addControl(new GMapTypeControl());
			
			if(is_all == '1') { refreshMap(data.marker); }
			else { refreshMap(); }
        }
	}

	function refreshMap(desc) {

        if (markerClusterer != null) {
			markerClusterer.clearMarkers();
        }

        zoom = -1 
        size = -1 
        style = "-1"
		if(typeof desc === 'undefined') { markerClusterer = new MarkerClusterer(map, markers); }
		else { markerClusterer = new MarkerClusterer(map, markers, [], desc); }
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
