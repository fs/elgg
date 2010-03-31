<script type="text/javascript" src="http://www.google.com/jsapi?key=<?= $GLOBALS['google_api'] ?>"></script>
<script type="text/javascript" src="http://j.maxmind.com/app/geoip.js"></script>
<script type="text/javascript">

var $form = null;

$(function () {
	$form = $('#blogPostForm');
	$form.append(
		'<input type="hidden" value="" name="latitude" id="geolocation_latitude" />' +
		'<input type="hidden" value="" name="latitude" id="geolocation_longitude" />'
	);
});

google.load("maps", "2.x");
google.setOnLoadCallback(function () {
	var lt = geoip_latitude();
	var lg = geoip_longitude();
	
	var map = new google.maps.Map2(document.getElementById("map"));
	var center = new GLatLng(lt, lg);
	map.setCenter(center, 13);
	map.setUIToDefault();
	
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

	function store_point_location () {
		var point = marker.getLatLng();
		$form.find('#geolocation_latitude').val(point.y);
		$form.find('#geolocation_longitude').val(point.x);
	}
	
	store_point_location();
	
	GEvent.addListener(marker, "dragend", store_point_location);
	
	window.set_center = function (lt, lg) {
		map.setCenter(new GLatLng(lt, lg), 1);
		return false;
	};
});

</script>

<div id="map" style="height:400px"></div>
