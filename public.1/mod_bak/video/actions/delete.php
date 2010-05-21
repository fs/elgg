<?php

/**
 * Elgg video delete
 */

action_gatekeeper();

$username = $_SESSION['user']->username;
$return = "{$CONFIG->wwwroot}pg/video/$username";

// check for video
$guid = (int) get_input('video');
if (!$video = get_entity($guid)) {
	register_error(elgg_echo("video:deletefailed"));
	forward($return);
}

// check edit permissions
if (!$video->canEdit()) {
	register_error(elgg_echo("video:deletefailed"));
	forward($return);
}

// remove the thumbnail
if ($file = $video->thumbnail_path) {
	unlink($file);
}

// remove the screenshot
if ($file = $video->screenshot_path) {
	unlink($file);
}

// remove the transcoded video
if ($file = video_get_file($video->getGUID(), 'transcoded_std')) {
	$file->delete();
}

// remove the original video file
$file = video_get_file($video->getGUID());
$file->delete();

// delete the elgg object
if (!$video->delete()) {
	register_error(elgg_echo("video:deletefailed"));
	forward($return);
}

// succes message
system_message(elgg_echo("video:deleted"));
forward($return);
