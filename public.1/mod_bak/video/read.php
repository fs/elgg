<?php

/**
 * Generic video viewer
 */

// Load Elgg engine
require_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");

// Get the GUID of the entity we want to view
$guid = (int) get_input('guid');
		
// Get the video, if possible
if ($video = get_entity($guid)) {
	if ($video->container_guid) {
		set_page_owner($video->container_guid);
	} else {
		set_page_owner($video->owner_guid);
	}
		
	//set breadcrumbs
	$area2 = elgg_view('elggcampus_layout/breadcrumbs', array('object_title' => $video->title, 'object_type' => 'video'));
				
	$area2 .= elgg_view_entity($video,true);
	
	if(($video->owner_guid != get_loggedin_user()->guid) || $video->container_guid){
		$area3 = elgg_view('video/ownerblock');
	}
} else {	
	$area2 .= elgg_echo('video:notfound');		
}
		
$body = "<div id='video'>".elgg_view_layout('elggcampus_display',$area1, $area2, $area3)."</div>";
page_draw(sprintf(elgg_echo("video"),$_SESSION['user']->name), $body);