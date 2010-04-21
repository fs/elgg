<?php
if ($vars['entity']) {
	$lg = $vars['entity']->getLongitude() or
	$lg = 0;
	$lt = $vars['entity']->getLatitude() or
	$lt = 0;
} else if ($vars['user']) {
	$lg = $vars['user']->getLongitude() or
	$lg = 0;
	$lt = $vars['user']->getLatitude() or
	$lt = 0;
} else {
	$lg = 0;
	$lt = 0;
}
?>
<script type="text/javascript" src="http://www.google.com/jsapi?key=<?= $GLOBALS['google_api'] ?>"></script>
<script type="text/javascript" src="http://j.maxmind.com/app/geoip.js"></script>

<script type="text/javascript">

var $form = null;

$(function () {
	$form = $('input.submit_button').parents()
									.map(function () {
											if (this.tagName == 'FORM') {
												return this;
											}
									});

	if (!$form.html()) {
		$form = $('div.contentWrapper form').map(function () {
												if (this.action.indexOf('action/bookmarks/add')) {
													return this;
												}
											});
		
	}
	
	$form.append(
		'<input type="hidden" value="" name="latitude" id="geolocation_latitude" />' +
		'<input type="hidden" value="" name="longitude" id="geolocation_longitude" />'
	);
});

google.load("maps", "2.x");
google.setOnLoadCallback(function () {
	var lt = <?= $lt ?> || geoip_latitude();
	var lg = <?= $lg ?> || geoip_longitude();
	
  $('div.map').show();
  var map = new google.maps.Map2(document.getElementById("map"));
	var center = new GLatLng(lt, lg);
	map.setCenter(center, 13);
	map.setUIToDefault();
	
  $('div.map').hide();
	// Create our "tiny" marker icon
	// var blueIcon = new GIcon(G_DEFAULT_ICON);
	// blueIcon.image = "images/label.png";
	
	// Set up our GMarkerOptions object
	//markerOptions = { icon:blueIcon };
	
	var p = new GLatLng(lt, lg);
	var marker = new GMarker(center, {draggable: true});

	GEvent.addListener(marker, "dragstart", function() {
		map.closeInfoWindow();
	});

	map.addOverlay(marker);

	function store_point_location(point) {
		
		if (!point) {
			point = marker.getLatLng();
		}
		
		$form.find('#geolocation_latitude').val(point.y);
		$form.find('#geolocation_longitude').val(point.x);
	}
	
	function geocode() {
		var query = document.getElementById("query").value;
		if (/\s*^\-?\d+(\.\d+)?\s*\,\s*\-?\d+(\.\d+)?\s*$/.test(query)) {
			var latlng = parseLatLng(query);
			if (latlng == null) {
				document.getElementById("query").value = "";
			} else {
				reverseGeocode(latlng);
			}
		} else {
			forwardGeocode(query);
		}
	}
	
	function initGeocoder(query) {
		selected = null;
		
		var hash = 'q=' + query;
		var geocoder = new GClientGeocoder();
		
		hashFragment = '#' + escape(hash);
		window.location.hash = escape(hash);
		return geocoder;
	}
		
	function forwardGeocode(address) {
		var geocoder = initGeocoder(address);
		geocoder.getLocations(address, function(response) {
			showResponse(response, false);
		});  
	}
	
	function reverseGeocode(latlng) {
		var geocoder = initGeocoder(latlng.toUrlValue(6));
		geocoder.getLocations(latlng, function(response) {
			showResponse(response, true);
		});
	}
	
	function parseLatLng(value) {
		value.replace('/\s//g');
		var coords = value.split(',');
		var lat = parseFloat(coords[0]);
		var lng = parseFloat(coords[1]);
		if (isNaN(lat) || isNaN(lng)) {
			return null;
		} else {
			return new GLatLng(lat, lng);
		}
	}
	
	function showResponse(response, reverse) { 
		if (! response) {
			alert("Geocoder request failed");
		} else {
			latlng = new GLatLng(response.Placemark[0].Point.coordinates[1],
							 response.Placemark[0].Point.coordinates[0]);
			
			marker.setLatLng(latlng);
			map.panTo(latlng);
			store_point_location(latlng);
		}
	}
	
	$('#geosearch').submit(function() {
		geocode();
		return false;
	});
	
	store_point_location();
	
	GEvent.addListener(marker, "dragend", store_point_location);
	
	GEvent.addListener(map, "click", function(overlay, latlng) {
		marker.setLatLng(latlng);
		store_point_location(latlng);
	});
	
	window.set_center = function (lt, lg) {
		map.setCenter(new GLatLng(lt, lg), 1);
		return false;
	};
});

</script>
<div class="map-container">
	<label>Location <a href="#" class="view-map-link">view map</a></label>
	<div class="map"><a href="#" class="view-map-link close">Close</a>
		<div class="geosearch">
			<form name="geosearch" id="geosearch" onsubmit="return false;">
				<input type="text" name="query" id="query" value=""/>
				<input type="submit" id="query_submit" value="Search" />
			</form>
		</div>
		<div id="map"></div>
	</div>
</div>
