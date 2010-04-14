<?php

/**
 * Generic video edit
 * A user can edit the title, description, tags and access
 */

// Load Elgg engine
require_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");
gatekeeper(); 
		
// Get the GUID of the entity we want to edit
$guid = (int) get_input('video');
		
// Get the video, if possible
if ($video = get_entity($guid)) {
	if ($video->container_guid) {
		set_page_owner($video->container_guid);
	} else {
		set_page_owner($video->owner_guid);
	}
		
	//breadcrumbs
	$area2 = elgg_view('elggcampus_layout/breadcrumbs', array('object_title' => $video->title, 'object_type' => 'video'));
	
	//set the title
	if($video->title)
		$area2 .= "<div id='Page_Header'><div class='Page_Header_Title'>".elgg_view_title(elgg_echo('video:edit') . ": " . $video->title)."</div><div class='clearfloat'></div></div>";
	else
		$area2 .= "<div id='Page_Header'><div class='Page_Header_Title'>".elgg_view_title(elgg_echo('video:edit') . ": " . elgg_echo('video:untitled'))."</div><div class='clearfloat'></div></div>";
				
	
	$area2 .= elgg_view('video/forms/edit', array('entity' => $video));
	
} else {	
	$area2 .= "<div class='ContentWrapper'>".elgg_echo('notfound')."</div>";		
}

$area3 = elgg_view('video/ownerblock');
		
$body = "<div id='video'>".elgg_view_layout('elggcampus_display',$area1, $area2, $area3)."</div>";
page_draw(sprintf(elgg_echo("video"),$_SESSION['user']->name), $body);