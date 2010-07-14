<?php
// Load Elgg framework
require_once($_SERVER['DOCUMENT_ROOT'] . '/engine/start.php');

$types = get_registered_entity_types();

foreach ($types as $type => $subtypes) {
	// @todo when using index table, can include result counts on each of these.
	if (is_array($subtypes) && count($subtypes)) {
		foreach ($subtypes as $subtype) {
			$label = "item:$type:$subtype";
			$name = $type . "_" . $subtype;
			$select_checkboxes[] = array('label' => elgg_echo($label), 'name' => $name);
		}
	} else {
		$label = "item:$type";
		$name = $type;
		$select_checkboxes[] = array('label' => elgg_echo($label), 'name' => $name);
	}
}

$body = elgg_view('geolocation/scripts');
$body .= elgg_view('geolocation/map', array('select_checkboxes' => $select_checkboxes));
$sidebar = elgg_view('geolocation/search_map_sidebar');

$body = elgg_view_layout('one_column_with_sidebar', $body,  $sidebar);

// Draw the page
page_draw(elgg_echo('googleappslogin:google_sites_settings'),$body);

?>