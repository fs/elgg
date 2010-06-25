<?php
	/**
	 * Todo Create Action
	 * 
	 * @package Todo
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Jeff Tilson
	 * @copyright THINK Global School 2010
	 * @link http://www.thinkglobalschool.com/
	 * 
	 */
	
	// Start engine
	require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/engine/start.php');
	
	// must be logged in
	gatekeeper();
	
	// must have security token 
	action_gatekeeper();
	
	// get input
	$description = get_input('submission_description');
	$todo_guid = get_input('todo_guid');
	$content = get_input('submission_content');
		
	$user = get_loggedin_user();
	
	// Cache to session
	$_SESSION['user']->submission_content = $content;
	$_SESSION['user']->submission_description = $description;
	
	/*
	if (empty($title)) {
		register_error(elgg_echo('todo:error:titleblank'));
		forward($_SERVER['HTTP_REFERER']);
	}*/
	
	$submission = new ElggObject();
	$submission->subtype = "todosubmission";
	$submission->description = $description;
	$submission->content = serialize($content);
	$submission->access_id 	= ACCESS_LOGGED_IN;
	$submission->owner_id = $user->getGUID();
	$submission->todo_guid = $todo_guid;

	
	// Save
	if (!$submission->save()) {
		echo 0;
		return;
	}
	
	// This states that: 'Submission' is 'submittedo' 'Todo' 
	$success = add_entity_relationship($submission->getGUID(), SUBMISSION_RELATIONSHIP, $todo_guid);

	// Clear Cached info
	remove_metadata($_SESSION['user']->guid,'submission_content');
	remove_metadata($_SESSION['user']->guid,'submission_description');
	
	// Save successful, forward to index
	echo $success;
	return;
?>