#!/usr/bin/php
<?php
/**
 * This file is meant to be run via a shell command.
 */
// if we're being accessed through the web, bail.
if (array_key_exists('REQUEST_METHOD', $_SERVER)) {
	die('Cannot access');
}

if (!array_key_exists(2, $_SERVER['argv'])) {
	die('Missing required arguments');
}

// if we're passed a network, set it in _SERVER
if (array_key_exists(3, $_SERVER['argv'])) {
	$_SERVER['SERVER_NAME'] = $argv[3];
}

// grab the mode and guid of the video
$mode = $_SERVER['argv'][1];
$holder_guid = $_SERVER['argv'][2];

require_once dirname(dirname(dirname(__FILE__))) . '/engine/start.php';
elgg_set_ignore_access(TRUE);

if (!($holder = get_entity($holder_guid)) || (($holder->getSubtype() != 'video'))) {
	die('Unknown holder GUID.');
}

if (!$relationship = elgg_get_entities_from_relationship(array(
	'relationship' => 'original_video', 
	'relationship_guid' => $holder_guid, 
	'inverse_relationship' => true)
)) {
	die('Unknown video GUID.');
}

$video = $relationship[0];

// some sanity checks
if (($video->getSubtype() != 'video_file')) {
	die('Could not find specified video ' . $video_guid());
}

if ($video->transcoded_std_available) {
	die('Already Transcoded');
}

/*
Create a new file named the same _tx.flv
	Put metadata on this for:
		thumbnail filename
		transcoded
		transcoding output
		transcoding_start
		transcoding_stop
		transcoding_attempts

If success
	If remove original:
		Nothing
	If keep original:
		put MD on original for
			transcoded = '0'
			transcoded_guid = GUID
		put MD on transcoded for
			original_guid = GUID
*/

// get base filename
$filename = $video->getFilenameOnFilestore();
$tmp_arr = explode('.', $filename);
array_pop($tmp_arr);
$tmp_filename = implode('.', $tmp_arr);

$partial_name = $video->getFilename();
$tmp_arr = explode('.', $partial_name);
array_pop($tmp_arr);
$tmp_partial_name = implode('.', $tmp_arr);
//var_dump($tmp_partial_name);

// Do it.
if ($mode == 'transcode') {
	$new_filename = $tmp_filename . '_tx.flv';
	$new_partial_name = $tmp_partial_name . '_tx.flv';
		
	// should we downscale?
	// we never upscale.
	$src_w = $video->video_width;
	$src_h = $video->video_height;

	$defaults = explode('x', $CONFIG->ffmpegVideoSizeFull);

	$sizes = video_get_resize_sizes($src_w, $src_h, $defaults[0], $defaults[1]);
	$new_size = implode('x', $sizes);

	// mark this video as currently being transcoded to keep it out of cron.
	$holder->transcoding_std_in_progress = TRUE;

	$output = video_transcode_video($filename, $new_filename, $new_size);
	// @todo error checking...

	// create a new video object to hold the transcoded file.
	// @todo when $obj = new ElggObject($old_object) correctly clones with a blank GUID,
	// all this can be replaced.
	$tx_video = new ElggVideoFile();
	$tx_video->owner_guid = $holder->owner_guid; 
	$tx_video->container_guid = $holder->container_guid;
	$tx_video->access_id = $holder->access_id;

	// filestore specific info
	$tx_video->setFilename($new_partial_name);
	// @todo update to real mime type.
	$tx_video->setMimeType('video/x-flv');
	$tx_video->originalfilename = array_pop(explode('/', $new_partial_name));

	// tx_video-specific info
	$tx_video->transcoded = TRUE;
	$tx_video->transcoding_output = $output;

	$tx_video->save();

	// add a relationship and metadata to filter on during cron
	add_entity_relationship($tx_video->getGUID(), 'transcoded_std', $holder->getGUID());
	$holder->transcoded_std_available = TRUE;
	$holder->transcoding_std_in_progress = FALSE;


	// @todo if doing multi encodings, will not want to do this until after all are complete.
	if (!get_plugin_setting('keep_original', 'video')) {
		$video->delete();
	}


} elseif ($mode == 'thumbnail') {
	$new_filename = "{$tmp_filename}_thumb.jpg";
	$holder->thumbnail_path = $new_filename;
	$holder->thumbnail_output = $video->create_thumbnail($new_filename);
} elseif ($mode == 'screenshot') {
	$new_filename = $tmp_filename . '_ss.jpg';
	$output = video_extract_screenshot($filename, $new_filename);
	$holder->screenshot_path = $new_filename;
	$holder->screenshot_output = $output;
} else {
	die('Unknown mode');
}

exit;
// The meat.
// some transcoding specifics
if ($mode == 'transcode') {
	// If keeping the original, need to create a relationship between the two types
}

function video_extract_screenshot($video_filename, $thumbnail_name) {
	global $CONFIG;

	$cmd = $CONFIG->ffmpegScreenshotCmd;
	$cmd = str_replace('{{INPUT_NAME}}', $video_filename, $cmd);
	$cmd = str_replace('{{OUTPUT_NAME}}', $thumbnail_name, $cmd);
	$output = `$cmd`;
	//@todo error checking.
	return $output;
}


function video_transcode_video($video_filename, $new_filename, $size) {
	global $CONFIG;

	$cmd = $CONFIG->ffmpegTranscodeCmd;
	$cmd = str_replace('{{INPUT_NAME}}', $video_filename, $cmd);
	$cmd = str_replace('{{OUTPUT_NAME}}', $new_filename, $cmd);
	$cmd = str_replace('{{SIZE}}', $size, $cmd);

	$output = `$cmd`;
	//@todo error checking.
	return $output;
}
