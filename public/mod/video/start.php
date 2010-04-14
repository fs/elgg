<?php

/**
 * Elgg video plugin
 */

require_once(dirname(__FILE__) . '/lib/video_functions.php');

function video_init() {
	// Load system configuration
	global $CONFIG;

	// Set up menu for logged in users
	if (isloggedin()) {
		add_menu(elgg_echo('video:yours'), $CONFIG->wwwroot . "pg/video/" . $_SESSION['user']->username);
	}

	$CONFIG->playerPath = $CONFIG->wwwroot . 'mod/video/player/elggcampus.swf';

	// video info gathering (requires mplayer...why ffmpeg can't do this, no clue.)
	// note if you change this it will need to be normalized in the video_get_video_info() function.
	// I use the keys provided by mplayer.
	$CONFIG->videoInfoCmd='/usr/bin/mplayer -identify "{{INPUT_NAME}}" -ao null -vo null -frames 0 2>/dev/null | grep ^ID';

	// ffmpeg config
	$CONFIG->ffmpegPath = '/usr/bin/ffmpeg';
	//$CONFIG->ffmpegPresetPath = '/usr/share/ffmpeg/libx264-hq.ffpreset';
	//$CONFIG->ffmpegTranscodeArgs = "-i \"{{INPUT_NAME}}" -vcodec libx264 -s {{SIZE}} -ar 22050 -vpre {$CONFIG->ffmpegPresetPath} {{OUTPUT_NAME}}";
	$CONFIG->ffmpegTranscodeArgs = "-y -i \"{{INPUT_NAME}}\" -s {{SIZE}} -vcodec libx264 -ar 22050 -vpre hq \"{{OUTPUT_NAME}}\"";
	//$CONFIG->ffmpegTranscodeArgs = "-y -i \"{{INPUT_NAME}}\" -vcodec libx264 -ar 22050 \"{{OUTPUT_NAME}}\"";
	$CONFIG->ffmpegTranscodeCmd = "{$CONFIG->ffmpegPath} {$CONFIG->ffmpegTranscodeArgs}";

	// @todo configure thumbnail sizes.
	$CONFIG->ffmpegThumbnailArgs = "-y -i \"{{INPUT_NAME}}\" -f image2 -ss 1 -vframes 1 -s {{SIZE}} \"{{OUTPUT_NAME}}\"";
	//$CONFIG->ffmpegThumbnailArgs = "-y -i \"{{INPUT_NAME}}\" -f image2 -ss 1 -vframes 1 -s 196x148 \"{{OUTPUT_NAME}}\"";
	$CONFIG->ffmpegThumbnailCmd = "{$CONFIG->ffmpegPath} {$CONFIG->ffmpegThumbnailArgs}";

	$CONFIG->ffmpegScreenshotArgs = "-y -i \"{{INPUT_NAME}}\" -f image2 -ss 1 -vframes 1 \"{{OUTPUT_NAME}}\"";
	$CONFIG->ffmpegScreenshotCmd = "{$CONFIG->ffmpegPath} {$CONFIG->ffmpegScreenshotArgs}";

	$CONFIG->ffmpegVideoSizeFull = '640x480';
	$CONFIG->ffmpegThumbnailSizeGallery = '207x155';
	$CONFIG->ffmpegThumbnailSizeFull = '196x148';

	//@todo trigger a hook to delete all associated files

	// Extend system CSS with our own styles, which are defined in the blog/css view
	extend_view('css', 'video/css');

	// Register a page handler, so we can have nice URLs
	register_page_handler('video', 'video_page_handler');

	// Register a file handler for thumbs / video files
	register_page_handler('video_file', 'video_file_pagehandler');

	// Register a URL handler for blog posts
	register_entity_url_handler('video_url', 'object', 'video');

	// Register entity type for new videos
	register_entity_type('object', 'video');

	// Register entity type for old videos (this is for Brighton only)
	register_entity_type('object', 'video_old');

	// Add group menu option
	add_group_tool_option('video', elgg_echo('video:enablevideo'), true);

	// Defined allowed video types
	$CONFIG->videoformats = array('flv', 'mov', 'wmv', 'mp4', 'mpg','mpeg', 'avi', '3gp', '3g2', 'm4v', 'ogv');

	// Hook into cron for video encoding.
	register_plugin_hook('cron', 'minute', 'video_cron');

	// Register subtypes
	run_function_once('video_run_once');
}

/**
 * Run the conversion cron.
 *
 * @return Str for success/failure
 */
function video_cron() {
	global $CONFIG;

	// get videos that have < 3 attempts at transcoding. Order by date.
	// check against how many existing processes are running
	// only spawn new ones that <= 5 or we'll never finish.
	// ^^ good ideas.  @todo.

	// detect a multi installation and pass an extra param to the script.
	// kinda dirty, but works for now.
	//if ($CONFIG->master_network) {
		$network = $_SERVER['SERVER_NAME'];
	//}

	$ignore_access = elgg_set_ignore_access(TRUE);

	$videos = elgg_get_entities_from_metadata(array(
		'type' => 'object',
		'subtype' => 'video',
		// bug somewhere with metastrings and FALSE.  arg
		'metadata_name_value_pairs' => array(
//			//@todo if ever offering different encoding types, add here
			array('name' => 'transcoding_std_error', 'value' => '0'),
			array('name' => 'transcoding_std_in_progress', 'value' => '0'),
			array('name' => 'transcoded_std_available', 'value' => '0')
		),
		'order_by' => 'e.time_created asc',
	));

	foreach ($videos as $video) {
		$cmd = dirname(__FILE__) . "/process_video.php thumbnail \"{$video->getGUID()}\" $network > /dev/null 2>&1 &";
		exec($cmd);

		$cmd = dirname(__FILE__) . "/process_video.php screenshot \"{$video->getGUID()}\" $network  > /dev/null 2>&1 &";
		exec($cmd);

		$cmd = dirname(__FILE__) . "/process_video.php transcode \"{$video->getGUID()}\" $network  > /dev/null 2>&1 &";
		exec($cmd);
	}

	elgg_set_ignore_access($ignore_access);
}

