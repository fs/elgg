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
	center = new GLatLng(lt, lg);
	map.setCenter(center, 13);
	map.setUIToDefault();
	
	$('div.map').hide();
	
	// Create our "tiny" marker icon
	//var blueIcon = new GIcon(G_DEFAULT_ICON);
	//blueIcon.image = "images/label.png";
	
	// Set up our GMarkerOptions object
	//markerOptions = { icon:blueIcon };
	
	p = new GLatLng(lt, lg);
	marker = new GMarker(center, {draggable: true});

	GEvent.addListener(marker, "dragstart", function() {
		map.closeInfoWindow();
	});

	map.addOverlay(marker);

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

});

function store_point_location(point) {
	if (!point) {
		point = marker.getLatLng();
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
		<div id="map"></div>
	</div>
</div>
