<?php
/**
 * Elgg video edit action
 */
action_gatekeeper();

$title = get_input("title");
$desc = get_input("description");
$tags = get_input("tags");
$access_id = (int) get_input("access_id");
$permission = get_input('permission');
if($permission != 'yes'){
	register_error(elgg_echo('video:nopermission'));
	forward($CONFIG->wwwroot . "mod/video/edit.php");
}
$guid = (int) get_input('video');
if ($video = get_entity($guid)) {
	if ($video->canEdit()) {
		$container = get_entity($video->container_guid);
		$video->access_id = $access_id;
		$video->title = $title;
		$video->description = $desc;
		// Save tags
		$tags = explode(",", $tags);
		$video->tags = $tags;
		$result = $video->save();
		if ($result)
			system_message(elgg_echo("video:edited"));
		else
			register_error(elgg_echo("video:editfailed"));
		} else {
			$container = $_SESSION['user'];
			register_error(elgg_echo("video:editfailed"));
		}
} else {
	register_error(elgg_echo("video:editfailed"));
}
		
forward($video->getUrl());