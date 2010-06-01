<?php
require_once 'scripts.php'; 
?>

<?php if (get_context() == "profile"): 

    $username = get_input('username');
    $user = get_user_by_username($username);
    $user_guid=$user['guid'];
    $user_entity = get_entity($user_guid);

 ?>


<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?= $GLOBALS['google_api'] ?>" type="text/javascript"></script>
<p class="user_menu_item">
  <a href="javascript:show_map_and_marker('<?php echo elgg_echo("geolocation:home_location"); ?>', new GLatLng('<?php echo $user_entity->home_latitude; ?>', '<?php echo $user_entity->home_longitude; ?>'));"><?php echo elgg_echo("geolocation:home_location"); ?></a>
</p>
<p class="user_menu_item">
  <a href="javascript:show_map_and_marker('<?php echo elgg_echo("geolocation:current_location"); ?>', new GLatLng('<?php echo $user_entity->current_latitude; ?>', '<?php echo $user_entity->current_longitude; ?>'));"><?php echo elgg_echo("geolocation:current_location"); ?></a>
</p>
<div id="layout_map">
	<div id="content_area_user_title"><h2>Locations on map</h2></div>
        <div id="my_location_button" onclick="javascript:doGeolocation()"  title="Where am I?"></div>
	<div id="map">
		<div style="padding: 1em; color: gray">Loading...</div>
	</div>
</div>

<script type="text/javascript">
  var map = new google.maps.Map2(document.getElementById('map'));
  $('#layout_map').hide();

  function show_map_and_marker(type, latlng) {

    $('#layout_map div h2').html(type.substr(0, 1).toUpperCase() + type.substr(1) + ' on a map');

    map.clearOverlays();

        var marker = new GMarker(latlng);
        map.addOverlay(marker);
        map.setCenter(latlng, 12);

//    set_location(latlng, false);

    map.setUIToDefault();

    $('#layout_map').show();
    $.facebox($('#layout_map'));
}
</script>
<?php endif; ?>

