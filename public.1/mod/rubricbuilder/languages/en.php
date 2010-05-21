<?php
	/**
	 * Rubric language file
	 * 
	 * @package RubricBuilder
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Jeff Tilson
	 * @copyright THINK Global School 2010
	 * @link http://www.thinkglobalschool.com/
	 * 
	 */
	
	$english = array(
		
		// Entity/Object Related
		'item:object:rubric' => 'Rubrics',
		'rubricbuilder:title' => 'Rubric',
		
		// Default Content
		'rubricbuilder:criteria:default' => 'Criteria', 
		'rubricbuilder:level1' => '1',
		'rubricbuilder:level2' => '2',
		'rubricbuilder:level3' => '3',
		'rubricbuilder:level4' => '4',
		
		// Side Menu
		'rubricbuilder:pagetitle'	=> 'Rubrics',
		'rubricbuilder:myrubrics' => 'Your Rubrics', 
		'rubricbuilder:friendsrubrics' => 'Friends\' Rubrics',
		'rubricbuilder:allrubrics' => 'All Site Rubrics',
		'rubricbuilder:label:history' => "Rubric History",
		'rubricbuilder:label:view' => "View Rubric",
		
		// Actions
		'rubricbuilder:fork' => 'Make Copy',
		'rubricbuilder:restore' => 'Restore this version',
		
		// Action labels
		'rubricbuilder:deleted' => 'Rubric successfully deleted',
		'rubricbuilder:success'	=> 'Your action was successful',
		'rubricbuilder:posted' => 'Rubric successfully created',
		'rubricbuilder:edited' => 'Rubric updated',
		'rubricbuilder:restored' => 'Rubric successfully restored',

		// Confirmations
		'rubricbuilder:forkconfirm' => "Are you sure you want to duplicate this rubric?",
		'rubricbuilder:restoreconfirm' => "Are you sure you want to restore this rubric?",
		
		// Errors
		'rubricbuilder:notdeleted' => 'There was an error deleting the rubric',
		'rubricbuilder:error' => 'There was an error saving the rubric',
		'rubricbuilder:blank' => 'Rubric title cannot be blank', 
		'rubricbuilder:failure'	=> 'Your action failed',
	
		// Titles
		'rubricbuilder:create' => 'Create New Rubric',
		'rubricbuilder:editrubric' => 'Edit Rubric',
		'rubricbuilder:history' => "History",
		
        // River terms
        'rubricbuilder:river:created' => "%s created",
        'rubricbuilder:river:updated' => "%s updated",
        'rubricbuilder:river:posted' => "%s posted",
        
        // River links
        'rubricbuilder:river:create' => "a new rubric titled",
        'rubricbuilder:river:update' => "a rubric titled",
        'rubricbuilder:river:annotate' => "a comment on a rubric titled",
		'rubric:river:annotate' => 'a comment on a rubric titled',
		
		// Widget
		'rubricbuilder:num' => 'Number of rubrics to display',
		'rubricbuilder:widget:description' => 'A list of your rubrics',
		'rubricbuilder:more' => 'More rubrics',
		
		// Other
		'rubricbuilder:strapline' => "%s",
		'rubricbuilder:revision' => 'Revision created %s by %s',
	
	);

	add_translation("en",$english);
?>