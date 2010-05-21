<?php

/**
 * Elgg video view all
 */

// Load Elgg engine
require_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");
		
// Get the current page's owner
$page_owner = page_owner_entity();
if ($page_owner === false || is_null($page_owner)) {
	$page_owner = $_SESSION['user'];
	set_page_owner($page_owner->getGUID());
}

//set video header
$area1 .= elgg_view('video/video_header', array('context' => "everyone", 'type' => 'video'));

// Display videos
set_context('search');
$get_videos = list_entities('object', 'video', '',12, false, false, true);
$area2 .= "<div id='video_gallery_container'>".$get_videos."<div class='clearfloat'></div></div>";
set_context('video');

//get the latest comments on user's blog posts
$comments = get_annotations(0, "object", "video", "generic_comment", "", 0, 4, 0, "desc",0,0,page_owner());
$area3 .= elgg_view('elggcampus_layout/latest_comments', array('comments' => $comments));
//a view for the favourites plugin to extend
$area3 .= elgg_view("video/sidebar_options", array("object_type" => 'video'));
    	
//select the correct canvas area
$body = "<div id='video'>".elgg_view_layout("elggcampus_display", $area1, $area2, $area3)."</div>";
	
// Display page
page_draw(sprintf(elgg_echo('video:all')),$body);