<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?= $GLOBALS['google_api'] ?>" type="text/javascript"></script>
<script type="text/javascript">
	function show_map_and_marker(map_id, latlng) {
		map = new google.maps.Map2(document.getElementById(map_id));
		map.setCenter(latlng, 13);
		map.addOverlay(new GMarker(latlng));
	}
</script>
<p class="user_menu_friends">
	<a href="<?php echo $vars['url']; ?>pg/friends/<?php echo $vars['entity']->username; ?>/"><?php echo elgg_echo("geolocation:current_location"); ?></a>
</p>
<p class="user_menu_friends_of">
	<a href="<?php echo $vars['url']; ?>pg/friendsof/<?php echo $vars['entity']->username; ?>/"><?php echo elgg_echo("geolocation:home_location"); ?></a>
</p>