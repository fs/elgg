<?php
/**
 * Elgg video upload page
 */

// Load Elgg engine
require_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");
gatekeeper();
		
// Get the current page's owner
$page_owner = page_owner_entity();
if ($page_owner === false || is_null($page_owner)) {
	$page_owner = $_SESSION['user'];
	set_page_owner($page_owner->getGUID());
}
if ($page_owner instanceof ElggGroup)
	$container = $page_owner->guid;
	
//set breadcrumbs
$area1 = elgg_view('elggcampus_layout/breadcrumbs_general', array('object_type' => 'video'));
	
// title
$area2 = elgg_view_title(elgg_echo("video:upload"));
	    
// Display the user's wire
$area2 .= elgg_view('video/forms/upload');

//if the logged in user is not looking at their stuff, display the ownerblock otherwise
//show the users favourites
$area3 = elgg_view('video/ownerblock');	
//a view for the favourites plugin to extend
$area3 .= elgg_view("video/favourite", array("object_type" => 'videos'));
//get the latest comments on user's blog posts
$comments = get_annotations(0, "object", "video", "generic_comment", "", 0, 4, 0, "desc",0,0,page_owner());
$area3 .= elgg_view('elggcampus_layout/latest_comments', array('comments' => $comments));
//a view for the favourites plugin to extend
$area3 .= elgg_view("video/sidebar_options", array("object_type" => 'video'));
    
//select the correct canvas area
$body = "<div id='video'>".elgg_view_layout("elggcampus_display", $area1, $area2, $area3)."</div>";
		
// Display page
page_draw(sprintf(elgg_echo('videos')),$body);