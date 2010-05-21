<?php
/**
 * Elgg plugin project creator
 */

global $CONFIG;
action_gatekeeper();

// Get variables
$title = get_input("title");
$desc = get_input("description");
$tags = get_input("tags");
$access_id = (int) get_input("access_id");

// ensure video uploaded
$username = $_SESSION['user']->username;
$filename = $_FILES['upload']['tmp_name'];
if (!$filename) {
	register_error(elgg_echo('video:nofile'));
	forward("{$CONFIG->wwwroot}pg/video/$username/new/");
}

// check filesize
$max_filesize = elgg_get_ini_setting_in_bytes('upload_max_filesize');
if ($_FILES['upload']['size'] > $max_filesize) {
	register_error(elgg_echo('video:filesize'));
	forward("{$CONFIG->wwwroot}pg/video/$username/new/");
}

// ensure permissions
$permission = get_input('permission');
if($permission != 'yes'){
	register_error(elgg_echo('video:nopermission'));
	forward("{$CONFIG->wwwroot}pg/video/$username/new/");
}

$video_info = video_get_video_info_from_file($filename);

$container_guid = (int) get_input('container_guid', 0);
if (!$container_guid) {
	$container_guid == $_SESSION['user']->getGUID();
} else if (!($container_user = get_entity($container_guid))) {
	$container_user = $_SESSION['user'];
}

$name = $_FILES['upload']['name'];
$ext = array_pop(explode('.', $name));

//@todo figure out what formats are actually valid.
if (!in_array(strtolower($ext), $CONFIG->videoformats)) {
	register_error(elgg_echo('video:badformat'));
	forward("{$CONFIG->wwwroot}pg/video/$username/new/");
}/* else {
	if (substr_count($_FILES['upload']['name'],'.mov')) {
		$mimetype = 'application/mov';
	} else {
		$mimetype = 'application/wmv';
	}
}*/

// Initialise a new video holder object
$video = new ElggObject();
$video->subtype = 'video';
$video->owner_guid = get_loggedin_user()->getGUID();
$video->container_guid = (int)get_input('container_guid', get_loggedin_user()->getGUID());
$video->title = $title;
$video->description = $desc;
$tags = explode(',', $tags);
$video->tags = $tags;
$video->permission = $permission;
$video->access_id = $access_id;

// assign the length to the holder object
$video->length = $video_info['length'];

//@todo A bug exists where you can't specify for metadata NOT to exist.
// the workaround is to set all metadata we'll need to 0, and then check against that.
// This will also be the spot add new transcoding types.
$video->transcoding_std_error = FALSE;
$video->transcoding_std_in_progress = FALSE;
$video->transcoded_std_available = FALSE;

// making this an ElggVideo file so it won't show up in the list of files.
$org_video = new ElggVideoFile();
$filestorename = strtolower(time() . "_$name");
$org_video->setFilename("video/$filestorename");
$org_video->setMimeType($_FILES['upload']['type']);
$org_video->originalfilename = $name;
$org_video->open("write");
$org_video->write(get_uploaded_file('upload'));
$org_video->close();
$org_video->save();
//@todo probably not needed if the relationship exists.
$org_video->original = TRUE;
$org_video->access_id = $access_id;
$org_video->owner_guid = get_loggedin_user()->getGUID();
$org_video->container_guid = get_input('container_guid', get_loggedin_user()->getGUID());

// grab all the video info and assign it to the
// original video
foreach ($video_info as $k => $v) {
	$org_video->$k = $v;
}

$result = ($video->save() && $org_video->save());

if ($result){
	// add relationship so we can track the two.
	add_entity_relationship($org_video->getGUID(), 'original_video', $video->getGUID());
	system_message(elgg_echo('video:success'));
	//@todo shouldn't show in the river until video has been converted?
	add_to_river('river/object/video/create','create',$video->owner_guid,$video->guid);
} else {
	$video->delete();
	$org_video->delete();
	system_message(elgg_echo('video:failure'));
}

$container_user = get_entity($container_guid);

forward($CONFIG->wwwroot . "pg/video/" . $container_user->username);
