<?php $user = $_SESSION['user']; ?>
<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?= $GLOBALS['google_api'] ?>"></script>
<script type="text/javascript" src="http://j.maxmind.com/app/geoip.js"></script>
<?php if (isloggedin() && $user->getGUID() == $vars['entity']->getGUID()):

	if($vars['page'] == 'home') {
		$lat = $vars['entity']->home_latitude or
				$lat = 0;
		$lng = $vars['entity']->home_longitude or
				$lng = 0;
	} else {
		$lat = $vars['entity']->current_latitude or
				$lat = 0;
		$lng = $vars['entity']->current_longitude or
				$lng = 0;
	}

	?>
<div style="position:relative;width:698px;margin:0 10px;">
<div class="geosearch single">
	<form name="geosearch" id="geosearch" onsubmit="return false;">
		<input type="text" name="query" id="query" value=""/>
		<input type="submit" id="query_submit" value="Search" />
	</form>
</div>
<?
if ($vars['page'] == 'current') {
?>
<div id="my_location_button" onclick="javascript:doGeolocation()"  title="Where am I?"></div>
<?
}
?>
<div id="map" class="edit-location">
	<div style="padding: 1em; color: gray">Loading...</div>
</div></div>
<form action="<?php echo $vars['url']; ?>action/profile/edit" method="post" id="location_form" style="margin:10px;position:relative;">
		<?php echo elgg_view('input/securitytoken') ; ?>	
	<input type="hidden" value="<?php echo $lat; ?>" name="<?php echo $vars['page']; ?>_latitude" id="<?php echo $vars['page']; ?>_geolocation_latitude" />
	<input type="hidden" value="<?php echo $lng; ?>" name="<?php echo $vars['page']; ?>_longitude" id="<?php echo $vars['page']; ?>_geolocation_longitude" />
	<?php if ($vars['page'] == 'current'): ?>
	<input type="hidden" value="1" name="set_geolocation_auto_current_location" />	
	<input type="checkbox" value="yes" name="geolocation_auto_current_location" <?php if($user->geolocation_auto_current_location == 'yes') echo ' checked=checked' ;?>/> Set auto current location by ip after login<br />
	<?php endif; ?>
	<input type="submit" id="save_location" name="save" value="Save" />
	<?php if ($vars['page'] == 'current'): ?>
	<a href="javascript:setLocation()" class="set-location">Set current location by my current ip-address</a>
	<?php endif; ?>
</form>
<script type="text/javascript">
	var form = $('#location_form');

	function store_point_location(point) {		
		form.find('#<?php echo $vars['page']; ?>_geolocation_latitude').val(point.y);
		form.find('#<?php echo $vars['page']; ?>_geolocation_longitude').val(point.x);
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
			if (!response.Placemark || !response.Placemark[0]) {
				return;
			}

			box = response.Placemark[0].ExtendedData.LatLonBox;
			latlng = new GLatLng(response.Placemark[0].Point.coordinates[1],
			response.Placemark[0].Point.coordinates[0]);

			marker.setLatLng(latlng);
			map.panTo(latlng);

			store_point_location(latlng);
		}
	}

	function setLocation(){
		var lat = geoip_latitude();
		var lng = geoip_longitude();
		var latlng = new GLatLng(lat, lng);
		map.setCenter(latlng);
		marker.setLatLng(latlng);
		store_point_location(latlng);
	}
	
	function doGeolocation() {
		if (navigator.geolocation) {
			navigator.geolocation.getCurrentPosition(positionSuccess, positionError);
		} else {
			alert("Location detection not supported in your browser");
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
        //map.addOverlay(new GMarker(latLng));
		marker.setLatLng(latlng);
	}

	var lat = <?= $lat ?> || geoip_latitude();
	var lng = <?= $lng ?> || geoip_longitude();
	
	if (GBrowserIsCompatible()) {
		
											
		map = new google.maps.Map2(document.getElementById("map"));
		map.setUIToDefault();
		var latlng = new GLatLng(lat, lng);
		var marker = new GMarker(latlng, {draggable: true});
		map.addOverlay(marker);
		map.setCenter(latlng, 5);

		GEvent.addListener(marker, "dragstart", function() {
			map.closeInfoWindow();
		});

		GEvent.addListener(marker, "dragend", function(latlng) {
			store_point_location(latlng);
		});

		GEvent.addListener(map, "click", function(latlng) {
			marker.setLatLng(latlng);
			store_point_location(latlng);
		});

		$('#geosearch').submit(function() {
			geocode();
			return false;
		});
	}

</script>
<?php endif; ?>