function video_pagesetup() {
	global $CONFIG;
	$page_owner = page_owner_entity();

	// Group submenu option
	if ($page_owner instanceof ElggGroup && get_context() == 'video') {
		if($page_owner->video_enable == "yes"){
			add_submenu_item(sprintf(elgg_echo("video:group"),$page_owner->name), $CONFIG->wwwroot . "pg/video/" . $page_owner->username);
			add_submenu_item(elgg_echo('video:upload'),$CONFIG->wwwroot."pg/video/{$page_owner->username}/new/");
		}
	}

	// if the context is admin and is admin logged in
	if(get_context() == 'admin' && isadminloggedin()){
			add_submenu_item(elgg_echo('video:adminsettings'), $CONFIG->wwwroot . 'pg/video/' . get_loggedin_user()->username . '/videosettings/');
	}
}

/**
 * Set up subtype
 * @return unknown_type
 */
function video_run_once() {
	add_subtype('object', 'video_file', 'ElggVideoFile');
	set_plugin_setting('keep_original', TRUE);
}

/**
 * Handles video files
 * $page[0] is url of holder object
 * $page[1] is type of file (thumbnail, flv, original)
 * $page[2] is size (std)
 * $page[3] is video.flv or image.jpg.  This is required for the swf player.
 * @param $page
 * @return unknown_type
 */
function video_file_pagehandler($page) {
	if (!array_key_exists(0, $page)) {
		// @todo default to broken movie image if nothing passed.
	} else {
		$holder = get_entity($page[0]);
		$type = (array_key_exists(1, $page)) ? $page[1] : 'flv';
		$size = (array_key_exists(2, $page)) ? $page[2] : 'std';

		if (!$holder || $holder->getSubtype() != 'video') {
			// @todo default to broken movie image if nothing passed.
		} else {
			if ($type == 'thumbnail') {
				switch($size) {
					case 'std':
						$file = $holder->thumbnail_path;
						break;
					case 'full':
						$file = $holder->screenshot_path;
						break;
				}

				$content_type = 'image/jpeg';
			} else {
				// dealing with movies
				switch($type) {
					case 'original':
						$relationship = 'original_video';
						// @todo add content disposition to allow download
						// with original name
						break;

					case 'flv':
						$relationship = "transcoded_$size";
						break;
				}

				$e_tmp = elgg_get_entities_from_relationship(array(
					'relationship' => $relationship,
					'relationship_guid' => $holder->getGUID(),
					'inverse_relationship' => TRUE
				));

				// @todo should only ever be 1.
				$file_obj = $e_tmp[0];
				$file = $file_obj->getFilenameOnFilestore();
				$content_type = $file_obj->getMimeType();
			}

			if (!$file || !file_exists($file)) {
				exit;
			}

			// @todo add last icon time for caching problems.
			header("Content-type: $content_type");
			header('Expires: ' . date('r',time() + 864000));
			header("Pragma: public");
			header("Cache-Control: public");
			$h = fopen($file, 'rb');
			while (!feof($h)) {
				echo fread($h, 8192);
			}
			fclose($h);
		}
	}

}


/**
 * video page handler; allows the use of fancy URLs
 *
 * @param array $page From the page_handler function
 * @return true|false Depending on success
 */
function video_page_handler($page) {
	// The first component of a video URL is the username
	if (isset($page[0])) {
		set_input('username',$page[0]);
	}

	// In case we have further input
	if (isset($page[2])) {
		set_input('param2',$page[2]);
	}
	// In case we have further input
	if (isset($page[3])) {
		set_input('param3',$page[3]);
	}

	// The second part dictates what we're doing
	if (isset($page[1])) {
		switch($page[1]) {
			case "read":		set_input('guid',$page[2]);
								include(dirname(__FILE__) . "/read.php"); return true;
								break;
			case "friends":		include(dirname(__FILE__) . "/friends.php"); return true;
								break;
			case "videosettings":		include(dirname(__FILE__) . "/adminsettings.php"); return true;
								break;
			case "new":			include(dirname(__FILE__) . "/upload.php"); return true;
								break;

		}
		// If the URL is just 'video/username', or just 'video/', load the standard video index
	} else {
		@include(dirname(__FILE__) . "/index.php");
		return true;
	}

return false;

}

/**
 * Populates the ->getUrl() method for video objects
 *
 * @param ElggEntity $blogpost Video post entity
 * @return string Video post URL
 */
function video_url($video) {
	global $CONFIG;
	$title = $video->title;
	$title = friendly_title($title);
	return $CONFIG->url . "pg/video/" . $video->getOwnerEntity()->username . "/read/" . $video->getGUID() . "/" . $title;
}

// Make sure the blog initialisation function is called on initialisation
register_elgg_event_handler('init','system','video_init');
register_elgg_event_handler('pagesetup','system','video_pagesetup');

// Register actions
global $CONFIG;
register_action("video/upload",false,$CONFIG->pluginspath . "video/actions/upload.php");
register_action("video/edit",false,$CONFIG->pluginspath . "video/actions/edit.php");
register_action("video/delete",false,$CONFIG->pluginspath . "video/actions/delete.php");
register_action('video/download', FALSE, "{$CONFIG->pluginspath}video/actions/download.php");
