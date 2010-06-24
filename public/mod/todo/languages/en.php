<?php
	/**
	 * Todo English language translation
	 * 
	 * @package Todo
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Jeff Tilson
	 * @copyright THINK Global School 2010
	 * @link http://www.thinkglobalschool.com/
	 * 
	 */
$english = array(
	
	// Generic Title
	'todo:title' => 'To Do\'s',
	'item:object:todo' => 'To Do\'s',
	'item:object:todosubmission' => 'To Do Submission',
	
	// Page titles 
	'todo:title:yourtodos'	=> 'To Do\'s I\'ve Assigned',
	'todo:title:assignedtodos' => 'To Do\'s Assigned To me',
	'todo:title:create' => 'Create New To Do',
	'todo:title:edit' => 'Edit To Do',
	'todo:title:alltodos' => 'All Site To Do\'s',
	'todo:title:ownedtodos' => 'To Do\'s created by %s',
	
	// Menu items
	'todo:menu:yourtodos' => 'To Do\'s Assigned To Me',
	'todo:menu:assignedtodos' => 'To Do\'s I\'ve Assigned', 
	'todo:menu:createtodo' => 'Create To Do',
	'todo:menu:admin' => 'todo Admin',
	'todo:menu:alltodos' => 'All Site To Do\'s',	
	'todo:menu:groupassignedtodos' => 'Group Assigned To Do\'s', 	
	'todo:menu:groupcreatetodo' => 'Create Group To Do', 	
	
	// Labels 
	'todo:label:noresults' => 'No Results',
	'todo:label:description' => 'Instructions',
	'todo:label:duedate' => 'Due Date',
	'todo:label:assignto' => 'Assign To', 
	'todo:label:returnrequired' => 'Return Required', 
	'todo:label:individuals' => 'Individual(s)',
	'todo:label:groups' => 'Group or Channel',
	'todo:label:loggedin' => 'Logged In Users', 
	'todo:label:assigneesonly' => 'Assignees Only',
	'todo:label:accesslevel' => 'View Access Level',
	'todo:label:assessmentrubric' => 'Assessment Rubric', 
	'todo:label:rubricnone' => 'None',
	'todo:label:rubricnew' => 'Create New',
	'todo:label:rubricselect' => 'Select Existing',
	'todo:label:selectgroup' => 'Select Group',
	'todo:label:viewrubric' => 'View Rubric',
	'todo:label:assignees' => 'Assignees',
	'todo:label:status' => 'Status',
	'todo:label:completetodo' => 'Complete This To Do',
	'todo:label:newsubmission' => 'New Submission',
	'todo:label:additionalcomments' => 'Additional Comments (Optional)',
	'todo:label:assignee' => 'Assignee',
	'todo:label:datecompleted' => 'Date Completed',
	'todo:label:submission' => 'Submission',
	'todo:label:complete' => 'Complete',
	'todo:label:incomplete' => 'Upcoming',
	'todo:label:statusincomplete' => 'Incomplete',
	'todo:label:viewsubmission' => 'View Submission',
	'todo:label:todo' => 'Assignment',
	'todo:label:moreinfo' => 'Additional Information',
	'todo:label:worksubmitted' => 'Work Submitted',
	'todo:label:addlink' => 'Add Link',
	'todo:label:addfile' => 'Add File',
	'todo:label:link' => 'Link',
	'todo:label:content' => 'Content',
	'todo:label:rubricpicker' => 'Choose Rubric',
	'todo:label:pastdue' => 'Past Due',
	'todo:label:nextweek' => 'Due Next Week', 
	'todo:label:future' => 'Future To Do\'s',
	
	// River
	'todo:river:annotate' => "a comment on a todo titled",
	
	// Messages
	'todo:success:create' => 'Todo successfully submitted',
	'todo:success:edit' => 'Todo successfully edited',
	'todo:success:delete' => 'Todo successfully deleted',
	'todo:success:submissiondelete' => 'Submission successfully deleted',
	'todo:error:requiredfields' => 'One of more required fields are missing',
	'todo:error:create' => 'There was an error creating your Todo',
	'todo:error:edit' => 'There was an error editing the Todo',
	'todo:error:delete' => 'There was an error deleting the Todo',
	'todo:error:submissiondelete' => 'There was an error deleting the submission',
	'todo:error:permission' => 'You do not have permission to create/edit this object', 
	'todo:error:permissiondenied' => 'Permission Denied', 
	
	// Other content
	'todo:strapline' => 'Due: %s',
	'todo:strapline:mode' => '%s',
	'groups:enabletodo' => 'Enable group to do\'s',
	'todo:group' => 'Group to do\'s', 

);

add_translation('en',$english);

?>