<?php
	/**
	 * Plugin geolocation
	 * 
	 * @author Alexander Ulitin <alexander.ulitin@flatsoft.com>
	 * @copyright Alexander Ulitin 2010
	 */


	function geolocation_init()
	{
		// Register geocoder hook
		register_plugin_hook('geolocation', 'location', 'geolocation_geocode');
		
		// Listen to create events on a low priority
		register_elgg_event_handler('create','all','geolocation_tagger', 1000);
		register_elgg_event_handler('update','all','geolocation_tagger', 1000);
		
		$GLOBALS['google_api'] = get_plugin_setting('google_api', 'geolocation');
		elgg_extend_view('blog/forms/edit', 'geolocation/scripts');
		// extend some views
		elgg_extend_view('css','geolocation/css');
		elgg_extend_view('blog/forms/edit','geolocation/geo_input');
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
		if (isset($GLOBALS['stop_recursion']) && $GLOBALS['stop_recursion'] == $object->guid) {
			return;
		}
		if ($object instanceof Locatable)
		{
			$location = false;
			/* // See if object has a specific location
			if (isset($object->location))
				$location = $object->location;
				
			// If not, see if user has a location
			if (!$location) {
				if (isset($object->owner_guid))
				{
					$user = get_entity($object->owner_guid);
					if (isset($user->location)) $location = $user->location;
				}
			} */
			
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
		//} else
		//die('gotcha!');
	}
	

	// Initialisation
	register_elgg_event_handler('init','system','geolocation_init');
?>
