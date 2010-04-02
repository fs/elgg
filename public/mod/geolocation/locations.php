<?php

	/**
	 * Elgg geolocation list page
	 *
	 * @package GeoLocations
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Alexander Ulitin <alexander.ulitin@flatsoft.com>
	 * @copyright FlatSourcing 2010
	 * @link http://elgg.org/
	 */

	// Load Elgg engine
	global $CONFIG;
	require_once($CONFIG->path . "engine/start.php");
	$user = $_SESSION['user'];
	
	// Get the current page's owner
	$page_owner = page_owner_entity();
	if ($page_owner === false || is_null($page_owner)) {
		$page_owner = $_SESSION['user'];
		set_page_owner($_SESSION['guid']);
	}

	$area2 = elgg_view_title(elgg_echo('geolocation:googlemaps'));

	// Get a list of google sites
	$area2 .= '<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false&region=GB">
	<script type="text/javascript">
	
		function initialize() {
			
			var myLatlng = new google.maps.LatLng(-34.397, 150.644);
			var myOptions = {
				zoom: 8,
				center: myLatlng,
				mapTypeId: google.maps.MapTypeId.ROADMAP
			}
			var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
			
		}
		
		$(function () {
			initialize();
		});
	
	</script>

	';

	$body = elgg_view_layout("two_column_left_sidebar", '', $area1 . $area2, '');

	// Get categories, if they're installed
	//global $CONFIG;
	//$area3 = elgg_view('blog/categorylist',array('baseurl' => $CONFIG->wwwroot . 'search/?subtype=blog&owner_guid='.$page_owner->guid.'&tagtype=universal_categories&tag=','subtype' => 'blog', 'owner_guid' => $page_owner->guid));

	// Display them in the page
	$body = elgg_view_layout("two_column_left_sidebar", '', $area1 . $area2, $area3);

	// Display page
	page_draw(elgg_echo('geolocation:googlemaps'), $body);

?>
