<?php

/**
 * A function to grab the embed code and send it back for all new videos
 **/

function video_player_new($location){

	$video = "<object width='640' height='377' id='videoPlayer' name='videoPlayer' type='application/x-shockwave-flash' classid='clsid:d27cdb6e-ae6d-11cf-96b8-444553540000' ><param name='movie' value='http://dibbler.brighton.ac.uk/swfs/videoPlayer.swf' /><param name='quality' value='high' /> <param name='bgcolor' value='#000000' /><param name='allowfullscreen' value='true' /> <param name='flashvars' value= '&videoWidth=0&videoHeight=0&dsControl=manual&dsSensitivity=100&serverURL=rtmp://{$location}'/><embed src='http://dibbler.brighton.ac.uk/swfs/videoPlayer.swf' width='640' height='377' id='videoPlayer' quality='high' bgcolor='#000000' name='videoPlayer' allowfullscreen='true' pluginspage='http://www.adobe.com/go/getflashplayer'   flashvars='&videoWidth=0&videoHeight=0&dsControl=manual&dsSensitivity=100&serverURL=rtmp://dibbler.brighton.ac.uk/vod/mp4:sample2_1000kbps.f4v&DS_Status=true&streamType=vod&autoStart=true' type='application/x-shockwave-flash'></embed></object>";

	return $video;
}

function video_player_old($location){
}

/**
 * This function uploaded the video file to the encoding server
 **/
function upload_video(){
}

/**
 * This function watches for the new video file to be encoded
 **/
function video_processing(){
}

/**
 * Parse urls for video
 **/
function parse_video_urls($text) {
	return preg_replace_callback('/(^flv=)(?<!=["\'])((ht|f)tps?:\/\/[^\s\r\n\t<>"\'\!\(\)]+)/i',
		create_function(
			'$matches',
			'
				$url = $matches[1];
				$urltext = str_replace("/", "/<wbr />", $url);
				return "<a href=\"$url\" style=\"text-decoration:underline;\">$urltext</a>";
			'
	), $text);
}


function video_get_video_processes() {

}

function video_get_video_info_from_file($filename) {
	global $CONFIG;

	$cmd = str_replace('{{INPUT_NAME}}', $filename, $CONFIG->videoInfoCmd);
	$output = `$cmd`;
	$tmp_info = explode("\n", $output);
	$info = array();
	foreach($tmp_info as $line) {
		if (empty($line)) {
			continue;
		}

		$name = $value = NULL;
		list($name, $value) = explode('=', $line);

		// this will match multiple values.
		// mplayer *appears* to list the used values last
		// so it will overwrite with the correct values
		switch($name) {
			case 'ID_VIDEO_FORMAT':
			case 'ID_VIDEO_BITRATE':
			case 'ID_VIDEO_WIDTH':
			case 'ID_VIDEO_HEIGHT':
			case 'ID_VIDEO_FPS':
			case 'ID_VIDEO_ASPECT':
			case 'ID_AUDIO_FORMAT':
			case 'ID_AUDIO_BITRATE':
			case 'ID_AUDIO_RATE':
			case 'ID_LENGTH':
			case 'ID_VIDEO_CODEC':
			case 'ID_AUDIO_BITRATE':
			case 'ID_AUDIO_RATE':
			case 'ID_AUDIO_CODEC':
				$info[strtolower(str_replace('ID_', '', $name))] = $value;
				break;
			default:
				continue;
		}
	}

	return $info;
}

/**
 * Download the file
 *
 * @param str $filecontents
 * @param str $filename
 * @param str $mime
 * @param int $split
 * @return void
 */
function elgg_download_file($filecontents, $filename, $mime='application/octet-stream', $split=8192) {
	// download headers
	header("Pragma: public");
	header("Content-type: $mime");
	header("Content-Disposition: attachment; filename=\"$filename\"");

	// output the file directly, split into octets
	foreach(str_split($filecontents, $split) as $chunk) {
		echo $chunk;
	}
	exit;
}

/**
 * Returns the video's file
 *
 * @param int $guid the video GUID
 * @param str $relationship
 * @param bool $inverse
 * @return ElggVideoFile
 */
function video_get_file($guid, $relationship='original_video', $inverse=TRUE) {
	$files = elgg_get_entities_from_relationship(array(
		'relationship' => $relationship,
		'relationship_guid' => $guid,
		'inverse_relationship' => $inverse)
	);

	// one-to-one relationship
	return $files[0];
}

/**
 * Calculates the resulting width and height given src WxH and max WxH
 *
 * @param int $src_w
 * @param int $src_h
 * @param int $max_w
 * @param int $max_h
 * @return array
 */
function video_get_resize_sizes($src_w, $src_h, $max_w, $max_h) {
	$max['width'] = $max_w;
	$max['height'] = $max_h;

	$src['width'] = $src_w;
	$src['height'] = $src_h;

	// determine key dimension; off-hand will be other
	$key = ($src_w > $src_h) ? 'width' : 'height';
	$off = ($src_w > $src_h) ? 'height' : 'width';

	// greater than default: downsize
	$targets = array();
	if ($src[$key] > $max[$key]) {
		// the key dimension will fill the box
		$targets[$key] = $max[$key];
		
		// pillar- or letter-boxing will occur due to the smaller size
		$targets[$off] = floor(($src[$off]* $max[$key]) / $src[$key]);
	}
	
	// less than default: no change
	else {
		$targets[$key] = $src[$key];
		$targets[$off] = $src[$off];
	}

	// must be multiples of 2.
	if ($targets['width'] % 2) {
		$targets['width']++;
	}

	if ($targets['height'] % 2) {
		$targets['height']++;
	}
	
	return $targets;
}

/**
 * A basic video class that extends the file class.
 */
class ElggVideoFile extends ElggFile {
	protected function initialise_attributes() {
		parent::initialise_attributes();

		// override the default file subtype.
		// required to here because you can't change subtypes
		$this->attributes['subtype'] = 'video_file';
	}
	
	public function download_video() {
		elgg_download_file($this->grabFile(), $this->originalfilename, $this->getMimeType());
	}
	
	/**
	 * Creates a thumbnail for the video
	 *
	 * Preserves aspect ratio for the default configuration values. Letter- or
	 * pillar-boxing is enforced upon downsizing the image.
	 *
	 * @todo Error checking
	 *
	 * @param str $filename The thumbnail's full filepath
	 * @return image
	 */
	public function create_thumbnail($filename) {
		global $CONFIG;

		// sanity check
		if (!$this->video_width || !$this->video_height) {
			elgg_log('Cannot create a thumbnail for a video without a width or height', 'WARNING');
			return FALSE;
		}
		
		// retrieve configured default sizes
		$defaults = explode('x', $CONFIG->ffmpegThumbnailSizeGallery);
		$defaults['width'] = $defaults[0];
		$defaults['height'] = $defaults[1];
		$targets = video_get_resize_sizes($this->video_width, $this->video_height, $defaults[0], $defaults[1]);
	
		//var_dump ("Original: {$this->video_width}x{$this->video_height}");
		//var_dump("New: {$targets['width']}x{$targets['height']}");
	
		$cmd = $CONFIG->ffmpegThumbnailCmd;
		$cmd = str_replace('{{INPUT_NAME}}', $this->getFilenameOnFilestore(), $cmd);
		$cmd = str_replace('{{OUTPUT_NAME}}', $filename, $cmd);
		$cmd = str_replace('{{SIZE}}', implode('x', $targets), $cmd);
		return `$cmd`;
	}
}
