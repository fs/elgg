<?php

	/**
	 * Elgg profile icon hover over: actions
	 * 
	 * @package ElggProfile
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Curverider Ltd <info@elgg.com>
	 * @copyright Curverider Ltd 2008-2010
	 * @link http://elgg.com/
	 * 
	 * @uses $vars['entity'] The user entity. If none specified, the current user is assumed. 
	 */
	
	$user = $_SESSION['user'];
	
	if (isloggedin() && $user->getGUID() == $vars['entity']->getGUID()) {
		
		/*
		if (empty($user->home_latitude)) {
			create_metadata($user->guid, 'home_latitude', 0, 'text', $user->guid, $access_id);
		}
		
		if (empty($vars['entity']->home_latitude)) {
			$vars['entity']->home_latitude = '0';
		}
		if (empty($vars['entity']->home_longitude)) {
			$vars['entity']->home_longitude = '0';
		}
		
		if (empty($vars['entity']->home_latitude)) {
			$vars['entity']->home_latitude = '0';
		}
		if (empty($vars['entity']->home_longitude)) {
			$vars['entity']->home_longitude = '0';
		}
		*/
		
		$h_lt = $vars['entity']->home_latitude or
		$h_lt = 0;
		$h_lg = $vars['entity']->home_longitude or
		$h_lg = 0;
		
		$c_lt = $vars['entity']->current_latitude or
		$c_lt = 0;
		$c_lg = $vars['entity']->current_longitude or
		$c_lg = 0;
		
		?>
		
		<script src="http://maps.google.com/maps?file=api&amp;v=3&amp;key=<?= $GLOBALS['google_api'] ?>"></script>
		<script type="text/javascript" src="http://j.maxmind.com/app/geoip.js"></script>
		<script type="text/javascript">
		
		var $form = null;
		var points = new Array();
		var map = null;
		var current_type = null;
		var marker = null;
		
		$(function () {
			
			var h_lt = <?= $h_lt ?> || geoip_latitude();
			var h_lg = <?= $h_lg ?> || geoip_longitude();
			
			var c_lt = <?= $c_lt ?> || geoip_latitude();
			var c_lg = <?= $c_lg ?> || geoip_longitude();
			
			
			
			points['home'] = {'lt' : h_lt, 'lg' : h_lg};
			points['current'] = {'lt' : c_lt, 'lg' : c_lg};
			
			$form = $('input.submit_button').parents()
											.map(function () {
													if (this.tagName == 'FORM') {
														return this;
													}
											});
			$form.prepend(
				'<p><a href="#" onclick="show_user_map(\'home\');return false;">Set Home Location</a></p>'+
				'<p><a href="#" onclick="show_user_map();return false;">Set Current Location</a></p>'+
				'<p>'+
				'<input type="hidden" value="<?=$h_lt?>" name="home_latitude" id="home_geolocation_latitude" />' +
				'<input type="hidden" value="<?=$h_lg?>" name="home_longitude" id="home_geolocation_longitude" />'+
				'<input type="hidden" value="<?=$c_lt?>" name="current_latitude" id="current_geolocation_latitude" />' +
				'<input type="hidden" value="<?=$c_lg?>" name="current_longitude" id="current_geolocation_longitude" />'+
				'</p>'
			);
			
			$form.find('input#home_geolocation_latitude').val(h_lt);
			$form.find('input#home_geolocation_longitude').val(h_lg);
			$form.find('input#current_geolocation_latitude').val(c_lt);
			$form.find('input#current_geolocation_longitude').val(c_lg);
			

			map = new google.maps.Map2(document.getElementById("map"));
			map.setUIToDefault();
			$('#layout_map').hide();
			
			// Create our "tiny" marker icon
			// var blueIcon = new GIcon(G_DEFAULT_ICON);
			// blueIcon.image = "images/label.png";
			
			// Set up our GMarkerOptions object
			//markerOptions = { icon:blueIcon };
			
			window.set_center = function (lt, lg) {
				map.setCenter(new GLatLng(lt, lg), 1);
				return false;
			};
			
		});
		
		function store_point_location(point) {
			
			$form.find('#' + current_type + '_geolocation_latitude').val(point.y);
			$form.find('#' + current_type + '_geolocation_longitude').val(point.x);
			
		}
		
		function show_user_map(type) {
			
			if (type != 'home') {
				type = 'current';
			}
			
			current_type = type;
			
			if (GBrowserIsCompatible()) {
			
				$('#layout_map div h2').html(type.substr(0, 1).toUpperCase() + type.substr(1) + ' location on a map');
				$('#layout_map').show();
				$.facebox($('#layout_map'));
				
				$('#geosearch #query').val('');
				map.clearOverlays();
				
				var lt = $form.find('#' + current_type + '_geolocation_latitude').val();
				var lg = $form.find('#' + current_type + '_geolocation_longitude').val();
				var p = new GLatLng(lt, lg);
				marker = new GMarker(p, {draggable: true});
				
				map.setCenter(p, 11);
				map.addOverlay(marker);
				
				store_point_location(p);
				
				GEvent.addListener(marker, "dragstart", function() {
					map.closeInfoWindow();
				});
				
				GEvent.addListener(marker, "dragend", function(point) {
					store_point_location(point);
				});
				
				GEvent.addListener(map, "click", function(overlay, latlng) {
					
					marker.setLatLng(latlng);
					store_point_location(latlng);
				});
				
				$('#geosearch').submit(function() {
					geocode();
					return false;
				});
				
				$(document).bind('close.facebox', function() { 
					
				});
				
			}
			
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
				if (!response.Placemark || !response.Placemark[0]) {
					return;
				}
				//console.log(response);
				box = response.Placemark[0].ExtendedData.LatLonBox;
				//console.log(box);
				latlng = new GLatLng(response.Placemark[0].Point.coordinates[1],
								 response.Placemark[0].Point.coordinates[0]);
				
				marker.setLatLng(latlng);
				map.panTo(latlng);
				store_point_location(latlng);
			}
		}
		
		</script>
		
		<div id="layout_map">
			<div id="content_area_user_title"><h2>Locations on map</h2></div>
			<div><form name="geosearch" id="geosearch" onsubmit="return false;"><input type="text" name="query" id="query" value=""/><input type="submit" id="query_submit" value="Search" /></form></div>
			<div id="map" style="left:30px;">
				<div style="padding: 1em; color: gray">Loading...</div>
			</div>
		</div>
		
		<?
		
	}
	
	
?>
