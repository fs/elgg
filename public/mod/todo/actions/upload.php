<?php
	/**
	 * Todo simple file upload
	 * 
	 * @package Todo
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Jeff Tilson
	 * @copyright THINK Global School 2010
	 * @link http://www.thinkglobalschool.com/
	 * 
	 */

	// Start engine as this action is triggered via ajax
	require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/engine/start.php');

	global $CONFIG;
	
	// Logged in users only
	gatekeeper();
	
	// must have security token 
	action_gatekeeper();
	
	// must have a file if a new file upload
	if (empty($_FILES['upload']['name'])) {
		echo 0;
		return;
	}
	
	$file = new ElggFile();
	
	$title = $_FILES['upload']['name'];
	
	$file->title = $title;
	$file->access_id = ACCESS_LOGGED_IN; // something else...

	// we have a file upload, so process it
	if (isset($_FILES['upload']['name']) && !empty($_FILES['upload']['name'])) {
		
		$prefix = "file/";
		
		$filestorename = elgg_strtolower(time().$_FILES['upload']['name']);
		
		$file->setFilename($prefix.$filestorename);
		$file->setMimeType($_FILES['upload']['type']);
		$file->originalfilename = $_FILES['upload']['name'];
		$file->simpletype = "submission";
	
		$file->open("write");
		$file->write(get_uploaded_file('upload'));
		$file->close();
		
		$guid = $file->save();
	} 

	// handle results differently for new files and file updates
	if ($guid) {
		//echo elgg_view('output/url', array('href' => $file->getURL(), 'text' => $file->title));
		//echo $file->getURL();
		echo '{"guid": "' . $file->getGUID() . '", "name":"' . $file->title . '", "url": "' . $file->getURL() . '"}';
		return;
	} else {
		echo 0;
		return;
	}
