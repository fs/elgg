<script type="text/javascript">

$(document).ready(function () {
	$('a.view-map-link').click(function () {
		$(this.parentNode.parentNode).children(".map").slideToggle("fast");
		return false;
	});
});

function markerClick(url, latlng) {

	return function() {
		map.openInfoWindowHtml(latlng, url, {maxWidth:300, maxHeight:300, autoScroll:true});
	}

}
</script>