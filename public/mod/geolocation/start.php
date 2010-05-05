<?php
/**
 * Plugin geolocation
 * Mobworking.net geocoder
 *
 * @author Marcus Povey <marcus@dushka.co.uk>
 * @copyright Marcus Povey 2008-2009
 */


function geolocation_init() {
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

	register_page_handler('search_region','geolocation_page_handler');
	register_page_handler('geolocation','geolocation_page_handler');


//extend toolbar
	elgg_extend_view('page_elements/searchbox', 'geolocation/toolbar_link', 400);

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

// extend photo views
	elgg_extend_view('tidypics/forms/upload', 'geolocation/scripts');
	elgg_extend_view('tidypics/forms/upload','geolocation/geo_input');
	elgg_extend_view('tidypics/forms/edit', 'geolocation/scripts');
	elgg_extend_view('tidypics/forms/edit','geolocation/geo_input');

	elgg_extend_view('canvas_header/submenu_group','geolocation/search_all_link');
	elgg_extend_view('canvas_header/submenu_group','geolocation/search_kml_link');
	//elgg_extend_view('canvas_header/submenu_group','geolocation/search_region');

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

	if (get_input('region')) {
		$entities = array();
		$box = geolocation_geocode_box(get_input('region'));

		switch($entity_type) {
			case 'user':

				if (is_array($returnvalue['entities']) && count($returnvalue['entities'])) {
					foreach ($returnvalue['entities'] as $entity) {

						if ($entity->current_latitude && $entity->current_longitude) {
							if ($box->east >= $entity->current_longitude &&
									$box->west <= $entity->current_longitude &&
									$box->north >= $entity->current_latitude &&
									$box->south <= $entity->current_latitude) {
								$entities[] = $entity;
							}
						}

					}
				}

				// Change search results
				return array('count' => count($entities), 'entities' => $entities);


				break;

			case 'object':
				if (is_array($returnvalue['entities']) && count($returnvalue['entities'])) {

					foreach ($returnvalue['entities'] as $entity) {
						if ($entity->getLatitude() && $entity->getLongitude()) {
							if ($box->east >= $entity->getLongitude() &&
									$box->west <= $entity->getLongitude() &&
									$box->north >= $entity->getLatitude() &&
									$box->south <= $entity->getLatitude()) {
								$entities[] = $entity;
							}
						}
					}

				}
				return array('count' => count($entities), 'entities' => $entities);
				break;


			default:

				break;

		}
	}
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

function get_json_markers() {
	global $CONFIG;

	require_once($CONFIG->path . "engine/start.php");

	$check_types = get_input('check_types', false);

	if($check_types) {
		$types = array();
		$subtypes = array();
		foreach($check_types as $item) {
			if( preg_match("/^object_(.*)/", $item, $matches) ) {
				if(!in_array('object', $types)) $types[] = 'object';
				$subtypes[] = $matches[1];
			} else {
				$types[] = $item;
			}
		}
	} elseif(get_input('types') == 'all') {
		$entity_types = get_registered_entity_types();

		$types = array_keys($entity_types);
		$orig_subtypes = $entity_types['object'];
		$subtypes = array();

		$ignore_subtypes = array('site_activity');
		foreach($orig_subtypes as $item) {
			if(!in_array($item, $ignore_subtypes)) $subtypes[] = $item;
		}

	}

	$result = elgg_get_entities(
			array(
			'types' => $types,
			'subtypes' => $subtypes,
			'limit' => 1000
			)
	);

	return $result;
}

function geolocation_page_handler($page) {

	global $CONFIG;

	require_once($CONFIG->path . "engine/start.php");

	switch ($page[0]) {
		case 'data':

			$result = get_json_markers() ;

			if(get_input('view') == 'kml') {
				$GLOBALS['my_search_result'] = $result;
				page_draw('kml', 'body');
				exit();
			}

			$data = array();

			$icons = array(
					'album',
					'image',
					'user',
					'blog',
					'file',
					'video'
			);

			foreach($result as $item) {
				if($item->type == 'user' && $item->current_latitude != null && $item->current_longitude != null  && $item->current_latitude != '0.004806518549043713' && $item->current_longitude != '0.35430908203125') {
					$data['marker'][]['latitude'] = $item->current_latitude;
					$key = count($data['marker'])-1;
					$data['marker'][$key]['longitude'] = $item->current_longitude;
					$data['marker'][$key]['desc'] = '<a href="' . $item->getURL() . '">' . $item->name . '</a>';
					$data['marker'][$key]['desc'] .= $item->description;
					if( in_array($item->type, $icons) ) $data['marker'][$key]['icon'] = $item->type;
				} elseif($item->getLatitude() != null && $item->getLatitude() != '0.004806518549043713' && $item->getLongitude() != '0.35430908203125') {
					$data['marker'][]['latitude'] = $item->getLatitude();
					$key = count($data['marker'])-1;
					$data['marker'][$key]['longitude'] = $item->getLongitude();
					if (get_subtype_from_id($item->subtype) == 'image') {
						$data['marker'][$key]['desc'] = '<a href="' . $item->getURL() . '">image</a>';
					} else {
						$data['marker'][$key]['desc'] = '<a href="' . $item->getURL() . '">' . $item->title . '</a>';
						$data['marker'][$key]['desc'] .= $item->description;
					}

					if( $item->type == 'object' && in_array(get_subtype_from_id($item->subtype), $icons) ) $data['marker'][$key]['icon'] = get_subtype_from_id($item->subtype);
				}
			}


			if(get_input('types') == 'all') echo 'var data = ';
			echo json_encode($data);


			break;

		default:


			$user = $_SESSION['user'];

			// Get the current page's owner
			$page_owner = page_owner_entity();
			if ($page_owner === false || is_null($page_owner)) {
				$page_owner = $_SESSION['user'];
				set_page_owner($_SESSION['guid']);
			}

			// The second part dictates what we're doing
			$screen = $page[0];

			$box = geolocation_geocode_box($screen);

			$results = array();

			$result_list = array();
			$user_list = array();
			$full_list = array();

			$user_list = elgg_get_entities(array('type'=>'user'));
			foreach ($user_list as $entity) {
				if ($entity->current_latitude && $entity->current_longitude) {
					if ($box->east >= $entity->current_longitude &&
							$box->west <= $entity->current_longitude &&
							$box->north >= $entity->current_latitude &&
							$box->south <= $entity->current_latitude) {
						$result_list[] = $entity;
					}
				}
			}

			$full_list = elgg_get_entities(array('type'=>'object'));
			foreach ($full_list as $entity) {
				if ($entity->latitude && $entity->longitude) {
					if ($box->east >= $entity->latitude &&
							$box->west <= $entity->latitude &&
							$box->north >= $entity->longitude &&
							$box->south <= $entity->longitude) {
						$result_list[] = $entity;
					}
				}
			}

			$current_params = array(
					'query' => $screen,
					'offset' => '0',
					'sort' => 'relevance',
					'order' => 'desc',
					'search_type' => 'all'
			);


			$results['entities'] = $result_list;
			$results['count'] = count($result_list);

			require_once dirname(__FILE__) . '/locations.php';

			break;

	}

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
function geolocation_geocode($hook, $entity_type, $returnvalue, $params) {
	if (isset($params['location'])) {
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
 * Google geocoder box.
 *
 * Listen for an Elgg Geocode request and use google maps to geocode it.
 */
function geolocation_geocode_box($location = null) {
	if (!empty($location)) {
		$google_api = get_plugin_setting('google_api', 'geolocation');

// Desired address
		$address = "http://maps.google.com/maps/geo?q=".urlencode($location)."&output=json&key=" . $google_api;

// Retrieve the URL contents
		$result = file_get_contents($address);
		$obj = json_decode($result);
		if (!empty($obj) &&
				!empty($obj->Placemark) &&
				!empty($obj->Placemark[0]) &&
				!empty($obj->Placemark[0]->ExtendedData) &&
				!empty($obj->Placemark[0]->ExtendedData->LatLonBox)) {
			return $obj->Placemark[0]->ExtendedData->LatLonBox;
		}
	} else {
		return false;
	}
}

/**
 * Listen to the create events of new Locatable things and tag
 * them with a location (if possible).
 */
function geolocation_tagger($event, $object_type, $object) {

	if($object->name == 'site_list') return;

	if ($object_type == 'metadata') {
		$object = $object->getOwnerEntity();
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
			if ($user && $user->current_latitude != null && $user->current_longitude != null) {
				$location = array($user->current_latitude, $user->current_longitude);
			}

			if ($user->current_latitude != null && $user->current_longitude != null) {
				$object->setLatLong($user->current_latitude, $user->current_longitude);
				return;
			}
		}

// Have we got a location
		if ($location) {
// Handle when location is given in a tag field (as it is with users)
			if (is_array($location))
				$location = implode(', ', $location);

			$latlong = elgg_geocode_location($location);



			if ($latlong) {
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
