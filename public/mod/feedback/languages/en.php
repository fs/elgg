<?php

    /**
     * Elgg Feedback plugin
     * Feedback interface for Elgg sites
     * 
     * @package Feedback
     * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
     * @author Prashant Juvekar
     * @copyright Prashant Juvekar
     * @link http://www.linkedin.com/in/prashantjuvekar
     */

	$english = array(

		'item:object:feedback' => 'Feedback',
		'feedback:label' => 'Feedback',
		'feedback:title' => 'Feedback',

		'feedback:message' => 'Love it? Hate it? Want to suggest new features or report a bug? We would love to hear from you.',
		
		'feedback:default:id' => 'Name and/or Email',
		'feedback:default:txt' => 'Let us know what you think!',
		'feedback:default:txt:err' => 'No feedback message has been provided.\nWe value your suggestions and criticisms.\nPlease enter your message and press Send.',
		'feedback:default:title' => 'Title',
		'feedback:default:title:err' => 'No title has been provided, please enter a title for your feedback.',

		'feedback:captcha:blank' => 'No captcha input provided',
		
		'feedback:submit_msg' => 'Submitting...',
		'feedback:submit_err' => 'Could not submit feedback!',
		
		'feedback:submit:error' => 'Could not submit feedback!',
		'feedback:error:status' => 'There was an error updating the status',
		'feedback:submit:success' => 'Feedback submitted successfully. Thank you!',
		
		'feedback:admin:menu' => 'Feedback',
		'feedback:admin:title' => 'All Site Feedback',
		
		'feedback:title:yourfeedback' => "Your Feedback",
		
		'feedback:submenu:yourfeedback' => 'Your Feedback',
		'feedback:submenu:allfeedback' => 'All Feedback',
		
		'feedback:update:submit' => 'Submit',
		'feedback:update:confirm' => 'Are you sure you want to update?',
		'feedback:update:like' => "Feelin' It",
		'feedback:update:dislike' => "Not Feelin' It",
		'feedback:update:success' => 'Feedback was updated successfully',
		'feedback:update:failure' => 'There was an error updating the feedback',
				
		'feedback:delete:success' => 'Feedback was deleted successfully',
		
		'feedback:mood:' => 'None',
		'feedback:mood:angry' => 'Angry',
		'feedback:mood:neutral' => 'Neutral',
		'feedback:mood:happy' => 'Happy',

		'feedback:status' => 'Status',
		'feedback:status:submitted' => 'Submitted',
		'feedback:status:acknowledged' => 'Acknowledged',
		'feedback:status:resolvedaction' => 'Resolved with Action',
		'feedback:status:resolvednoaction' => 'Resolved without Action',
		'feedback:status:resolved' => 'Resolved',		
		'feedback:status:inprogress'	=>	'In Progress',
		'feedback:status:duplicate' =>	'Duplicate',
	
		'feedback:comments'	=> 'Comments',
		'feedback:comments:new' => 'New Comment',

		'feedback:about:' => 'None',
		'feedback:about:bug_report' => 'Bug Report',
		'feedback:about:content' => 'Content',
		'feedback:about:suggestions' => 'Suggestions',
		'feedback:about:compliment' => 'Compliment',
		'feedback:about:other' => 'Other',
		
		'feedback:list:mood' => 'Mood',
		'feedback:list:about' => 'About',
		'feedback:list:page' => 'Submit Page',
		'feedback:list:from' => 'From',
		'feedback:list:title' => 'Title', 
		
		'feedback:user_1' => "User Name 1: ",
		'feedback:user_2' => "User Name 2: ",
		'feedback:user_3' => "User Name 3: ",
		'feedback:user_4' => "User Name 4: ",
		'feedback:user_5' => "User Name 5: ",
		
		'feedback:email:subject' => 'Received feedback from %s',
		'feedback:email:body' => '%s',
		'feedback:email:updatedsubject' => "Your feedback has been updated!",
		'feedback:email:updatedbody' => 'Your feedback has been updated, check out your feedback here: %s',
		
		'feedback:river:create' => 'new feedback titled',
		'feedback:river:created' => "%s submitted",
		'feedback:river:update' => 'feedback titled',
		'feedback:river:updated' => "%s has updated the status of",
		'feedback:river:posted' => "%s posted a comment on",
		
		/* Spiced up text for the river */
		'feedback:river:comment:like' => "%s is feelin' the feedback titled %s",
		'feedback:river:comment:dislike' => "%s is not feelin' the feedback titled %s",
		'feedback:river:comment' => "%s posted a comment on feedback titled %s",
		
		'feedback:viewtitle' => 'View Feedback',
		
		'feedback:noresults' => 'No Results', 
		
		'feedback:strapline' => 'From %s %s',
		
		'feedback:settings:disablepublic' => 'Visible to only logged in users', 
		'feedback:settings:riverdisplay' => 'Show new feedback and updates on river'
	);
					
	add_translation("en",$english);
?>