<?php
	/**
	 * Restore rubric action
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
	$rev = (int)get_input('rev');
	
	if ($rev) {	
		$revision = get_annotation($rev);
		// Make sure we have an annotation object, and that it belongs to this rubric
		if ($revision && $revision->entity_guid == $guid) {
			$revision = unserialize($revision->value);

			$title 			= $revision['title'];
			$description 	= $revision['description'];
			$contents		= $revision['contents'];
			$num_rows		= $revision['rows'];
			$num_cols		= $revision['cols'];
			
		} else {
			// Something funny is going on...
			forward();
		}	
		
		// Make sure we actually have permission to edit
		$rubric = get_entity($guid);				
				
		$revision = array("contents" => $contents, "title" => $title, "description" => $description, "rows" => $num_rows, "cols" => $num_cols);
	
		// Annotate for revision history
		$rubric->annotate('rubric', serialize($revision), $rubric->access_id);	
	
		// Success message
		system_message(elgg_echo("rubricbuilder:restored"));		
	
		// Nuke the cached data in case we're coming from the edit page
		remove_metadata($_SESSION['user']->guid,'rubrictitle');
		remove_metadata($_SESSION['user']->guid,'rubricdescription');
		remove_metadata($_SESSION['user']->guid,'rubrictags');
		remove_metadata($_SESSION['user']->guid,'rubriccontents');
		remove_metadata($_SESSION['user']->guid,'rubriccached');
	
		// Forward to the main blog page
		forward("pg/rubric/view/" . $guid);
	} else {
		register_error(elgg_echo("rubricbuilder:error"));
	}
	
?>