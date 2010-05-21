<?php

/**
 * Elgg video view all
 */

// Load Elgg engine
require_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");
		
admin_gatekeeper();
set_context('admin');
$title = elgg_echo('video:settings');
$area2 = elgg_view_title($title);
$area2 .= elgg_view('video/admin/form');

$area3 = "<a href=\"\">Back to admin</a>";

//select the correct canvas area
$body = elgg_view_layout("elggcampus_display", $area1, $area2, $area3);
	
// Display page
page_draw(sprintf(elgg_echo('video:admin')),$body);