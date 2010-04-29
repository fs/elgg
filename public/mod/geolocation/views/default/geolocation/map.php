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
		map.setUIToDefault();
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

	function refreshMap(datajson) {
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

		var center = bounds.getCenter();
		var zoom   = map.getBoundsZoomLevel(bounds);
		map.setCenter(center, zoom);

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
					refreshMap(result);
				}
            });
            return false; // <-- important!
        });
    });
</script>


<div>
	<form method="GET" action="" id="typesForm">
		<ul>
			<?php foreach($vars['select_checkboxes'] as $item): ?>
			<li>
				<input id="label_<?php echo $item['name']; ?>" type="checkbox" name="check_types[]" value="<?php echo $item['name']; ?>" />
				<label for="label_<?php echo $item['name']; ?>"><?php echo $item['label']; ?></label>
			</li>
			<?php endforeach; ?>
			<li>Select: <a href="javascript:toggleAll(1)">All</a> | <a href="javascript:toggleAll(0)">None</a></li>
			<li><input type="submit" name="do" value="Update map"></li>
		</ul>
	</form>
</div>
<div id="map" style="width: 100%">
	<div style="padding: 1em; color: gray">Loading...</div>
</div>