<?php

/**
 * Elgg video index page
 */

// Load Elgg engine
require_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");

// Get the current page's owner
$page_owner = page_owner_entity();
if ($page_owner === false || is_null($page_owner)) {
	$page_owner = $_SESSION['user'];
	set_page_owner($page_owner->getGUID());
}

//set breadcrumbs
$area1 = elgg_view('elggcampus_layout/breadcrumbs_general', array('object_type' => 'video'));

//set video header
if(page_owner()== get_loggedin_user()->guid){
	$area1 .= elgg_view('video/video_header', array('context' => "own", 'type' => 'video'));
}elseif($page_owner instanceof ElggGroup){
	$area1 .= elgg_view('groups/video_header_group');
}else{
	$area1 .= elgg_view('video/video_header_visit', array('type' => 'video'));
}

// Display the user's wire
set_context('search');
$get_videos = list_entities('object', 'video', $page_owner->getGUID(),12, false, false, true);
$area2 .= "<div id='video_gallery_container'>".$get_videos."<div class='clearfloat'></div></div>";
set_context('video');

//if the logged in user is not looking at their stuff, display the ownerblock otherwise
//show the users favourites
if(page_owner()	!= get_loggedin_user()->guid){
	$area3 = elgg_view('video/ownerblock');
}else{
	//a view for the favourites plugin to extend
	$area3 .= elgg_view("video/favourite", array("object_type" => 'videos'));
}
//get the latest comments on user's blog posts
$comments = get_annotations(0, "object", "video", "generic_comment", "", 0, 4, 0, "desc",0,0,page_owner());
$area3 .= elgg_view('elggcampus_layout/latest_comments', array('comments' => $comments));
//a view for the favourites plugin to extend
$area3 .= elgg_view("video/sidebar_options", array("object_type" => 'video'));

//select the correct canvas area
$body = "<div id='video'>".elgg_view_layout("elggcampus_display", $area1, $area2, $area3)."</div>";

// Display page
page_draw(sprintf(elgg_echo('videos')),$body);