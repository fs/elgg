<?php
	/**
	 * Plugin geolocation
	 * Mobworking.net geocoder
	 * 
	 * @author Marcus Povey <marcus@dushka.co.uk>
	 * @copyright Marcus Povey 2008-2009
	 */


	function geolocation_init()
	{
		global $CONFIG;
		
		$GLOBALS['google_api'] = get_plugin_setting('google_api', 'geolocation');
		
		// Register geocoder hook
		register_plugin_hook('geolocation', 'location', 'geolocation_geocode');
		
		// Listen to create events on a low priority
		register_elgg_event_handler('create','all','geolocation_tagger', 900);
		register_elgg_event_handler('update','all','geolocation_tagger', 900);
		
		register_plugin_hook('search', 'all', 'my_subtype_search_hook');
		
		// extend user functionality
		extend_elgg_settings_page('geolocation/scripts','usersettings/user', 900);
		extend_elgg_settings_page('geolocation/geo_input', 'usersettings/user', 900);
		register_plugin_hook('usersettings:save','user','geolocation_user_settings_save');
		
		//register_page_handler('map_search','geolocation_page_handler');
		register_page_handler('geolocation','geolocation_page_handler');
		
		// extend some views
		elgg_extend_view('search/listing', 'geolocation/search_link');
		//elgg_extend_view('search/entity_list', 'geolocation/search_map');
		elgg_extend_view('css','geolocation/css');
		// extend blog views
		elgg_extend_view('blog/forms/edit', 'geolocation/scripts');
		elgg_extend_view('blog/forms/edit','geolocation/geo_input');
		// extend bookmarks views
		elgg_extend_view('bookmarks/form', 'geolocation/scripts');
		elgg_extend_view('bookmarks/form','geolocation/geo_input');
		// extend file views
		elgg_extend_view('file/upload', 'geolocation/scripts');
		elgg_extend_view('file/upload','geolocation/geo_input');

		elgg_extend_view('canvas_header/submenu_group','geolocation/search_all_link');
		elgg_extend_view('canvas_header/submenu_group','geolocation/search_kml_link');
		
		elgg_extend_view('canvas/layouts/two_column_left_sidebar', 'geolocation/search', 1000);
		
		elgg_extend_view('profile/edit','geolocation/profile_links');
		
		register_elgg_event_handler('profileupdate','all','geolocation_profile_update');
		
	}
	
	function my_subtype_search_hook($hook, $entity_type, $returnvalue, $params) {
		
		if (!isset($GLOBALS['my_search_result'])) {
			$GLOBALS['my_search_result'] = array();
		}
		if (empty($GLOBALS['search_count'])) {
			$GLOBALS['search_count'] = 0;
		}
		
		if (is_array($returnvalue['entities'])) {
			$GLOBALS['my_search_result'] = array_merge($GLOBALS['my_search_result'], $returnvalue['entities']);
		}
		
		$GLOBALS['search_count'] = (int) $returnvalue['count'] + $GLOBALS['search_count'];
		
		// Change search results
		//return array('count' => count($entities), 'entities' => $entities);
		
	}
	
	function geolocation_profile_update($event, $object_type, $object) {
		if ($object instanceof ElggUser) {
			
			if ($object_type == 'metadata') {
				$object = $object->getEntity();
			}
			if (isset($GLOBALS['profile_stop_recursion']) && $GLOBALS['profile_stop_recursion'] == $object->guid) {
				return;
			}
			
			$GLOBALS['profile_stop_recursion'] = $object->guid;
			
			$home_latitude = get_input('home_latitude');
			$home_longitude = get_input('home_longitude');
			
			$current_latitude = get_input('current_latitude');
			$current_longitude = get_input('current_longitude');
			
			if ($current_latitude && $current_longitude) {
				$object->setLatLong($current_latitude, $current_longitude);
				$object->current_latitude = $current_latitude;
				$object->current_longitude = $current_longitude;
			}
			if ($home_latitude && $home_longitude) {
				$object->home_latitude = $home_latitude;
				$object->home_longitude = $home_longitude;
			}
			
		}
	}
	
	function geolocation_page_handler($page) {
		
		global $CONFIG;
		
		// The second part dictates what we're doing
		$screen = $page[0];
		
		switch ($screen) {
			
			case 'kml':
				echo elgg_extend_view('blog/forms/edit','geolocation/geo_input');
				break;
			
			default:
				
				break;
			
		}
		
		exit;
		
	}
	
	function geolocation_user_settings_save() {
		
		gatekeeper();
		
		$user_id = get_input('guid');
		$user = null;
		$error = false;
		
		if (!$user_id) {
			$user = get_loggedin_user();
		} else {
			$user = get_entity($user_id);
		}
		
		if (($user) && ($user->canEdit())) {
			$lat = get_input('latitude');
			$lang = get_input('longitude');
			$user->setLatLong($lat, $lang);
			$user->location = null;
			if (!$user->save()) {
				$error = true;
			}
		
		} else {
			$error = true;
		}
		
	}
	
	/** 
	 * Google geocoder.
	 *
	 * Listen for an Elgg Geocode request and use google maps to geocode it.
	 */
	function geolocation_geocode($hook, $entity_type, $returnvalue, $params)
	{
		if (isset($params['location']))
		{
			$google_api = get_plugin_setting('google_api', 'geolocation');
		
			// Desired address
		   	$address = "http://maps.google.com/maps/geo?q=".urlencode($params['location'])."&output=json&key=" . $google_api;
			
		   	// Retrieve the URL contents
	   		$result = file_get_contents($address);
	   		$obj = json_decode($result);
	   		
	   		$obj = $obj->Placemark[0]->Point->coordinates;
			
	   		return array('lat' => $obj[1], 'long' => $obj[0]);
			
		}
	}
	
	/**
	 * Listen to the create events of new Locatable things and tag
	 * them with a location (if possible).
	 */ 
	function geolocation_tagger($event, $object_type, $object)
	{
		
		if ($object_type == 'metadata') {
			$object = $object->getEntity();
		}
		
		if ($object instanceof ElggUser) {
			return;
		}
		
		if (isset($GLOBALS['stop_recursion']) && $GLOBALS['stop_recursion'] == $object->guid) {
			return;
		}
		
		if ($object instanceof Locatable) {
			$location = false;
			
			// Nope, so use input params
			if (!$location) {
				$GLOBALS['stop_recursion'] = $object->guid;
				$lat = get_input('latitude');
				$lang = get_input('longitude');
				if ($lat && $lang) {
					$object->setLatLong($lat, $lang);
					return;
				}
			}
			
			// Nope, so use logged in user
			if (!$location) {
				$user = get_loggedin_user();
				if (($user) && (isset($user->location)))
					$location = $user->location;
			}
			
			// Have we got a location
			if ($location)
			{
				// Handle when location is given in a tag field (as it is with users)
				if (is_array($location))
					$location = implode(', ', $location);
				
				$latlong = elgg_geocode_location($location);
				
				if ($latlong)
				{
					$object->setLatLong($latlong['lat'], $latlong['long']);
					$object->setLocation($location);
				}
			}
		}
	}
	
	require_once 'models/functions.php';

	// Initialisation
	register_elgg_event_handler('init','system','geolocation_init');
?>
