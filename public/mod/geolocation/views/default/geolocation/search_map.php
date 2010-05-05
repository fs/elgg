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
	var styles = [[{
				url: '../images/people35.png',
				height: 35,
				width: 35,
				opt_anchor: [16, 0],
				opt_textColor: '#FF00FF'
			},
			{
				url: '../images/people45.png',
				height: 45,
				width: 45,
				opt_anchor: [24, 0],
				opt_textColor: '#FF0000'
			},
			{
				url: '../images/people55.png',
				height: 55,
				width: 55,
				opt_anchor: [32, 0]
			}],
		[{
				url: '../images/conv30.png',
				height: 27,
				width: 30,
				anchor: [3, 0],
				textColor: '#FF00FF'
			},
			{
				url: '../images/conv40.png',
				height: 36,
				width: 40,
				opt_anchor: [6, 0],
				opt_textColor: '#FF0000'
			},
			{
				url: '../images/conv50.png',
				width: 50,
				height: 45,
				opt_anchor: [8, 0]
			}],
		[{
				url: '../images/heart30.png',
				height: 26,
				width: 30,
				opt_anchor: [4, 0],
				opt_textColor: '#FF00FF'
			},
			{
				url: '../images/heart40.png',
				height: 35,
				width: 40,
				opt_anchor: [8, 0],
				opt_textColor: '#FF0000'
			},
			{
				url: '../images/heart50.png',
				width: 50,
				height: 44,
				opt_anchor: [12, 0]
			}]];
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
