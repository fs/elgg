<?php

action_gatekeeper();

// ensure valid video file
$guid = get_input('video');
if (!$video = get_entity($guid)) {
	register_error(elgg_echo('video:downloadfail'));
}

// grab the original ElggVideoFile
$file = video_get_file($video->getGUID());
$file->download_video();
