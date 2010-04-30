<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?= $GLOBALS['google_api'] ?>" type="text/javascript"></script>
<script src="/mod/geolocation/js/markerclusterer.js" type="text/javascript"></script>
<script src="/pg/geolocation/data?types=all" type="text/javascript"></script>
<script type="text/javascript">
	var markerClusterer = null;
	var markers = [];

	function markerClick(url, latlng) {
		return function() {
			map.openInfoWindowHtml(latlng, url, {maxWidth:300, maxHeight:300, autoScroll:true});
		}
	}

	jQuery(function() {
		map = new google.maps.Map2(document.getElementById("map"));		
		refreshMap(data);
	});

	function toggleAll(toggle) {
		var allCheckboxes = $("#typesForm input:checkbox:enabled");
		if(toggle) {
			allCheckboxes.attr('checked', 'checked');
		} else {
			allCheckboxes.removeAttr('checked');
		}
	}

	function setPosition() {
		var center = bounds.getCenter();
		var zoom   = map.getBoundsZoomLevel(bounds);

		map.setCenter(center, zoom);
	}

	function loadMarkers(datajson) {
		markers = [];
		var points = datajson.marker;
		bounds = new GLatLngBounds();

		for (i in points) {
			var latlng = new GLatLng(points[i].latitude, points[i].longitude);
			var marker = new GMarker(latlng);
			bounds.extend(marker.getLatLng());
			if(points[i].desc) {
				var fn = markerClick(points[i].desc, latlng);
				GEvent.addListener(marker, "click", fn);
			}

			markers.push(marker);
		}
	}

	function refreshMarkers(datajson){
		loadMarkers(datajson);
		
		map.addControl(new GLargeMapControl());
		map.addControl(new GMapTypeControl());

		if (markerClusterer != null) {
			markerClusterer.clearMarkers();
        }
        markerClusterer = new MarkerClusterer(map, markers);
	}

	function refreshMap(datajson) {
		loadMarkers(datajson);

		setPosition();

		map.addControl(new GLargeMapControl());
		map.addControl(new GMapTypeControl());

		if (markerClusterer != null) {
			markerClusterer.clearMarkers();
        }
        markerClusterer = new MarkerClusterer(map, markers);
	}

	$(document).ready(function() {
        $('#typesForm').bind('submit', function() {
            $(this).ajaxSubmit({
                url: '/pg/geolocation/data',
				dataType : "json",                     // тип загружаемых данных
				success: function (result) { // вешаем свой обработчик на функцию success
					refreshMarkers(result);
				}
            });
            return false; // <-- important!
        });

		$('#geosearch').submit(function() {
			geocode();
			return false;
		});
    });

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
			map.panTo(latlng);
			//refreshMap(data, latlng)
		}
	}

</script>


<div class="filter-toolbar">
	<form method="GET" action="" id="typesForm">
		<p class="title">Include on map:</p>
		<ul>
			<?php foreach($vars['select_checkboxes'] as $item): ?>
			<li>
				<input id="label_<?php echo $item['name']; ?>" type="checkbox" name="check_types[]" value="<?php echo $item['name']; ?>" />
				<label for="label_<?php echo $item['name']; ?>"><?php echo $item['label']; ?></label>
			</li>
			<?php endforeach; ?>
		</ul>
		<div class="update-map">
			<span>Select: <a href="javascript:toggleAll(1)">All</a> | <a href="javascript:toggleAll(0)">None</a></span>
			<span><input type="submit" name="do" value="Update map"></span>
		</div>
	</form>
</div>
<div class="google-map">
	<div class="geosearch single">
		<form name="geosearch" id="geosearch" onsubmit="return false;">
			<input type="text" name="query" id="query" value=""/>
			<input type="submit" id="query_submit" value="Search" />
		</form>
	</div>
	<div id="map" style="width: 100%">
		<div style="padding: 1em; color: gray">Loading...</div>
	</div>
</div>