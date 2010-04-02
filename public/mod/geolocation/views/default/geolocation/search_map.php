<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?= $vars['map_api'] ?>" 
		type="text/javascript"></script>
<script type="text/javascript">

	var markers = new Array();
	
	jQuery(function() {
		if (GBrowserIsCompatible()) {
			
			var map = new GMap2(document.getElementById("map_canvas"));
			map.setCenter(new GLatLng(37.4328, -122.077), 13);
			map.addControl(new GSmallMapControl());
			
			var marker_1 = new GMarker(new GLatLng(37.4268, -122.065));
			GEvent.addListener(marker_1, "click", function() {
				var html = '<div style="width: 210px; padding-right: 10px"><a href="/apis/maps/signup.html">Sign up<\/a> for a Google Maps API key, or <a href="/apis/maps/documentation/index.html">read more about the API<\/a>.<\/div>';
				marker_1.openInfoWindowHtml(html);
			});
			map.addOverlay(marker_1);
			GEvent.trigger(marker_1, "click");
			markers[1] = marker_1;
			
			var marker_2 = new GMarker(new GLatLng(37.4228, -122.085));
			GEvent.addListener(marker_2, "click", function() {
				var html = '<div style="width: 210px; padding-right: 10px"><a href="/apis/maps/signup.html">Sign up<\/a> for a Google Maps API key, or <a href="/apis/maps/documentation/index.html">read more about the API<\/a>.<\/div>';
				marker_2.openInfoWindowHtml(html);
			});
			marker_2
			map.addOverlay(marker_2);
			GEvent.trigger(marker_2, "click");
			markers[2] = marker_2;
			
			var marker_3 = new GMarker(new GLatLng(37.4178, -122.115));
			GEvent.addListener(marker_3, "click", function() {
				var html = '<div style="width: 210px; padding-right: 10px"><a href="/apis/maps/signup.html">Sign up<\/a> for a Google Maps API key, or <a href="/apis/maps/documentation/index.html">read more about the API<\/a>.<\/div>';
				marker_3.openInfoWindowHtml(html);
			});
			map.addOverlay(marker_3);
			GEvent.trigger(marker_3, "click");
			markers[3] = marker_3;
			
		}
	});

	function selectMarker(n) {
		GEvent.trigger(markers[n], "click");
	}

</script>

<div id="map_canvas" style="border: 1px solid #979797; background-color: #e5e3df; width: 500px; height: 300px; margin:0 4em;">
	<div style="padding: 1em; color: gray">Loading...</div>
</div>
