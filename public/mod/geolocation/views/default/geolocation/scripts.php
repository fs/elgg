<script type="text/javascript">

function markerClick(url, latlng) {
	return function() {
		map.openInfoWindowHtml(latlng, url, {maxWidth:300, maxHeight:300, autoScroll:true});
	}
}

function set_location(new_latlng){        
	map.panTo(new_latlng);
	if (map.getZoom() < 10) {
		map.setZoom(12);
	}
                                        var marker = new GMarker(new_latlng, {draggable: true});
                                        map.addOverlay(marker);
                                        map.setCenter(latlng, 5);

	//marker.setLatLng(new_latlng);
	store_point_location(new_latlng);
}

function set_current_location(){
	var lat = geoip_latitude();
	var lng = geoip_longitude();
	var latlng = new GLatLng(lat, lng);
	set_location(latlng);
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
		set_location(latlng);
	}
}

function doGeolocation() {
		//console.log(navigator);
		if (navigator.geolocation) {
			navigator.geolocation.getCurrentPosition(positionSuccess, positionError);
		} else {
			alert("Location detection not supported in your browser");
		}
	}

function positionError(err) {
	//
	//alert(err);
}

function positionSuccess(position) {
	// Centre the map on the new location
	var coords = position.coords;
	var new_latLng = new GLatLng(coords.latitude, coords.longitude);
	set_location(new_latLng);
}

$(document).ready(function () {
	$('a.view-map-link').click(function () {
		$(this.parentNode.parentNode).children(".map").slideToggle("fast");
		return false;
	});
});

</script>