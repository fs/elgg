<script type="text/javascript">

$(document).ready(function () {
	$('a.view-map-link').click(function () {
		$(this.parentNode.parentNode).children(".map").slideToggle("fast");
		return false;
	});
});
</script>