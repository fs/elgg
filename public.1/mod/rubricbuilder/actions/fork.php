<?php
	/**
	 * Fork rubric action
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
		
	/** For now anyone can duplicate a rubric **/
	//if ($rubric->getSubtype() == "rubric" && $rubric->canEdit()) {
		
		$new_rubric = new Rubric();
		$new_rubric->contents			= $rubric->contents;
		$new_rubric->title 				= "Copy of " . $rubric->title;
		$new_rubric->description 		= $rubric->description;
		$new_rubric->owner_guid			= get_loggedin_userid();
		$new_rubric->container_guid 	= (int)get_input($rubric->container_guid, get_loggedin_userid());
		$new_rubric->access_id			= $rubric->access_id;
		$new_rubric->write_access_id	= $rubric->write_access_id;
		$new_rubric->tags 				= $rubric->tags;
		$new_rubric->comments_on		= $rubric->comments_on;
		$new_rubric->num_rows 			= $rubric->num_rows;
		$new_rubric->num_cols			= $rubric->num_cols;

		if (!$new_rubric->save()) {
			register_error(elgg_echo("rubricbuilder:error"));		
			forward($_SERVER['HTTP_REFERER']);
		}
	
		
		// Nuke the cached data in case we're coming from the edit page
		remove_metadata($_SESSION['user']->guid,'rubrictitle');
		remove_metadata($_SESSION['user']->guid,'rubricdescription');
		remove_metadata($_SESSION['user']->guid,'rubrictags');
		remove_metadata($_SESSION['user']->guid,'rubriccontents');
		remove_metadata($_SESSION['user']->guid,'rubriccached');
		
		// Forward to the main blog page
		forward("pg/rubric/edit/" . $new_rubric->getGUID());
	//}	
?>