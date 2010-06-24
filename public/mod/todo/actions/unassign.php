<?php
	/**
	 * Todo remove assignee from todo relationship
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
	
	// Logged in check
	gatekeeper();
	
	// must have security token 
	action_gatekeeper();

	$assignee_guid = get_input('assignee_guid');
	$assignee = get_entity($assignee_guid);
	
	$todo_guid = get_input('todo_guid');
	$todo = get_entity($todo_guid);
	
	if ($todo && $todo->getSubtype() == "todo") {
		
		$success = $assignee->removeRelationship($todo_guid, TODO_ASSIGNEE_RELATIONSHIP);
		
		echo $success ? 1 : 0;
		return;
	}
	echo 0;
	return;

?>