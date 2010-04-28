<?php

// Load Elgg framework
require_once($_SERVER['DOCUMENT_ROOT'] . '/engine/start.php');



$types = get_registered_entity_types();

foreach ($types as $type => $subtypes) {
	// @todo when using index table, can include result counts on each of these.
	if (is_array($subtypes) && count($subtypes)) {
		foreach ($subtypes as $subtype) {
			$label = "item:$type:$subtype";

//			$data = htmlspecialchars(http_build_query(array(
//				'q' => $query,
//				'entity_subtype' => $subtype,
//				'entity_type' => $type,
//				'owner_guid' => $owner_guid,
//				'search_type' => 'entities',
//				'friends' => $friends
//			)));

//			$url = "{$CONFIG->wwwroot}pg/search/?$data";
//
//			add_submenu_item(elgg_echo($label), $url);
			$name = $type . "_" . $subtype;
		}
	} else {
		$label = "item:$type";

//		$data = htmlspecialchars(http_build_query(array(
//			'q' => $query,
//			'entity_type' => $type,
//			'owner_guid' => $owner_guid,
//			'search_type' => 'entities',
//			'friends' => $friends
//		)));

//		$url = "{$CONFIG->wwwroot}pg/search/?$data";
//
//		add_submenu_item(elgg_echo($label), $url);
		$name = $type;
	}
	$select_checkboxes[] = array('label' => elgg_echo($label), 'name' => $name);
}

$body = elgg_view('geolocation/map', array('select_checkboxes' => $select_checkboxes));

$body = elgg_view_layout('two_column_left_sidebar', '', $body);

// Draw the page
page_draw(elgg_echo('googleappslogin:google_sites_settings'),$body);