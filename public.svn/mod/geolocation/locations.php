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

	$area2 = elgg_view_title(elgg_echo('geolocation:googlemaps'));

	// Get a list of google sites
	//$area2 .= elgg_entities_listing('search/listing', array('search_types' => $entity->getVolatileData('search')));
	
	if (is_array($results['entities']) && $results['count']) {
		$area2 .= elgg_view('entities/entity_list', array('entities' => $results['entities'], 'viewtype' => 'list'));
	}
	
	$body = elgg_view_layout("two_column_left_sidebar", '', $area1 . $area2, '');

	// Get categories, if they're installed
	//global $CONFIG;
	//$area3 = elgg_view('blog/categorylist',array('baseurl' => $CONFIG->wwwroot . 'search/?subtype=blog&owner_guid='.$page_owner->guid.'&tagtype=universal_categories&tag=','subtype' => 'blog', 'owner_guid' => $page_owner->guid));

	// Display them in the page
	$body = elgg_view_layout("two_column_left_sidebar", '', $area1 . $area2, $area3);

	// Display page
	page_draw(elgg_echo('geolocation:googlemaps'), $body);

?>
