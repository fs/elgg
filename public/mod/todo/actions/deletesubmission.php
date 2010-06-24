<?php
	/**
	 * Todo Delete Submission Action
	 * 
	 * @package Todo
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Jeff Tilson
	 * @copyright THINK Global School 2010
	 * @link http://www.thinkglobalschool.com/
	 * 
	 */

	// Logged in users
	gatekeeper();
	
	// must have security token 
	action_gatekeeper();
	
	// get input
	$guid = get_input('submission_guid');

	$submission = get_entity($guid);
	$todo_guid = $submission->todo_guid;
	
	$candelete = $submission->canEdit();
	
	if ($submission->getSubtype() == "todosubmission" && $candelete) {
		
		// Delete it!
		$rowsaffected = $submission->delete();
		
		if ($rowsaffected > 0) {
			// Success message
			system_message(elgg_echo("todo:success:submissiondelete"));
			
		} else {
			register_error(elgg_echo("todo:error:submissiondelete"));
		}
		
		// Forward
		forward("pg/todo/viewtodo/$todo_guid");
	}
?>