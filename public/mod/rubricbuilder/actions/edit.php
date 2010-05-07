<?php
	/**
	 * Add rubric action
	 * 
	 * @package RubricBuilder
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Jeff Tilson
	 * @copyright THINK Global School 2010
	 * @link http://www.thinkglobalschool.com/
	 * 
	 */


	// must be logged in
	gatekeeper();
	
	// must have security token 
	action_gatekeeper();
	
	// get parameters that were posted
	$guid			= (int)get_input('rubric_guid');
	$title 			= get_input('rubric_title');
	$description 	= get_input('rubric_description');
	$tags			= get_input('rubric_tags');
	$access_id		= (int)get_input('access_id', ACCESS_PRIVATE);
	$write_access	= (int)get_input('write_access_id', ACCESS_PRIVATE);
	$comments		= get_input('rubric_comments_select', 'Off');

	$tagarray 		= string_to_tag_array($tags);
	
	$rubric = get_entity($guid);
	if ($rubric->getSubtype() == "rubric" && $rubric->canEdit()) {
	
		$rows = get_input('num_rows');
		$cols = get_input('num_cols');
	
		// Get rubric content
		for($i = 0; $i < $rows; $i++) {
			for ($j = 0; $j < $cols; $j++) {
				$rubric_content[$i][$j] = get_input("$i|$j");	
			}
		}
	
		// Cache to the session
		$_SESSION['user']->rubrictitle = $title;
		$_SESSION['user']->rubricdescription = $description;
		$_SESSION['user']->rubriccontents = serialize($rubric_content);
		$_SESSION['user']->rubrictags = $tags;
		$_SESSION['user']->rubriccached = true;
		$_SESSION['user']->num_rows = $rows;
		$_SESSION['user']->num_cols = $cols;
			
		if (empty($title)) {
			register_error(elgg_echo("rubricbuilder:blank"));
			forward($_SERVER['HTTP_REFERER']);
		}

		$rubric->contents		= serialize($rubric_content);
		$rubric->title 			= $title;
		$rubric->description 	= $description;
		$rubric->container_guid = $rubric->owner_guid;	
		$rubric->write_access_id= $write_access;
		$rubric->access_id		= $access_id;
		$rubric->tags 			= $tagarray;
		$rubric->comments_on	= $comments;
		$rubric->num_rows 		= $rows;
		$rubric->num_cols		= $cols;
	
		if (!$rubric->save()) {
			register_error(elgg_echo("rubricbuilder:error"));		
			forward($_SERVER['HTTP_REFERER']);
		}
	
		// Success message
		system_message(elgg_echo("rubricbuilder:edited"));
		
		// add to river
		add_to_river('river/object/rubricbuilder/update', 'update', get_loggedin_userid(), $rubric->guid);
		
		$revision = array("contents" => $rubric->contents, "title" => $rubric->title, "description" => $rubric->description, "rows" => $rubric->num_rows, "cols" => $rubric->num_cols);

		// Annotate for revision history
		$rubric->annotate('rubric', serialize($revision), $rubric->access_id);
		
		remove_metadata($_SESSION['user']->guid,'rubrictitle');
		remove_metadata($_SESSION['user']->guid,'rubricdescription');
		remove_metadata($_SESSION['user']->guid,'rubrictags');
		remove_metadata($_SESSION['user']->guid,'rubriccontents');
		remove_metadata($_SESSION['user']->guid,'rubriccached');
	
		forward("pg/rubric/view/$guid");
	}
?>