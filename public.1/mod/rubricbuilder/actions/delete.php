<?php
	/**
	 * Delete rubric action
	 * 
	 * @package RubricBuilder
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Jeff Tilson
	 * @copyright THINK Global School 2010
	 * @link http://www.thinkglobalschool.com/
	 * 
	 */

	// Make sure we're logged in (send us to the front page if not)
	gatekeeper();

	// Get input data
	$guid = (int)get_input('rubric_guid');
		
	// Make sure we actually have permission to edit
	$rubric = get_entity($guid);
	
	$user = get_entity(get_loggedin_userid());
	
	$can_delete = false;	
	if (($user->getGUID() == $rubric->owner_guid) || ($user->admin || $user->siteadmin)) {
		$can_delete = true;
	}
	
	if ($rubric->getSubtype() == "rubric" && $rubric->canEdit() && $can_delete) {
		
		// Delete it!
		$rowsaffected = $rubric->delete();
		
		if ($rowsaffected > 0) {
			// Success message
			system_message(elgg_echo("rubricbuilder:deleted"));
			
		} else {
			register_error(elgg_echo("rubricbuilder:notdeleted"));
		}
		
		// Forward to the main blog page
		forward("pg/rubric/");
	}	
?>