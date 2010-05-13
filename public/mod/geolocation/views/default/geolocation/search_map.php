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
			
			//prepareGeolocation();
			doGeolocation();
			
			if(is_all == '1') { refreshMap(data.marker); }
			else { refreshMap(); }
        }
	}

	function markerClick(url, latlng) {

		return function() {
			map.openInfoWindowHtml(latlng, url, {maxWidth:300, maxHeight:300, autoScroll:true});
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

	function doGeolocation() {
		if (navigator.geolocation) {
			navigator.geolocation.getCurrentPosition(positionSuccess, positionError);
		} else {
			//alert("Location detection not supported in browser");
		}
	}

	function positionError(err) {
		
	}

	function positionSuccess(position) {
		// Centre the map on the new location
		var coords = position.coords || position.coordinate || position;
		var latLng = new google.maps.LatLng(coords.latitude, coords.longitude);
		map.setCenter(latLng);
		map.setZoom(12);
		alert(position);
		var marker = new map.Marker({
			map: map,
			position: latLng,
			title: 'Why, there you are!'
		});
		//document.getElementById('info').innerHTML = 'Looking for <b>' +
		//coords.latitude + ', ' + coords.longitude + '</b>...';
		
		// And reverse geocode.
		/*
		(new google.maps.Geocoder()).geocode({latLng: latLng}, function(resp) {
			var place = "You're around here somewhere!";
			if (resp[0]) {
				var bits = [];
				for (var i = 0, I = resp[0].address_components.length; i < I; ++i) {
					var component = resp[0].address_components[i];
					if (contains(component.types, 'political')) {
						bits.push('<b>' + component.long_name + '</b>');
					}
				}
				if (bits.length) {
					place = bits.join(' > ');
				}
				marker.setTitle(resp[0].formatted_address);
			}
			//document.getElementById('info').innerHTML = place;
		})
		*/;
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
