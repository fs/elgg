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
<script type="text/javascript" src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?= $GLOBALS['google_api'] ?>"></script>
<script type="text/javascript" src="http://j.maxmind.com/app/geoip.js"></script>

<script type="text/javascript">



var $form = null;
var map = null;
var p = null;
var marker = null;
var center = null;
var lt = null;
var lg = null;

//function showResponse(response, reverse) {
//
//		if (! response) {
//			alert("Geocoder request failed");
//		} else {
//			if (!response.Placemark || !response.Placemark[0]) {
//				return;
//			}
//
//			box = response.Placemark[0].ExtendedData.LatLonBox;
//			latlng = new GLatLng(response.Placemark[0].Point.coordinates[1],
//			response.Placemark[0].Point.coordinates[0]);
//
//			set_location(latlng, true);
//		}
//	}

function showResponse(response, reverse) {
	if (! response) {
		alert("Geocoder request failed");
	} else {
		latlng = new GLatLng(response.Placemark[0].Point.coordinates[1],
							 response.Placemark[0].Point.coordinates[0]);
		set_location(latlng, true);
	}
}


$(function () {
	$form = $('input.submit_button').parents().map(function () {
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
	
	lt = <?= $lt ?> || geoip_latitude();
	lg = <?= $lg ?> || geoip_longitude();
	
	$('div.map').show();
	map = new google.maps.Map2(document.getElementById("map"));
	map.setUIToDefault();
	
	$('div.map').hide();
	
	// Create our "tiny" marker icon
	//var blueIcon = new GIcon(G_DEFAULT_ICON);
	//blueIcon.image = "images/label.png";
	
	// Set up our GMarkerOptions object
	//markerOptions = { icon:blueIcon };
	
	latlng = new GLatLng(lt, lg);
        set_location(latlng, true);

	$('#geosearch').submit(function() {
		geocode();
		return false;
	});
	
});

function positionSuccess(position) {
	// Centre the map on the new location
	var coords = position.coords;
	var new_latLng = new GLatLng(coords.latitude, coords.longitude);

        set_location(new_latLng, true);
}

function store_point_location(point) {
	if (!point) {
		point = where_i_am_marker.getLatLng();
	}        

	$form.find('#geolocation_latitude').val(point.y);
	$form.find('#geolocation_longitude').val(point.x);
}

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
		<div id="my_location_button" onclick="javascript:doGeolocation()"  title="Where am I?"></div>
		<div id="map"></div>
	</div>
</div>